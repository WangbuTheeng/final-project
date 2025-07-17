/**
 * Frontend Performance Optimization System
 * Handles lazy loading, image optimization, and performance monitoring
 */

class PerformanceOptimizer {
    constructor() {
        this.observers = new Map();
        this.metrics = {
            loadTime: 0,
            domContentLoaded: 0,
            firstContentfulPaint: 0,
            largestContentfulPaint: 0,
            cumulativeLayoutShift: 0,
            firstInputDelay: 0
        };
        
        this.init();
    }

    init() {
        this.setupPerformanceMonitoring();
        this.setupLazyLoading();
        this.setupImageOptimization();
        this.setupResourcePreloading();
        this.setupMemoryManagement();
        this.setupNetworkOptimization();
    }

    /**
     * Setup performance monitoring and Core Web Vitals tracking
     */
    setupPerformanceMonitoring() {
        // Track page load time
        window.addEventListener('load', () => {
            this.metrics.loadTime = performance.now();
            this.reportMetrics();
        });

        // Track DOM Content Loaded
        document.addEventListener('DOMContentLoaded', () => {
            this.metrics.domContentLoaded = performance.now();
        });

        // Track Core Web Vitals
        this.trackCoreWebVitals();

        // Monitor memory usage
        this.monitorMemoryUsage();

        // Track user interactions
        this.trackUserInteractions();
    }

    /**
     * Track Core Web Vitals metrics
     */
    trackCoreWebVitals() {
        // First Contentful Paint
        if ('PerformanceObserver' in window) {
            const fcpObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const fcp = entries.find(entry => entry.name === 'first-contentful-paint');
                if (fcp) {
                    this.metrics.firstContentfulPaint = fcp.startTime;
                }
            });
            fcpObserver.observe({ entryTypes: ['paint'] });

