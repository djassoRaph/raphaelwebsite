<!-- Theme Initialization - Load ASAP to prevent flash -->
<script>
  // Immediately apply saved theme before page renders
  (function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
  })();
</script>
