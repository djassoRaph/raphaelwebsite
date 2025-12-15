<?php $base_path = '../'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Productivity. And debugging. | Blog | Raphaël Reck</title>
  <meta name="description" content="Working on automatically updating my blog with git push. This launches a workflow on GitHub, then pushes everything to FTP via a script."/>
  <link rel="canonical" href="https://raphaelreck.com/blog/Productivityanddebugging.php"/>
  <link rel="stylesheet" href="../style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <meta property="og:type" content="article"/>
  <meta property="og:title" content="Productivity. And debugging."/>
  <meta property="og:url" content="https://raphaelreck.com/blog/Productivityanddebugging.html"/>
  <meta property="og:site_name" content="Raphaël Reck"/>
  <meta property="article:published_time" content="2025-07-30T00:00:00+02:00"/>
</head>
<body>
  <button id="theme-toggle" class="theme-toggle" aria-label="Toggle theme"><i class="fas fa-moon"></i></button>
  <div class="container">
    <?php include __DIR__ . '/../includes/navigation.php'; ?>

    <main>
      <article class="blog-post">
        <header class="post-header">
          <h1>Productivity. And debugging.</h1>
          <p class="post-meta">
            <time datetime="2025-07-30">July 31, 2025</time>
          </p>
        </header>

        <p>So I've been working on automatically updateing my blog simply by doing a git push.</p>
        <p>This launches a workflow on my github. Then pushes everything to my FTP via a script.</p>
        <p>I'll have to study it.</p>
        <p> Because I just bindly thought it would do bash scripts, but no, it's calling some one elses script.</p>
        <p>Being on holiday does give you the time to work on your side projects ! Lets go ! </p>

        <hr>
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
