<?php
session_start();
require_once 'config/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pageTitle = "Owner Dashboard";
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Fetch Properties
$prop_query = "SELECT p.*, (SELECT COUNT(*) FROM inquiries WHERE property_id = p.id) as inq_count, 
              (SELECT image_path FROM property_images WHERE property_id = p.id AND is_primary = 1 LIMIT 1) as main_img
              FROM properties p WHERE owner_id = $user_id ORDER BY created_at DESC";
$my_properties = mysqli_query($conn, $prop_query);
$prop_count = mysqli_num_rows($my_properties);

// Fetch Latest Inquiries
$inq_query = "SELECT i.*, p.title as property_title, u.name as sender_name, u.email as sender_email
              FROM inquiries i 
              JOIN properties p ON i.property_id = p.id 
              JOIN users u ON i.sender_id = u.id
              WHERE p.owner_id = $user_id 
              ORDER BY i.is_read ASC, i.created_at DESC LIMIT 10";
$recent_inquiries = mysqli_query($conn, $inq_query);
$inq_count = mysqli_num_rows($recent_inquiries);
$grouped_chats = [];
while ($row = mysqli_fetch_assoc($recent_inquiries)) {
    $group_key = $row['property_id'] . '_' . $row['sender_id'];
    if (!isset($grouped_chats[$group_key])) {
        $grouped_chats[$group_key] = [
            'property_id' => $row['property_id'],
            'title' => $row['property_title'],
            'sender_name' => $row['sender_name'],
            'messages' => []
        ];
    }
    // Prepend so chronological order
    array_unshift($grouped_chats[$group_key]['messages'], $row);
}
$chat_count = count($grouped_chats);
// Fetch Total Views
$views_result = mysqli_query($conn, "SELECT SUM(views) as total_views FROM properties WHERE owner_id = $user_id");
$views_row = mysqli_fetch_assoc($views_result);
$total_views = $views_row['total_views'] ?? 0;
?>

