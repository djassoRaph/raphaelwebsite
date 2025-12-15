<?php $base_path = '../'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include __DIR__ . '/../includes/theme-init.php'; ?>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Blog | Raphaël Reck</title>
    <meta name="description" content=""/>
    <link rel="canonical" href="https://raphaelreck.com/blog/blankblog.php"/>
    <link rel="stylesheet" href="../style.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <meta property="og:type" content="article"/>
    <meta property="og:title" content="blankblog"/>
    <meta property="og:description" content="blankblog"/>
    <meta property="og:url" content="https://raphaelreck.com/blankblog.html"/>
    <meta property="og:site_name" content="Raphaël Reck"/>
    <meta property="article:author" content="Raphaël Reck"/>
    <meta property="article:published_time" content="2025-08-30T00:00:00+02:00"/>
    <meta property="article:tag" content="Godot"/>
    <meta property="article:tag" content="AI"/>
    <meta property="article:tag" content="Debugging"/>

    <style>
        .post-header { margin-bottom: 1rem; }
        .post-meta { opacity: .7; font-size: .9rem; }
        .tag { display:inline-block; margin-right:.5rem; font-size:.8rem; padding:.15rem .5rem; border:1px solid currentColor; border-radius:999px; }
        pre { overflow:auto; padding:1rem; border:1px solid #333; border-radius:.5rem; }
        code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace; }
        .callout { border-left:4px solid #888; padding:.75rem 1rem; background:rgba(127,127,127,.06); margin:1rem 0; }
    </style>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "blankblog",
        "datePublished": "2025-12-10T00:00:00+02:00",
        "dateModified": "2025-12-10T00:00:00+02:00",
        "author": { "@type": "Person", "name": "Raphaël Reck" },
        "publisher": { "@type": "Person", "name": "Raphaël Reck" },
        "mainEntityOfPage": { "@type": "WebPage", "@id": "https://raphaelreck.com/blog/blankblog.html" },
        "keywords": ["Productivity", "AI", "Tools", "Work", "Efficiency", "Creative"],
        "description": "."
    }
    </script>
</head>
<body>
    <div class="container">
            <?php include __DIR__ . '/../includes/navigation.php'; ?>
        <main>
            <article class="blog-post">
                <header class="post-header">
                    <h1>Unfortunately, for you, however you are maidenless.</h1>
                    <p class="post-meta">
                        <time datetime="2025-12-10">December 10, 2025</time>
                        <span></span>
                        <span class="tag">Productivity</span>
                        <span class="tag">IA</span>
                        <span class="tag">Tools</span>
                        <span class="tag">Work</span>
                        <span class="tag">Efficiency</span>
                        <span class="tag">Creative</span>
                    </p>
                </header>
                <content>
                    <div>
                        <h2>Having the support and the assistance of an IA</h2>
                        <p>My thoughts, and experience, with artificial intelligence in enhancing productivity and creativity.</p>
                        <p>Sometimes I wonder, yes, my brain should do the work, and not rely on the assistance of an IA.</p>
                        <p>I do take the time to do some things by myself. I could be lazy and just tell the IA to do everything, but then I think of the tokens and the cost associated with it.</p>
                        <p>So when Claude is 'dry' or when I'm deep in something. I go around and do it myself. The thinking, the research and the writing and coding.</p>
                        <p>Just the other day, it was funny to see, my company had co pilot disabled or not working. So I had to go do research the <i>old way</i> via google and looking on stack over flow.</p>
                        <p>It was worrysome because there wasn't really any recent articles related to my topic. I mean digging into data that dates back to 2007... Eugh. I felt kind of weird.</p>
                        <p>Prompting my current circumstances, with context really goes a long way, faster than googling and going through pages trying to find the right topic.</p>
                        <p>I didn't really find any helpful information, and I had to do a lot of digging to get my docker images up and running.</p>
                        
                        
                        <br>
                        <h3>Over designing it.</h3>
                        <p>We finally pushed through some requested evolutions at work and are working on actually migrating the project from WAMP to DOCKER with wsl! Amazing</p>
                        <p>At first, I wanted to just copy the project and make it identical. Wrong move my dear, after an afternoon, I came to a realization.</p>
                        <p>Then suddenly out of no where, "<b>boilerplate</b>!" </p>
                        <quote>Boilerplate is basically some code that is required every time you develop a project. By using a boilerplate, you save a lot of time. </quote>
                        <p>So I went ahead and started all over my project, rather than migrating a single instance of a single site. I've decided to make and adapt images for ALL the websites of my current client!</p>
                        <p>I still have to finish this.</p>
                        
                        <br>
                        <h3>Conclusion</h3>
                        <p>Now we go back to the title of the blogpost.</p>
                        <p><blockquote>Unfortunately, for you, however you are maidenless.</blockquote></p>
                        <p>"The No Maidens meme is actually a mashup of two other memes: the Maidenless meme from Elden Ring, and the "No bitches?" meme, which started as a reference to the movie Megamind."</p>
                    </div>
                </content>
               
                <br>
                <button class="link-btn"><a href="index.php">← Back to Blog Index</a></button>
            </article>
        </main>
    </div>
  <?php include __DIR__ . '/../includes/mobile-menu.php'; ?>
</html>
