<?php $base_path = '../'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Blog | Raphaël Reck</title>
    <meta name="description" content="Welcome to my blog. I document my journey in software development, including bugs, AI-powered projects, architectural decisions, and philosophical ramblings. New posts usually arrive on Wednesdays."/>
    <link rel="canonical" href="https://raphaelreck.com/blog/index.php"/>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="alternate" type="application/rss+xml" title="RSS" href="/feed.xml" />
    <link rel="alternate" type="application/atom+xml" title="Atom" href="/atom.xml" />
    <link rel="alternate" type="application/json" title="JSON Feed" href="/feed.json" />

    <meta property="og:type" content="website"/>
    <meta property="og:title" content="Raphaël Reck's Blog"/>
    <meta property="og:description" content="Documenting my work in software, including side projects, AI explorations, and development insights."/>
    <meta property="og:url" content="https://raphaelreck.com/blog/index.html"/>
    <meta property="og:site_name" content="Raphaël Reck"/>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "Raphaël Reck's Blog",
        "description": "Welcome to my blog. I document my journey in software development, including bugs, AI-powered projects, architectural decisions, and philosophical ramblings. New posts usually arrive on Wednesdays.",
        "url": "https://raphaelreck.com/blog/index.html",
        "publisher": {
            "@type": "Person",
            "name": "Raphaël Reck"
        }
    }
    </script>
