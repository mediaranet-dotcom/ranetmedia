<x-filament-panels::page>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .hidden {
                display: block !important;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .bg-gradient-to-r {
                background: linear-gradient(to right, #3B82F6, #2563EB) !important;
                color: white !important;
            }

            .bg-blue-50 {
                background: #EFF6FF !important;
            }

            .text-white {
                color: white !important;
            }

            /* Ensure all content is visible */
            * {
                visibility: visible !important;
            }
        }
    </style>

    <div class="space-y-6">

        <!-- Header Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        📅 Laporan Pembayaran Per Periode Bulan
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Menampilkan pembayaran yang berhasil dikelompokkan berdasarkan periode bulan. Klik pada periode untuk melihat detail.
                    </p>
                </div>
                <div class="flex space-x-3 no-print">
                    <button onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                    <button onclick="exportToPDF()"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Payment Periods -->
        @php
        $paymentsByPeriod = $this->getPaymentsByPeriod();
        $totalTransactions = collect($paymentsByPeriod)->sum('count') ?: 0;
        $totalRevenue = collect($paymentsByPeriod)->sum('total') ?: 0;
        $avgPerPeriod = count($paymentsByPeriod) > 0 ? $totalRevenue / count($paymentsByPeriod) : 0;
        $periodCount = count($paymentsByPeriod);

        // Ensure we always have values to display
        $displayTransactions = $totalTransactions;
        $displayRevenue = $totalRevenue;
        $displayAverage = $avgPerPeriod;
        @endphp

        {{-- Debug: Periods={{ $periodCount }}, Transactions={{ $totalTransactions }}, Revenue={{ $totalRevenue }} --}}

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Transaksi -->
            <div class="bg-white dark:bg-gray-800 border-l-4 border-blue-500 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $displayTransactions }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Transaksi</div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white dark:bg-gray-800 border-l-4 border-green-500 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">IDR {{ number_format($displayRevenue, 0, ',', '.') }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</div>
                    </div>
                </div>
            </div>

            <!-- Rata-rata per Periode -->
            <div class="bg-white dark:bg-gray-800 border-l-4 border-purple-500 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">IDR {{ number_format($displayAverage, 0, ',', '.') }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Rata-rata per Periode</div>
                    </div>
                </div>
            </div>
        </div>

        @if(count($paymentsByPeriod) > 0)
        <div class="space-y-4">
            @foreach($paymentsByPeriod as $periodData)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <!-- Period Header - Clickable -->
                <div class="bg-blue-50 dark:bg-blue-900/20 px-6 py-4 border-b dark:border-gray-700 cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                    onclick="togglePeriod('period-{{ $loop->index }}')">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 transform transition-transform duration-200"
                                id="icon-period-{{ $loop->index }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <div>
                                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">
                                    📅 Periode: {{ $periodData['period'] }}
                                </h3>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    {{ $periodData['count'] }} pembayaran berhasil
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                                IDR {{ number_format($periodData['total'], 0, ',', '.') }}
                            </div>
                            <div class="text-sm text-blue-700 dark:text-blue-300">Total Periode</div>

                            <!-- Progress Bar -->
                            @php
                            $maxTotal = collect($paymentsByPeriod)->max('total');
                            $percentage = $maxTotal > 0 ? ($periodData['total'] / $maxTotal) * 100 : 0;
                            @endphp
                            <div class="mt-2 w-32 ml-auto" style="--progress-width: {{ $percentage }}%;">
                                <div class="bg-blue-200 dark:bg-blue-800 rounded-full h-2">
                                    <div class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full transition-all duration-300" style="width: var(--progress-width)"></div>
                                </div>
                                <div class="text-xs text-blue-600 dark:text-blue-300 mt-1">{{ number_format($percentage, 1) }}%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details - Hidden by default -->
                <div id="period-{{ $loop->index }}" class="hidden overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Jumlah
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Metode
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($periodData['payments'] as $payment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $payment->service->customer->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    IDR {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($payment->payment_method === 'cash') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                                    @elseif($payment->payment_method === 'bank_transfer') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                                                    @elseif($payment->payment_method === 'e_wallet') bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200
                                                    @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($payment->status === 'completed') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                                    @elseif($payment->status === 'pending') bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                                                    @elseif($payment->status === 'failed') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                                                    @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 @endif">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
            <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">📅</div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak Ada Data Pembayaran</h3>
            <p class="text-gray-500 dark:text-gray-400">Belum ada pembayaran yang tercatat dalam sistem.</p>
        </div>
        @endif

    </div>

    <script>
        function togglePeriod(periodId) {
            const element = document.getElementById(periodId);
            const icon = document.getElementById('icon-' + periodId);

            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
                icon.style.transform = 'rotate(90deg)';
            } else {
                element.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        function exportToPDF() {
            // Show alert to confirm function is called
            alert('Membuka dialog print untuk export PDF...');

            // Expand all periods first
            const hiddenElements = document.querySelectorAll('[id^="period-"]');
            hiddenElements.forEach(element => {
                element.classList.remove('hidden');
            });

            // Use browser's print to PDF functionality
            setTimeout(() => {
                window.print();
            }, 500);

            // Collapse periods back after a short delay
            setTimeout(() => {
                hiddenElements.forEach(element => {
                    element.classList.add('hidden');
                });
            }, 2000);
        }
    </script>
</x-filament-panels::page>