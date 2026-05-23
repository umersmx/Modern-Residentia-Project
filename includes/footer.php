    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-wave">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" preserveAspectRatio="none">
                <path d="M0,60 C360,120 720,0 1080,60 C1260,90 1380,80 1440,60 L1440,120 L0,120 Z" fill="currentColor"></path>
            </svg>
        </div>
        <div class="container">
            <div class="row g-4 py-5">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand mb-3">
                        <div class="brand-icon me-2" style="display:inline-flex"><i class="fas fa-building"></i></div>
                        <span class="brand-text text-white fs-4">Modern<span class="brand-highlight">Residentia</span></span>
                    </div>
                    <p class="text-white-50 mb-4">Your trusted partner in finding the perfect rental property. Connecting owners and renters with a seamless, modern experience.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-heading">Quick Links</h6>
                    <ul class="footer-links">
                        <li><a href="<?php echo SITE_URL; ?>/index.php">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/properties.php">Properties</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/login.php">Login</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/register.php">Register</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-heading">Property Types</h6>
                    <ul class="footer-links">
                        <li><a href="<?php echo SITE_URL; ?>/properties.php?type=apartment">Apartments</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/properties.php?type=house">Houses</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/properties.php?type=room">Rooms</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/properties.php?type=hostel">Hostels</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/properties.php?type=commercial">Commercial</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-heading">Contact Us</h6>
                    <ul class="footer-contact">
                        <li><i class="fas fa-map-marker-alt me-2"></i> Lahore, Pakistan</li>
                        <li><i class="fas fa-phone me-2"></i> +92 300 1234567</li>
                        <li><i class="fas fa-envelope me-2"></i> info@residentia.com</li>
                        <li><i class="fas fa-clock me-2"></i> Mon - Sat: 9AM - 6PM</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary" style="opacity: 0.1;">
            <div class="row py-3">
                <div class="col-md-6">
                    <p class="text-white-50 mb-0 small">&copy; <?php echo date('Y'); ?> Modern Residentia. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-white-50 mb-0 small">Built with <i class="fas fa-heart text-danger"></i> by <span class="text-white fw-bold">Umer Farooq</span> using PHP & Bootstrap 5</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
