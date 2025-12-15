<?php $base_path = '../'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include __DIR__ . '/../includes/theme-init.php'; ?>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>When AI‑Generated Code Meets Legacy Drupal: A Horror Story | Blog | Raphaël Reck</title>
  <meta name="description" content="A real debugging story: conflicting email systems, redirect loops, and AI-generated boilerplate breaking a legacy Drupal workflow - and how we fixed it."/>
  <link rel="canonical" href="https://raphaelreck.com/blog/2025-08-25-ai-vs-legacy-drupal.php"/>
  <link rel="stylesheet" href="../style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <!-- Open Graph -->
  <meta property="og:type" content="article"/>
  <meta property="og:title" content="When AI‑Generated Code Meets Legacy Drupal: A Horror Story"/>
  <meta property="og:description" content="Conflicting email systems, redirect loops, and AI boilerplate in a legacy Drupal workflow - what went wrong and how we fixed it."/>
  <meta property="og:url" content="https://raphaelreck.com/blog/2025-08-25-ai-vs-legacy-drupal.html"/>
  <meta property="og:site_name" content="Raphaël Reck"/>
  <meta property="article:author" content="Raphaël Reck"/>
  <meta property="article:published_time" content="2025-08-25T00:00:00+02:00"/>
  <meta property="article:tag" content="Drupal"/>
  <meta property="article:tag" content="AI"/>
  <meta property="article:tag" content="Debugging"/>

  <!-- Optional: light code highlighting via CSS, if you have styles; otherwise plain <pre><code> renders fine -->
  <style>
    .post-header { margin-bottom: 1rem; }
    .post-meta { opacity: .7; font-size: .9rem; }
    .tag { display:inline-block; margin-right:.5rem; font-size:.8rem; padding:.15rem .5rem; border:1px solid currentColor; border-radius:999px; }
    pre { overflow:auto; padding:1rem; border:1px solid #333; border-radius:.5rem; }
    code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace; }
    .callout { border-left:4px solid #888; padding:.75rem 1rem; background:rgba(127,127,127,.06); margin:1rem 0; }
  </style>

  <!-- JSON-LD for SEO -->
  <script type="application/ld+json">
  {
    "@context":"https://schema.org",
    "@type":"BlogPosting",
    "headline":"When AI‑Generated Code Meets Legacy Drupal: A Horror Story",
    "datePublished":"2025-08-25T00:00:00+02:00",
    "dateModified":"2025-08-25T00:00:00+02:00",
    "author":{"@type":"Person","name":"Raphaël Reck"},
    "publisher":{"@type":"Person","name":"Raphaël Reck"},
    "mainEntityOfPage":{"@type":"WebPage","@id":"https://raphaelreck.com/blog/2025-08-25-ai-vs-legacy-drupal.html"},
    "keywords":["Drupal","AI","Debugging","Architecture","Legacy code"],
    "description":"A real debugging story: conflicting email systems, redirect loops, and AI-generated boilerplate breaking a legacy Drupal workflow - and how we fixed it."
  }
  </script>
</head>
<body>
  <button id="theme-toggle" class="theme-toggle" aria-label="Toggle theme"><i class="fas fa-moon"></i></button>
  <div class="container">
        <?php include __DIR__ . '/../includes/navigation.php'; ?>

    <main>
      <article class="blog-post">
        <header class="post-header">
          <h1>When AI‑Generated Code Meets Legacy Drupal: A Horror Story</h1>
          <p class="post-meta">
            <time datetime="2025-08-25">August 25, 2025</time>
            · <span>~7–8 min read</span>
            · <span class="tag">Drupal</span>
            <span class="tag">AI</span>
            <span class="tag">Debugging</span>
          </p>
        </header>

        <p><em>(Client and environment details anonymized.)</em></p>

        <h2 id="setup">The Setup: A Simple Form That Wasn’t</h2>
        <p>
          What started as a “quick bugfix” spiraled into a multi‑day debugging run:
          conflicting email systems, infinite redirects, and the realization that parts of the codebase
          had been stitched together by AI assistants with zero awareness of the project’s architecture.
        </p>
        <p>
          Symptom: users submitted a form but saw no success message and received no confirmation email.
          Root cause: several small, generic snippets interacting in all the wrong ways.
        </p>

        <h2 id="first-red-flag">The First Red Flag: <code>&lt;front&gt;</code></h2>
        <p>Buried in a form handler, I found this:</p>
        <pre><code class="language-php">&lt;?php
        return new RedirectResponse(Url::fromRoute('&lt;front&gt;')-&gt;toString());
        </code></pre>
        <p>
          Looks harmless. In reality, <code>&lt;front&gt;</code> is generic boilerplate AI tools love to suggest.
          Here, it clashed with Drupal’s own form redirect flow, preventing user success messages from ever appearing.
        </p>

        <h2 id="cascade">The Cascade of Failures</h2>

        <h3>1) Three Email Systems, One Message</h3>
        <ul>
          <li>Drupal YAML email handler</li>
          <li>Custom PHP utility (legacy)</li>
          <li>Global hook on submission</li>
        </ul>
        <p>
          All three tried to send the same confirmation. None agreed on recipients.
          Some referenced placeholder values; others bypassed handler enablement.
        </p>

        <h3>2) The <code>_default</code> Value Trap</h3>
