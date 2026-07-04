<?php

    /*!
	 * FLY CASH v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */

?>

    <footer class="admin-footer">
        <span>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($configs->getConfig('APP_NAME'), ENT_QUOTES, 'UTF-8'); ?> â€” Admin Panel</span>
        <span>Powered by AYM</span>
    </footer>

</main><!-- /admin-main -->

<!-- Scripts (core) -->
<script src="./assets/js/jquery-3.2.1.min.js"></script>
<script src="./assets/js/popper.min.js"></script>
<script src="./assets/js/bootstrap.min.js"></script>
<script src="./assets/js/app.js"></script>

<?php if (isset($sessionsdata)): ?>
<script>
var pending = <?php echo (int)($pendingRequests ?? 0); ?>;
var processing = <?php echo (int)($processingRequests ?? 0); ?>;
var rejected = <?php echo (int)($rejectedRequests ?? 0); ?>;
var completed = <?php echo (int)($completedRequests ?? 0); ?>;
var sessionsjsondata = <?php echo json_encode($sessionsdata ?? []); ?>;
</script>
<script src="./assets/plugins/charts/Chart.min.js"></script>
<script src="./assets/plugins/sparkline/jquery.sparkline.min.js"></script>
<script src="./assets/plugins/sparkline/jquery.charts-sparkline.js"></script>
<script src="./assets/plugins/customScroll/jquery.mCustomScrollbar.min.js" defer></script>
<script src="./assets/plugins/data-tables/datatables.min.js" defer></script>
<script src="./assets/js/main.js"></script>
<script src="./assets/js/custom-chart.js"></script>
<?php endif; ?>

</body>
</html>
