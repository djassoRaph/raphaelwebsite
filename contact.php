<?php
// contact.php — form handler for raphaelreck.com
// Deployed on o2switch

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// --- Config ---
$to      = 'contact@raphaelreck.com';
$subject_prefix = '[raphaelreck.com]';

// --- Collect & sanitize inputs ---
$name    = trim(strip_tags($_POST['name']    ?? ''));
$email   = trim(strip_tags($_POST['email']   ?? ''));
$type    = trim(strip_tags($_POST['enquiry_type'] ?? 'not specified'));
$message = trim(strip_tags($_POST['message'] ?? ''));

// --- Basic validation ---
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email address']);
    exit;
}

// --- Build email ---
$subject = $subject_prefix . ' New enquiry from ' . $name;

$body  = "New contact form submission\n";
$body .= "===========================\n\n";
$body .= "Name:    " . $name . "\n";
$body .= "Email:   " . $email . "\n";
$body .= "Type:    " . $type . "\n\n";
$body .= "Message:\n" . $message . "\n";

$headers  = "From: noreply@raphaelreck.com\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// --- Send ---
$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Mail failed to send']);
}