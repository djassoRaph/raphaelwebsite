<?php $base_path = '../'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Old thought: Keep it simple | Blog | Raphaël Reck</title>
  <meta name="description" content="I believe the web should be simple. I don't like frameworks. They bloat, obfuscate, and complicate things that HTML already did well decades ago."/>
  <link rel="canonical" href="https://raphaelreck.com/blog/frameworks.php"/>
  <link rel="stylesheet" href="../style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <meta property="og:type" content="article"/>
  <meta property="og:title" content="Old thought: Keep it simple"/>
  <meta property="og:url" content="https://raphaelreck.com/blog/frameworks.html"/>
  <meta property="og:site_name" content="Raphaël Reck"/>
  <meta property="article:published_time" content="2025-07-01T00:00:00+02:00"/>
</head>
<body>
  <button id="theme-toggle" class="theme-toggle" aria-label="Toggle theme"><i class="fas fa-moon"></i></button>
  <div class="container">
    <?php include __DIR__ . '/../includes/navigation.php'; ?>

    <main>
      <article class="blog-post">
        <header class="post-header">
          <h1>Old thought: Keep it simple</h1>
          <p class="post-meta">
            <time datetime="2025-07-01">July 2025</time>
          </p>
        </header>

        <p>Old thought</p>
        <p>
      I believe the web should be simple. I don't like frameworks. They bloat, obfuscate, and complicate things that HTML already did well decades ago.
      The more we "frame" "work", the more tedious life becomes. <br>More stepping over things to actually get to the source of what you want.
      It's like climbing through a window each time.
      </p>
      <img class="framework" src="../public/climbingframeworks.png" alt="Climbing Frameworks">

        <br>
        <button class="link-btn"><a href="index.php">← Back to Blog Index</a></button>
      </article>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
  </div>

  
  <?php include __DIR__ . '/../includes/mobile-menu.php'; ?>
</body>
</html>
</body>
</html>
