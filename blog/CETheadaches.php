<?php $base_path = '../'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include __DIR__ . '/../includes/theme-init.php'; ?>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CET headaches again. And procrastination. | Blog | Raphaël Reck</title>
  <meta name="description" content="Struggling with productivity, working on a house bidding website, smart mirror projects, and delivering code to clients in Marseille without Git."/>
  <link rel="canonical" href="https://raphaelreck.com/blog/CETheadaches.php"/>
  <link rel="stylesheet" href="../style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <meta property="og:type" content="article"/>
  <meta property="og:title" content="CET headaches again. And procrastination."/>
  <meta property="og:url" content="https://raphaelreck.com/blog/CETheadaches.html"/>
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
          <h1>CET headaches again. And procrastination.</h1>
          <p class="post-meta">
            <time datetime="2025-07-30">July 30, 2025</time>
          </p>
        </header>

        <p>
          I've not been able to set my head straight to actually be productive. I've just not been able to produce the things on my list.
          I've been working on a website that deals with a house bidding  -  to help sell a house. It's available at
          <a href="https://julien.raphaelreck.com" target="_blank">julien.raphaelreck.com</a>. I'll be getting 400 Euros for making it.
        </p>
        <p>
          It was nice to work with AI to code the website and generate prompts. It works, but nothing extraordinary.
          Still, it might open other opportunities  -  I have a meeting with a potential client. Just sent him a quotation for a website.
        </p>
        <p>
          I also revisited an old project: a smart mirror core with AI  -  the idea is to display workout images, with a timer and a motion detector to guide home workouts in front of a mirror.
          I only have a Raspberry Pi so far. It's early days, but the idea lives. <br> I saw this video recently. <br> <a href="https://www.youtube.com/watch?v=y8dx7ZimArc"> Building a Smart Morning Routine Dashboard (From a Jailbroken Lululemon Mirror) </a> I really like what they did.
        </p>
        <p>
          Other than that, I still need to finalize the Roots Time Corporation manifesto and upload the memes I like (and remade) to the site.
        </p>
        <p>
          I managed just before leaving the office for my holidays to deliver some code for the client for them to test. Working for the city of Marseille with old code and practices and databases is a pain. No Git....
        </p>

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