<section class="py-5 min-vh-100">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="dashboard-sidebar shadow-sm">
                    <div class="text-center mb-4">
                        <div class="user-avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; overflow: hidden;">
                            <?php if (!empty($_SESSION['avatar'])): ?>
                                <img src="<?php echo SITE_URL . '/' . $_SESSION['avatar']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <h5 class="fw-bold mb-0"><?php echo $_SESSION['user_name']; ?></h5>
                        <p class="text-muted small">Property Owner</p>
                    </div>
                    <nav class="nav flex-column">
                        <a class="sidebar-nav-link active" href="owner_dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Overview</a>
                        <a class="sidebar-nav-link" href="#" data-bs-toggle="modal" data-bs-target="#addPropertyModal"><i class="fas fa-plus-circle me-2"></i> Post New Ad</a>
                        <a class="sidebar-nav-link" href="#my-properties"><i class="fas fa-list me-2"></i> My Properties</a>
                        <a class="sidebar-nav-link" href="#messages"><i class="fas fa-comments me-2"></i> Messages</a>
                        <a class="sidebar-nav-link" href="edit_profile.php"><i class="fas fa-user-edit me-2"></i> Edit Profile</a>
                        <hr>
                        <a class="sidebar-nav-link text-danger" href="actions/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                    </nav>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0">Owner Dashboard</h3>
                    <button class="btn btn-primary px-4 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#addPropertyModal">
                        <i class="fas fa-plus me-2"></i> Add New Property
                    </button>
                </div>

                <?php if (isset($_SESSION['success_msg'])): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                        <?php echo $_SESSION['success_msg']; ?>
                        <?php unset($_SESSION['success_msg']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-3 mb-5">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary-subtle text-primary"><i class="fas fa-building"></i></div>
                            <h4 class="fw-bold mb-1"><?php echo $prop_count; ?></h4>
                            <p class="text-muted mb-0">Listed Properties</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-info-subtle text-info"><i class="fas fa-eye"></i></div>
                            <h4 class="fw-bold mb-1"><?php echo number_format($total_views); ?></h4>
                            <p class="text-muted mb-0">Total Ad Views</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-success-subtle text-success"><i class="fas fa-comment-dots"></i></div>
                            <h4 class="fw-bold mb-1"><?php echo $inq_count; ?></h4>
                            <p class="text-muted mb-0">New Inquiries</p>
                        </div>
                    </div>
                </div>

                <h5 id="my-properties" class="fw-bold mb-3 pt-3">Manage My Ads</h5>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Property Info</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                    <th>Inquiries</th>
                                    <th class="pe-4 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($prop_count > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($my_properties)): ?>
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo $row['main_img'] ?: 'assets/images/property-placeholder.jpg'; ?>" class="rounded-3 me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold"><?php echo $row['title']; ?></div>
                                                        <small class="text-muted"><?php echo $row['location']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="fw-bold">Rs. <?php echo number_format($row['price']); ?></span></td>
                                            <td>
                                                <?php if($row['status'] === 'approved'): ?>
                                                    <span class="badge bg-success-subtle text-success px-3 rounded-pill">Active</span>
                                                <?php elseif($row['status'] === 'pending'): ?>
                                                    <span class="badge bg-warning-subtle text-warning px-3 rounded-pill">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-subtle text-danger px-3 rounded-pill"><?php echo ucfirst($row['status']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge bg-light text-dark border px-3 rounded-pill"><i class="fas fa-eye text-muted me-1"></i> <?php echo number_format($row['views']); ?></span></td>
                                            <td><span class="badge bg-light text-dark border px-3 rounded-pill"><i class="fas fa-comment-dots text-muted me-1"></i> <?php echo $row['inq_count']; ?></span></td>
                                            <td class="pe-4 text-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm border rounded-circle" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                                                        <li><a class="dropdown-item" href="property_detail.php?id=<?php echo $row['id']; ?>"><i class="fas fa-eye me-2"></i>View Ad</a></li>
                                                        <li><a class="dropdown-item" href="edit_property.php?id=<?php echo $row['id']; ?>"><i class="fas fa-edit me-2"></i>Edit Details</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="actions/delete_property.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this listing?')"><i class="fas fa-trash-alt me-2"></i>Delete</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">You haven't posted any properties yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <h5 id="messages" class="fw-bold mb-3 pt-3">Latest Inquiries</h5>
                 <div class="row g-4 mb-5">
                    <?php if ($chat_count > 0): ?>
                        <?php foreach($grouped_chats as $group_key => $chat): ?>
                            <div class="col-12">
                                <div class="chat-container bg-white rounded-4 p-0 shadow-sm border overflow-hidden">
                                    <!-- Chat Header -->
                                    <div class="bg-light p-3 border-bottom d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <?php echo strtoupper(substr($chat['sender_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0"><?php echo $chat['sender_name']; ?></h6>
                                                <small class="text-muted">Inquired about: <a href="property_detail.php?id=<?php echo $chat['property_id']; ?>" class="text-primary text-decoration-none fw-bold"><?php echo $chat['title']; ?></a></small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Chat Messages Area -->
                                    <div class="chat-messages p-4" style="max-height: 400px; overflow-y: auto;">
                                        <?php 
                                        $last_msg_id = null;
                                        $last_msg_had_reply = false;
                                        foreach($chat['messages'] as $msg): 
                                            $last_msg_id = $msg['id']; // Track the last inquiry ID for replies
                                        ?>
                                            <!-- Renter Message (Left for Owner) -->
                                            <div class="d-flex justify-content-start mb-3">
                                                <div class="flex-shrink-0 me-2">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                </div>
                                                <div class="me-3" style="max-width: 75%;">
                                                    <div class="bg-white border p-3 rounded-4 rounded-top-0 shadow-sm">
                                                        <p class="mb-0 small"><?php echo $msg['message']; ?></p>
                                                    </div>
                                                    <div class="mt-1">
                                                        <small class="text-muted" style="font-size: 0.7rem;"><?php echo date('M d, H:i', strtotime($msg['created_at'])); ?></small>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (!empty($msg['reply'])): ?>
                                            <!-- Owner Reply (Right for Owner) -->
                                            <div class="d-flex justify-content-end mb-3">
                                                <div class="ms-3" style="max-width: 75%;">
                                                    <div class="bg-success text-white p-3 rounded-4 rounded-bottom-0 shadow-sm">
                                                        <p class="mb-0 small"><?php echo $msg['reply']; ?></p>
                                                    </div>
                                                    <div class="text-end mt-1">
                                                        <small class="text-muted" style="font-size: 0.7rem;"><?php echo date('M d, H:i', strtotime($msg['replied_at'])); ?></small>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ms-2">
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-building"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php 
                                            $last_msg_had_reply = true;
                                            else:
                                                $last_msg_had_reply = false;
                                            endif; 
                                            ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Reply Input Area -->
                                    <div class="chat-input-area p-3 bg-white border-top">
                                        <form action="actions/reply_inquiry.php" method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="inquiry_id" value="<?php echo $last_msg_id; ?>">
                                            <input type="text" name="reply" class="form-control rounded-pill bg-light border-0 px-4" placeholder="Write your message..." required>
                                            <button type="submit" class="btn btn-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px; min-width: 45px;">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-4 text-muted border rounded-4 bg-white">No inquiries received yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Property Modal -->
<div class="modal fade" id="addPropertyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <form action="actions/add_property.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header border-0 bg-primary text-white p-4">
                    <h5 class="modal-title fw-bold">Post New Property Ad</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted text-uppercase">Property Title</label>
                            <input type="text" name="title" class="form-control bg-light border-0 py-2" placeholder="e.g. Modern 2-Bed Luxury Flat" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Property Type</label>
                            <select name="type" class="form-select bg-light border-0 py-2" required>
                                <?php foreach(PROPERTY_TYPES as $val => $label): ?>
                                    <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Monthly Rent (Rs)</label>
                            <input type="number" name="price" class="form-control bg-light border-0 py-2" placeholder="e.g. 45000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Location / Area</label>
                            <input type="text" name="location" class="form-control bg-light border-0 py-2" placeholder="e.g. Gulberg III" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">City</label>
                            <input type="text" name="city" class="form-control bg-light border-0 py-2" value="Lahore" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Bedrooms</label>
                            <input type="number" name="bedrooms" class="form-control bg-light border-0 py-2" value="1" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Bathrooms</label>
                            <input type="number" name="bathrooms" class="form-control bg-light border-0 py-2" value="1" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Area (Sqft)</label>
                            <input type="number" name="area_sqft" class="form-control bg-light border-0 py-2" placeholder="e.g. 1100">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted text-uppercase">Description</label>
                            <textarea name="description" class="form-control bg-light border-0 py-2" rows="4" placeholder="Describe your property features, rules, etc." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted text-uppercase">Property Images</label>
                            <input type="file" name="images[]" id="propertyImages" class="form-control bg-light border-0" multiple accept="image/*" required>
                            <small class="text-muted mt-1 d-block">Select between 3 to 6 images. First image will be used as the main thumbnail.</small>
                            <div id="imagePreview" class="mt-3 d-flex flex-wrap"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Publish Listing</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
