<?php
/**
 * Navigation Component
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

<!-- Primary Navigation -->
<nav class="nav-primary">
  <a href="<?php echo $base_path; ?>public/raphael-reck.pdf" target="_blank" download class="nav-btn">
    <i class="fas fa-file-pdf"></i> Download CV
  </a>
  <a href="<?php echo $base_path; ?>blog/index.php" class="nav-btn">
    <i class="fas fa-blog"></i> Blog
  </a>
  <a href="https://www.linkedin.com/in/raphael-reck-link/" target="_blank" class="nav-btn">
    <i class="fab fa-linkedin"></i> LinkedIn
  </a>
  <a href="https://github.com/djassoRaph" target="_blank" class="nav-btn">
    <i class="fab fa-github"></i> GitHub
  </a>
</nav>

<!-- Quick Links -->
<nav class="nav-secondary">
  <?php if ($base_path !== ''): ?>
  <a href="<?php echo $base_path; ?>index.php" class="link-btn">
    <i class="fas fa-arrow-left"></i> Home Page
  </a>
  <?php endif; ?>
  <a href="<?php echo $base_path; ?>roots-time/manifesto.php" class="link-btn">
    <i class="fas fa-book-open"></i> Roots Time Corporation
  </a>
  <a href="<?php echo $base_path; ?>memes/laugh.php" class="link-btn">
    <i class="fas fa-laugh"></i> Universal Darwinism
  </a>
  <a href="https://webasix.com/" target="_blank" class="link-btn">
    <i class="fas fa-briefcase"></i> Consulting Work
  </a>
</nav>
