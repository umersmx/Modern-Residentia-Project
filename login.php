<?php
$pageTitle = "Login";
require_once 'includes/header.php';
?>

<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card auth-card">
                    <div class="row g-0">
                        <div class="col-lg-6 d-none d-lg-block auth-image">
                            <div class="h-100 d-flex align-items-center p-5 text-white" style="background: rgba(0,0,0,0.2); backdrop-filter: blur(2px);">
                                <div>
                                    <div class="section-badge mb-4" style="border-color: rgba(255,255,255,0.2); background: rgba(255,255,255,0.1);"><span style="color: white;">Welcome Back</span></div>
                                    <h2 class="display-5 fw-bold mb-4">Sign In to <span class="serif-heading">Your Account</span></h2>
                                    <p class="lead" style="opacity: 0.85;">Continue your journey with Modern Residentia and find your perfect space.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 p-5">
                            <div class="text-center mb-4">
                                <div class="brand-icon mx-auto mb-3">
                                    <i class="fas fa-building"></i>
                                </div>
                                <h3 class="fw-bold">Sign In</h3>
                                <p class="text-muted">Enter your credentials to access your account</p>
                            </div>

                            <?php if (isset($_SESSION['error_msg'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $_SESSION['error_msg']; ?>
                                    <?php unset($_SESSION['error_msg']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['success_msg'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $_SESSION['success_msg']; ?>
                                    <?php unset($_SESSION['success_msg']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form action="actions/login.php" method="POST">
                                <input type="hidden" name="login" value="1">
                                <div class="mb-3">
                                    <label class="form-label fw-600">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope" style="color: var(--emerald);"></i></span>
                                        <input type="email" name="email" class="form-control py-2" placeholder="name@example.com" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-600">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock" style="color: var(--emerald);"></i></span>
                                        <input type="password" name="password" class="form-control py-2" placeholder="••••••••" required>
                                        <button type="button" class="input-group-text btn-toggle-pw" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary-nav btn-shimmer w-100 py-2 mb-4">
                                    <i class="fas fa-sign-in-alt me-2"></i> Login to Account
                                </button>
                                <div class="text-center">
                                    <p class="text-muted small mb-0">Don't have an account? <a href="register.php" class="fw-bold text-decoration-none" style="color: var(--emerald);">Register here</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
