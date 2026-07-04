<?php

    /*!
	 * POCKET v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */

?>

		<!--begin::Global Theme Bundle(used by all pages) -->
		<script src="assets/plugins/global/plugins.bundle.js" type="text/javascript"></script>
		<script src="assets/js/scripts.bundle.js" type="text/javascript"></script>
		<script src="assets/js/pocket_rewards_app.js?ver=3.7" type="text/javascript"></script>
		<script src="assets/js/custom.js?ver=3.7" type="text/javascript"></script>
		<!--end::Global Theme Bundle -->

		<!-- Modern theme JS -->
		<script>
		(function() {
			// Mobile toggle
			var toggle = document.getElementById('mobileToggle');
			var nav = document.getElementById('mainNav');
			var overlay = document.getElementById('mobileOverlay');
			if (toggle && nav) {
				function closeNav() { nav.classList.remove('open'); if (overlay) overlay.classList.remove('open'); }
				toggle.addEventListener('click', function(e) { e.stopPropagation(); nav.classList.toggle('open'); if (overlay) overlay.classList.toggle('open'); });
				if (overlay) overlay.addEventListener('click', closeNav);
				document.addEventListener('click', function(e) { if (!nav.contains(e.target) && !toggle.contains(e.target)) closeNav(); });
			}
			// Close dropdown on outside click
			document.addEventListener('click', function(e) {
				var dd = document.getElementById('userDropdown');
				if (dd && dd.classList.contains('show') && !e.target.closest('.user-menu')) dd.classList.remove('show');
			});
			// Scroll to top
			var btn = document.getElementById('scrollTopBtn');
			if (btn) {
				window.addEventListener('scroll', function() { btn.classList.toggle('show', window.scrollY > 300); });
				btn.addEventListener('click', function() { window.scrollTo({ top: 0, behavior: 'smooth' }); });
			}
		})();

		// Notifications (only on dashboard pages where notifBell exists)
		if (document.getElementById('notifBell')) {
		var lastNotifId = 0;
		var notifCheckInterval = null;
		var notifToastContainer = null;

		function ensureToastContainer() {
			if (!notifToastContainer) {
				notifToastContainer = document.createElement('div');
				notifToastContainer.className = 'notif-toast-container';
				document.body.appendChild(notifToastContainer);
			}
		}

		function showNotifToast(title, msg) {
			ensureToastContainer();
			var toast = document.createElement('div');
			toast.className = 'notif-toast';
			toast.innerHTML = '<div class="notif-toast-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></div><div class="notif-toast-content"><div class="notif-toast-title">' + title + '</div><div class="notif-toast-msg">' + msg + '</div></div>';
			notifToastContainer.appendChild(toast);
			setTimeout(function() { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 5000);
		}

		function updateNotifDropdown(data) {
			var badge = document.getElementById('notifBadge');
			var list = document.getElementById('notifList');
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
			var html = '';
			for (var i = 0; i < data.notifications.length; i++) {
				var n = data.notifications[i];
				var cls = n.is_read == '0' ? 'notif-item unread' : 'notif-item';
				var pts = parseFloat(n.points) > 0 ? '<span style="color:var(--primary);font-weight:600">+' + n.points + '</span>' : '';
				html += '<div class="' + cls + '" onclick="markNotifRead(' + n.id + ')"><div class="notif-item-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div><div class="notif-item-content"><div class="notif-item-title">' + n.title + ' ' + pts + '</div><div class="notif-item-msg">' + n.message + '</div></div></div>';
			}
			list.innerHTML = html;
			// show toast for NEW notifications (higher than lastNotifId)
			for (var j = 0; j < data.notifications.length; j++) {
				if (parseInt(data.notifications[j].id) > lastNotifId) {
					showNotifToast(data.notifications[j].title, data.notifications[j].message);
				}
			}
			if (data.notifications.length > 0) {
				lastNotifId = parseInt(data.notifications[0].id);
			}
		}

		function fetchNotif() {
			var xhr = new XMLHttpRequest();
			xhr.open('GET', 'notification-api.php?action=fetch', true);
			xhr.onreadystatechange = function() {
				if (xhr.readyState === 4 && xhr.status === 200) {
					try {
						var data = JSON.parse(xhr.responseText);
						if (data.success) updateNotifDropdown(data);
					} catch(e) {}
				}
			};
			xhr.send();
		}

		function toggleNotifDropdown() {
			var dd = document.getElementById('notifDropdown');
			if (dd) dd.classList.toggle('show');
		}

		function markNotifRead(id) {
			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'notification-api.php?action=read', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.onreadystatechange = function() {
				if (xhr.readyState === 4) fetchNotif();
			};
			xhr.send('id=' + id);
		}

		function markAllNotifRead(e) {
			if (e) e.preventDefault();
			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'notification-api.php?action=read_all', true);
			xhr.onreadystatechange = function() {
				if (xhr.readyState === 4) fetchNotif();
			};
			xhr.send();
		}

		// Initial fetch and start polling
		fetchNotif();
		notifCheckInterval = setInterval(fetchNotif, 15000);

		// Close notification dropdown on outside click
		document.addEventListener('click', function(e) {
			var bell = document.getElementById('notifBell');
			if (bell && !bell.contains(e.target)) {
				var dd = document.getElementById('notifDropdown');
				if (dd) dd.classList.remove('show');
			}
		});
		}
		</script>