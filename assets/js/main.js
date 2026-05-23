/**
 * Modern Residentia — Premium UI Interactions
 * Scroll animations, counters, theme toggle, and micro-interactions
 */

// Toggle password visibility (used on login, register, edit profile pages)
function togglePassword(btn) {
    var input = btn.parentElement.querySelector("input[type='password'], input[type='text']");
    if (!input) return;
    var icon = btn.querySelector("i");
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

document.addEventListener("DOMContentLoaded", function() {
    "use strict";

    // ==================== SCROLL REVEAL ====================
    const revealElements = document.querySelectorAll('.reveal');
    if (revealElements.length > 0) {
        const revealObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        revealElements.forEach(function(el) {
            revealObserver.observe(el);
        });
    }

    // ==================== ANIMATED COUNTER ====================
    const counters = document.querySelectorAll('.counter-value');
    if (counters.length > 0) {
        const counterObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(function(counter) {
            counterObserver.observe(counter);
        });
    }

    function animateCounter(el) {
        var target = parseInt(el.getAttribute('data-target'));
        var suffix = el.getAttribute('data-suffix') || '';
        var duration = 2000;
        var startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            // Ease out cubic
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = Math.floor(eased * target);
            el.textContent = current.toLocaleString() + suffix;
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }
        requestAnimationFrame(step);
    }

    // ==================== NAVBAR SCROLL EFFECT ====================
    var navbar = document.getElementById("mainNavbar");
    if (navbar) {
        window.addEventListener("scroll", function() {
            if (window.scrollY > 50) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
        });
        // Trigger on load in case page is already scrolled
        if (window.scrollY > 50) {
            navbar.classList.add("scrolled");
        }
    }

    // ==================== AVATAR PREVIEW ====================
    var avatarInput = document.getElementById("avatarInput");
    if (avatarInput) {
        avatarInput.addEventListener("change", function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.getElementById("avatarPreview");
                    var initial = document.getElementById("avatarInitial");
                    if (preview) {
                        preview.src = e.target.result;
                        preview.setAttribute("style", "width: 100px; height: 100px; object-fit: cover; display: block !important; margin: 0 auto 1rem auto !important;");
                    }
                    if (initial) {
                        initial.setAttribute("style", "display: none !important;");
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // ==================== BOOTSTRAP TOOLTIPS ====================
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) {
        return new bootstrap.Tooltip(el);
    });

    // ==================== SMOOTH SCROLL ====================
    document.querySelectorAll('a[href^="#"]').forEach(function(link) {
        link.addEventListener("click", function(e) {
            var target = document.querySelector(this.getAttribute("href"));
            if (target) {
                e.preventDefault();
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: "smooth"
                });
            }
        });
    });

    // ==================== IMAGE UPLOAD PREVIEW ====================
    var imageInput = document.getElementById("propertyImages");
    if (imageInput) {
        var imgError = document.createElement("div");
        imgError.id = "imgError";
        imgError.className = "text-danger small mt-1";
        imgError.style.display = "none";
        imageInput.parentElement.appendChild(imgError);

        imageInput.addEventListener("change", function() {
            var previewContainer = document.getElementById("imagePreview");
            previewContainer.innerHTML = "";
            imgError.style.display = "none";

            var files = this.files;
            if (files.length < 3) {
                imgError.textContent = "Please select at least 3 images.";
                imgError.style.display = "block";
            } else if (files.length > 6) {
                imgError.textContent = "Maximum 6 images allowed. You selected " + files.length + ".";
                imgError.style.display = "block";
                this.value = "";
                return;
            }

            if (files) {
                Array.from(files).forEach(function(file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var img = document.createElement("img");
                        img.src = e.target.result;
                        img.className = "img-thumbnail me-2 mb-2";
                        img.style.width = "100px";
                        img.style.height = "100px";
                        img.style.objectFit = "cover";
                        img.style.borderRadius = "12px";
                        img.style.border = "1px solid rgba(16,185,129,0.2)";
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });

        var addForm = imageInput.closest("form");
        if (addForm) {
            addForm.addEventListener("submit", function(e) {
                var count = imageInput.files.length;
                if (count < 3) {
                    e.preventDefault();
                    imgError.textContent = "Please select at least 3 images before submitting.";
                    imgError.style.display = "block";
                    imageInput.scrollIntoView({ behavior: "smooth", block: "center" });
                } else if (count > 6) {
                    e.preventDefault();
                    imgError.textContent = "Maximum 6 images allowed.";
                    imgError.style.display = "block";
                }
            });
        }
    }

    // ==================== AJAX FAVORITE ====================
    var favBtn = document.getElementById("favBtn");
    if (favBtn) {
        favBtn.addEventListener("click", function() {
            var btn = this;
            var propertyId = btn.getAttribute("data-property-id");
            var isFavorited = btn.getAttribute("data-favorited") === "1";
            var favMsg = document.getElementById("favMsg");

            btn.disabled = true;

            fetch("ajax/toggle_favorite.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "property_id=" + propertyId
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.status === "success") {
                    if (data.action === "added") {
                        btn.classList.remove("btn-outline-danger");
                        btn.classList.add("btn-danger");
                        btn.querySelector("i").className = "fas fa-heart me-2";
                        btn.querySelector("span").textContent = "Saved to Favorites";
                        btn.setAttribute("data-favorited", "1");
                    } else {
                        btn.classList.remove("btn-danger");
                        btn.classList.add("btn-outline-danger");
                        btn.querySelector("i").className = "far fa-heart me-2";
                        btn.querySelector("span").textContent = "Save to Favorites";
                        btn.setAttribute("data-favorited", "0");
                    }

                    favMsg.textContent = data.message;
                    favMsg.className = "text-center mt-2 small text-success";
                    favMsg.style.display = "block";
                    setTimeout(function() { favMsg.style.display = "none"; }, 2000);
                } else {
                    favMsg.textContent = data.message;
                    favMsg.className = "text-center mt-2 small text-danger";
                    favMsg.style.display = "block";
                }
                btn.disabled = false;
            })
            .catch(function() {
                favMsg.textContent = "Something went wrong. Try again.";
                favMsg.className = "text-center mt-2 small text-danger";
                favMsg.style.display = "block";
                btn.disabled = false;
            });
        });
    }

    // ==================== THEME TOGGLE ====================
    const themeToggleBtn = document.getElementById('themeToggle');
    if (themeToggleBtn) {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        if (currentTheme === 'light') {
            themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
        } else {
            themeToggleBtn.innerHTML = '<i class="fas fa-sun text-warning"></i>';
        }

        themeToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const current = document.documentElement.getAttribute('data-bs-theme');
            const targetTheme = current === 'dark' ? 'light' : 'dark';

            // Smooth transition
            document.body.style.transition = 'background 0.5s ease, color 0.5s ease';

            document.documentElement.setAttribute('data-bs-theme', targetTheme);
            localStorage.setItem('theme', targetTheme);

            if (targetTheme === 'light') {
                this.innerHTML = '<i class="fas fa-moon animate__animated animate__flipInY"></i>';
            } else {
                this.innerHTML = '<i class="fas fa-sun text-warning animate__animated animate__flipInY"></i>';
            }
        });
    }

    // ==================== PARALLAX HERO ORBS ====================
    var heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        window.addEventListener('scroll', function() {
            var scrollY = window.scrollY;
            if (scrollY < 800) {
                heroSection.style.setProperty('--parallax-y', (scrollY * 0.3) + 'px');
            }
        });
    }

    // ==================== CARD TILT EFFECT ====================
    var propertyCards = document.querySelectorAll('.property-card');
    propertyCards.forEach(function(card) {
        card.addEventListener('mousemove', function(e) {
            var rect = card.getBoundingClientRect();
            var x = e.clientX - rect.left;
            var y = e.clientY - rect.top;
            var centerX = rect.width / 2;
            var centerY = rect.height / 2;
            var rotateX = (y - centerY) / 20;
            var rotateY = (centerX - x) / 20;

            card.style.transform = 'perspective(1000px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-8px) scale(1.01)';
        });

        card.addEventListener('mouseleave', function() {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0) scale(1)';
        });
    });
});
