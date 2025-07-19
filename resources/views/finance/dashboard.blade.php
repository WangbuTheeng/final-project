@extends('layouts.dashboard')

@section('title', 'Finance Dashboard')

@push('styles')
<style>
/* Modern Finance Dashboard Styles */
.finance-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.finance-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.finance-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.stat-card {
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    border: none;
    color: white;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.stat-card.revenue {
    --gradient-start: #4facfe;
    --gradient-end: #00f2fe;
}

.stat-card.expenses {
    --gradient-start: #fa709a;
    --gradient-end: #fee140;
}

.stat-card.outstanding {
    --gradient-start: #f97316;
    --gradient-end: #ea580c;
    color: white;
}

.stat-card.profit {
    --gradient-start: #10b981;
    --gradient-end: #059669;
    color: white;
}

/* Improve text visibility in stat cards */
.stat-card h3, .stat-card .text-3xl {
    font-weight: 700;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
    color: white !important;
}

.stat-card p, .stat-card span {
    color: white !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
}

.stat-card .opacity-80 {
    opacity: 0.95 !important;
    color: white !important;
}

/* Specific styling for currency text */
.stat-card .text-3xl.font-bold {
    text-shadow: 0 3px 10px rgba(0, 0, 0, 0.6);
    font-weight: 800;
    letter-spacing: -0.5px;
}

/* Add background overlay for better text contrast */
.stat-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.1);
    pointer-events: none;
    z-index: 1;
}

.stat-card > * {
    position: relative;
    z-index: 2;
}

/* Enhanced currency text styling */
.currency-text {
    background: rgba(255, 255, 255, 0.15);
    padding: 8px 12px;
    border-radius: 8px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: inline-block;
    margin-top: 4px;
}

/* Salary info styling */
.salary-info {
    background: rgba(255, 255, 255, 0.1);
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
}

