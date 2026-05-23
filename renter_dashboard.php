<?php
include 'config/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'renter') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pageTitle = "Renter Dashboard";
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Fetch Favorites
$fav_query = "SELECT p.*, pi.image_path FROM favorites f 
              JOIN properties p ON f.property_id = p.id 
              LEFT JOIN property_images pi ON p.id = pi.property_id AND pi.is_primary = 1
              WHERE f.user_id = $user_id";
$favorites = mysqli_query($conn, $fav_query);
$fav_count = mysqli_num_rows($favorites);

// Fetch Inquiries
$inq_query = "SELECT i.*, p.title as property_title FROM inquiries i 
              JOIN properties p ON i.property_id = p.id 
              WHERE i.sender_id = $user_id 
              ORDER BY i.created_at DESC";
$inquiries = mysqli_query($conn, $inq_query);
$inq_count = mysqli_num_rows($inquiries);
$grouped_chats = [];
while ($row = mysqli_fetch_assoc($inquiries)) {
    $prop_id = $row['property_id'];
    if (!isset($grouped_chats[$prop_id])) {
        $grouped_chats[$prop_id] = [
            'property_id' => $prop_id,
            'title' => $row['property_title'],
            'messages' => []
        ];
    }
    // Prepend to make oldest messages first (chronological order)
    array_unshift($grouped_chats[$prop_id]['messages'], $row);
}
$chat_count = count($grouped_chats);
?>

