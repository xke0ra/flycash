<?php

    /*!
	 * FLY CASH v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */

?>
<header class="modern-header">
    <div class="header-container">
        <div class="header-left">
            <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
            <a href="index.php" class="header-logo">
                <img src="../admin/images/<?php echo esc_attr($configs->getConfig('SITE_LOGO_DARK')); ?>" alt="<?php echo esc_attr($APP_NAME); ?>" height="32">
            </a>
        </div>

        <nav class="header-nav" id="mainNav">
            <a href="index.php" class="nav-link <?php if($pagename == 'dashboard') { echo 'active'; } ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>
            <a href="redeem.php" class="nav-link <?php if($pagename == 'redeem') { echo 'active'; } ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/><polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/><line x1="4" y1="4" x2="9" y2="9"/></svg>
                Redeem
            </a>
            <a href="refer.php" class="nav-link <?php if($pagename == 'refer') { echo 'active'; } ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Refer & Earn
            </a>
            <a href="transactions.php" class="nav-link <?php if($pagename == 'transactions') { echo 'active'; } ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Transactions
            </a>
            <a href="profile.php" class="nav-link <?php if($pagename == 'profile' || $pagename == 'change-password') { echo 'active'; } ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Account
            </a>
        </nav>

        <div class="header-right">
            <div class="notif-bell" id="notifBell" onclick="toggleNotifDropdown()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <span class="notif-badge" id="notifBadge" style="display:none">0</span>
                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-dropdown-header">
                        <strong>Notifications</strong>
                        <a href="#" onclick="markAllNotifRead(event)" style="font-size:12px;color:var(--primary)">Mark all read</a>
                    </div>
                    <div class="notif-dropdown-body" id="notifList">
                        <div class="notif-empty">No new notifications</div>
                    </div>
                </div>
            </div>
            <div class="user-menu">
                <div class="user-trigger" onclick="document.getElementById('userDropdown').classList.toggle('show')">
                    <span class="user-avatar"><?php echo esc_attr(strtoupper($req_user_info['fullname'][0])); ?></span>
                    <span class="user-name"><?php echo esc_attr($req_user_info['fullname']); ?></span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="user-dropdown" id="userDropdown">
                    <a href="profile.php" class="dropdown-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        My Profile
                    </a>
                    <a href="transactions.php" class="dropdown-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        My Transactions
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php/?access_token=<?php echo account::getAccessToken(); ?>" class="dropdown-link text-danger">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Log Out
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
