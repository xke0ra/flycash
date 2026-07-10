<?php
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
		</script>

		<!-- Service Worker Registration -->
		<script>
		if ('serviceWorker' in navigator) {
			navigator.serviceWorker.register('sw.js').catch(function(err) {
				console.warn('[SW] Registration failed:', err);
			});
		}
		</script>