            // Largest Contentful Paint
            const lcpObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const lastEntry = entries[entries.length - 1];
                this.metrics.largestContentfulPaint = lastEntry.startTime;
            });
            lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] });

            // Cumulative Layout Shift
            const clsObserver = new PerformanceObserver((list) => {
                let clsValue = 0;
                for (const entry of list.getEntries()) {
                    if (!entry.hadRecentInput) {
                        clsValue += entry.value;
                    }
                }
                this.metrics.cumulativeLayoutShift = clsValue;
            });
            clsObserver.observe({ entryTypes: ['layout-shift'] });

            // First Input Delay
            const fidObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const firstInput = entries[0];
                if (firstInput) {
                    this.metrics.firstInputDelay = firstInput.processingStart - firstInput.startTime;
                }
            });
            fidObserver.observe({ entryTypes: ['first-input'] });
        }
    }

    /**
     * Setup lazy loading for images and content
     */
    setupLazyLoading() {
        // Lazy load images
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    // Load the actual image
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    
                    // Load srcset if available
                    if (img.dataset.srcset) {
                        img.srcset = img.dataset.srcset;
                        img.removeAttribute('data-srcset');
                    }
                    
                    // Add loaded class for animations
                    img.classList.add('loaded');
                    
                    imageObserver.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.1
        });

        // Observe all lazy images
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });

        this.observers.set('images', imageObserver);

        // Lazy load content sections
        this.setupContentLazyLoading();
    }

    /**
     * Setup content lazy loading for heavy sections
     */
    setupContentLazyLoading() {
        const contentObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    
                    // Load content based on data attributes
                    if (element.dataset.lazyLoad) {
                        this.loadLazyContent(element);
                    }
                    
                    contentObserver.unobserve(element);
                }
            });
        }, {
            rootMargin: '100px 0px',
            threshold: 0.1
        });

        // Observe lazy content sections
        document.querySelectorAll('[data-lazy-load]').forEach(element => {
            contentObserver.observe(element);
        });

        this.observers.set('content', contentObserver);
    }

    /**
     * Load lazy content via AJAX
     */
    async loadLazyContent(element) {
        const url = element.dataset.lazyLoad;
        const placeholder = element.querySelector('.lazy-placeholder');
        
        try {
            // Show loading state
            if (placeholder) {
                placeholder.innerHTML = '<div class="animate-pulse">Loading...</div>';
            }
            
            const response = await fetch(url);
            const html = await response.text();
            
            // Replace content
            element.innerHTML = html;
            element.classList.add('lazy-loaded');
            
            // Initialize any new Alpine.js components
            if (window.Alpine) {
                Alpine.initTree(element);
            }
            
        } catch (error) {
            console.error('Failed to load lazy content:', error);
            if (placeholder) {
                placeholder.innerHTML = '<div class="text-red-500">Failed to load content</div>';
            }
        }
    }

    /**
     * Setup image optimization
     */
    setupImageOptimization() {
        // Convert images to WebP if supported
        if (this.supportsWebP()) {
            document.querySelectorAll('img[data-webp]').forEach(img => {
                img.src = img.dataset.webp;
            });
        }

        // Setup responsive images
        this.setupResponsiveImages();
    }

    /**
     * Check WebP support
     */
    supportsWebP() {
        const canvas = document.createElement('canvas');
        canvas.width = 1;
        canvas.height = 1;
        return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
    }

    /**
     * Setup responsive images based on viewport
     */
    setupResponsiveImages() {
        const updateImageSources = () => {
            const viewportWidth = window.innerWidth;
            
            document.querySelectorAll('img[data-responsive]').forEach(img => {
                const sources = JSON.parse(img.dataset.responsive);
                let selectedSource = sources.default;
                
                // Find the best source for current viewport
                Object.keys(sources).forEach(breakpoint => {
                    if (breakpoint !== 'default' && viewportWidth >= parseInt(breakpoint)) {
                        selectedSource = sources[breakpoint];
                    }
                });
                
                if (img.src !== selectedSource) {
                    img.src = selectedSource;
                }
            });
        };

        // Update on resize (debounced)
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(updateImageSources, 250);
        });

        // Initial update
        updateImageSources();
    }

    /**
     * Setup resource preloading
     */
    setupResourcePreloading() {
        // Preload critical resources
        this.preloadCriticalResources();
        
        // Setup link prefetching for likely navigation
        this.setupLinkPrefetching();
    }

    /**
     * Preload critical resources
     */
    preloadCriticalResources() {
        const criticalResources = [
            { href: '/css/app.css', as: 'style' },
            { href: '/js/app.js', as: 'script' },
            { href: '/fonts/inter.woff2', as: 'font', type: 'font/woff2', crossorigin: 'anonymous' }
        ];

        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            Object.assign(link, resource);
            document.head.appendChild(link);
        });
    }

    /**
     * Setup link prefetching for likely navigation
     */
    setupLinkPrefetching() {
        const linkObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const link = entry.target;
                    this.prefetchLink(link.href);
                    linkObserver.unobserve(link);
                }
            });
        }, {
            rootMargin: '200px 0px'
        });

        // Observe navigation links
        document.querySelectorAll('a[href^="/"]').forEach(link => {
            linkObserver.observe(link);
        });
    }

    /**
     * Prefetch a link
     */
    prefetchLink(href) {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = href;
        document.head.appendChild(link);
    }

    /**
     * Setup memory management
     */
    setupMemoryManagement() {
        // Clean up observers when page is hidden
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseObservers();
            } else {
                this.resumeObservers();
            }
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            this.cleanup();
        });
    }

    /**
     * Setup network optimization
     */
    setupNetworkOptimization() {
        // Detect connection quality
        if ('connection' in navigator) {
            const connection = navigator.connection;
            
            // Adjust behavior based on connection
            if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                this.enableDataSaverMode();
            }
            
            // Listen for connection changes
            connection.addEventListener('change', () => {
                this.handleConnectionChange(connection);
            });
        }
    }

    /**
     * Enable data saver mode for slow connections
     */
    enableDataSaverMode() {
        // Disable auto-playing videos
        document.querySelectorAll('video[autoplay]').forEach(video => {
            video.removeAttribute('autoplay');
        });

        // Reduce image quality
        document.querySelectorAll('img').forEach(img => {
            if (img.dataset.lowQuality) {
                img.src = img.dataset.lowQuality;
            }
        });

        // Disable non-essential animations
        document.body.classList.add('reduced-motion');
    }

    /**
     * Handle connection changes
     */
    handleConnectionChange(connection) {
        if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
            this.enableDataSaverMode();
        } else {
            this.disableDataSaverMode();
        }
    }

    /**
     * Disable data saver mode
     */
    disableDataSaverMode() {
        document.body.classList.remove('reduced-motion');
        // Re-enable features as needed
    }

    /**
     * Monitor memory usage
     */
    monitorMemoryUsage() {
        if ('memory' in performance) {
            setInterval(() => {
                const memory = performance.memory;
                const usage = {
                    used: Math.round(memory.usedJSHeapSize / 1048576), // MB
                    total: Math.round(memory.totalJSHeapSize / 1048576), // MB
                    limit: Math.round(memory.jsHeapSizeLimit / 1048576) // MB
                };

                // Warn if memory usage is high
                if (usage.used / usage.limit > 0.8) {
                    console.warn('High memory usage detected:', usage);
                    this.triggerMemoryCleanup();
                }
            }, 30000); // Check every 30 seconds
        }
    }

    /**
     * Track user interactions for performance insights
     */
    trackUserInteractions() {
        const interactions = ['click', 'scroll', 'keydown'];
        
        interactions.forEach(event => {
            document.addEventListener(event, (e) => {
                this.recordInteraction(event, e);
            }, { passive: true });
        });
    }

    /**
     * Record user interaction for analytics
     */
    recordInteraction(type, event) {
        const interaction = {
            type,
            timestamp: performance.now(),
            target: event.target.tagName,
            className: event.target.className
        };

        // Store in session storage for analytics
        const interactions = JSON.parse(sessionStorage.getItem('user_interactions') || '[]');
        interactions.push(interaction);
        
        // Keep only last 100 interactions
        if (interactions.length > 100) {
            interactions.shift();
        }
        
        sessionStorage.setItem('user_interactions', JSON.stringify(interactions));
    }

    /**
     * Trigger memory cleanup
     */
    triggerMemoryCleanup() {
        // Clear old cached data
        if ('caches' in window) {
            caches.keys().then(names => {
                names.forEach(name => {
                    if (name.includes('old-')) {
                        caches.delete(name);
                    }
                });
            });
        }

        // Clear old session storage
        const keys = Object.keys(sessionStorage);
        keys.forEach(key => {
            if (key.startsWith('temp_') || key.startsWith('cache_')) {
                sessionStorage.removeItem(key);
            }
        });
    }

    /**
     * Pause observers to save resources
     */
    pauseObservers() {
        this.observers.forEach(observer => {
            observer.disconnect();
        });
    }

    /**
     * Resume observers
     */
    resumeObservers() {
        // Re-initialize observers
        this.setupLazyLoading();
    }

    /**
     * Report performance metrics
     */
    reportMetrics() {
        // Send metrics to analytics endpoint
        if (navigator.sendBeacon) {
            const data = JSON.stringify({
                metrics: this.metrics,
                userAgent: navigator.userAgent,
                url: window.location.href,
                timestamp: Date.now()
            });

            navigator.sendBeacon('/api/performance-metrics', data);
        }
    }

    /**
     * Get current performance metrics
     */
    getMetrics() {
        return {
            ...this.metrics,
            memoryUsage: performance.memory ? {
                used: Math.round(performance.memory.usedJSHeapSize / 1048576),
                total: Math.round(performance.memory.totalJSHeapSize / 1048576)
            } : null,
            connectionType: navigator.connection ? navigator.connection.effectiveType : null
        };
    }

    /**
     * Cleanup resources
     */
    cleanup() {
        this.observers.forEach(observer => {
            observer.disconnect();
        });
        this.observers.clear();
    }
}

// Initialize performance optimizer
const performanceOptimizer = new PerformanceOptimizer();

// Make it globally available
window.performanceOptimizer = performanceOptimizer;

export default PerformanceOptimizer;
