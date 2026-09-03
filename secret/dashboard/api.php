<?php
// api.php — private dashboard data aggregator (Calendar + Tasks + Briefing)
// Deployed on o2switch. Stateless: fetches fresh from Google/Notion/Anthropic on every request.

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

const WORKDAY_HOURS  = 8.0;
const ALLOWED_ORIGIN = 'https://raphaelreck.com';

// --- Guards ---

function fail(int $status, string $message): void {
    http_response_code($status);
    echo json_encode(['error' => $message]);
    exit;
}

function checkOrigin(): void {
    $origin  = $_SERVER['HTTP_ORIGIN']  ?? null;
    $referer = $_SERVER['HTTP_REFERER'] ?? null;

    if ($origin !== null) {
        if ($origin !== ALLOWED_ORIGIN) {
            fail(403, 'Forbidden');
        }
        return;
    }

    if ($referer !== null) {
        if (strpos($referer, ALLOWED_ORIGIN . '/') !== 0) {
            fail(403, 'Forbidden');
        }
        return;
    }

    // Neither header present — Basic Auth (enforced by Apache before this
    // script runs at all) remains the authoritative gate in that case.
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    fail(405, 'Method not allowed');
}

checkOrigin();

define('DASHBOARD_API', true);
require __DIR__ . '/config.php';

// --- Shared HTTP wrapper ---

function httpRequest(string $method, string $url, array $headers = [], ?string $body = null): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    $responseBody = curl_exec($ch);
    if ($responseBody === false) {
        error_log('[dashboard] cURL transport error for ' . $url . ': ' . curl_error($ch));
        curl_close($ch);
        throw new Exception('Upstream request failed', 502);
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['status' => $status, 'body' => $responseBody];
}

// --- Google Calendar ---

function base64UrlEncode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function mintServiceAccountJwt(string $clientEmail, string $privateKeyPem, string $scope): string {
    $now    = time();
    $header = base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $claims = base64UrlEncode(json_encode([
        'iss'   => $clientEmail,
        'scope' => $scope,
        'aud'   => 'https://oauth2.googleapis.com/token',
        'iat'   => $now,
        'exp'   => $now + 3600,
    ]));

    $privateKey = openssl_pkey_get_private($privateKeyPem);
    if ($privateKey === false) {
        throw new Exception('Google Calendar authentication failed', 502);
    }

    $signature = '';
    $signed = openssl_sign($header . '.' . $claims, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    if ($signed === false) {
        throw new Exception('Google Calendar authentication failed', 502);
    }

    return $header . '.' . $claims . '.' . base64UrlEncode($signature);
}

function getGoogleAccessToken(): string {
    if (!is_readable(GOOGLE_SERVICE_ACCOUNT_KEY_PATH)) {
        error_log('[dashboard] service account key not readable at ' . GOOGLE_SERVICE_ACCOUNT_KEY_PATH);
        throw new Exception('Google Calendar authentication failed', 502);
    }

    $keyData = json_decode(file_get_contents(GOOGLE_SERVICE_ACCOUNT_KEY_PATH), true);
    if (!isset($keyData['client_email'], $keyData['private_key'])) {
        throw new Exception('Google Calendar authentication failed', 502);
    }

    $jwt = mintServiceAccountJwt(
        $keyData['client_email'],
        $keyData['private_key'],
        'https://www.googleapis.com/auth/calendar.readonly'
    );

    $body = http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion'  => $jwt,
    ]);

    $response = httpRequest('POST', 'https://oauth2.googleapis.com/token', [
        'Content-Type: application/x-www-form-urlencoded',
    ], $body);

    $data = json_decode($response['body'], true);
    if ($response['status'] !== 200 || !isset($data['access_token'])) {
        error_log('[dashboard] Google token exchange failed, status ' . $response['status'] . ': ' . substr($response['body'], 0, 500));
        throw new Exception('Google Calendar authentication failed', 502);
    }

    return $data['access_token'];
}

function getCalendarTimeWindow(): array {
    $tz    = new DateTimeZone('Europe/Paris');
    $now   = new DateTime('now', $tz);
    $start = (clone $now)->setTime(0, 0, 0);
    $end   = (clone $now)->setTime(23, 59, 59);

    return [$start->format(DateTime::ATOM), $end->format(DateTime::ATOM), $now->format('Y-m-d')];
}

