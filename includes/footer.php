</main>
        
        <!-- Footer -->
        <footer class="bg-dark text-white pt-5 pb-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <h5 class="text-uppercase fw-bold mb-3">About IYEF</h5>
                        <p>
                            Indefatigable Youth Empowerment Foundation (IYEF) is a registered non-profit, non-governmental, 
                            and faith-based organization dedicated to empowering and uplifting weak and vulnerable adolescents.
                        </p>
                        <div class="social-links mt-3">
                            <?php if (!empty($settings['facebook_url'])): ?>
                                <a href="<?php echo $settings['facebook_url']; ?>" class="text-white me-2"><i class="fab fa-facebook-f fa-lg"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($settings['twitter_url'])): ?>
                                <a href="<?php echo $settings['twitter_url']; ?>" class="text-white me-2"><i class="fab fa-twitter fa-lg"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($settings['instagram_url'])): ?>
                                <a href="<?php echo $settings['instagram_url']; ?>" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-2 mb-4">
                        <h5 class="text-uppercase fw-bold mb-3">Quick Links</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="" class="text-white text-decoration-none">Home</a></li>
                            <li class="mb-2"><a href="about.php" class="text-white text-decoration-none">About Us</a></li>
                            <li class="mb-2"><a href="programs.php" class="text-white text-decoration-none">Programs</a></li>
                            <li class="mb-2"><a href="events.php" class="text-white text-decoration-none">Events</a></li>
                            <li class="mb-2"><a href="blog.php" class="text-white text-decoration-none">Blog</a></li>
                            <li class="mb-2"><a href="contact.php" class="text-white text-decoration-none">Contact</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3 mb-4">
                        <h5 class="text-uppercase fw-bold mb-3">Our Programs</h5>
                        <ul class="list-unstyled">
                            <?php
                            // Fetch limited programs for footer
                            $footer_programs_query = "SELECT id, title, slug FROM programs ORDER BY created_at DESC LIMIT 5";
                            $footer_programs_result = $conn->query($footer_programs_query);
                            while ($program = $footer_programs_result->fetch_assoc()) {
                                echo '<li class="mb-2"><a href="program.php?slug=' . $program['slug'] . '" class="text-white text-decoration-none">' . $program['title'] . '</a></li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="col-md-3 mb-4">
                        <h5 class="text-uppercase fw-bold mb-3">Contact Us</h5>
                        <address>
                            <p><i class="fas fa-map-marker-alt me-2"></i> <?php echo $settings['address'] ?? '123 NGO Street, City, Country'; ?></p>
                            <p><i class="fas fa-phone me-2"></i> <?php echo $settings['contact_phone'] ?? '+1234567890'; ?></p>
                            <p><i class="fas fa-envelope me-2"></i> <?php echo $settings['contact_email'] ?? 'info@iyef.org'; ?></p>
                        </address>
                    </div>
                </div>
                <hr class="my-4 bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0">&copy; <?php echo date('Y'); ?> INDEFATIGABLE YOUTH EMPOWERMENT FOUNDATION (IYEF). All Rights Reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p class="mb-0">
                            <a href="privacy.php" class="text-white text-decoration-none me-3">Privacy Policy</a>
                            <a href="terms.php" class="text-white text-decoration-none">Terms of Service</a>
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Back to Top Button -->
        <a href="#" class="btn btn-primary back-to-top position-fixed bottom-0 end-0 m-4 rounded-circle shadow" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-arrow-up"></i>
        </a>

        <!-- Bootstrap JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Custom JS -->
        <script src="assets/js/script.js"></script>
        
        <!-- Initialize tooltips -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
                
                // Back to top button
                var backToTopButton = document.querySelector('.back-to-top');
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 300) {
                        backToTopButton.style.display = 'flex';
                    } else {
                        backToTopButton.style.display = 'none';
                    }
                });
                
                backToTopButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({top: 0, behavior: 'smooth'});
                });
            });
        </script>
    </body>
</html>
<?php
// Close database connection
$conn->close();
?>