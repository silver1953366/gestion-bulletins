// ============================================
// SCRIPT: script.js
// INPTIC Gabon - Main JavaScript
// ============================================

(function() {
    'use strict';

    // ---------- DOM Elements ----------
    const elements = {
        // Navigation
        header: document.querySelector('.elementor-element-3ec66a33'),
        
        // Forms
        contactForm: document.querySelector('.elementor-form'),
        
        // Cookie consent
        cookieAccept: document.getElementById('cookieadmin_accept_button'),
        cookieReject: document.getElementById('cookieadmin_reject_button'),
        cookieCustomize: document.getElementById('cookieadmin_customize_button'),
        cookieSave: document.getElementById('cookieadmin_prf_modal_button'),
        cookieModal: document.querySelector('.cookieadmin_cookie_modal'),
        
        // Scroll elements
        backToTop: null
    };

    // ---------- Utility Functions ----------
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // ---------- Counter Animation ----------
    function initCounters() {
        const counters = document.querySelectorAll('.elementor-counter-number');
        
        if (!counters.length) return;
        
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px'
        };
        
        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const toValue = parseInt(counter.getAttribute('data-to-value')) || 0;
                    const duration = parseInt(counter.getAttribute('data-duration')) || 2000;
                    const delimiter = counter.getAttribute('data-delimiter') || '';
                    
                    animateCounter(counter, 0, toValue, duration, delimiter);
                    observer.unobserve(counter);
                }
            });
        }, observerOptions);
        
        counters.forEach(counter => {
            counterObserver.observe(counter);
        });
    }
    
    function animateCounter(element, start, end, duration, delimiter) {
        const range = end - start;
        const increment = end > start ? 1 : -1;
        const stepTime = Math.abs(Math.floor(duration / range));
        
        let current = start;
        const timer = setInterval(() => {
            current += increment;
            element.textContent = delimiter ? formatNumber(current) : current;
            
            if (current === end) {
                clearInterval(timer);
            }
        }, stepTime);
    }

    // ---------- Swiper Sliders ----------
    function initSliders() {
        // Hero slider
        const heroSlider = document.querySelector('.elementor-slides-wrapper');
        if (heroSlider && typeof Swiper !== 'undefined') {
            new Swiper(heroSlider, {
                slidesPerView: 1,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: '.elementor-swiper-button-next',
                    prevEl: '.elementor-swiper-button-prev',
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                speed: 800,
            });
        }
        
        // Carousel sliders
        const carousels = document.querySelectorAll('.e-n-carousel');
        carousels.forEach(carousel => {
            if (typeof Swiper !== 'undefined') {
                new Swiper(carousel, {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: carousel.parentElement.querySelector('.elementor-swiper-button-next'),
                        prevEl: carousel.parentElement.querySelector('.elementor-swiper-button-prev'),
                    },
                    breakpoints: {
                        768: {
                            slidesPerView: 1,
                        },
                        1025: {
                            slidesPerView: 1,
                        }
                    }
                });
            }
        });
    }

    // ---------- Video Player ----------
    function initVideoPlayers() {
        const videoWidgets = document.querySelectorAll('.elementor-widget-video');
        
        videoWidgets.forEach(widget => {
            const youtubeUrl = widget.querySelector('[data-settings]');
            if (youtubeUrl) {
                try {
                    const settings = JSON.parse(youtubeUrl.getAttribute('data-settings'));
                    if (settings.youtube_url) {
                        const videoId = extractYouTubeId(settings.youtube_url);
                        if (videoId) {
                            const container = widget.querySelector('.elementor-wrapper');
                            if (container) {
                                const iframe = document.createElement('iframe');
                                iframe.src = `https://www.youtube.com/embed/${videoId}?controls=1&rel=0`;
                                iframe.width = '100%';
                                iframe.height = '100%';
                                iframe.frameBorder = '0';
                                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                                iframe.allowFullscreen = true;
                                container.appendChild(iframe);
                            }
                        }
                    }
                } catch (e) {
                    console.error('Error parsing video settings:', e);
                }
            }
        });
    }
    
    function extractYouTubeId(url) {
        const patterns = [
            /(?:youtube\.com\/watch\?v=)([^&]+)/,
            /(?:youtu\.be\/)([^?]+)/,
            /(?:youtube\.com\/embed\/)([^/?]+)/
        ];
        
        for (const pattern of patterns) {
            const match = url.match(pattern);
            if (match) return match[1];
        }
        return null;
    }

    // ---------- Sticky Header ----------
    function initStickyHeader() {
        const header = elements.header;
        if (!header) return;
        
        const stickyOffset = 100;
        let lastScroll = 0;
        
        window.addEventListener('scroll', debounce(() => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > stickyOffset) {
                header.style.transform = 'translateY(0)';
                header.style.transition = 'transform 0.3s ease';
            } else {
                header.style.transform = 'translateY(0)';
            }
            
            lastScroll = currentScroll;
        }, 10));
    }

    // ---------- Form Handling ----------
    function initFormHandler() {
        const form = elements.contactForm;
        if (!form) return;
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            // Show loading state
            submitButton.textContent = 'Envoi en cours...';
            submitButton.disabled = true;
            
            // Collect form data
            const formData = new FormData(form);
            formData.append('action', 'contact_form_submit');
            
            try {
                // Simulate API call - replace with actual endpoint
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                // Show success message
                showNotification('Message envoyé avec succès ! Nous vous contacterons rapidement.', 'success');
                form.reset();
            } catch (error) {
                showNotification('Une erreur est survenue. Veuillez réessayer plus tard.', 'error');
            } finally {
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            }
        });
        
        // Add input validation
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => validateField(input));
            input.addEventListener('input', () => {
                if (input.classList.contains('error')) {
                    validateField(input);
                }
            });
        });
    }
    
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            showFieldError(field, 'Ce champ est requis');
        } else if (field.type === 'email' && value && !isValidEmail(value)) {
            isValid = false;
            showFieldError(field, 'Veuillez entrer une adresse email valide');
        } else if (field.type === 'tel' && value && !isValidPhone(value)) {
            isValid = false;
            showFieldError(field, 'Veuillez entrer un numéro de téléphone valide');
        } else {
            clearFieldError(field);
        }
        
        return isValid;
    }
    
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function isValidPhone(phone) {
        const re = /^[\d\s\+\(\)\-]+$/;
        return re.test(phone);
    }
    
    function showFieldError(field, message) {
        field.classList.add('error');
        field.style.borderColor = '#dc3545';
        
        let errorDiv = field.parentElement.querySelector('.field-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.style.color = '#dc3545';
            errorDiv.style.fontSize = '12px';
            errorDiv.style.marginTop = '5px';
            field.parentElement.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
    }
    
    function clearFieldError(field) {
        field.classList.remove('error');
        field.style.borderColor = '';
        
        const errorDiv = field.parentElement.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 15px 20px;
            background-color: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: #fff;
            border-radius: 8px;
            z-index: 10000;
            animation: slideIn 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
    
    // Add animation styles for notifications
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    // ---------- Cookie Consent ----------
    function initCookieConsent() {
        const cookieBox = document.querySelector('.cookieadmin_law_container');
        const modal = document.querySelector('.cookieadmin_cookie_modal');
        
        // Check if user has already consented
        const hasConsented = localStorage.getItem('cookie_consent');
        
        if (hasConsented === null) {
            if (cookieBox) cookieBox.style.display = 'block';
        } else if (hasConsented === 'true') {
            if (cookieBox) cookieBox.style.display = 'none';
            enableAnalyticsCookies();
        } else {
            if (cookieBox) cookieBox.style.display = 'none';
        }
        
        // Accept all button
        if (elements.cookieAccept) {
            elements.cookieAccept.addEventListener('click', () => {
                localStorage.setItem('cookie_consent', 'true');
                if (cookieBox) cookieBox.style.display = 'none';
                if (modal) modal.style.display = 'none';
                enableAllCookies();
            });
        }
        
        // Reject all button
        if (elements.cookieReject) {
            elements.cookieReject.addEventListener('click', () => {
                localStorage.setItem('cookie_consent', 'false');
                if (cookieBox) cookieBox.style.display = 'none';
                if (modal) modal.style.display = 'none';
                disableAllCookies();
            });
        }
        
        // Customize button
        if (elements.cookieCustomize) {
            elements.cookieCustomize.addEventListener('click', () => {
                if (modal) modal.style.display = 'flex';
            });
        }
        
        // Save preferences
        if (elements.cookieSave) {
            elements.cookieSave.addEventListener('click', () => {
                localStorage.setItem('cookie_consent', 'custom');
                if (cookieBox) cookieBox.style.display = 'none';
                if (modal) modal.style.display = 'none';
                saveCookiePreferences();
            });
        }
        
        // Close modal
        const closeBtn = document.querySelector('.cookieadmin_close_pref');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                if (modal) modal.style.display = 'none';
            });
        }
    }
    
    function enableAllCookies() {
        enableAnalyticsCookies();
        enableMarketingCookies();
        enableFunctionalCookies();
    }
    
    function disableAllCookies() {
        // Disable all non-essential cookies
        console.log('Non-essential cookies disabled');
    }
    
    function enableAnalyticsCookies() {
        // Enable Google Analytics
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        }
    }
    
    function enableMarketingCookies() {
        // Enable marketing cookies
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'ad_storage': 'granted',
                'ad_user_data': 'granted',
                'ad_personalization': 'granted'
            });
        }
    }
    
    function enableFunctionalCookies() {
        // Enable functional cookies
        console.log('Functional cookies enabled');
    }
    
    function saveCookiePreferences() {
        const functionalCheckbox = document.getElementById('cookieadmin-functional');
        const analyticsCheckbox = document.getElementById('cookieadmin-analytics');
        const marketingCheckbox = document.getElementById('cookieadmin-marketing');
        
        if (analyticsCheckbox && analyticsCheckbox.checked) {
            enableAnalyticsCookies();
        }
        
        if (marketingCheckbox && marketingCheckbox.checked) {
            enableMarketingCookies();
        }
        
        if (functionalCheckbox && functionalCheckbox.checked) {
            enableFunctionalCookies();
        }
    }

    // ---------- Smooth Scroll ----------
    function initSmoothScroll() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    const headerOffset = 100;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // ---------- Lazy Loading ----------
    function initLazyLoading() {
        const lazyImages = document.querySelectorAll('.lws-optimize-lazyload');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const src = img.getAttribute('data-src');
                        const srcset = img.getAttribute('data-srcset');
                        
                        if (src) img.src = src;
                        if (srcset) img.srcset = srcset;
                        
                        img.classList.remove('lws-optimize-lazyload');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            lazyImages.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback for older browsers
            lazyImages.forEach(img => {
                const src = img.getAttribute('data-src');
                if (src) img.src = src;
            });
        }
    }

    // ---------- Back to Top Button ----------
    function initBackToTop() {
        const button = document.createElement('button');
        button.innerHTML = '↑';
        button.className = 'back-to-top';
        button.setAttribute('aria-label', 'Retour en haut');
        button.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #1863dc;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 24px;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        `;
        
        document.body.appendChild(button);
        
        button.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        window.addEventListener('scroll', debounce(() => {
            if (window.pageYOffset > 300) {
                button.style.display = 'flex';
            } else {
                button.style.display = 'none';
            }
        }, 100));
        
        // Hover effect
        button.addEventListener('mouseenter', () => {
            button.style.backgroundColor = '#0d4fb3';
            button.style.transform = 'translateY(-3px)';
        });
        
        button.addEventListener('mouseleave', () => {
            button.style.backgroundColor = '#1863dc';
            button.style.transform = 'translateY(0)';
        });
    }

    // ---------- Mobile Menu Toggle ----------
    function initMobileMenu() {
        // Check if mobile menu button exists, if not create one
        const header = elements.header;
        if (!header) return;
        
        // Create mobile menu button if it doesn't exist
        let mobileBtn = document.querySelector('.mobile-menu-toggle');
        if (!mobileBtn && window.innerWidth <= 768) {
            mobileBtn = document.createElement('button');
            mobileBtn.className = 'mobile-menu-toggle';
            mobileBtn.innerHTML = '☰';
            mobileBtn.setAttribute('aria-label', 'Menu');
            mobileBtn.style.cssText = `
                display: none;
                background: none;
                border: none;
                font-size: 28px;
                cursor: pointer;
                color: #333;
            `;
            
            const navContainer = document.querySelector('.elementor-element-498b7326');
            if (navContainer) {
                navContainer.appendChild(mobileBtn);
            }
            
            mobileBtn.addEventListener('click', () => {
                const nav = document.querySelector('.elementor-element-12a0f835');
                if (nav) {
                    nav.classList.toggle('mobile-open');
                }
            });
        }
        
        // Handle resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                const nav = document.querySelector('.elementor-element-12a0f835');
                if (nav) nav.classList.remove('mobile-open');
            }
        });
    }
    
    // Add mobile styles
    const mobileStyles = document.createElement('style');
    mobileStyles.textContent = `
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block !important;
            }
            .elementor-element-12a0f835 {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 80%;
                height: calc(100vh - 80px);
                background: #fff;
                flex-direction: column;
                padding: 30px;
                transition: left 0.3s ease;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
                z-index: 999;
            }
            .elementor-element-12a0f835.mobile-open {
                left: 0;
            }
            .elementor-element-12a0f835 .elementor-button,
            .elementor-element-12a0f835 .elementor-social-icons-wrapper {
                margin: 10px 0;
            }
        }
    `;
    document.head.appendChild(mobileStyles);

    // ---------- Initialize on DOM Load ----------
    document.addEventListener('DOMContentLoaded', () => {
        initSliders();
        initCounters();
        initVideoPlayers();
        initStickyHeader();
        initFormHandler();
        initCookieConsent();
        initSmoothScroll();
        initLazyLoading();
        initBackToTop();
        initMobileMenu();
    });

    // ---------- Window Load ----------
    window.addEventListener('load', () => {
        // Additional initialization after all resources are loaded
        document.body.classList.add('loaded');
    });

})();