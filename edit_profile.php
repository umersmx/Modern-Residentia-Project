<?php
include 'config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pageTitle = "Edit Profile";
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Fetch current user data
$result = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user = mysqli_fetch_assoc($result);
?>

<section class="py-5 min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white p-4 border-0">
                        <h4 class="fw-bold mb-1"><i class="fas fa-user-edit me-2"></i> Edit Profile</h4>
                        <p class="mb-0 small opacity-75">Update your personal information</p>
                    </div>
                    <div class="card-body p-4">

                        <?php if (isset($_SESSION['profile_success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                                <?php echo $_SESSION['profile_success']; ?>
                                <?php unset($_SESSION['profile_success']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['profile_error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                                <?php echo $_SESSION['profile_error']; ?>
                                <?php unset($_SESSION['profile_error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="actions/update_profile.php" method="POST" enctype="multipart/form-data">
                            <div class="text-center mb-4">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="<?php echo SITE_URL . '/' . $user['avatar']; ?>" class="rounded-circle mb-3 border border-3 border-primary d-block mx-auto" style="width: 100px; height: 100px; object-fit: cover;" id="avatarPreview">
                                <?php else: ?>
                                    <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.2rem;" id="avatarInitial">
                                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                    </div>
                                    <img src="" class="rounded-circle mb-3 border border-3 border-primary d-block mx-auto" style="width: 100px; height: 100px; object-fit: cover; display:none !important;" id="avatarPreview">
                                <?php endif; ?>
                                <div>
                                    <label for="avatarInput" class="btn btn-sm btn-outline-primary rounded-pill px-3 mb-2" style="cursor:pointer;">
                                        <i class="fas fa-camera me-1"></i> Change Photo
                                    </label>
                                    <input type="file" name="avatar" id="avatarInput" accept="image/*" class="d-none">
                                </div>
                                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill"><?php echo ucfirst($user['role']); ?> Account</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                                        <input type="text" name="name" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-phone text-muted"></i></span>
                                        <input type="text" name="phone" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="e.g. 0300-1234567">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Account Role</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-shield-alt text-muted"></i></span>
                                        <input type="text" class="form-control bg-light border-0 py-2" value="<?php echo ucfirst($user['role']); ?>" disabled>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="my-2">
                                    <p class="small text-muted mb-3"><i class="fas fa-lock me-1"></i> Leave password fields empty to keep your current password</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-key text-muted"></i></span>
                                        <input type="password" name="new_password" class="form-control bg-light border-0 py-2" placeholder="Enter new password">
                                        <button type="button" class="input-group-text bg-light border-0 btn-toggle-pw" onclick="togglePassword(this)"><i class="fas fa-eye text-muted"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-key text-muted"></i></span>
                                        <input type="password" name="confirm_password" class="form-control bg-light border-0 py-2" placeholder="Confirm new password">
                                        <button type="button" class="input-group-text bg-light border-0 btn-toggle-pw" onclick="togglePassword(this)"><i class="fas fa-eye text-muted"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="<?php echo $_SESSION['role'] == 'owner' ? 'owner_dashboard.php' : ($_SESSION['role'] == 'admin' ? 'admin_dashboard.php' : 'renter_dashboard.php'); ?>" class="btn btn-light px-4 rounded-pill">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 pt-3 border-top">
                            <p class="small text-muted mb-1"><i class="fas fa-calendar me-1"></i> Member since: <?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
