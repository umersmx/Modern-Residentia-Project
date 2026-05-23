<?php
include 'config/db_connection.php';
$pageTitle = "Home";
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Fetch Featured Properties
$query = "SELECT p.*, pi.image_path FROM properties p 
          LEFT JOIN property_images pi ON p.id = pi.property_id AND pi.is_primary = 1
          WHERE p.status = 'approved' AND p.is_featured = 1 
          LIMIT 6";
$featured = mysqli_query($conn, $query);
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="hero-grid-overlay"></div>
    <div class="container">
        <div class="hero-content">
            <div class="section-badge animate__animated animate__fadeInDown">
                <span>Premium Property Platform</span>
            </div>
            <h1 class="fw-900 text-white animate__animated animate__fadeIn">Find Your Perfect <br><span class="serif-heading text-gold">Next Home</span> in Lahore</h1>
            <p class="lead text-white-50 mb-4 mx-auto" style="max-width: 600px;">Modern Residentia is the most trusted property rental platform, helping thousands of renters find their ideal space every month.</p>
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="search-card animate__animated animate__fadeInUp animate__delay-1s">
                        <form action="properties.php" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" class="form-control py-3" placeholder="Enter keywords, city or area...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="type" class="form-select py-3">
                                    <option value="">Property Type</option>
                                    <?php foreach(PROPERTY_TYPES as $val => $label): ?>
                                        <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="price_range" class="form-select py-3">
                                    <option value="">Price Range</option>
                                    <option value="0-20000">Below 20,000</option>
                                    <option value="20000-50000">20,000 - 50,000</option>
                                    <option value="50000-100000">50,000 - 100,000</option>
                                    <option value="100000+">100,000+</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-shimmer w-100 h-100 fw-bold rounded-3" style="min-height: 50px;">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 stats-section">
    <div class="container">
        <div class="row g-4 reveal">
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number counter-value" data-target="2500" data-suffix="+">0</div>
                    <div class="stat-label">Active Listings</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number counter-value" data-target="1200" data-suffix="+">0</div>
                    <div class="stat-label">Happy Renters</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number counter-value" data-target="800" data-suffix="+">0</div>
                    <div class="stat-label">Verified Owners</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number counter-value" data-target="150" data-suffix="+">0</div>
                    <div class="stat-label">Daily Inquiries</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <div class="section-badge mx-auto"><span>Why Choose Us</span></div>
            <h2 class="fw-bold section-title mb-3">The <span class="serif-heading text-gold">Premium</span> Experience</h2>
            <p class="section-subtitle mx-auto">We provide a luxury experience for finding your next home, with verified listings and dedicated support.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 reveal reveal-delay-1">
                <div class="feature-card h-100">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h5 class="fw-bold mb-3">Verified Properties</h5>
                    <p class="text-muted mb-0">Every listing is manually verified by our team to ensure authenticity and quality standards.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 reveal reveal-delay-2">
                <div class="feature-card h-100">
                    <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                    <h5 class="fw-bold mb-3">Instant Connect</h5>
                    <p class="text-muted mb-0">Connect directly with property owners instantly. No middlemen, no hidden charges.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 reveal reveal-delay-3">
                <div class="feature-card h-100">
                    <div class="feature-icon"><i class="fas fa-headset"></i></div>
                    <h5 class="fw-bold mb-3">24/7 Support</h5>
                    <p class="text-muted mb-0">Our dedicated support team is always ready to help you with any queries or concerns.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Listings -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 reveal">
            <div>
                <div class="section-badge"><span>Exclusive Properties</span></div>
                <h2 class="fw-bold section-title mb-0">Featured Listings</h2>
            </div>
            <a href="properties.php" class="btn btn-outline-primary rounded-pill px-4 fw-bold">View All <i class="fas fa-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            <?php while($row = mysqli_fetch_assoc($featured)): ?>
                <div class="col-lg-4 col-md-6 reveal">
                    <div class="property-card">
                        <div class="card-img-wrapper">
                            <img src="<?php echo $row['image_path'] ? $row['image_path'] : 'assets/images/property-placeholder.jpg'; ?>" alt="<?php echo $row['title']; ?>">
                            <div class="badge-overlay">
                                <span class="badge bg-primary px-3 py-2 rounded-pill"><?php echo ucfirst($row['type']); ?></span>
                            </div>
                            <div class="price-tag">
                                Rs. <?php echo number_format($row['price']); ?> <small class="text-muted fw-normal">/mo</small>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <span class="text-muted small"><?php echo $row['location']; ?>, <?php echo $row['city']; ?></span>
                            </div>
                            <h5 class="card-title fw-bold mb-3"><?php echo $row['title']; ?></h5>
                            <div class="d-flex justify-content-between border-top pt-3">
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
                                <a href="property_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary w-100 rounded-3">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5">
    <div class="container">
        <div class="cta-banner rounded-4 p-5 text-white reveal">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-3">Own a property? <span class="serif-heading">Rent it out</span> with us!</h2>
                    <p class="lead mb-lg-0" style="opacity: 0.85;">Join our network of property owners and start reaching thousands of potential renters today. Easy management, secure platform.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="register.php" class="btn btn-light btn-lg px-4 py-3 fw-bold rounded-3">Get Started Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
