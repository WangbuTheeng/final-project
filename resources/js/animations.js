/**
 * Modern Animation System for College Management System
 * Provides smooth micro-interactions and enhanced user experience
 */

// Animation utilities
const AnimationUtils = {
    // Easing functions
    easing: {
        easeInOut: 'cubic-bezier(0.4, 0, 0.2, 1)',
        easeOut: 'cubic-bezier(0, 0, 0.2, 1)',
        easeIn: 'cubic-bezier(0.4, 0, 1, 1)',
        bounce: 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
        elastic: 'cubic-bezier(0.175, 0.885, 0.32, 1.275)'
    },

    // Duration constants
    duration: {
        fast: 150,
        normal: 200,
        slow: 300,
        slower: 500
    },

    // Create ripple effect
    createRipple(element, event) {
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        const ripple = document.createElement('span');
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
            z-index: 1000;
        `;

        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    },

    // Animate element entrance
    animateIn(element, animation = 'fadeInUp', delay = 0) {
        element.style.opacity = '0';
        element.style.transform = this.getInitialTransform(animation);
        
        setTimeout(() => {
            element.style.transition = `all ${this.duration.normal}ms ${this.easing.easeOut}`;
            element.style.opacity = '1';
            element.style.transform = 'translate3d(0, 0, 0) scale(1)';
        }, delay);
    },

    // Get initial transform for animation
    getInitialTransform(animation) {
        const transforms = {
            fadeInUp: 'translate3d(0, 20px, 0)',
            fadeInDown: 'translate3d(0, -20px, 0)',
            fadeInLeft: 'translate3d(-20px, 0, 0)',
            fadeInRight: 'translate3d(20px, 0, 0)',
            zoomIn: 'scale(0.9)',
            zoomOut: 'scale(1.1)'
        };
        return transforms[animation] || 'translate3d(0, 20px, 0)';
    },

    // Stagger animation for multiple elements
    staggerIn(elements, animation = 'fadeInUp', staggerDelay = 100) {
        elements.forEach((element, index) => {
            this.animateIn(element, animation, index * staggerDelay);
        });
    },

    // Smooth scroll to element
    scrollTo(element, offset = 0) {
        const elementPosition = element.offsetTop - offset;
        window.scrollTo({
            top: elementPosition,
            behavior: 'smooth'
        });
    },

    // Pulse animation
    pulse(element, intensity = 1.05) {
        element.style.transition = `transform ${this.duration.fast}ms ${this.easing.easeOut}`;
        element.style.transform = `scale(${intensity})`;
        
        setTimeout(() => {
            element.style.transform = 'scale(1)';
        }, this.duration.fast);
    },

    // Shake animation for errors
    shake(element) {
        element.style.animation = 'shake 0.5s ease-in-out';
        setTimeout(() => {
            element.style.animation = '';
        }, 500);
    }
};

// Page transition animations
const PageTransitions = {
    init() {
        this.setupPageTransitions();
        this.setupFormTransitions();
        this.setupCardAnimations();
    },

    setupPageTransitions() {
        // Animate page content on load
        document.addEventListener('DOMContentLoaded', () => {
            const mainContent = document.querySelector('main, .main-content, [role="main"]');
            if (mainContent) {
                AnimationUtils.animateIn(mainContent, 'fadeInUp', 100);
            }

            // Animate cards with stagger
            const cards = document.querySelectorAll('.card, [class*="bg-white"], [class*="shadow"]');
            if (cards.length > 0) {
                AnimationUtils.staggerIn(Array.from(cards), 'fadeInUp', 50);
            }
        });
    },

    setupFormTransitions() {
        // Animate form sections
        const formSections = document.querySelectorAll('.form-section, .space-y-6 > div');
        if (formSections.length > 0) {
            AnimationUtils.staggerIn(Array.from(formSections), 'fadeInUp', 100);
        }

        // Form validation animations
        document.addEventListener('invalid', (e) => {
            AnimationUtils.shake(e.target);
        }, true);
    },

    setupCardAnimations() {
        // Hover animations for cards
        const cards = document.querySelectorAll('.group, [class*="hover:"]');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transition = `transform ${AnimationUtils.duration.normal}ms ${AnimationUtils.easing.easeOut}`;
            });
        });
    }
};

// Interactive feedback system
const InteractiveFeedback = {
    init() {
        this.setupButtonFeedback();
        this.setupFormFeedback();
        this.setupNavigationFeedback();
    },

    setupButtonFeedback() {
        // Add ripple effect to buttons
        document.addEventListener('click', (e) => {
            const button = e.target.closest('button, .btn, [role="button"]');
            if (button && !button.disabled) {
                AnimationUtils.createRipple(button, e);
            }
        });

        // Pulse effect on focus
        document.addEventListener('focusin', (e) => {
            const focusable = e.target.closest('button, input, select, textarea, a');
            if (focusable) {
                focusable.style.transition = `box-shadow ${AnimationUtils.duration.fast}ms ${AnimationUtils.easing.easeOut}`;
            }
        });
    },

    setupFormFeedback() {
        // Success animations for form inputs
        document.addEventListener('input', (e) => {
            const input = e.target;
            if (input.checkValidity() && input.value.length > 0) {
                this.showSuccessState(input);
            }
        });

        // Error animations
        document.addEventListener('invalid', (e) => {
            this.showErrorState(e.target);
        }, true);
    },

    setupNavigationFeedback() {
        // Smooth navigation transitions
        const navLinks = document.querySelectorAll('nav a, .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                // Add loading state
                const icon = link.querySelector('i');
                if (icon) {
                    icon.style.animation = 'spin 1s linear infinite';
                    setTimeout(() => {
                        icon.style.animation = '';
                    }, 1000);
                }
            });
        });
    },

    showSuccessState(element) {
        const container = element.closest('.form-group, .relative');
        if (container) {
            const successIcon = container.querySelector('.fa-check-circle');
            if (successIcon) {
                successIcon.style.animation = 'bounceIn 0.5s ease-out';
                setTimeout(() => {
                    successIcon.style.animation = '';
                }, 500);
            }
        }
    },

    showErrorState(element) {
        AnimationUtils.shake(element);
        const container = element.closest('.form-group, .relative');
        if (container) {
            container.style.animation = 'shake 0.5s ease-in-out';
            setTimeout(() => {
                container.style.animation = '';
            }, 500);
        }
    }
};

// Loading animations
const LoadingAnimations = {
    show(element, type = 'spinner') {
        const loader = this.createLoader(type);
        element.style.position = 'relative';
        element.appendChild(loader);
        return loader;
    },

    hide(loader) {
        if (loader && loader.parentNode) {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.remove();
            }, AnimationUtils.duration.normal);
        }
    },

    createLoader(type) {
        const loader = document.createElement('div');
        loader.className = 'loading-overlay';
        loader.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: opacity ${AnimationUtils.duration.normal}ms;
        `;

        const spinner = document.createElement('div');
        if (type === 'spinner') {
            spinner.className = 'animate-spin rounded-full h-8 w-8 border-2 border-blue-500 border-t-transparent';
        } else if (type === 'dots') {
            spinner.innerHTML = `
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            `;
        }

        loader.appendChild(spinner);
        return loader;
    }
};

// Initialize all animation systems
document.addEventListener('DOMContentLoaded', () => {
    PageTransitions.init();
    InteractiveFeedback.init();
});

// Export for use in other modules
window.AnimationUtils = AnimationUtils;
window.PageTransitions = PageTransitions;
window.InteractiveFeedback = InteractiveFeedback;
window.LoadingAnimations = LoadingAnimations;
