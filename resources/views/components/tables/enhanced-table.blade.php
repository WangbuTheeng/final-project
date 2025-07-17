@props([
    'headers' => [],
    'data' => [],
    'searchable' => true,
    'sortable' => true,
    'filterable' => false,
    'exportable' => false,
    'selectable' => false,
    'pagination' => null,
    'emptyMessage' => 'No data available',
    'loading' => false,
    'striped' => true,
    'hover' => true,
    'compact' => false,
])

@php
    $tableId = 'table_' . uniqid();
@endphp

<div class="enhanced-table-container bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
     x-data="enhancedTable('{{ $tableId }}')"
     x-init="init()">
    
    <!-- Table Header with Controls -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Title and Description -->
            <div>
                @if(isset($title))
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                @endif
                @if(isset($description))
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $description }}</p>
                @endif
            </div>
            
            <!-- Controls -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                @if($searchable)
                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            x-model="searchQuery"
                            @input.debounce.300ms="performSearch()"
                            placeholder="Search..."
                            class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm w-full sm:w-64"
                        >
                        <div x-show="searchQuery" @click="clearSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                            <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
                        </div>
                    </div>
                @endif
                
                @if($filterable)
                    <!-- Filter Dropdown -->
                    <div class="relative" x-data="{ filterOpen: false }">
                        <button
                            @click="filterOpen = !filterOpen"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <i class="fas fa-filter mr-2"></i>
                            Filters
                            <i class="fas fa-chevron-down ml-2 transition-transform" :class="{ 'rotate-180': filterOpen }"></i>
                        </button>
                        
                        <div x-show="filterOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             @click.away="filterOpen = false"
                             class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 z-50"
                             style="display: none;">
                            <div class="p-4">
                                {{ $filters ?? '' }}
                            </div>
                        </div>
                    </div>
                @endif
                
                @if($exportable)
                    <!-- Export Button -->
                    <button
                        @click="exportData()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200"
                    >
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                @endif
            </div>
        </div>
        
        @if($selectable)
            <!-- Bulk Actions -->
            <div x-show="selectedItems.length > 0" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-blue-700 dark:text-blue-300">
                        <span x-text="selectedItems.length"></span> item(s) selected
                    </span>
                    <div class="flex space-x-2">
                        {{ $bulkActions ?? '' }}
                        <button @click="clearSelection()" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            Clear selection
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Loading Overlay -->
    <div x-show="loading" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm z-40 flex items-center justify-center"
         style="display: none;">
        <div class="flex items-center space-x-3">
            <div class="animate-spin rounded-full h-8 w-8 border-2 border-blue-500 border-t-transparent"></div>
            <span class="text-gray-700 dark:text-gray-300 font-medium">Loading...</span>
        </div>
    </div>
    
    <!-- Table Container -->
    <div class="overflow-x-auto">
        <table class="w-full {{ $compact ? 'table-compact' : '' }}">
            <!-- Table Header -->
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    @if($selectable)
                        <th class="px-6 py-3 text-left">
                            <input
                                type="checkbox"
                                x-model="selectAll"
                                @change="toggleSelectAll()"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                        </th>
                    @endif
                    
                    @foreach($headers as $header)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            @if($sortable && isset($header['sortable']) && $header['sortable'])
                                <button
                                    @click="sort('{{ $header['key'] }}')"
                                    class="group inline-flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-100"
                                >
                                    <span>{{ $header['label'] }}</span>
                                    <span class="flex flex-col">
                                        <i class="fas fa-caret-up text-xs" 
                                           :class="{ 'text-blue-500': sortField === '{{ $header['key'] }}' && sortDirection === 'asc' }"></i>
                                        <i class="fas fa-caret-down text-xs -mt-1" 
                                           :class="{ 'text-blue-500': sortField === '{{ $header['key'] }}' && sortDirection === 'desc' }"></i>
                                    </span>
                                </button>
                            @else
                                {{ $header['label'] }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            
            <!-- Table Body -->
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <template x-if="filteredData.length === 0 && !loading">
                    <tr>
                        <td :colspan="columnCount" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">{{ $emptyMessage }}</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Try adjusting your search or filter criteria</p>
                            </div>
                        </td>
                    </tr>
                </template>
                
                <template x-for="(item, index) in paginatedData" :key="index">
                    <tr class="{{ $hover ? 'hover:bg-gray-50 dark:hover:bg-gray-700' : '' }} {{ $striped ? 'even:bg-gray-50 dark:even:bg-gray-700/50' : '' }} transition-colors duration-150 stagger-item"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedItems.includes(item.id) }"
                        x-data="{ item: item }">

                        @if($selectable)
                            <td class="px-6 py-4">
                                <input
                                    type="checkbox"
                                    :value="item.id"
                                    x-model="selectedItems"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                            </td>
                        @endif

                        {{ $slot }}
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    
    <!-- Table Footer with Pagination -->
    @if($pagination)
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <!-- Results Info -->
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing <span x-text="startIndex"></span> to <span x-text="endIndex"></span> of <span x-text="totalItems"></span> results
                </div>
                
                <!-- Pagination Controls -->
                <div class="flex items-center space-x-2">
                    <button
                        @click="previousPage()"
                        :disabled="currentPage === 1"
                        class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-600"
                    >
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <template x-for="page in visiblePages" :key="page">
                        <button
                            @click="goToPage(page)"
                            :class="{ 'bg-blue-500 text-white': page === currentPage, 'hover:bg-gray-100 dark:hover:bg-gray-600': page !== currentPage }"
                            class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md"
                            x-text="page"
                        ></button>
                    </template>
                    
                    <button
                        @click="nextPage()"
                        :disabled="currentPage === totalPages"
                        class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-600"
                    >
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function enhancedTable(tableId) {
    return {
        tableId: tableId,
        searchQuery: '',
        sortField: '',
        sortDirection: 'asc',
        selectedItems: [],
        selectAll: false,
        currentPage: 1,
        itemsPerPage: 10,
        loading: false,
        data: @json($data),
        filteredData: [],
        
        init() {
            this.filteredData = this.data;
            this.updatePagination();
        },
        
        get columnCount() {
            return {{ count($headers) + ($selectable ? 1 : 0) }};
        },
        
        get paginatedData() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredData.slice(start, end);
        },
        
        get totalItems() {
            return this.filteredData.length;
        },
        
        get totalPages() {
            return Math.ceil(this.totalItems / this.itemsPerPage);
        },
        
        get startIndex() {
            return this.totalItems === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
        },
        
        get endIndex() {
            return Math.min(this.currentPage * this.itemsPerPage, this.totalItems);
        },
        
        get visiblePages() {
            const pages = [];
            const start = Math.max(1, this.currentPage - 2);
            const end = Math.min(this.totalPages, this.currentPage + 2);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },
        
        performSearch() {
            if (!this.searchQuery) {
                this.filteredData = this.data;
            } else {
                this.filteredData = this.data.filter(item => {
                    return Object.values(item).some(value => 
                        String(value).toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                });
            }
            this.currentPage = 1;
            this.updatePagination();
        },
        
        clearSearch() {
            this.searchQuery = '';
            this.performSearch();
        },
        
        sort(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            
            this.filteredData.sort((a, b) => {
                let aVal = a[field];
                let bVal = b[field];
                
                if (typeof aVal === 'string') {
                    aVal = aVal.toLowerCase();
                    bVal = bVal.toLowerCase();
                }
                
                if (this.sortDirection === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });
        },
        
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedItems = this.paginatedData.map(item => item.id);
            } else {
                this.selectedItems = [];
            }
        },
        
        clearSelection() {
            this.selectedItems = [];
            this.selectAll = false;
        },
        
        goToPage(page) {
            this.currentPage = page;
        },
        
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        
        updatePagination() {
            // Update select all state
            this.selectAll = this.selectedItems.length === this.paginatedData.length && this.paginatedData.length > 0;
        },
        
        exportData() {
            // Export functionality
            const csvContent = this.convertToCSV(this.filteredData);
            this.downloadCSV(csvContent, 'table-export.csv');
        },
        
        convertToCSV(data) {
            if (data.length === 0) return '';
            
            const headers = Object.keys(data[0]);
            const csvHeaders = headers.join(',');
            const csvRows = data.map(row => 
                headers.map(header => `"${row[header] || ''}"`).join(',')
            );
            
            return [csvHeaders, ...csvRows].join('\n');
        },
        
        downloadCSV(content, filename) {
            const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
}
</script>

<style>
.table-compact td,
.table-compact th {
    padding: 0.5rem 1rem;
}

.enhanced-table-container {
    position: relative;
}
</style>