<section class="py-5 min-vh-100">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="dashboard-sidebar shadow-sm">
                    <div class="text-center mb-4">
                        <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; overflow: hidden;">
                            <?php if (!empty($_SESSION['avatar'])): ?>
                                <img src="<?php echo SITE_URL . '/' . $_SESSION['avatar']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <h5 class="fw-bold mb-0"><?php echo $_SESSION['user_name']; ?></h5>
                        <p class="text-muted small">Resident Renter</p>
                    </div>
                    <nav class="nav flex-column">
                        <a class="sidebar-nav-link active" href="#"><i class="fas fa-tachometer-alt me-2"></i> Overview</a>
                        <a class="sidebar-nav-link" href="#saved-properties"><i class="fas fa-heart me-2"></i> My Favorites</a>
                        <a class="sidebar-nav-link" href="#inquiries"><i class="fas fa-envelope me-2"></i> My Inquiries</a>
                        <a class="sidebar-nav-link" href="edit_profile.php"><i class="fas fa-user-edit me-2"></i> Edit Profile</a>
                        <hr>
                        <a class="sidebar-nav-link text-danger" href="actions/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                    </nav>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0">Dashboard Overview</h3>
                    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#requestPropertyModal">
                        <i class="fas fa-search-location me-2"></i> Request Property
                    </button>
                </div>

                <?php if (isset($_SESSION['request_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                        <?php echo $_SESSION['request_success']; unset($_SESSION['request_success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['request_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                        <?php echo $_SESSION['request_error']; unset($_SESSION['request_error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row g-3 mb-5">
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-danger-subtle text-danger"><i class="fas fa-heart"></i></div>
                            <h4 class="fw-bold mb-1"><?php echo $fav_count; ?></h4>
                            <p class="text-muted mb-0">Favorite Properties</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary-subtle text-primary"><i class="fas fa-paper-plane"></i></div>
                            <h4 class="fw-bold mb-1"><?php echo $inq_count; ?></h4>
                            <p class="text-muted mb-0">Total Inquiries Sent</p>
                        </div>
                    </div>
                </div>

                <h5 id="saved-properties" class="fw-bold mb-3 pt-3">Saved Properties</h5>
                <div class="row g-4 mb-5">
                    <?php
                    if ($fav_count > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($favorites)): ?>
                            <div class="col-md-6">
                                <div class="property-card h-100 shadow-sm border-0 rounded-4 overflow-hidden bg-white">
                                    <div class="d-flex flex-column flex-sm-row g-0">
                                        <div class="dashboard-fav-image-wrapper">
                                            <img src="<?php echo $row['image_path'] ? $row['image_path'] : 'assets/images/property-placeholder.jpg'; ?>" class="h-100 w-100" style="object-fit: cover;">
                                        </div>
                                        <div class="p-3 flex-grow-1">
                                            <h6 class="fw-bold mb-1 text-truncate"><?php echo $row['title']; ?></h6>
                                            <p class="small text-muted mb-2"><i class="fas fa-map-marker-alt me-1"></i><?php echo $row['location']; ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-primary fw-bold small">Rs. <?php echo number_format($row['price']); ?></span>
                                                <a href="property_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary px-3 rounded-pill">View</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-light border shadow-sm rounded-4 p-4 text-center">
                                <i class="far fa-heart fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">You haven't saved any properties yet.</p>
                                <a href="properties.php" class="btn btn-primary mt-3 px-4 rounded-pill">Browse Properties</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <h5 id="inquiries" class="fw-bold mb-3 pt-3">Recent Inquiries</h5>
                <div class="row g-4 mb-5">
                    <?php if ($chat_count > 0): ?>
                        <?php foreach($grouped_chats as $prop_id => $chat): ?>
                            <div class="col-12">
                                <div class="chat-container bg-white rounded-4 p-0 shadow-sm border overflow-hidden">
                                    <!-- Property Info Header -->
                                    <div class="bg-light p-3 border-bottom d-flex align-items-center justify-content-between">
                                        <h6 class="fw-bold mb-0"><i class="fas fa-home text-primary me-2"></i> Chat regarding: <a href="property_detail.php?id=<?php echo $chat['property_id']; ?>" class="text-primary text-decoration-none"><?php echo $chat['title']; ?></a></h6>
                                    </div>

                                    <!-- Chat Messages Area -->
                                    <div class="chat-messages p-4" style="max-height: 400px; overflow-y: auto;">
                                        <?php 
                                        $last_msg_had_reply = false;
                                        foreach($chat['messages'] as $msg): 
                                        ?>
                                            <!-- Renter Message (Right) -->
                                            <div class="d-flex justify-content-end mb-3">
                                                <div class="ms-3" style="max-width: 75%;">
                                                    <div class="bg-primary text-white p-3 rounded-4 rounded-bottom-0 shadow-sm">
                                                        <p class="mb-0 small"><?php echo $msg['message']; ?></p>
                                                    </div>
                                                    <div class="text-end mt-1">
                                                        <small class="text-muted" style="font-size: 0.7rem;"><?php echo date('M d, H:i', strtotime($msg['created_at'])); ?></small>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ms-2">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (!empty($msg['reply'])): ?>
                                            <!-- Owner Reply (Left) -->
                                            <div class="d-flex justify-content-start mb-3">
                                                <div class="flex-shrink-0 me-2">
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-building"></i>
                                                    </div>
                                                </div>
                                                <div class="me-3" style="max-width: 75%;">
                                                    <div class="bg-white border p-3 rounded-4 rounded-top-0 shadow-sm">
                                                        <p class="mb-0 small"><?php echo $msg['reply']; ?></p>
                                                    </div>
                                                    <div class="mt-1">
                                                        <small class="text-muted" style="font-size: 0.7rem;"><?php echo date('M d, H:i', strtotime($msg['replied_at'])); ?></small>
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
                                        <form action="actions/reply_back.php" method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="property_id" value="<?php echo $chat['property_id']; ?>">
                                            <input type="text" name="message" class="form-control rounded-pill bg-light border-0 px-4" placeholder="Type your message..." required>
                                            <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px; min-width: 45px;">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-light border shadow-sm rounded-4 p-4 text-center">
                                <i class="far fa-envelope-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No inquiries sent yet.</p>
                                <a href="properties.php" class="btn btn-primary mt-3 px-4 rounded-pill">Find a Property</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Request Property Modal -->
<div class="modal fade" id="requestPropertyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white border-0 p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-search-location me-2"></i> Request a Property</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="actions/request_property.php" method="POST">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Can't find what you're looking for? Tell the Admin your requirements and they will help find a suitable house for you.</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Desired Location / Area</label>
                        <input type="text" name="desired_location" class="form-control bg-light border-0 py-2" placeholder="e.g. DHA Phase 5, Bahria Town" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Property Type</label>
                            <select name="property_type" class="form-select bg-light border-0 py-2" required>
                                <option value="House">House</option>
                                <option value="Apartment">Apartment</option>
                                <option value="Room">Room</option>
                                <option value="Commercial">Commercial</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Min Bedrooms</label>
                            <input type="number" name="min_bedrooms" class="form-control bg-light border-0 py-2" value="1" min="1" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Maximum Monthly Budget (Rs)</label>
                        <input type="number" name="max_budget" class="form-control bg-light border-0 py-2" placeholder="e.g. 50000" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Additional Requirements</label>
                        <textarea name="additional_notes" class="form-control bg-light border-0 py-2" rows="3" placeholder="Any specific needs like parking, near school, ground floor, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-paper-plane me-2"></i> Send Request to Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
