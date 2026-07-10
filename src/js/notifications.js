/**
 * Live notification polling
 */

(function() {
  if (!document.getElementById('notifBell')) return;

  let lastNotifId = 0;
  let notifCheckInterval = null;
  let notifToastContainer = null;

  function ensureToastContainer() {
    if (!notifToastContainer) {
      notifToastContainer = document.createElement('div');
      notifToastContainer.className = 'notif-toast-container';
      document.body.appendChild(notifToastContainer);
    }
  }

  function showNotifToast(title, msg) {
    ensureToastContainer();
    const toast = document.createElement('div');
    toast.className = 'notif-toast';
    toast.innerHTML =
      '<div class="notif-toast-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></div><div class="notif-toast-content"><div class="notif-toast-title">' +
      title +
      '</div><div class="notif-toast-msg">' +
      msg +
      '</div></div>';
    notifToastContainer.appendChild(toast);
    setTimeout(function() {
      if (toast.parentNode) toast.parentNode.removeChild(toast);
    }, 5000);
  }

  function updateNotifDropdown(data) {
    const badge = document.getElementById('notifBadge');
    const list = document.getElementById('notifList');
    if (!badge || !list) return;

    if (data.count > 0) {
      badge.style.display = 'inline';
      badge.textContent = data.count > 99 ? '99+' : data.count;
    } else {
      badge.style.display = 'none';
    }

    if (data.notifications.length === 0) {
      list.innerHTML = '<div class="notif-empty">No new notifications</div>';
      return;
    }

    let html = '';
    for (let i = 0; i < data.notifications.length; i++) {
      const n = data.notifications[i];
      const cls = n.is_read == '0' ? 'notif-item unread' : 'notif-item';
      const pts = parseFloat(n.points) > 0
        ? '<span style="color:var(--primary);font-weight:600">+' + n.points + '</span>'
        : '';
      html +=
        '<div class="' +
        cls +
        '" onclick="markNotifRead(' +
        n.id +
        ')"><div class="notif-item-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div><div class="notif-item-content"><div class="notif-item-title">' +
        n.title +
        ' ' +
        pts +
        '</div><div class="notif-item-msg">' +
        n.message +
        '</div></div></div>';
    }
    list.innerHTML = html;

    for (let j = 0; j < data.notifications.length; j++) {
      if (parseInt(data.notifications[j].id) > lastNotifId) {
        showNotifToast(data.notifications[j].title, data.notifications[j].message);
      }
    }
    if (data.notifications.length > 0) {
      lastNotifId = parseInt(data.notifications[0].id);
    }
  }

  function fetchNotif() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'notification-api.php?action=fetch', true);
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        try {
          const data = JSON.parse(xhr.responseText);
          if (data.success) updateNotifDropdown(data);
        } catch (e) { /* ignore parse errors */ }
      }
    };
    xhr.send();
  }

  window.toggleNotifDropdown = function() {
    const dd = document.getElementById('notifDropdown');
    if (dd) dd.classList.toggle('show');
  };

  window.markNotifRead = function(id) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'notification-api.php?action=read', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) fetchNotif();
    };
    xhr.send('id=' + id);
  };

  window.markAllNotifRead = function(e) {
    if (e) e.preventDefault();
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'notification-api.php?action=read_all', true);
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) fetchNotif();
    };
    xhr.send();
  };

  // Initial fetch and polling
  fetchNotif();
  notifCheckInterval = setInterval(fetchNotif, 15000);

  // Close dropdown on outside click
  document.addEventListener('click', function(e) {
    const bell = document.getElementById('notifBell');
    if (bell && !bell.contains(e.target)) {
      const dd = document.getElementById('notifDropdown');
      if (dd) dd.classList.remove('show');
    }
  });
})();
