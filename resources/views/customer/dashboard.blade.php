<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - RANET</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-wifi text-blue-600 text-2xl mr-3"></i>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">RANET</h1>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-6">
                    <span class="text-gray-600">Halo, <strong>{{ $customer->name }}</strong></span>
                    <a href="{{ route('customer.logout') }}" class="text-red-600 hover:text-red-700">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </nav>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-blue-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav id="mobile-menu" class="hidden md:hidden mt-4 pb-4 border-t border-gray-200">
                <div class="flex flex-col space-y-3 pt-4">
                    <span class="text-gray-600 py-2">Halo, <strong>{{ $customer->name }}</strong></span>
                    <a href="{{ route('customer.logout') }}" class="text-red-600 hover:text-red-700 py-2">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <div class="container mx-auto px-4 py-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Selamat Datang!</h2>
                    <p class="opacity-90">{{ $customer->name }}</p>
                    <p class="text-sm opacity-75">ID Pelanggan: {{ $customer->customer_id }}</p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-user-circle text-6xl opacity-20"></i>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Active Services -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Layanan Aktif</p>
                        <p class="text-2xl font-bold text-green-600">{{ $activeServices }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-wifi text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Invoices -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Tagihan Pending</p>
                        <p class="text-2xl font-bold text-orange-600">{{ $pendingInvoices }}</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-file-invoice text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Outstanding -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Tagihan</p>
                        <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-money-bill text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Invoices -->
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-file-invoice mr-2 text-blue-600"></i>
                        Tagihan Terbaru
                    </h3>
                </div>
                <div class="p-6">
                    @if($recentInvoices->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentInvoices as $invoice)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-800">{{ $invoice->invoice_number }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                            @if($invoice->status === 'paid') bg-green-100 text-green-800
                                            @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $invoice->service->package->name }}</p>
                                <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Periode: {{ \Carbon\Carbon::parse($invoice->billing_period_start)->locale('id')->format('M Y') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    Jatuh tempo: {{ $invoice->due_date->locale('id')->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4 text-center">
                        <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Lihat Semua Tagihan <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="fas fa-file-invoice text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">Tidak ada tagihan</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Active Services -->
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-cog mr-2 text-blue-600"></i>
                        Layanan Aktif
                    </h3>
                </div>
                <div class="p-6">
                    @if($services->count() > 0)
                    <div class="space-y-4">
                        @foreach($services as $service)
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-800">{{ $service->package->name }}</h4>
                                <span class="px-2 py-1 text-xs rounded-full 
                                        @if($service->status === 'active') bg-green-100 text-green-800
                                        @elseif($service->status === 'suspended') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-1">
                                <i class="fas fa-tachometer-alt mr-1"></i>
                                {{ $service->package->speed }}
                            </p>
                            <p class="text-sm text-gray-600 mb-1">
                                <i class="fas fa-calendar mr-1"></i>
                                Mulai: {{ $service->start_date->format('d/m/Y') }}
                            </p>
                            <p class="text-sm font-semibold text-gray-800">
                                <i class="fas fa-money-bill mr-1"></i>
                                Rp {{ number_format($service->monthly_fee ?? $service->package->price, 0, ',', '.') }}/bulan
                            </p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="fas fa-wifi text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">Tidak ada layanan aktif</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="mt-6 bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-credit-card mr-2 text-blue-600"></i>
                    Informasi Pembayaran
                </h3>
            </div>
            <div class="p-6">
                @if($companySetting && $companySetting->bank_details)
                @php
                $bankDetails = explode("\n", $companySetting->bank_details);
                $bankAccounts = [];
                $ewallets = [];
                $currentSection = '';

                foreach($bankDetails as $line) {
                $line = trim($line);
                if(empty($line)) continue;

                if(stripos($line, 'e-wallet') !== false || stripos($line, 'ewallet') !== false) {
                $currentSection = 'ewallet';
                continue;
                }

                if($currentSection === 'ewallet') {
                $ewallets[] = $line;
                } else {
                $bankAccounts[] = $line;
                }
                }
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Bank Transfer -->
                    @if(count($bankAccounts) > 0)
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-university text-blue-600 text-xl mr-2"></i>
                            <h4 class="font-semibold text-gray-800">Transfer Bank</h4>
                        </div>
                        <div class="space-y-2">
                            @foreach($bankAccounts as $account)
                            <div class="flex items-center justify-between bg-white p-2 rounded border">
                                <span class="text-sm text-gray-700">{{ $account }}</span>
                                <button onclick="copyToClipboard('{{ $account }}')" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-copy text-xs"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- E-Wallet -->
                    @if(count($ewallets) > 0)
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-mobile-alt text-green-600 text-xl mr-2"></i>
                            <h4 class="font-semibold text-gray-800">E-Wallet</h4>
                        </div>
                        <div class="space-y-2">
                            @foreach($ewallets as $ewallet)
                            <div class="flex items-center justify-between bg-white p-2 rounded border">
                                <span class="text-sm text-gray-700">{{ $ewallet }}</span>
                                <button onclick="copyToClipboard('{{ $ewallet }}')" class="text-green-600 hover:text-green-700">
                                    <i class="fas fa-copy text-xs"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Payment Instructions -->
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-yellow-600 mt-1 mr-2"></i>
                        <div class="text-sm text-yellow-800">
                            <p class="font-medium mb-1">Cara Pembayaran:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Transfer sesuai jumlah tagihan yang tertera</li>
                                <li>Sertakan nomor invoice sebagai berita transfer</li>
                                <li>Konfirmasi pembayaran melalui WhatsApp atau email</li>
                                <li>Pembayaran akan diproses dalam 1x24 jam</li>
                            </ul>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-credit-card text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">Informasi pembayaran belum tersedia</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-bolt mr-2 text-blue-600"></i>
                Aksi Cepat
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="#" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-colors">
                    <i class="fas fa-file-invoice text-blue-600 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700">Lihat Tagihan</span>
                </a>
                <a href="#" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 transition-colors">
                    <i class="fas fa-credit-card text-green-600 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700">Bayar Tagihan</span>
                </a>
                <a href="{{ route('service.application.form') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-300 transition-colors">
                    <i class="fas fa-plus text-purple-600 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700">Layanan Baru</span>
                </a>
                <a href="#" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-orange-50 hover:border-orange-300 transition-colors">
                    <i class="fas fa-headset text-orange-600 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700">Bantuan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Menu JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');

                    // Toggle icon
                    const icon = mobileMenuBtn.querySelector('i');
                    if (mobileMenu.classList.contains('hidden')) {
                        icon.className = 'fas fa-bars text-xl';
                    } else {
                        icon.className = 'fas fa-times text-xl';
                    }
                });
            }
        });

        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                toast.textContent = 'Disalin ke clipboard!';
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);

                // Show success message
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                toast.textContent = 'Disalin ke clipboard!';
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 2000);
            });
        }
    </script>
</body>

</html>