{{-- Responsive Utilities Component --}}

{{-- Responsive Card Component --}}
@php
    $cardClasses = 'bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 responsive-card transition-all duration-200 hover:shadow-md';
    if (isset($compact) && $compact) {
        $cardClasses .= ' mobile-compact';
    }
@endphp

{{-- Responsive Grid Component --}}
@if(isset($gridType))
    @php
        $gridClasses = 'grid gap-4 sm:gap-6 responsive-grid';
        switch($gridType) {
            case 'stats':
                $gridClasses .= ' grid-cols-1 sm:grid-cols-2 lg:grid-cols-4';
                break;
            case 'content':
                $gridClasses .= ' grid-cols-1 lg:grid-cols-2';
                break;
            case 'actions':
                $gridClasses .= ' grid-cols-1 md:grid-cols-2 lg:grid-cols-4';
                break;
            default:
                $gridClasses .= ' grid-cols-1 md:grid-cols-2 lg:grid-cols-3';
        }
    @endphp
@endif

{{-- Responsive Table Wrapper --}}
@if(isset($tableWrapper) && $tableWrapper)
<div class="overflow-x-auto responsive-table">
    <div class="min-w-full">
        {{ $slot ?? '' }}
    </div>
</div>
@endif

{{-- Responsive Text Utilities --}}
<style>
/* Responsive text utilities */
.responsive-text-xs { @apply text-xs sm:text-sm; }
.responsive-text-sm { @apply text-sm sm:text-base; }
.responsive-text-base { @apply text-sm sm:text-base lg:text-lg; }
.responsive-text-lg { @apply text-base sm:text-lg lg:text-xl; }
.responsive-text-xl { @apply text-lg sm:text-xl lg:text-2xl; }

/* Responsive spacing utilities */
.responsive-p-sm { @apply p-2 sm:p-3 lg:p-4; }
.responsive-p-md { @apply p-3 sm:p-4 lg:p-6; }
.responsive-p-lg { @apply p-4 sm:p-6 lg:p-8; }

.responsive-m-sm { @apply m-2 sm:m-3 lg:m-4; }
.responsive-m-md { @apply m-3 sm:m-4 lg:m-6; }
.responsive-m-lg { @apply m-4 sm:m-6 lg:m-8; }

/* Responsive gap utilities */
.responsive-gap-sm { @apply gap-2 sm:gap-3 lg:gap-4; }
.responsive-gap-md { @apply gap-3 sm:gap-4 lg:gap-6; }
.responsive-gap-lg { @apply gap-4 sm:gap-6 lg:gap-8; }

/* Mobile-first button styles */
.responsive-btn {
    @apply px-3 py-2 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium rounded-lg transition-all duration-200;
}

.responsive-btn-sm {
    @apply px-2 py-1 sm:px-3 sm:py-2 text-xs font-medium rounded transition-all duration-200;
}

/* Responsive icon sizes */
.responsive-icon-sm { @apply text-sm sm:text-base; }
.responsive-icon-md { @apply text-base sm:text-lg; }
.responsive-icon-lg { @apply text-lg sm:text-xl lg:text-2xl; }

/* Mobile navigation helpers */
.mobile-nav-item {
    @apply block px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors duration-200;
}

/* Responsive form elements */
.responsive-input {
    @apply block w-full px-3 py-2 sm:px-4 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200;
}

.responsive-select {
    @apply block w-full px-3 py-2 sm:px-4 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200;
}

/* Responsive table cells */
.responsive-td {
    @apply px-3 sm:px-6 py-3 sm:py-4 text-sm;
}

.responsive-th {
    @apply px-3 sm:px-6 py-2 sm:py-3 text-xs font-medium text-gray-500 uppercase tracking-wider;
}

/* Hide/show utilities for different breakpoints */
.mobile-only { @apply block sm:hidden; }
.tablet-only { @apply hidden sm:block lg:hidden; }
.desktop-only { @apply hidden lg:block; }
.mobile-hidden { @apply hidden sm:block; }
.tablet-hidden { @apply block sm:hidden lg:block; }
.desktop-hidden { @apply block lg:hidden; }

