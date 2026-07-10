<?php
// Generate CSRF token for admin forms
	if (!isset($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta content="ie=edge" http-equiv="x-ua-compatible" />
    <?php include_once 'inc/title.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./assets/css/modern-admin.css?ver=3.0" />
</head>
<body>

<!-- Sidebar -->
<nav class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <a href="admin.php" class="sidebar-logo"><?php echo strtoupper(htmlspecialchars($configs->getConfig('APP_NAME'), ENT_QUOTES, 'UTF-8')); ?></a>
    </div>
    <div class="sidebar-profile">
        <span class="profile-avatar"><?php echo strtoupper(substr(htmlspecialchars($helper->getAdminFullName(admin::getAdminID()), ENT_QUOTES, 'UTF-8'), 0, 1)); ?></span>
        <div class="profile-info">
            <strong><?php echo htmlspecialchars($helper->getAdminFullName(admin::getAdminID()), ENT_QUOTES, 'UTF-8'); ?></strong>
            <span>Administrator</span>
        </div>
    </div>
    <div class="sidebar-menu">
        <a href="admin.php" class="menu-item <?php if($pagename == 'dashboard') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span>Dashboard</span>
        </a>
        <a href="users.php" class="menu-item <?php if($pagename == 'users') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Users</span>
        </a>
        <div class="menu-divider">Records</div>
        <a href="requests.php" class="menu-item <?php if($pagename == 'pending-requests') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <span>Pending</span>
        </a>
        <a href="processing-requests.php" class="menu-item <?php if($pagename == 'processing-requests') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            <span>Processing</span>
        </a>
        <a href="completed.php" class="menu-item <?php if($pagename == 'completed-requests') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <span>Completed</span>
        </a>
        <a href="rejected-requests.php" class="menu-item <?php if($pagename == 'rejected-requests') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            <span>Rejected</span>
        </a>
        <div class="menu-divider">OfferWalls</div>
        <a href="offerwalls.php" class="menu-item <?php if($pagename == 'offerwalls') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            <span>All OfferWalls</span>
        </a>
        <a href="add-offerwall.php" class="menu-item <?php if($pagename == 'add-offerwall') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Add Offerwall</span>
        </a>
        <div class="menu-divider">YouTube Offers</div>
        <a href="youtube-offers.php" class="menu-item <?php if($pagename == 'youtube-offers') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.94 2C5.12 20 12 20 12 20s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg>
            <span>All Videos</span>
        </a>
        <a href="add-youtube-offer.php" class="menu-item <?php if($pagename == 'add-youtube-offer') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Add Video</span>
        </a>
        <div class="menu-divider">Payouts</div>
        <a href="payouts.php" class="menu-item <?php if($pagename == 'payouts') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <span>Redeem Options</span>
        </a>
        <a href="add-payout.php" class="menu-item <?php if($pagename == 'add-payout') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Add Redeem</span>
        </a>
        <div class="menu-divider">Settings</div>
        <a href="profile.php" class="menu-item <?php if($pagename == 'admin-profile') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Admin Profile</span>
        </a>
        <a href="settings.php" class="menu-item <?php if($pagename == 'configuration') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span>Configuration</span>
        </a>
        <a href="postbacks.php" class="menu-item <?php if($pagename == 'postbacks') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            <span>Postbacks S2S</span>
        </a>
        <div class="menu-divider"></div>
        <a href="push.php" class="menu-item <?php if($pagename == 'push-single') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span>Send Push</span>
        </a>
        <a href="tracker.php" class="menu-item <?php if($pagename == 'tracker') echo 'active'; ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
            <span>Tracker</span>
        </a>
        <div class="menu-divider"></div>
        <a href="logout.php/?access_token=<?php echo htmlspecialchars(admin::getAccessToken(), ENT_QUOTES, 'UTF-8'); ?>" class="menu-item logout">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Logout</span>
        </a>
    </div>
</nav>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleAdminSidebar()"></div>

<!-- Main -->
<main class="admin-main">

<!-- Top Bar -->
<header class="admin-topbar">
    <button class="topbar-toggle" id="adminToggle" onclick="toggleAdminSidebar()">
        <span></span><span></span><span></span>
    </button>
    <div class="topbar-left">
        <h6 class="topbar-title"><?php echo htmlspecialchars($configs->getConfig('APP_NAME'), ENT_QUOTES, 'UTF-8'); ?></h6>
        <div class="topbar-breadcrumb">
            <a href="admin.php">Dashboard</a>
        </div>
    </div>
    <div class="topbar-right">
        <button class="dark-mode-toggle" id="darkModeToggle" title="Toggle Dark Mode"></button>
        <a href="logout.php/?access_token=<?php echo htmlspecialchars(admin::getAccessToken(), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-logout">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Logout
        </a>
    </div>
</header>
