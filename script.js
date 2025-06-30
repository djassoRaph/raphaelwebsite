function toggleLanguage() {
  const current = window.location.pathname;
  if (current.includes('_fr')) {
    // replace _fr before the .html extension
    window.location.href = current.replace('_fr', '');
    return;
  }
  const dot = current.lastIndexOf('.');
  if (dot !== -1) {
    const path = current.substring(0, dot) + '_fr' + current.substring(dot);
    window.location.href = path;
  }
}