/* Responsive flexbox utilities */
.responsive-flex {
    @apply flex flex-col sm:flex-row items-start sm:items-center;
}

.responsive-flex-reverse {
    @apply flex flex-col-reverse sm:flex-row items-start sm:items-center;
}

/* Responsive grid auto-fit */
.responsive-grid-auto {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

@media (min-width: 640px) {
    .responsive-grid-auto {
        gap: 1.5rem;
    }
}

@media (min-width: 1024px) {
    .responsive-grid-auto {
        gap: 2rem;
    }
}

/* Responsive card layouts */
.responsive-card-grid {
    @apply grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6;
}

.responsive-card-list {
    @apply space-y-4 sm:space-y-6;
}

/* Responsive sidebar adjustments */
@media (max-width: 1023px) {
    .sidebar-responsive {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
    }
    
    .sidebar-responsive.open {
        transform: translateX(0);
    }
}

/* Responsive content margins */
.responsive-content {
    @apply px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8;
}

/* Responsive modal sizing */
.responsive-modal {
    @apply w-full max-w-sm sm:max-w-md lg:max-w-lg xl:max-w-xl mx-4 sm:mx-auto;
}

/* Responsive dropdown positioning */
.responsive-dropdown {
    @apply absolute right-0 mt-2 w-48 sm:w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50;
}

/* Touch-friendly interactive elements */
@media (max-width: 1023px) {
    .touch-friendly {
        min-height: 44px;
        min-width: 44px;
    }
    
    .touch-friendly-sm {
        min-height: 36px;
        min-width: 36px;
    }
}

/* Responsive typography scale */
.responsive-heading-1 { @apply text-2xl sm:text-3xl lg:text-4xl font-bold; }
.responsive-heading-2 { @apply text-xl sm:text-2xl lg:text-3xl font-bold; }
.responsive-heading-3 { @apply text-lg sm:text-xl lg:text-2xl font-semibold; }
.responsive-heading-4 { @apply text-base sm:text-lg lg:text-xl font-semibold; }

/* Responsive container widths */
.responsive-container {
    @apply w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
}

.responsive-container-sm {
    @apply w-full max-w-3xl mx-auto px-4 sm:px-6 lg:px-8;
}

/* Responsive image handling */
.responsive-img {
    @apply w-full h-auto object-cover rounded-lg;
}

.responsive-avatar {
    @apply w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 rounded-full object-cover;
}

/* Responsive status indicators */
.responsive-badge {
    @apply inline-flex items-center px-2 py-1 sm:px-2.5 sm:py-0.5 rounded-full text-xs font-medium;
}

/* Print-friendly responsive styles */
@media print {
    .responsive-print-hidden {
        display: none !important;
    }
    
    .responsive-print-full-width {
        width: 100% !important;
        max-width: none !important;
    }
}
</style>

{{-- JavaScript for responsive behavior --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add responsive classes based on screen size
    function updateResponsiveClasses() {
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
        const isDesktop = window.innerWidth >= 1024;
        
        document.body.classList.toggle('is-mobile', isMobile);
        document.body.classList.toggle('is-tablet', isTablet);
        document.body.classList.toggle('is-desktop', isDesktop);
        
        // Update touch-friendly elements
        const touchElements = document.querySelectorAll('.touch-friendly, .touch-friendly-sm');
        touchElements.forEach(el => {
            if (isMobile || isTablet) {
                el.style.minHeight = el.classList.contains('touch-friendly-sm') ? '36px' : '44px';
                el.style.minWidth = el.classList.contains('touch-friendly-sm') ? '36px' : '44px';
            }
        });
    }
    
    // Initial call and resize listener
    updateResponsiveClasses();
    window.addEventListener('resize', updateResponsiveClasses);
    
    // Handle responsive table scrolling
    const responsiveTables = document.querySelectorAll('.responsive-table');
    responsiveTables.forEach(table => {
        if (table.scrollWidth > table.clientWidth) {
            table.classList.add('has-scroll');
        }
    });
});
</script>
