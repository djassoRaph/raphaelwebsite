function toggleLanguage() {
    const current = window.location.pathname;
    if (current.includes('index_fr.html')) {
      window.location.href = 'index.html';
    } else {
      window.location.href = 'index_fr.html';
    }
  }