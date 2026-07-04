(function () {
  'use strict';

  /* ===== DOM READY ===== */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    initDarkMode();
    initSidebar();
    initValidation();
  }

  /* ===== DARK MODE ===== */
  function initDarkMode() {
    var saved = localStorage.getItem('flycash-theme');
    if (saved === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
    var toggle = document.getElementById('darkModeToggle');
    if (!toggle) return;
    updateDarkIcon();
    toggle.addEventListener('click', function () {
      var html = document.documentElement;
      var isDark = html.getAttribute('data-theme') === 'dark';
      if (isDark) {
        html.removeAttribute('data-theme');
        localStorage.setItem('flycash-theme', 'light');
      } else {
        html.setAttribute('data-theme', 'dark');
        localStorage.setItem('flycash-theme', 'dark');
      }
      updateDarkIcon();
    });
    function updateDarkIcon() {
      var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
      toggle.innerHTML = isDark
        ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>'
        : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
    }
  }

  /* ===== SIDEBAR ===== */
  function initSidebar() {
    var toggleBtn = document.getElementById('adminToggle');
    var sidebar = document.getElementById('adminSidebar');
    var overlay = document.getElementById('sidebarOverlay');
    if (!toggleBtn || !sidebar) return;
    window.toggleAdminSidebar = function () {
      sidebar.classList.toggle('open');
      if (overlay) overlay.classList.toggle('open');
    };
  }

  /* ===== FORM VALIDATION ===== */
  function initValidation() {
    var forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      });
      var inputs = form.querySelectorAll('input, select, textarea');
      inputs.forEach(function (input) {
        input.addEventListener('blur', function () {
          validateField(input);
        });
        input.addEventListener('input', function () {
          if (form.classList.contains('was-validated')) {
            validateField(input);
          }
        });
      });
    });
  }

  function validateField(input) {
    var group = input.closest('.form-group');
    if (!group) return;
    var errorEl = group.querySelector('.error-text');
    if (input.checkValidity()) {
      group.classList.remove('has-error');
      group.classList.add('has-success');
      if (errorEl) errorEl.textContent = '';
    } else {
      group.classList.remove('has-success');
      group.classList.add('has-error');
      if (errorEl) {
        errorEl.textContent = input.validationMessage || 'This field is required';
      }
    }
  }

  /* ===== LOADING OVERLAY ===== */
  window.showLoading = function () {
    var overlay = document.getElementById('loadingOverlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.id = 'loadingOverlay';
      overlay.className = 'loading-overlay';
      overlay.innerHTML = '<div class="loading-spinner"></div>';
      document.body.appendChild(overlay);
    }
    requestAnimationFrame(function () { overlay.classList.add('show'); });
  };

  window.hideLoading = function () {
    var overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.classList.remove('show');
  };

  /* ===== SKELETON HELPER ===== */
  window.showSkeleton = function (containerId) {
    var container = document.getElementById(containerId);
    if (!container) return;
    container.dataset.originalHtml = container.innerHTML;
    container.innerHTML = '';
    for (var i = 0; i < 3; i++) {
      var row = document.createElement('div');
      row.style.cssText = 'display:flex;gap:12px;align-items:center;margin-bottom:12px;';
      row.innerHTML =
        '<div class="skeleton skeleton-avatar"></div>' +
        '<div style="flex:1"><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-text"></div></div>';
      container.appendChild(row);
    }
  };

  window.hideSkeleton = function (containerId) {
    var container = document.getElementById(containerId);
    if (!container || !container.dataset.originalHtml) return;
    container.innerHTML = container.dataset.originalHtml;
    delete container.dataset.originalHtml;
  };

  /* ===== AJAX HELPER ===== */
  window.ajax = function (url, opts) {
    opts = opts || {};
    return new Promise(function (resolve, reject) {
      var xhr = new XMLHttpRequest();
      xhr.open(opts.method || 'GET', url);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      if (opts.contentType) {
        xhr.setRequestHeader('Content-Type', opts.contentType);
      }
      xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
          resolve(xhr.responseText);
        } else {
          reject(new Error(xhr.statusText));
        }
      };
      xhr.onerror = function () { reject(new Error('Network error')); };
      if (opts.showLoading !== false) window.showLoading();
      xhr.onloadend = function () {
        if (opts.showLoading !== false) window.hideLoading();
      };
      xhr.send(opts.body || null);
    });
  };

})();