.chart-container {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.chart-container h3 {
    color: #1f2937 !important;
    font-weight: 700;
}

.chart-container p, .chart-container span {
    color: #374151 !important;
}

.metric-badge {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.trend-up {
    color: #10b981;
}

.trend-down {
    color: #ef4444;
}

.quick-action-btn {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border: none;
    color: white !important;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-height: 48px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
    color: white !important;
    text-decoration: none !important;
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
}

.quick-action-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

.quick-action-btn i {
    font-size: 1rem;
}

/* Individual button colors */
.quick-action-btn.btn-invoice {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.quick-action-btn.btn-invoice:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
}

.quick-action-btn.btn-payment {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.quick-action-btn.btn-payment:hover {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);
}

.quick-action-btn.btn-fees {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.quick-action-btn.btn-fees:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    box-shadow: 0 10px 25px rgba(139, 92, 246, 0.4);
}

.quick-action-btn.btn-reports {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.quick-action-btn.btn-reports:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
}

.transaction-item {
    background: white;
    border-radius: 15px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
}

.transaction-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-paid {
    background: #d1fae5;
    color: #065f46;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-overdue {
    background: #fee2e2;
    color: #991b1b;
}

.glass-effect {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

.animate-delay-1 { animation-delay: 0.1s; }
.animate-delay-2 { animation-delay: 0.2s; }
.animate-delay-3 { animation-delay: 0.3s; }
.animate-delay-4 { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<!-- Modern Finance Dashboard -->
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header Section -->
    <div class="finance-gradient">
        <div class="container mx-auto px-6 py-12">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="animate-fade-in-up">
                    <h1 class="text-4xl lg:text-5xl font-bold text-white mb-4">
                        Finance Dashboard
                    </h1>
                    <p class="text-blue-100 text-lg lg:text-xl max-w-2xl">
                        Comprehensive financial overview with real-time analytics and insights
                    </p>
                    <div class="flex items-center mt-6 space-x-4">
                        <div class="metric-badge animate-delay-1">
                            <i class="fas fa-chart-line"></i>
                            <span>Live Data</span>
                        </div>
                        <div class="metric-badge animate-delay-2">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure</span>
                        </div>
                    </div>
                </div>
                <div class="mt-8 lg:mt-0 animate-fade-in-up animate-delay-3">
                    <div class="glass-effect rounded-2xl p-6 text-center">
                        <div class="text-3xl font-bold text-white mb-2">
                            NRs {{ number_format($totalRevenue, 2) }}
                        </div>
                        <div class="text-blue-100 text-sm">Total Revenue</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-12 -mt-8 relative z-10">

        <!-- Modern Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <!-- Total Revenue Card -->
            <div class="stat-card revenue rounded-2xl p-8 animate-fade-in-up">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-sm opacity-80">Total Revenue</div>
                        <div class="text-3xl font-bold currency-text">NRs {{ number_format($totalRevenue, 0) }}</div>
                    </div>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-arrow-up trend-up mr-2"></i>
                    <span class="opacity-80">+12.5% from last month</span>
                </div>
            </div>

            <!-- Outstanding Amount Card -->
            <div class="stat-card outstanding rounded-2xl p-8 animate-fade-in-up animate-delay-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-sm opacity-80">Outstanding</div>
                        <div class="text-3xl font-bold currency-text">NRs {{ number_format($outstandingAmount, 0) }}</div>
                    </div>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-clock mr-2"></i>
                    <span class="opacity-80">{{ $totalInvoices }} pending invoices</span>
                </div>
            </div>

            <!-- Total Students Card -->
            <div class="stat-card expenses rounded-2xl p-8 animate-fade-in-up animate-delay-2">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-sm opacity-80">Total Students</div>
                        <div class="text-3xl font-bold">{{ number_format($totalStudents) }}</div>
                    </div>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-user-plus mr-2"></i>
                    <span class="opacity-80">Active enrollments</span>
                </div>
            </div>

            <!-- Teachers Salary Card -->
            <div class="stat-card profit rounded-2xl p-8 animate-fade-in-up animate-delay-3">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                        <i class="fas fa-chalkboard-teacher text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-sm opacity-80">Teachers</div>
                        <div class="text-3xl font-bold">{{ number_format($totalTeachers) }}</div>
                    </div>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-money-check-alt mr-2"></i>
                    <span class="opacity-80 salary-info">NRs {{ number_format($salariesPaidThisMonth, 0) }} paid</span>
                </div>
            </div>
        </div>
        <!-- Charts and Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Revenue Chart -->
            <div class="lg:col-span-2 chart-container animate-fade-in-up">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Revenue Trends</h3>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Monthly Revenue</span>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-6 animate-fade-in-up animate-delay-1">
                <div class="chart-container">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h3>
                    <div class="space-y-4">
                        @can('create-invoices')
                        <a href="{{ route('finance.invoices.create') }}" class="quick-action-btn btn-invoice w-full justify-center">
                            <i class="fas fa-file-invoice"></i>
                            <span>Create Invoice</span>
                        </a>
                        @endcan

                        @can('create-payments')
                        <a href="{{ route('finance.payments.create') }}" class="quick-action-btn btn-payment w-full justify-center">
                            <i class="fas fa-credit-card"></i>
                            <span>Record Payment</span>
                        </a>
                        @endcan

                        @can('manage-fees')
                        <a href="{{ route('finance.fees.create') }}" class="quick-action-btn btn-fees w-full justify-center">
                            <i class="fas fa-tags"></i>
                            <span>Manage Fees</span>
                        </a>
                        @endcan

                        @can('view-financial-reports')
                        <a href="{{ route('finance.reports.index') }}" class="quick-action-btn btn-reports w-full justify-center">
                            <i class="fas fa-chart-bar"></i>
                            <span>View Reports</span>
                        </a>
                        @endcan
                    </div>
                </div>

                <!-- Monthly Summary -->
                <div class="chart-container">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">This Month</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">New Invoices</span>
                            <span class="font-semibold text-blue-600">{{ $totalInvoices }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Payments Received</span>
                            <span class="font-semibold text-green-600">NRs {{ number_format($totalRevenue, 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Outstanding</span>
                            <span class="font-semibold text-orange-600">NRs {{ number_format($outstandingAmount, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Recent Payments -->
            <div class="chart-container animate-fade-in-up">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Recent Payments</h3>
                    <a href="{{ route('finance.payments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All
                    </a>
                </div>
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($recentPayments as $payment)
                    <div class="transaction-item">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-arrow-down text-white"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $payment->student->user->name ?? 'Unknown Student' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $payment->payment_method }} • {{ $payment->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-green-600">
                                    +NRs {{ number_format($payment->amount, 2) }}
                                </div>
                                <div class="status-badge status-paid">
                                    {{ ucfirst($payment->status) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-receipt text-4xl mb-4 opacity-50"></i>
                        <p>No recent payments found</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Overdue Invoices -->
            <div class="chart-container animate-fade-in-up animate-delay-1">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Overdue Invoices</h3>
                    <a href="{{ route('finance.invoices.index') }}?status=overdue" class="text-red-600 hover:text-red-800 text-sm font-medium">
                        View All
                    </a>
                </div>
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($overdueInvoices as $invoice)
                    <div class="transaction-item border-l-red-500">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-exclamation text-white"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $invoice->student->user->name ?? 'Unknown Student' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Due: {{ $invoice->due_date->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-red-600">
                                    NRs {{ number_format($invoice->balance, 2) }}
                                </div>
                                <div class="status-badge status-overdue">
                                    Overdue
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-check-circle text-4xl mb-4 opacity-50 text-green-500"></i>
                        <p>No overdue invoices</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="text-center py-12 animate-fade-in-up animate-delay-4">
            <div class="inline-flex items-center space-x-2 text-gray-500">
                <i class="fas fa-shield-alt"></i>
                <span>Secure Financial Management System</span>
            </div>
            <p class="text-sm text-gray-400 mt-2">
                Last updated: {{ now()->format('M d, Y \a\t g:i A') }}
            </p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($monthlyRevenue as $revenue)
                        '{{ date("M Y", mktime(0, 0, 0, $revenue->month, 1, $revenue->year)) }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Monthly Revenue',
                    data: [
                        @foreach($monthlyRevenue as $revenue)
                            {{ $revenue->total }},
                        @endforeach
                    ],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: NRs ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#6b7280',
                            callback: function(value) {
                                return 'NRs ' + value.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // Add smooth scroll behavior for quick actions
    document.querySelectorAll('.quick-action-btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.02)';
        });

        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-2px) scale(1)';
        });
    });

    // Add loading animation for transaction items
    const transactionItems = document.querySelectorAll('.transaction-item');
    transactionItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
        item.classList.add('animate-fade-in-up');
    });
});
</script>
@endpush
