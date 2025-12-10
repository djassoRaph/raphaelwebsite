/**
 * Mobile Navigation Menu
 * Sliding menu with overlay dismiss and proper accessibility
 */

(function() {
  'use strict';
  
  // Wait for DOM to be fully loaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileNav);
  } else {
    initMobileNav();
  }
  
  function initMobileNav() {
    const body = document.body;
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('mobile-menu');
    const overlay = document.getElementById('mobile-menu-overlay');
    const closeBtn = document.getElementById('mobile-menu-close');
    
    if (!menuToggle || !menu || !overlay) {
      console.warn('Mobile navigation elements not found');
      return;
    }
    
    // Open menu
    function openMenu() {
      menu.classList.add('active');
      overlay.classList.add('active');
      menuToggle.classList.add('active');
      body.classList.add('mobile-menu-open');
      menuToggle.setAttribute('aria-expanded', 'true');
      
      // Focus the close button for keyboard navigation
      if (closeBtn) {
        closeBtn.focus();
      }
    }
    
    // Close menu
    function closeMenu() {
      menu.classList.remove('active');
      overlay.classList.remove('active');
      menuToggle.classList.remove('active');
      body.classList.remove('mobile-menu-open');
      menuToggle.setAttribute('aria-expanded', 'false');
      
      // Return focus to toggle button
      menuToggle.focus();
    }
    
    // Toggle menu
    function toggleMenu() {
      const isOpen = menu.classList.contains('active');
      if (isOpen) {
        closeMenu();
      } else {
        openMenu();
      }
    }
    
    // Event Listeners
    menuToggle.addEventListener('click', toggleMenu);
    
    if (closeBtn) {
      closeBtn.addEventListener('click', closeMenu);
    }
    
    // Click overlay to close
    overlay.addEventListener('click', closeMenu);
    
    // Close menu when navigating to a new page
    const navLinks = menu.querySelectorAll('a');
    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        // Small delay to allow navigation to start
        setTimeout(closeMenu, 100);
      });
    });
    
    // Keyboard accessibility: Escape key closes menu
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && menu.classList.contains('active')) {
        closeMenu();
      }
    });
    
    // Trap focus inside menu when open (accessibility)
    menu.addEventListener('keydown', (e) => {
      if (e.key === 'Tab') {
        const focusableElements = menu.querySelectorAll(
          'button, a, input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        if (e.shiftKey && document.activeElement === firstElement) {
          e.preventDefault();
          lastElement.focus();
        } else if (!e.shiftKey && document.activeElement === lastElement) {
          e.preventDefault();
          firstElement.focus();
        }
      }
    });
    
    // Mark current page as active
    const currentPath = window.location.pathname;
    navLinks.forEach(link => {
      const linkPath = new URL(link.href).pathname;
      if (linkPath === currentPath) {
        link.classList.add('active');
        link.setAttribute('aria-current', 'page');
      }
    });
    
    // Handle window resize - close menu if viewport becomes desktop size
    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        if (window.innerWidth > 768 && menu.classList.contains('active')) {
          closeMenu();
        }
      }, 250);
    });
  }
})();
