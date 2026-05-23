<?php
include 'config/db_connection.php';
$pageTitle = "Browse Properties";
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Get filter parameters from URL
$type = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$city = isset($_GET['city']) ? $_GET['city'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$bedrooms = isset($_GET['bedrooms']) ? $_GET['bedrooms'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build the SQL query with filters
$query = "SELECT p.*, pi.image_path FROM properties p 
          LEFT JOIN property_images pi ON p.id = pi.property_id AND pi.is_primary = 1
          WHERE p.status = 'approved'";

// Apply search filter
if ($search != '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $query .= " AND (p.title LIKE '%$safe_search%' OR p.location LIKE '%$safe_search%' OR p.city LIKE '%$safe_search%')";
}

// Apply type filter
if ($type != '') {
    $safe_type = mysqli_real_escape_string($conn, $type);
    $query .= " AND p.type = '$safe_type'";
}

// Apply price range filters
if ($min_price != '') {
    $query .= " AND p.price >= " . intval($min_price);
}

if ($max_price != '') {
    $query .= " AND p.price <= " . intval($max_price);
}

// Apply bedrooms filter
if ($bedrooms != '') {
    $query .= " AND p.bedrooms >= " . intval($bedrooms);
}

// Apply sorting
if ($sort == 'price_low') {
    $query .= " ORDER BY p.price ASC";
} elseif ($sort == 'price_high') {
    $query .= " ORDER BY p.price DESC";
} else {
    $query .= " ORDER BY p.created_at DESC";
}

// --- PAGINATION LOGIC ---
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Clone query to get total count before applying limit
$count_query = str_replace("SELECT p.*, pi.image_path", "SELECT COUNT(p.id) as total", $query);
$count_query = preg_replace('/ORDER BY.*/', '', $count_query); // Remove ORDER BY for count
$count_result = mysqli_query($conn, $count_query);
$total_row = mysqli_fetch_assoc($count_result);
$total_properties = $total_row['total'];
$total_pages = ceil($total_properties / $limit);

// Apply limit to main query
$query .= " LIMIT $limit OFFSET $offset";

// Execute the main query
$properties = mysqli_query($conn, $query);
$total_count = mysqli_num_rows($properties); // Count on current page
?>

<section class="py-5" style="min-height: 80vh;">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filters -->
            <!-- Sidebar Filters -->
            <div class="col-lg-3 mb-4">
                <!-- Mobile Filter Toggle Button -->
                <button class="btn btn-outline-nav w-100 d-lg-none mb-3 py-2.5 d-flex align-items-center justify-content-center gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                    <i class="fas fa-filter"></i>
                    <span>Show / Hide Filters</span>
                </button>

                <div class="collapse d-lg-block" id="filterCollapse">
                    <div class="card border-0 rounded-4 p-4 sticky-top" style="top: 100px; z-index: 10; border: 1px solid var(--border-subtle) !important;">
                        <h5 class="fw-bold mb-1">Filters</h5>
                        <div class="gold-divider mb-4"></div>
                        <form method="GET" action="properties.php">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Keywords</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search title..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Property Type</label>
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <?php foreach (PROPERTY_TYPES as $val => $label): ?>
                                        <option value="<?php echo $val; ?>" <?php echo $type == $val ? 'selected' : ''; ?>>
                                            <?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Price Range (Rs)</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" name="min_price" class="form-control"
                                            placeholder="Min" value="<?php echo htmlspecialchars($min_price); ?>">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="max_price" class="form-control"
                                            placeholder="Max" value="<?php echo htmlspecialchars($max_price); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Bedrooms</label>
                                <select name="bedrooms" class="form-select">
                                    <option value="">Any</option>
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo $bedrooms == $i ? 'selected' : ''; ?>>
                                            <?php echo $i; ?>+</option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Sort By</label>
                                <select name="sort" class="form-select">
                                    <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First
                                    </option>
                                    <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low
                                        to High</option>
                                    <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price:
                                        High to Low</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold mb-2">Apply
                                Filters</button>
                            <a href="properties.php" class="btn btn-link text-decoration-none small w-100 text-center" style="color: var(--text-muted);">Reset
                                All Filters</a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Property Grid -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0"><?php echo $total_properties; ?> Properties Found</h4>
                </div>

                <div class="row g-4">
                    <?php if ($total_properties > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($properties)): ?>
                            <div class="col-md-6 mb-4 reveal">
                                <div class="property-card h-100">
                                    <div class="card-img-wrapper">
                                        <img src="<?php echo $row['image_path'] ? $row['image_path'] : 'assets/images/property-placeholder.jpg'; ?>"
                                            alt="<?php echo $row['title']; ?>">
                                        <div class="badge-overlay">
                                            <span
                                                class="badge bg-primary px-3 py-2 rounded-pill"><?php echo ucfirst($row['type']); ?></span>
                                        </div>
                                        <div class="price-tag">
                                            Rs. <?php echo number_format($row['price']); ?> <small
                                                class="text-muted fw-normal">/mo</small>
                                        </div>
                                    </div>
                                    <div class="card-body p-4 d-flex flex-column">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                            <span class="text-muted small"><?php echo $row['location']; ?>,
                                                <?php echo $row['city']; ?></span>
                                        </div>
                                        <h5 class="card-title fw-bold mb-3"><?php echo $row['title']; ?></h5>
                                        <div class="d-flex justify-content-between border-top pt-3 mt-auto">
                                            <div class="text-center">
                                                <i class="fas fa-bed text-muted mb-1"></i>
                                                <div class="small fw-bold"><?php echo $row['bedrooms']; ?> Beds</div>
                                            </div>
                                            <div class="text-center">
                                                <i class="fas fa-bath text-muted mb-1"></i>
                                                <div class="small fw-bold"><?php echo $row['bathrooms']; ?> Baths</div>
                                            </div>
                                            <div class="text-center">
                                                <i class="fas fa-vector-square text-muted mb-1"></i>
                                                <div class="small fw-bold"><?php echo $row['area_sqft']; ?> sqft</div>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <a href="property_detail.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-outline-primary w-100 rounded-3">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center py-5" style="background: var(--bg-card); border-radius: var(--radius-xl); border: 1px solid var(--border-subtle);">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <p class="mb-0 text-muted">No properties found matching your criteria.</p>
                                <a href="properties.php" class="btn btn-primary mt-3 rounded-pill px-4">View All
                                    Properties</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination Controls -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php 
                        // Preserve URL parameters for pagination links
                        $query_string = $_GET;
                        
                        // Previous Button
                        if ($page > 1) {
                            $query_string['page'] = $page - 1;
                            echo '<li class="page-item"><a class="page-link border-0 rounded-start-pill px-4 py-2" href="?' . http_build_query($query_string) . '">Previous</a></li>';
                        } else {
                            echo '<li class="page-item disabled"><span class="page-link border-0 rounded-start-pill px-4 py-2">Previous</span></li>';
                        }
                        
                        // Page Numbers
                        for ($i = 1; $i <= $total_pages; $i++) {
                            $query_string['page'] = $i;
                            $active = ($i == $page) ? 'active' : '';
                            echo '<li class="page-item ' . $active . '"><a class="page-link border-0 px-3 py-2 mx-1" href="?' . http_build_query($query_string) . '">' . $i . '</a></li>';
                        }
                        
                        // Next Button
                        if ($page < $total_pages) {
                            $query_string['page'] = $page + 1;
                            echo '<li class="page-item"><a class="page-link border-0 rounded-end-pill px-4 py-2" href="?' . http_build_query($query_string) . '">Next</a></li>';
                        } else {
                            echo '<li class="page-item disabled"><span class="page-link border-0 rounded-end-pill px-4 py-2">Next</span></li>';
                        }
                        ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>