</head>
<body>
  <?php include __DIR__ . '/../includes/theme-toggle.php'; ?>

  <div class="container">
    <?php include __DIR__ . '/../includes/navigation.php'; ?>

    <h1>Web Blog</h1>
    <p>Posts coming weekly, usually Wednesdays.</p>

    <section>
      <p>
        Welcome to my corner of the internet. I document things here bugs, ideas, architecture wins, AI ramblings, time audits, and the occasional philosophical spiral.
      </p>
      <blockquote class="quote">
        If you really want something to work... keep it simple.
      </blockquote>
    </section>
    <br>
    <div class="blog-posts">
      <article class="blog-post">
        <p><em>December 10, 2025</em></p>
        <h2><a href="maidenless.php"> Unfortunately, for you, however you are maidenless.</a></h2>
        <p>Some of my thoughts, and experience, with artificial intelligence in enhancing productivity and creativity.</p>
      </article>
      <hr>
      <article class="blog-post">
        <p><em>December 07, 2025</em></p>
        <h2><a href="2025-12-07-whats_in_the_wind.php">What's been in the Wind: Two Projects, One Developer Block</a></h2>
        <p>How pausing one game project led to launching two new ones : a civic engagement platform and an open-source social MMO.</p>
      </article>
      <hr>
      <article class="blog-post">
        <p><em>October 30, 2025</em></p>
        <h2><a href="llmr.php">Introducing LLMR: RSS for the AI Era</a></h2>
        <p>Jibberish mode but for LLMs when browsing websites.</p>
      </article>
      <hr>
      <article class="blog-post">
        <p><em>October 08, 2025</em></p>
        <h2><a href="2025-10-08-juggling-fireballs.php">Juggling balls on fire</a></h2>
        <p>What do you do when your team of two becomes a team of one, and the projects are on fire?</p>
      </article>
      <hr>
      <article class="blog-post">
        <p><em>September 22, 2025</em></p>
        <h2><a href="you-cant-get-them-all.php">You can't get them all.</a></h2>
        <p>But you can get the right ones.</p>
        <p>Sometimes I wish days were 72 hours long, so I could get more things done.</p>
      </article>
      <hr>
      <article class="blog-post">
        <p><em>September 06, 2025</em></p>
        <h2><a href="claudio.php">Claude behaves unexpectedly!</a></h2>
        <p>Hey Claude. I wanna bounce ideas about my game project.</p>
        <p><i>Sure thing. Here's your... entire project. Just coded from top to buttom. That doesn't work... ^^</i></p>
      </article>
      <hr>
      <article class="blog-post">
        <p><em>September 04, 2025</em></p>
        <h2><a href="laravel040925.php">Laravel, just make it work!</a></h2>
        <p>See this pile of trash ? It's an unfinished website, make it work the way we want but we wont tell you how!</p>
        <p>Here's some of the bugs I fixed just recently after inheriting the unfinished "legacy" code base</p>
      </article>
      <hr>
      <article class="blog-post">
        <p><em>August 30, 2025</em></p>
        <h2><a href="yaygodot.php">Godot: From 2D buggy ideas to Functional Isometric 3D scene !</a></h2>
        <p>A real debugging story: how AI assistants (GPT, Claude, Gemini) helped fix character collisions and movement in a Godot 3D project, turning a buggy scene into a functional game prototype.</p>
      </article>
      <hr>
      <article class="blog-post">
        <p><em>August 30, 2025</em></p>
        <h2><a href="when-webservices-lie.php">Magical Drupal messages, Empty Emails and Wrong API Responses.</a></h2>
        <p>Another legacy system integration nightmare featuring PowerPoint "documentation," Drupal hooks that fire too early, and web services that return completely different data than promised. When you can't test locally and debugging happens through client screenshots, every assumption becomes a liability. Spoiler: the eligibility check wasn't returning "Yes" or "No" like the specs claimed.</p>
      </article>
      <hr>
      <article class="blog-post">
        <div>
          <p><em><time datetime="2025-08-25">Posted on August the 25th, 2025</time></em></p>
          <h2>Why I switched from GPT to Claude for coding</h2>
          <p>
            After using GPT for a while, I found that Claude's approach to coding tasks was more aligned with my workflow. Claude seemed to understand context better and provided more relevant suggestions.
          </p>
          <p>Except now it doesn't anymore, and I'm not sure why.</p>
        </div>
      </article>
      <hr>
      <article class="blog-post">
        <h2>
        <a href="2025-08-25-ai-vs-legacy-drupal.php">
        When AI-Generated Code Meets Legacy Drupal: A Horror Story
        </a>
      </h2>
      <p class="post-meta">
        <time datetime="2025-08-25">August 25, 2025</time> · Debugging · Drupal · AI
      </p>
      <p>What started as a "quick bugfix" spiraled into a multi-day nightmare></p>
      </article>
      <hr>
      <article class="blog-post">
        <h2><a href="bitcoinscam.php">Bitcoin Scam Attempt: How a Script Kiddy Tried to Extort Me for $200</a></h2>
        <p><em><time datetime="2025-08-18">Posted on August 18, 2025</time></em></p>
        <p>Online scams are everywhere</p>
      </article>
      <hr>
      <article class="blog-post">
        <h2> <a href="HolidaysSideProject.php">Holidays and side projects.</a></h2>
        <p><em><time datetime="2025-08-17">Posted on August the 17th, 2025</time></em></p>
        <div>
         <p>I've been working on side projects a lot</p>
        </div>
      </article>
      <hr>
      <article class="blog-post">
        <h2>Productivity. And debugging.</h2>
        <p><em><time datetime="2025-07-30">Posted on July 31, 2025</time></em></p>
        <div>
          <p>So, I've been working on automatically updating my blog simply by doing a git push.</p>
          <p>This launches a workflow on my github. Then pushes everything to my FTP via a script.</p>
            <p>I'll have to study it.</p>
            <p> Because I just bindly thought it would do bash scripts, but no, it's calling some one elses script.</p>
          <p>Being on holiday does give you the time to work on your side projects ! Lets go ! </p>
        </div>
      </article>
      <hr>
      <article class="blog-post">
        <h2><a href="CETheadaches.php">CET headaches again. And procrastination.</a></h2>
        <p><em><time datetime="2025-07-30">Posted on July 30, 2025</time></em></p>
        <div>
          <p>
            I've not been able to set my head straight to actually be productive. I've just not been able to produce the things on my list.
          </p>
      </div>
      </article>
      <hr>
      <article class="blog-post">
        <h2><a href="SVNgit.php">SVN/Git Wrangling, Laravel Rage, and CET Headaches</a></h2>
        <p><em><time datetime="2025-07-06"></time>Posted on July 6, 2025</time></em></p>
        <div>
        <p>This week was chaos and cleanup.</p>

        <p style="font-family: monospace; color: gray;">
          // no JS frameworks were harmed in the making of this log
        </p>
        </div>
      </article>
      <hr>
      <article class="blog-post">
      <div>
        <p>Old thought</p>
        <p>
      I believe the web should be simple. I don't like frameworks. They bloat, obfuscate, and complicate things that HTML already did well decades ago.
      The more we "frame" "work", the more tedious life becomes. <br>More stepping over things to actually get to the source of what you want.
      It's like climbing through a window each time.
      </p>
      <img class="framework" src="../public/climbingframeworks.png" alt="Climbing Frameworks">
      </div>
      </article>
      <hr>
    </div>
  </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
  </div>

  
  <?php include __DIR__ . '/../includes/mobile-menu.php'; ?>
</body>
</html>