function fetchCalendarEvents(string $accessToken, string $calendarId, string $timeMin, string $timeMax): array {
    $query = http_build_query([
        'timeMin'      => $timeMin,
        'timeMax'      => $timeMax,
        'singleEvents' => 'true',
        'orderBy'      => 'startTime',
        'timeZone'     => 'Europe/Paris',
    ]);
    $url = 'https://www.googleapis.com/calendar/v3/calendars/' . rawurlencode($calendarId) . '/events?' . $query;

    $response = httpRequest('GET', $url, ['Authorization: Bearer ' . $accessToken]);
    $data = json_decode($response['body'], true);

    if ($response['status'] !== 200 || !isset($data['items'])) {
        error_log('[dashboard] Calendar fetch failed for ' . $calendarId . ', status ' . $response['status'] . ': ' . substr($response['body'], 0, 500));
        throw new Exception('Google Calendar request failed', 502);
    }

    return $data['items'];
}

function calculateFreeHours(array $events): float {
    $busySeconds = 0;
    foreach ($events as $event) {
        $startStr = $event['start']['dateTime'] ?? null;
        $endStr   = $event['end']['dateTime'] ?? null;
        if ($startStr === null || $endStr === null) {
            continue; // all-day event — uses 'date', not 'dateTime'
        }
        $busySeconds += max(0, strtotime($endStr) - strtotime($startStr));
    }

    return round(max(0, WORKDAY_HOURS - $busySeconds / 3600), 1);
}

function summarizeEventForResponse(array $event): array {
    return [
        'title'   => $event['summary'] ?? 'Untitled',
        'start'   => $event['start']['dateTime'] ?? $event['start']['date'] ?? null,
        'end'     => $event['end']['dateTime'] ?? $event['end']['date'] ?? null,
        'all_day' => !isset($event['start']['dateTime']),
    ];
}

// --- Notion ---

function queryNotionTasks(): array {
    $results = [];
    $cursor  = null;

    do {
        $body = [
            'page_size' => 100,
            'filter'    => ['property' => 'Status', 'select' => ['does_not_equal' => 'Done']],
        ];
        if ($cursor !== null) {
            $body['start_cursor'] = $cursor;
        }

        $response = httpRequest('POST', 'https://api.notion.com/v1/data_sources/' . NOTION_DATA_SOURCE_ID . '/query', [
            'Authorization: Bearer ' . NOTION_TOKEN,
            'Notion-Version: ' . NOTION_VERSION,
            'Content-Type: application/json',
        ], json_encode($body));

        $data = json_decode($response['body'], true);
        if ($response['status'] !== 200 || !isset($data['results'])) {
            error_log('[dashboard] Notion query failed, status ' . $response['status'] . ': ' . substr($response['body'], 0, 500));
            throw new Exception('Notion request failed', 502);
        }

        $results = array_merge($results, $data['results']);
        $cursor  = ($data['has_more'] ?? false) ? ($data['next_cursor'] ?? null) : null;
    } while ($cursor !== null);

    return $results;
}

function extractTaskFields(array $page): array {
    $props     = $page['properties'] ?? [];
    $titleRuns = $props['Title']['title'] ?? [];
    $descRuns  = $props['Description']['rich_text'] ?? [];

    return [
        'id'           => $page['id'] ?? '',
        'title'        => implode('', array_column($titleRuns, 'plain_text')) ?: '(untitled)',
        'status'       => $props['Status']['select']['name'] ?? null,
        'priority'     => $props['Priority']['select']['name'] ?? null,
        'due_date'     => $props['Due Date']['date']['start'] ?? null,
        'description'  => implode('', array_column($descRuns, 'plain_text')),
        'created_time' => $props['Date Created']['created_time'] ?? null,
        'notion_url'   => $page['url'] ?? null,
    ];
}

