<?php
/**
 * Mobile Menu Component
 *
 * Usage: Set $base_path variable before including this file
 *
 * Examples:
 *   Root level (index.php):           $base_path = '';
 *   One level deep (blog/index.php):  $base_path = '../';
 *   Two levels deep:                  $base_path = '../../';
 */

// Default to root if not set
if (!isset($base_path)) {
    $base_path = '';
}
?>

<!-- Mobile Menu Toggle Button -->
<button
  id="mobile-menu-toggle"
  class="mobile-menu-toggle"
  aria-label="Toggle navigation menu"
  aria-expanded="false">
  <div class="hamburger">
    <span></span>
    <span></span>
    <span></span>
  </div>
</button>

<!-- Overlay -->
<div id="mobile-menu-overlay" class="mobile-menu-overlay"></div>

<!-- Sliding Menu -->
<nav id="mobile-menu" class="mobile-menu">
  <div class="mobile-menu-header">
    <h2 class="mobile-menu-title">Navigation</h2>
    <button id="mobile-menu-close" class="mobile-menu-close">×</button>
  </div>

  <!-- Primary Navigation -->
  <div class="mobile-nav-section">
    <h3>Main</h3>
    <div class="mobile-nav-links">
      <a href="<?php echo $base_path; ?>index.php" class="mobile-nav-link">
        <i class="fas fa-home"></i>
        <span>Home</span>
      </a>
      <a href="<?php echo $base_path; ?>blog/index.php" class="mobile-nav-link">
        <i class="fas fa-blog"></i>
        <span>Blog</span>
      </a>
      <a href="<?php echo $base_path; ?>public/raphael-reck.pdf" class="mobile-nav-link" target="_blank">
        <i class="fas fa-file-pdf"></i>
        <span>Download CV</span>
      </a>
    </div>
  </div>

  <div class="mobile-nav-divider"></div>

  <!-- Projects -->
  <div class="mobile-nav-section">
    <h3>Projects</h3>
    <div class="mobile-nav-links">
      <a href="<?php echo $base_path; ?>roots-time/manifesto.php" class="mobile-nav-link">
        <i class="fas fa-book"></i>
        <span>Roots Time Corporation</span>
      </a>
      <a href="<?php echo $base_path; ?>memes/laugh.php" class="mobile-nav-link">
        <i class="fas fa-dna"></i>
        <span>Universal Darwinism</span>
      </a>
      <a href="https://webasix.com/" class="mobile-nav-link" target="_blank">
        <i class="fas fa-briefcase"></i>
        <span>Consulting Work</span>
      </a>
    </div>
  </div>

  <div class="mobile-nav-divider"></div>

  <!-- Social -->
  <div class="mobile-nav-section">
    <h3>Connect</h3>
    <div class="mobile-nav-links">
      <a href="https://github.com/djassoRaph" class="mobile-nav-link" target="_blank">
        <i class="fab fa-github"></i>
        <span>GitHub</span>
      </a>
      <a href="https://www.linkedin.com/in/raphael-reck-link/" class="mobile-nav-link" target="_blank">
        <i class="fab fa-linkedin"></i>
        <span>LinkedIn</span>
      </a>
    </div>
  </div>
</nav>

<script src="<?php echo $base_path; ?>mobile-nav.js"></script>
