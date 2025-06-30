function toggleLanguage() {
  let path = window.location.pathname;

  // Ensure we always target the index file when the URL ends with /
  if (path.endsWith('/')) {
    path += 'index.html';
  }

  if (path.includes('_fr')) {
    // replace _fr before the .html extension
    path = path.replace('_fr', '');
  } else {
    const dot = path.lastIndexOf('.');
    if (dot !== -1) {
      path = path.substring(0, dot) + '_fr' + path.substring(dot);
    }
  }

  window.location.href = path;
}
