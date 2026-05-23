<?php
include 'config/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id = intval($_GET['id']);

$result = mysqli_query($conn, "SELECT * FROM properties WHERE id=$id AND owner_id=$user_id");
$property = mysqli_fetch_assoc($result);

if (!$property) {
    $_SESSION['error_msg'] = "Property not found or you don't have permission.";
    header("Location: owner_dashboard.php");
    exit();
}

$pageTitle = "Edit Property";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<section class="py-5 min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white p-4 border-0">
                        <h4 class="fw-bold mb-1"><i class="fas fa-edit me-2"></i> Edit Property</h4>
                        <p class="mb-0 small opacity-75">Update your listing details and status</p>
                    </div>
                    <div class="card-body p-4">

                        <?php if (isset($_SESSION['edit_success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                                <?php echo $_SESSION['edit_success']; unset($_SESSION['edit_success']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['edit_error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                                <?php echo $_SESSION['edit_error']; unset($_SESSION['edit_error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="actions/edit_property.php" method="POST">
                            <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Property Title</label>
                                    <input type="text" name="title" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($property['title']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Property Type</label>
                                    <select name="type" class="form-select bg-light border-0 py-2" required>
                                        <?php foreach(PROPERTY_TYPES as $val => $label): ?>
                                            <option value="<?php echo $val; ?>" <?php echo $property['type'] == $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Monthly Rent (Rs)</label>
                                    <input type="number" name="price" class="form-control bg-light border-0 py-2" value="<?php echo $property['price']; ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Location / Area</label>
                                    <input type="text" name="location" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($property['location']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">City</label>
                                    <input type="text" name="city" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($property['city']); ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Bedrooms</label>
                                    <input type="number" name="bedrooms" class="form-control bg-light border-0 py-2" value="<?php echo $property['bedrooms']; ?>" min="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Bathrooms</label>
                                    <input type="number" name="bathrooms" class="form-control bg-light border-0 py-2" value="<?php echo $property['bathrooms']; ?>" min="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Area (Sqft)</label>
                                    <input type="number" name="area_sqft" class="form-control bg-light border-0 py-2" value="<?php echo $property['area_sqft']; ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Description</label>
                                    <textarea name="description" class="form-control bg-light border-0 py-2" rows="4" required><?php echo htmlspecialchars($property['description']); ?></textarea>
                                </div>

                                <div class="col-12">
                                    <hr class="my-2">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Listing Status</label>
                                    <select name="status" class="form-select bg-light border-0 py-2">
                                        <option value="pending" <?php echo $property['status'] == 'pending' ? 'selected' : ''; ?>>Pending Review</option>
                                        <option value="approved" <?php echo $property['status'] == 'approved' ? 'selected' : ''; ?>>Approved (Active)</option>
                                        <option value="rented" <?php echo $property['status'] == 'rented' ? 'selected' : ''; ?>>Rented Out</option>
                                    </select>
                                    <small class="text-muted mt-1 d-block">Mark as "Rented Out" once a renter is found.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Current Status</label>
                                    <div class="p-2 bg-light rounded-3 text-center">
                                        <?php if($property['status'] == 'approved'): ?>
                                            <span class="badge bg-success px-3 py-2 rounded-pill fs-6"><i class="fas fa-check-circle me-1"></i> Active</span>
                                        <?php elseif($property['status'] == 'pending'): ?>
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6"><i class="fas fa-clock me-1"></i> Pending</span>
                                        <?php elseif($property['status'] == 'rented'): ?>
                                            <span class="badge bg-info px-3 py-2 rounded-pill fs-6"><i class="fas fa-handshake me-1"></i> Rented</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger px-3 py-2 rounded-pill fs-6"><i class="fas fa-times-circle me-1"></i> <?php echo ucfirst($property['status']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="owner_dashboard.php" class="btn btn-light px-4 rounded-pill">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
