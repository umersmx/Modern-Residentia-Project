<?php
$pageTitle = "Register";
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
                                    <div class="section-badge mb-4" style="border-color: rgba(255,255,255,0.2); background: rgba(255,255,255,0.1);"><span style="color: white;">Get Started</span></div>
                                    <h2 class="display-5 fw-bold mb-4">Join Us <span class="serif-heading">Today</span></h2>
                                    <p class="lead" style="opacity: 0.85;">Create an account and start exploring the best rental properties available in the market.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 p-5">
                            <div class="text-center mb-4">
                                <div class="brand-icon mx-auto mb-3">
                                    <i class="fas fa-building"></i>
                                </div>
                                <h3 class="fw-bold">Create Account</h3>
                                <p class="text-muted">Fill in the details to register your account</p>
                            </div>

                            <?php if (isset($_SESSION['error_msg'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $_SESSION['error_msg']; ?>
                                    <?php unset($_SESSION['error_msg']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form action="actions/register.php" method="POST">
                                <input type="hidden" name="register" value="1">
                                <div class="mb-3">
                                    <label class="form-label fw-600">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user" style="color: var(--emerald);"></i></span>
                                        <input type="text" name="name" class="form-control py-2" placeholder="John Doe" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-600">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope" style="color: var(--emerald);"></i></span>
                                        <input type="email" name="email" class="form-control py-2" placeholder="john@example.com" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-600">Choose Role</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check role-selector p-0 flex-fill">
                                            <input class="btn-check" type="radio" name="role" id="roleRenter" value="renter" checked>
                                            <label class="btn btn-outline-secondary w-100 py-2" for="roleRenter">
                                                <i class="fas fa-search me-2"></i> Renter
                                            </label>
                                        </div>
                                        <div class="form-check role-selector p-0 flex-fill">
                                            <input class="btn-check" type="radio" name="role" id="roleOwner" value="owner">
                                            <label class="btn btn-outline-secondary w-100 py-2" for="roleOwner">
                                                <i class="fas fa-home me-2"></i> Owner
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label fw-600">Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password" class="form-control py-2" placeholder="••••••••" required>
                                            <button type="button" class="input-group-text btn-toggle-pw" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-600">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" name="confirm_password" class="form-control py-2" placeholder="••••••••" required>
                                            <button type="button" class="input-group-text btn-toggle-pw" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary-nav btn-shimmer w-100 py-2 mb-4">
                                    <i class="fas fa-user-plus me-2"></i> Create Account
                                </button>
                                <div class="text-center">
                                    <p class="text-muted small mb-0">Already have an account? <a href="login.php" class="fw-bold text-decoration-none" style="color: var(--emerald);">Sign in here</a></p>
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
