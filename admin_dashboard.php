<?php
include 'config/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Admin Dashboard";
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Stats
$res1 = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role != 'admin'");
$total_users = mysqli_fetch_assoc($res1)['count'];

$res2 = mysqli_query($conn, "SELECT COUNT(*) as count FROM properties WHERE status = 'pending'");
$pending_props = mysqli_fetch_assoc($res2)['count'];

$res3 = mysqli_query($conn, "SELECT COUNT(*) as count FROM properties WHERE status = 'rented'");
$total_rentals = mysqli_fetch_assoc($res3)['count'];

// Recent users
$users = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC LIMIT 10");

// Pending properties
$pending_list = mysqli_query($conn, "SELECT p.*, u.name as owner_name FROM properties p JOIN users u ON p.owner_id = u.id WHERE p.status = 'pending' ORDER BY p.created_at ASC");
?>

<section class="py-5 min-vh-100">
    <div class="container">
        <h3 class="fw-bold mb-4">System Administration</h3>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="fas fa-users"></i></div>
                    <h4 class="fw-bold mb-1"><?php echo $total_users; ?></h4>
                    <p class="text-muted mb-0">Total Active Users</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card border border-warning">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="fas fa-clock"></i></div>
                    <h4 class="fw-bold mb-1"><?php echo $pending_props; ?></h4>
                    <p class="text-muted mb-0">Pending Approvals</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card border border-success">
                    <div class="stat-icon bg-success-subtle text-success"><i class="fas fa-check-circle"></i></div>
                    <h4 class="fw-bold mb-1"><?php echo $total_rentals; ?></h4>
                    <p class="text-muted mb-0">Properties Rented</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Property Moderation -->
            <div class="col-lg-7">
                <h5 class="fw-bold mb-3">Pending Moderation</h5>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Property</th>
                                    <th>Owner</th>
                                    <th class="pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($pending_list) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($pending_list)): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold"><?php echo $row['title']; ?></div>
                                                <small class="text-muted"><?php echo $row['location']; ?> • Rs. <?php echo number_format($row['price']); ?></small>
                                            </td>
                                            <td><small><?php echo $row['owner_name']; ?></small></td>
                                            <td class="pe-4 text-end">
                                                <a href="property_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" target="_blank" title="View Ad Details"><i class="fas fa-eye"></i></a>
                                                <a href="actions/update_status.php?id=<?php echo $row['id']; ?>&status=approved" class="btn btn-sm btn-success rounded-pill px-3">Approve</a>
                                                <a href="actions/update_status.php?id=<?php echo $row['id']; ?>&status=rejected" class="btn btn-sm btn-outline-danger rounded-pill px-3">Reject</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center py-4 text-muted">No pending properties.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- User Management -->
            <div class="col-lg-5">
                <h5 class="fw-bold mb-3">Recent Users</h5>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="list-group list-group-flush">
                        <?php while($row = mysqli_fetch_assoc($users)): ?>
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold small"><?php echo $row['name']; ?></div>
                                            <small class="text-muted"><?php echo ucfirst($row['role']); ?> • <?php echo $row['email']; ?></small>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link btn-sm text-muted" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                                            <li><a class="dropdown-item" href="edit_profile.php"><i class="fas fa-user-edit me-2"></i>Edit Profile</a></li>
                                            <li><a class="dropdown-item text-danger" href="actions/delete_user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this user?')"><i class="fas fa-user-slash me-2"></i>Delete</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-12">
                <h5 class="fw-bold mb-3">Renter Property Requests</h5>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Renter</th>
                                    <th>Desired Location</th>
                                    <th>Type</th>
                                    <th>Min. Bed</th>
                                    <th>Max Budget</th>
                                    <th>Notes</th>
                                    <th>Status</th>
                                    <th class="pe-4 text-end">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $requests = mysqli_query($conn, "SELECT pr.*, u.name as renter_name, u.email as renter_email, u.phone as renter_phone FROM property_requests pr JOIN users u ON pr.renter_id = u.id ORDER BY pr.created_at DESC");
                                if (mysqli_num_rows($requests) > 0): 
                                    while($req = mysqli_fetch_assoc($requests)): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold"><?php echo $req['renter_name']; ?></div>
                                            <small class="text-muted"><?php echo $req['renter_phone'] ?: $req['renter_email']; ?></small>
                                        </td>
                                        <td><?php echo $req['desired_location']; ?></td>
                                        <td><?php echo $req['property_type']; ?></td>
                                        <td><?php echo $req['min_bedrooms']; ?></td>
                                        <td>Rs. <?php echo number_format($req['max_budget']); ?></td>
                                        <td><small class="text-muted d-inline-block text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($req['additional_notes']); ?>"><?php echo htmlspecialchars($req['additional_notes']) ?: '-'; ?></small></td>
                                        <td>
                                            <span class="badge <?php echo $req['status'] == 'pending' ? 'bg-warning text-dark' : 'bg-success'; ?> rounded-pill px-3">
                                                <?php echo ucfirst($req['status']); ?>
                                            </span>
                                        </td>
                                        <td class="pe-4 text-end text-muted small"><?php echo date('M d, Y', strtotime($req['created_at'])); ?></td>
                                    </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr><td colspan="8" class="text-center py-4 text-muted">No property requests found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
