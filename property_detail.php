<?php
include 'config/db_connection.php';

$id = $_GET['id'];

// Fetch property with owner info
$query = "SELECT p.*, u.name as owner_name, u.phone as owner_phone, u.email as owner_email 
          FROM properties p 
          JOIN users u ON p.owner_id = u.id 
          WHERE p.id = $id";
$result = mysqli_query($conn, $query);
$property = mysqli_fetch_assoc($result);

if (!$property) {
    header("Location: properties.php");
    exit();
}

// Increment view count
mysqli_query($conn, "UPDATE properties SET views = views + 1 WHERE id = $id");

$pageTitle = $property['title'];
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Fetch all images for this property
$img_result = mysqli_query($conn, "SELECT image_path, is_primary FROM property_images WHERE property_id=$id ORDER BY is_primary DESC");
$all_images = [];
while($img = mysqli_fetch_assoc($img_result)) {
    $all_images[] = $img;
}

// Check if current user has favorited this property (for AJAX favorite button)
$is_favorited = false;
if (isset($_SESSION['user_id'])) {
    $fav_check = mysqli_query($conn, "SELECT id FROM favorites WHERE user_id=" . $_SESSION['user_id'] . " AND property_id=$id");
    $is_favorited = mysqli_num_rows($fav_check) > 0;
}
?>

