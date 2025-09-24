<x-filament-panels::page>
    @php
    try {
    $dashboardStats = $this->getDashboardStats();
    $paymentStats = $this->getPaymentStats();
    $recentPayments = $this->getRecentPayments();
    } catch (\Exception $e) {
    // Fallback data jika ada error
    $dashboardStats = [
    'total_customers' => \App\Models\Customer::count(),
    'paid_this_month' => 0,
    'unpaid_this_month' => 0,
    'total_payment_this_month' => 0,
    'outstanding_invoices' => 0,
    'outstanding_amount' => 0,
    'month_name' => \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM YYYY'),
    ];
    $paymentStats = [
    'total_paid' => 0,
    'total_count' => 0,
    'this_month' => 0,
    'this_month_count' => 0,
    'this_month_name' => \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM'),
    'pending_count' => 0,
    'pending_amount' => 0,
    ];
    $recentPayments = collect([]);
    }
    @endphp

    <div class="space-y-8">

        <!-- Welcome Header with Gradient -->
        <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-2xl shadow-2xl p-8 text-white relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5" />
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)" />
                </svg>
            </div>

            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0a2 2 0 01-2 2H10a2 2 0 01-2-2v0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Dashboard Ranet Provider</h1>
                            <p class="text-blue-100 text-lg">{{ $dashboardStats['month_name'] }}</p>
                        </div>
                    </div>
                    <p class="text-blue-200 text-sm">Sistem Manajemen Internet Service Provider</p>
                </div>
                <div class="text-right">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                        <div class="text-4xl font-bold mb-1">{{ number_format($dashboardStats['total_customers']) }}</div>
                        <div class="text-blue-100 text-sm font-medium">Total Pelanggan</div>
                        <div class="text-xs text-blue-200 mt-1">Terdaftar di sistem</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Stats cards will be rendered by Filament widgets -->
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart widgets will be rendered here -->
        </div>

        <!-- Additional Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Sudah Bayar Bulan Ini -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900">
                            {{ number_format($dashboardStats['paid_this_month']) }}
                        </div>
                        <div class="text-sm text-gray-600">
                            Sudah Bayar Bulan Ini
                        </div>
                    </div>
                </div>
            </div>

            <!-- Belum Bayar Bulan Ini -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900">
                            {{ number_format($dashboardStats['unpaid_this_month']) }}
                        </div>
                        <div class="text-sm text-gray-600">
                            Belum Bayar Bulan Ini
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Pembayaran Bulan Ini -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900">
                            Rp {{ number_format($dashboardStats['total_payment_this_month'], 0, ',', '.') }}
                        </div>
                        <div class="text-sm text-gray-600">
                            Total Pembayaran Bulan Ini
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Tertunggak -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900">
                            {{ number_format($dashboardStats['outstanding_invoices']) }}
                        </div>
                        <div class="text-sm text-gray-600">
                            Invoice Tertunggak
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Recent Payments Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    💳 Pembayaran Terbaru
                </h2>
                <span class="text-sm text-gray-500">5 transaksi terakhir</span>
            </div>

            @if($recentPayments->count() > 0)
            <div class="space-y-4">
                @foreach($recentPayments as $payment)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            @php
                            $customerName = 'N/A';
                            if ($payment->service && $payment->service->customer) {
                            $customerName = $payment->service->customer->name;
                            } elseif ($payment->invoice && $payment->invoice->customer) {
                            $customerName = $payment->invoice->customer->name;
                            }
                            @endphp
                            <div class="font-medium text-gray-900">{{ $customerName }}</div>
                            <div class="text-sm text-gray-500">{{ $payment->payment_date->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                        <div class="text-sm text-green-600">{{ ucfirst($payment->status) }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p>Belum ada pembayaran</p>
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <span class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </span>
                    Aksi Cepat
                </h2>
                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Menu Utama</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Kelola Pelanggan -->
                <a href="{{ url('/admin/customers') }}" class="group relative overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 hover:from-blue-100 hover:to-blue-200 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:bg-blue-600 transition-colors shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="text-blue-400 group-hover:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 mb-1">Kelola Pelanggan</div>
                        <div class="text-sm text-gray-600">Tambah & kelola data pelanggan</div>
                    </div>
                </a>

                <!-- Kelola Pembayaran -->
                <a href="{{ url('/admin/payments') }}" class="group relative overflow-hidden bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 hover:from-green-100 hover:to-green-200 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center group-hover:bg-green-600 transition-colors shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="text-green-400 group-hover:text-green-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 mb-1">Kelola Pembayaran</div>
                        <div class="text-sm text-gray-600">Lihat & kelola pembayaran</div>
                    </div>
                </a>

                <!-- Kelola Invoice -->
                <a href="{{ url('/admin/invoices') }}" class="group relative overflow-hidden bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 hover:from-orange-100 hover:to-orange-200 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center group-hover:bg-orange-600 transition-colors shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="text-orange-400 group-hover:text-orange-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 mb-1">Kelola Invoice</div>
                        <div class="text-sm text-gray-600">Buat & kelola invoice</div>
                    </div>
                </a>

                <!-- Laporan -->
                <a href="{{ url('/admin/payment-period-report') }}" class="group relative overflow-hidden bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 hover:from-purple-100 hover:to-purple-200 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center group-hover:bg-purple-600 transition-colors shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="text-purple-400 group-hover:text-purple-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 mb-1">Laporan</div>
                        <div class="text-sm text-gray-600">Lihat laporan periode</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 text-center border border-gray-200">
            <div class="flex items-center justify-center space-x-2 mb-2">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Ranet Provider</h3>
            </div>
            <p class="text-sm text-gray-600 mb-1">Sistem Manajemen Internet Service Provider</p>
            <p class="text-xs text-gray-500">Dashboard terintegrasi untuk mengelola pelanggan, pembayaran, dan layanan</p>
        </div>

    </div>
</x-filament-panels::page>