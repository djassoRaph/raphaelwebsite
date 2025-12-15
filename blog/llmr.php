<?php $base_path = '../'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Introducing LLMR: RSS for the AI Era | Blog | Raphaël Reck</title>
  <meta name="description" content="A new format to make websites AI-readable: LLMR reduces token consumption by 96%, cutting costs from $960 to $36/month for AI queries. Think RSS, but for LLMs."/>
  <link rel="canonical" href="https://raphaelreck.com/blog/llmr-rss-for-ai.php"/>
  <link rel="stylesheet" href="../style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <script defer src="../script.js"></script>

  <!-- Open Graph -->
  <meta property="og:type" content="article"/>
  <meta property="og:title" content="Introducing LLMR: RSS for the AI Era"/>
  <meta property="og:description" content="A new format to make websites AI-readable, reducing token consumption by 96% and cutting AI query costs dramatically."/>
  <meta property="og:url" content="https://raphaelreck.com/blog/llmr-rss-for-ai.html"/>
  <meta property="og:site_name" content="Raphaël Reck"/>
  <meta property="article:author" content="Raphaël Reck"/>
  <meta property="article:published_time" content="2025-10-30T00:00:00+02:00"/>
  <meta property="article:tag" content="AI"/>
  <meta property="article:tag" content="Web Standards"/>
  <meta property="article:tag" content="Innovation"/>

  <style>
    .post-header { margin-bottom: 1rem; }
    .post-meta { opacity: .7; font-size: .9rem; }
    .tag { display:inline-block; margin-right:.5rem; font-size:.8rem; padding:.15rem .5rem; border:1px solid currentColor; border-radius:999px; }
    .callout { border-left:4px solid #888; padding:.75rem 1rem; background:rgba(127,127,127,.06); margin:1rem 0; }
    pre { overflow:auto; padding:1rem; border:1px solid #333; border-radius:.5rem; background:#f8f9fa; }
    code { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, 'Cascadia Code', monospace; }
    .stats-box { background:#e3f2fd; border-left:4px solid #2196f3; padding:1rem; margin:1.5rem 0; border-radius:4px; }
    .comparison { display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin:1rem 0; }
    .comparison-item { padding:1rem; border:1px solid #ddd; border-radius:4px; }
    blockquote { border-left:4px solid #666; padding-left:1rem; margin:1.5rem 0; font-style:italic; opacity:.9; }
    
    @media (max-width: 768px) {
      .comparison { grid-template-columns: 1fr; }
    }
    
    @media (prefers-color-scheme: dark) {
      pre { background:#222; color:#eee; border-color:#444; }
      .stats-box { background:#1a2332; border-left-color:#6ea0ff; }
      .comparison-item { border-color:#444; }
    }
  </style>

  <!-- JSON-LD for SEO -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": "Introducing LLMR: RSS for the AI Era",
    "datePublished": "2025-10-30T00:00:00+02:00",
    "dateModified": "2025-10-30T00:00:00+02:00",
    "author": { "@type": "Person", "name": "Raphaël Reck" },
    "publisher": { "@type": "Person", "name": "Raphaël Reck" },
    "mainEntityOfPage": { "@type": "WebPage", "@id": "https://raphaelreck.com/blog/llmr-rss-for-ai.html" },
    "keywords": ["AI","Web Standards","Innovation","LLMR","Machine Learning","Token Optimization"],
    "description": "A new format to make websites AI-readable: LLMR reduces token consumption by 96%, cutting costs from $960 to $36/month for AI queries."
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
          <h1>Introducing LLMR: RSS for the AI Era</h1>
          <p class="post-meta">
            <time datetime="2025-10-30">October 30, 2025</time>
            <span>~8 min read</span>
            <span class="tag">AI</span>
            <span class="tag">Web Standards</span>
            <span class="tag">Innovation</span>
          </p>
        </header>

        <h2>The Idea</h2>
        <p class="callout">Jibberish mode but for LLMs when browsing websites.</p>

        <p>When AI systems browse the web today, they're forced to parse HTML designed for humans. They wade through navigation bars, CSS classes, footer links, and advertising markup just to extract the actual content.</p>

        <p>For my technical blog with 10 posts, an AI must process ~32,000 tokens to understand my content. At GPT-4 pricing, that's $0.96 per query. If 1,000 AI agents visit per month, I'm looking at nearly $1,000 in indirect crawling costs - costs ultimately passed on through API pricing.</p>

        <p>There has to be a better way.</p>

        <h2>The Thought: LLMR Format</h2>

        <p>I built a proof-of-concept format called <strong>LLMR</strong> (LLM-Readable) that compresses an entire website into a single, structured JSON file optimized for AI consumption.</p>

        <p>Think of it as <strong>RSS for AI systems</strong> or <strong>Jibberish mode for LLMs</strong>.</p>

        <h3>What Does It Look Like?</h3>

        <pre><code>{
  "v": "1.0",
  "s": {
    "d": "raphaelreck.com",
    "t": "prof_tech_blog",
    "a": {
      "n": "Raphaël Reck",
      "exp": ["drupal", "laravel", "sys_arch"]
    }
  },
  "p": [
    {
      "id": "juggling-fireballs",
      "u": "/blog/2025-10-08-juggling-fireballs.html",
      "d": "2025-10-08",
      "tg": ["prod", "burn", "solo"],
      "rt": 4,
      "tc": 0,
      "emb": [0.26, 0.77, -0.21, -0.67, ...],
      "sum": "Managing heavy workload when partner experiences burnout"
    }
  ]
}</code></pre>

        <p><strong>Key features:</strong></p>
        <ul>
          <li><strong>Compressed keys</strong>: <code>d</code> = date, <code>tg</code> = tags, <code>tc</code> = technical content</li>
          <li><strong>Embeddings</strong>: Pre-computed semantic vectors for instant understanding</li>
          <li><strong>Metadata rich</strong>: Technical depth, code block count, read time</li>
          <li><strong>Single file</strong>: Entire site in one HTTP request</li>
        </ul>

        <h3>The Results</h3>

        <div class="stats-box">
          <p>For my 10-post blog:</p>
          <ul>
            <li><strong>Traditional HTML</strong>: 32,000 tokens</li>
            <li><strong>LLMR format</strong>: 1,200 tokens</li>
            <li><strong>Reduction</strong>: 96%</li>
          </ul>

          <p>If your site gets 1,000 AI queries per month:</p>
          <ul>
            <li>Traditional cost: $960/month</li>
            <li>LLMR cost: $36/month</li>
            <li><strong>Savings: $924/month (96%)</strong></li>
          </ul>
        </div>

        <h2>How It Works</h2>

        <p>I wrote a Python script that:</p>

        <ol>
          <li><strong>Scans all HTML files</strong> in your website</li>
          <li><strong>Extracts metadata</strong> (title, description, tags, dates)</li>
          <li><strong>Detects technical content</strong> (code blocks, debugging stories)</li>
          <li><strong>Generates embeddings</strong> (semantic fingerprints)</li>
          <li><strong>Creates compressed JSON</strong> with abbreviated keys</li>
        </ol>

        <p>Add it to your blog publishing workflow:</p>

        <pre><code># After generating RSS/Atom feeds: subprocess.run(['python3', 'generate_llmr.py', '.'])</code></pre>

        <p>Done. Your site now speaks AI.</p>

        <h2>Why This Matters</h2>

        <p>We already have standards for different consumers:</p>
        <ul>
          <li><strong>robots.txt</strong> - for web crawlers</li>
          <li><strong>sitemap.xml</strong> - for search engines</li>
          <li><strong>RSS/Atom</strong> - for feed readers</li>
          <li><strong>manifest.json</strong> - for web apps</li>
        </ul>

        <p>We need:</p>
        <ul>
          <li><strong>site.llmr</strong> - for AI systems</li>
        </ul>

        <h2>Real-World Benefits</h2>

        <div class="comparison">
          <div class="comparison-item">
            <h4>For content creators:</h4>
            <ul>
              <li>AI understands your content better</li>
              <li>Lower bandwidth costs</li>
              <li>More accurate AI summaries of your work</li>
            </ul>
          </div>

          <div class="comparison-item">
            <h4>For AI systems:</h4>
            <ul>
              <li>95%+ token reduction</li>
              <li>Instant semantic understanding</li>
              <li>No HTML parsing overhead</li>
            </ul>
          </div>
        </div>

        <p><strong>For users:</strong></p>
        <ul>
          <li>Faster AI responses about web content</li>
          <li>More accurate information</li>
          <li>Lower API costs</li>
        </ul>

        <h2>The Vision: A Consortium</h2>

        <p>For this to become mainstream, we need:</p>

        <ol>
          <li><input type="checkbox" checked>Working prototype</li>
          <li><input type="checkbox" disabled>Iterate and refactor, discuss and improve upon the idea</li>
          <li><input type="checkbox" disabled>Formal specification document</li>
          <li><input type="checkbox" disabled>AI company adoption (OpenAI, Anthropic <3, Google)</li>
          <li><input type="checkbox" disabled>CMS plugins (WordPress, Drupal, Ghost)</li>
          <li><input type="checkbox" disabled>Developer evangelism</li>
        </ol>

        <p>RSS started with one person (Dave Winer) saying "there should be a better way." This could be the RSS of the AI era.</p>

        <h2>Try It Yourself</h2>

        <p>I've open-sourced the generator:</p>

        <p><strong>GitHub</strong>: <a href="https://github.com/djassoRaph/open_llmr" target="_blank" rel="noopener">github.com/djassoRaph/open_llmr</a></p>

        <p><strong>Features:</strong></p>
        <ul>
          <li>Zero dependencies (pure Python)</li>
          <li>Works with any HTML static site</li>
          <li>Integrates with existing workflows</li>
          <li>Generates hash-based embeddings (production should use sentence-transformers)</li>
        </ul>

        <p><strong>Usage:</strong></p>
        <pre><code>python3 generate_llmr.py /path/to/your/website</code></pre>

        <p>Add this to your HTML <code>&lt;head&gt;</code>:</p>
        <pre><code>&lt;link rel="llm-index" type="application/json" href="/site.llmr"&gt;</code></pre>

        <h2>The Bigger Picture</h2>

        <p>We're entering an era where AI agents will browse the web as much as humans do. They'll:</p>
        <ul>
          <li>Research topics across hundreds of sites</li>
          <li>Summarize content for users</li>
          <li>Build knowledge graphs</li>
          <li>Answer questions with citations</li>
        </ul>

        <p>If we design for this future now - with formats like LLMR - we can:</p>
        <ul>
          <li>Reduce global token consumption</li>
          <li>Lower AI infrastructure costs</li>
          <li>Enable better AI understanding</li>
          <li>Maintain human-readable HTML alongside</li>
        </ul>

        <p>This isn't about replacing HTML. It's about working with it with AI-optimized metadata, the same way we augmented it with RSS for feed readers 20 years ago. Or how Jibberish mode does it's own thing.</p>

        <h2>Try it out! :)</h2>

        <p>If you're:</p>
        <ul>
          <li><strong>A developer</strong> - try the script on your blog</li>
          <li><strong>An AI company</strong> - consider supporting this format</li>
          <li><strong>A CMS developer</strong> - build a plugin</li> (Let me know if you need help! I'll also share it!)
          <li><strong>A standards body</strong> - let's formalize this</li>
        </ul>

        <p>Let's build <strong>LLM-First Web Architecture</strong> together.</p>

        <hr>

        <blockquote>
          "The best way to predict the future is to invent it. But the smartest way to invent it is to make sure everyone can read it, including... machines."
        </blockquote>

        <hr>

        <p><strong>What are your thoughts?</strong> Would you implement this on your site? Hit me up on <a href="https://linkedin.com/in/raphael-reck-link/" target="_blank" rel="noopener">LinkedIn</a> or <a href="https://github.com/djassoRaph" target="_blank" rel="noopener">GitHub</a> to discuss.</p>

        <hr>

        <hr>
        <p><em>Thank you for reading.<br>No tokens were wasted in the making of this post.</em></p>
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