<section class="py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="properties.php">Properties</a></li>
                <li class="breadcrumb-item active"><?php echo $property['type']; ?></li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Left Side: Gallery & Info -->
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 overflow-hidden mb-4" style="border: 1px solid var(--border-subtle) !important;">
                    <!-- Image Carousel -->
                    <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php if (empty($all_images)): ?>
                                <div class="carousel-item active">
                                    <img src="assets/images/property-placeholder.jpg" class="d-block w-100" style="height: 500px; object-fit: cover;">
                                </div>
                            <?php else: ?>
                                <?php foreach($all_images as $idx => $img): ?>
                                    <div class="carousel-item <?php echo $idx === 0 ? 'active' : ''; ?>">
                                        <img src="<?php echo $img['image_path']; ?>" class="d-block w-100" style="height: 500px; object-fit: cover;">
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php if (count($all_images) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge rounded-pill mb-2 px-3 py-2" style="background: rgba(16,185,129,0.15); color: var(--emerald);"><?php echo ucfirst($property['type']); ?></span>
                                <h2 class="fw-bold"><?php echo $property['title']; ?></h2>
                                <p class="text-muted"><i class="fas fa-map-marker-alt me-2" style="color: var(--emerald);"></i><?php echo $property['address'] ? $property['address'] : $property['location'] . ', ' . $property['city']; ?></p>
                            </div>
                            <div class="text-end">
                                <h3 class="fw-bold mb-0 text-gold">Rs. <?php echo number_format($property['price']); ?></h3>
                                <p class="text-muted small">Per Month</p>
                            </div>
                        </div>

                        <div class="row g-3 py-4 border-top border-bottom mb-4 text-center">
                            <div class="col-4">
                                <div class="p-3 rounded-3" style="background: var(--bg-glass-strong);">
                                    <i class="fas fa-bed fs-4 mb-2" style="color: var(--emerald);"></i>
                                    <div class="small text-muted">Bedrooms</div>
                                    <div class="fw-bold"><?php echo $property['bedrooms']; ?></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3" style="background: var(--bg-glass-strong);">
                                    <i class="fas fa-bath fs-4 mb-2" style="color: var(--emerald);"></i>
                                    <div class="small text-muted">Bathrooms</div>
                                    <div class="fw-bold"><?php echo $property['bathrooms']; ?></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3" style="background: var(--bg-glass-strong);">
                                    <i class="fas fa-vector-square fs-4 mb-2" style="color: var(--emerald);"></i>
                                    <div class="small text-muted">Area</div>
                                    <div class="fw-bold"><?php echo $property['area_sqft']; ?> sqft</div>
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3">Description</h5>
                        <p class="text-muted mb-4"><?php echo nl2br($property['description']); ?></p>

                        <h5 class="fw-bold mb-3">Amenities</h5>
                        <div class="row g-3 mb-4">
                            <?php
                            $amenities = json_decode($property['amenities'], true);
                            if (!$amenities) $amenities = [];
                            foreach($amenities as $amenity):
                            ?>
                                <div class="col-md-4 col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle me-2" style="color: var(--emerald);"></i>
                                        <span><?php echo $amenity; ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- MAP INTEGRATION -->
                        <h5 class="fw-bold mb-3">Location Map</h5>
                        <div id="propertyMap" style="height: 350px; width: 100%; border-radius: 12px; z-index: 1; border: 1px solid var(--border-subtle);"></div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Favorite, Contact & Owner -->
            <div class="col-lg-4">
                <!-- AJAX Favorite Button -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="card border-0 rounded-4 p-4 mb-4" style="border: 1px solid var(--border-subtle) !important;">
                    <button id="favBtn" class="btn w-100 py-2 fw-bold rounded-pill <?php echo $is_favorited ? 'btn-danger' : 'btn-outline-danger'; ?>"
                            data-property-id="<?php echo $id; ?>" data-favorited="<?php echo $is_favorited ? '1' : '0'; ?>">
                        <i class="<?php echo $is_favorited ? 'fas' : 'far'; ?> fa-heart me-2"></i>
                        <span><?php echo $is_favorited ? 'Saved to Favorites' : 'Save to Favorites'; ?></span>
                    </button>
                    <div id="favMsg" class="text-center mt-2 small" style="display:none;"></div>
                </div>
                <?php endif; ?>

                <div class="card border-0 rounded-4 p-4 mb-4" style="border: 1px solid var(--border-subtle) !important;">
                    <h5 class="fw-bold mb-1">Interested? Send Inquiry</h5>
                    <div class="gold-divider mb-4"></div>
                    <?php if (isset($_SESSION['user_id'])): ?>

                        <?php if (isset($_SESSION['inquiry_success'])): ?>
                            <div class="alert alert-success small mb-0">
                                Your inquiry has been sent! The owner will contact you soon.
                            </div>
                            <?php unset($_SESSION['inquiry_success']); ?>
                        <?php elseif (isset($_SESSION['inquiry_error'])): ?>
                            <div class="alert alert-danger small mb-3">
                                <?php echo $_SESSION['inquiry_error']; ?>
                            </div>
                            <?php unset($_SESSION['inquiry_error']); ?>
                            <form action="actions/send_inquiry.php" method="POST">
                                <input type="hidden" name="property_id" value="<?php echo $id; ?>">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Your Message</label>
                                    <textarea name="message" class="form-control" rows="4" placeholder="I'm interested in this property. When can I visit?" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="fas fa-paper-plane me-2"></i> Send Message
                                </button>
                            </form>
                        <?php else: ?>
                            <form action="actions/send_inquiry.php" method="POST">
                                <input type="hidden" name="property_id" value="<?php echo $id; ?>">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Your Message</label>
                                    <textarea name="message" class="form-control" rows="4" placeholder="I'm interested in this property. When can I visit?" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="fas fa-paper-plane me-2"></i> Send Message
                                </button>
                            </form>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="text-center py-3">
                            <p class="text-muted mb-3">Please login to contact the owner.</p>
                            <a href="login.php" class="btn btn-outline-primary w-100 fw-bold">Login to Inquire</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card border-0 rounded-4 p-4" style="border: 1px solid var(--border-subtle) !important;">
                    <h5 class="fw-bold mb-1">Property Owner</h5>
                    <div class="gold-divider mb-3"></div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 1.2rem; border-radius: 50%; background: var(--gradient-emerald); color: white;">
                            <?php echo strtoupper(substr($property['owner_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0"><?php echo $property['owner_name']; ?></h6>
                            <small class="text-muted">Registered Owner</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-phone text-muted me-2"></i>
                        <span><?php echo $property['owner_phone'] ? $property['owner_phone'] : 'Not provided'; ?></span>
                    </div>
                    <div class="mb-0">
                        <i class="fas fa-envelope text-muted me-2"></i>
                        <span><?php echo $property['owner_email']; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. AJAX Favorites
    const favBtn = document.getElementById('favBtn');
    const favMsg = document.getElementById('favMsg');
    if (favBtn) {
        favBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const propertyId = this.getAttribute('data-property-id');
            
            // Create form data
            const formData = new FormData();
            formData.append('property_id', propertyId);

            fetch('actions/save_favorite.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.action === 'added') {
                        favBtn.classList.remove('btn-outline-danger');
                        favBtn.classList.add('btn-danger');
                        favBtn.innerHTML = '<i class="fas fa-heart me-2"></i><span>Saved to Favorites</span>';
                    } else {
                        favBtn.classList.remove('btn-danger');
                        favBtn.classList.add('btn-outline-danger');
                        favBtn.innerHTML = '<i class="far fa-heart me-2"></i><span>Save to Favorites</span>';
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // 2. Leaflet Map Initialization
    const map = L.map('propertyMap').setView([31.5204, 74.3587], 13); // Default to Lahore
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([31.5204, 74.3587]).addTo(map)
        .bindPopup('<b><?php echo addslashes($property["title"]); ?></b><br><?php echo addslashes($property["city"]); ?>')
        .openPopup();
});
</script>

<?php require_once 'includes/footer.php'; ?>