<pre><code class="language-yaml">settings:
  to_mail: _default
  subject: _default
  from_mail: _default
</code></pre>
        <p>
          Intended to resolve into real addresses, left literal instead.
          Result: attempts like “from info@example.com to <code>_default</code>”. Delivery: none.
        </p>

        <h3>3) Redirect Loops Instead of Business Logic</h3>
        <p>
          Form A always redirected to Form B. Form B always redirected to Form A.
          The actual rule was conditional: certain users should stop after Form A and receive a notification.
          The hardcoded loop ignored that completely.
        </p>

        <h3>4) User Feedback Coupled to Backend Validation</h3>
<pre><code class="language-php">&lt;?php
if ($nb_demande &gt; 0) {
  \Drupal::messenger()-&gt;addMessage(t('Success!'), 'status');
}
</code></pre>
        <p>
          If a backend rightly rejected the request, the user saw nothing - not even
          a “we got your submission” info. Confidence killer.
        </p>

        <h2 id="ai-gun">The AI Smoking Gun</h2>
        <ul>
          <li>Generic redirects where project‑specific routes were required</li>
          <li>Token usage without session integration (e.g., swapping custom tokens without wiring)</li>
          <li>Email helpers overwriting their own recipient arrays</li>
          <li>Errors logged server‑side, zero UX feedback client‑side</li>
        </ul>
        <p>Each snippet looked fine in isolation - together, they collapsed.</p>

        <h2 id="fix">The Fix: Less Code, Not More</h2>
        <ol>
          <li><strong>Remove duplication:</strong> disable redundant PHP mail calls; use the single YAML handler.</li>
          <li><strong>Use supported tokens:</strong> replace broken custom tokens with standard ones like <code>[current-user:mail]</code> (or project‑specific, wired properly).</li>
          <li><strong>Override redirects conditionally:</strong> enforce business rules in PHP when config is too generic.</li>
          <li><strong>Decouple UX from backend verdicts:</strong> always acknowledge submission; show separate messages for validation outcomes.</li>
        </ol>

        <div class="callout">
          <strong>Rule of thumb:</strong> in legacy systems, integration > snippets.
          Prefer one well‑understood pathway over three competing “helpers.”
        </div>

        <h2 id="lessons">Lessons Learned</h2>
        <h3>For Developers</h3>
        <ul>
          <li>AI‑generated code defaults to generic patterns - verify architectural fit.</li>
          <li>Multiple systems solving the same problem is a smell.</li>
          <li>Never couple user feedback to backend acceptance; acknowledge first, validate second.</li>
        </ul>

        <h3>For Teams Using AI</h3>
        <ul>
          <li>Code reviews should sniff out boilerplate patterns, not just syntax errors.</li>
          <li>Integration tests matter most in legacy contexts.</li>
          <li>Document AI‑assisted code paths; gate architectural changes through senior review.</li>
        </ul>

        <h2 id="cost">The Real Cost</h2>
        <p>
          This “small fix” took days across multiple components. The bigger hit was to user trust,
          plus the hidden interest on technical debt from piling AI snippets on top of legacy flows.
        </p>

        <h2 id="conclusion">Conclusion</h2>
        <p>
          AI tools are excellent at neat, isolated snippets. But in complex Drupal stacks,
          context is the battlefield. Before copy‑pasting that perfect answer into production,
          remember: it doesn’t know your tokens, sessions, or business rules.
        </p>
        <p><strong>Sometimes the best code is the code you don’t write.</strong></p>

        <hr/>
        <p><em>Have you hit a similar AI‑code trap? I’m happy to review and untangle tricky integrations - reach out via LinkedIn or email.</em></p>
        <br>
        <button class="link-btn"><a href="index.php">← Back to Blog Index</a></button>
      </article>
    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
  </div>
  
  <?php include __DIR__ . '/../includes/mobile-menu.php'; ?>
</body>
</html>
