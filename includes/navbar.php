<?php
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $isLoggedIn ? $_SESSION['role'] : '';
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<nav class="navbar navbar-expand-lg sticky-top" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>/index.php">
            <div class="brand-icon me-2">
                <i class="fas fa-building"></i>
            </div>
            <span class="brand-text">Modern<span class="brand-highlight">Residentia</span></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="color: var(--text-secondary);">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/index.php">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'properties' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/properties.php">
                        <i class="fas fa-th-large me-1"></i> Properties
                    </a>
                </li>
                <?php if ($isLoggedIn): ?>
                    <?php if ($userRole === 'renter'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage === 'renter_dashboard' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/renter_dashboard.php">
                                <i class="fas fa-heart me-1"></i> My Dashboard
                            </a>
                        </li>
                    <?php elseif ($userRole === 'owner'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage === 'owner_dashboard' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/owner_dashboard.php">
                                <i class="fas fa-building me-1"></i> My Dashboard
                            </a>
                        </li>
                    <?php elseif ($userRole === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage === 'admin_dashboard' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin_dashboard.php">
                                <i class="fas fa-shield-alt me-1"></i> Admin Panel
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <div class="navbar-actions d-flex align-items-center gap-2">
                <!-- Theme Toggle Button -->
                <button class="btn btn-outline-nav" id="themeToggle" style="border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; border-width: 1.5px;">
                    <i class="fas fa-sun text-warning"></i>
                </button>

                <?php if ($isLoggedIn): ?>
                    <div class="dropdown">
                        <button class="btn btn-user-menu dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                            <div class="user-avatar-sm" style="overflow: hidden;">
                                <?php if (!empty($_SESSION['avatar'])): ?>
                                    <img src="<?php echo SITE_URL . '/' . $_SESSION['avatar']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <span class="d-none d-md-inline ms-2" style="line-height: 1;"><?php echo htmlspecialchars($userName); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                            <li class="dropdown-header">
                                <small class="text-muted text-uppercase"><?php echo ucfirst($userRole); ?> Account</small>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if ($userRole === 'renter'): ?>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/renter_dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            <?php elseif ($userRole === 'owner'): ?>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/owner_dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            <?php elseif ($userRole === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin_dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/actions/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-outline-nav" id="loginBtn">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                    <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-primary-nav btn-shimmer" id="registerBtn">
                        <i class="fas fa-user-plus me-1"></i> Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