function bucketTasksByStatus(array $pages): array {
    $buckets = ['Doing' => [], 'To Do' => [], 'Backlog' => [], 'Parking' => []];

    foreach ($pages as $page) {
        $task   = extractTaskFields($page);
        $status = $task['status'];

        if (!isset($buckets[$status])) {
            error_log('[dashboard] skipping task with unrecognized/empty status: ' . $task['id']);
            continue;
        }

        unset($task['status']);
        $buckets[$status][] = $task;
    }

    return $buckets;
}

// --- AI briefing ---

function buildBriefingPrompt(array $calendar, array $tasksByStatus, string $dateLabel): string {
    $eventCount  = count($calendar['events']);
    $holidayLine = $calendar['holiday'] ? (', holiday: ' . $calendar['holiday']['name']) : '';

    $taskLines = [];
    foreach ($tasksByStatus as $status => $tasks) {
        $titles        = array_slice(array_column($tasks, 'title'), 0, 5);
        $taskLines[]   = $status . ': ' . count($tasks) . (count($titles) ? ' (' . implode('; ', $titles) . ')' : '');
    }

    return "You are a terse, direct productivity assistant.\n" .
        "Today is {$dateLabel}.\n" .
        "Calendar: {$eventCount} event(s), {$calendar['free_hours']}h free{$holidayLine}.\n" .
        "Tasks — " . implode(', ', $taskLines) . ".\n\n" .
        "In 3-4 plain sentences (no bullet points, no headers), say what to focus on first today and one thing to skip. Be concrete, not generic.";
}

function generateBriefing(string $prompt): string {
    $response = httpRequest('POST', 'https://api.anthropic.com/v1/messages', [
        'x-api-key: ' . ANTHROPIC_API_KEY,
        'anthropic-version: 2023-06-01',
        'content-type: application/json',
    ], json_encode([
        'model'      => 'claude-haiku-4-5',
        'max_tokens' => 600,
        'messages'   => [['role' => 'user', 'content' => $prompt]],
    ]));

    $data = json_decode($response['body'], true);
    if ($response['status'] !== 200 || !isset($data['content'])) {
        error_log('[dashboard] Anthropic call failed, status ' . $response['status'] . ': ' . substr($response['body'], 0, 500));
        throw new Exception('Briefing generation failed', 502);
    }

    foreach ($data['content'] as $block) {
        if (($block['type'] ?? null) === 'text') {
            return $block['text'];
        }
    }

    throw new Exception('Briefing generation failed', 502);
}

// --- Main ---

try {
    [$timeMin, $timeMax, $dateLabel] = getCalendarTimeWindow();

    $accessToken   = getGoogleAccessToken();
    $primaryEvents = fetchCalendarEvents($accessToken, GOOGLE_CALENDAR_ID, $timeMin, $timeMax);
    $holidayEvents = fetchCalendarEvents($accessToken, GOOGLE_HOLIDAY_CALENDAR_ID, $timeMin, $timeMax);
    $holiday       = $holidayEvents ? ['name' => $holidayEvents[0]['summary'] ?? 'Public holiday'] : null;

    $calendarPayload = [
        'date'          => $dateLabel,
        'timezone'      => 'Europe/Paris',
        'workday_hours' => WORKDAY_HOURS,
        'free_hours'    => calculateFreeHours($primaryEvents),
        'holiday'       => $holiday,
        'events'        => array_map('summarizeEventForResponse', $primaryEvents),
    ];

    $tasksByStatus = bucketTasksByStatus(queryNotionTasks());
    $taskCounts    = array_map('count', $tasksByStatus);

    $briefing = null;
    try {
        $briefing = generateBriefing(buildBriefingPrompt($calendarPayload, $tasksByStatus, $dateLabel));
    } catch (Throwable $e) {
        error_log('[dashboard] briefing generation failed: ' . $e->getMessage());
        $briefing = null;
    }

    echo json_encode([
        'generated_at' => (new DateTime('now', new DateTimeZone('Europe/Paris')))->format(DateTime::ATOM),
        'calendar'     => $calendarPayload,
        'briefing'     => $briefing,
        'tasks'        => $tasksByStatus,
        'task_counts'  => $taskCounts,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    $status = $e->getCode();
    if (!is_int($status) || $status < 400 || $status >= 600) {
        $status = 500;
    }
    error_log('[dashboard api.php] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code($status);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
