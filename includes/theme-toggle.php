<!-- Theme Toggle -->
<button id="theme-toggle" class="theme-toggle" aria-label="Toggle theme">
  <i class="fas fa-moon"></i>
</button>

<script>
  // Theme Toggle Functionality
  const themeToggle = document.getElementById('theme-toggle');
  const htmlElement = document.documentElement;
  const icon = themeToggle.querySelector('i');

  // Check for saved theme preference or default to light mode
  const currentTheme = localStorage.getItem('theme') || 'light';
  htmlElement.setAttribute('data-theme', currentTheme);
  updateIcon(currentTheme);

  themeToggle.addEventListener('click', () => {
    const currentTheme = htmlElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';

    htmlElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateIcon(newTheme);
  });

  function updateIcon(theme) {
    if (theme === 'dark') {
      icon.classList.remove('fa-moon');
      icon.classList.add('fa-sun');
    } else {
      icon.classList.remove('fa-sun');
      icon.classList.add('fa-moon');
    }
  }
</script>
