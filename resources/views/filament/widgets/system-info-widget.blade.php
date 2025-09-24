<x-filament-widgets::widget>
    <x-filament::section>
        <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold mb-1">Informasi Sistem</h3>
                    <p class="text-indigo-100">{{ $this->getViewData()['current_date'] }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Pelanggan Hari Ini -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-8 h-8 bg-blue-400 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs text-white/70">Total</span>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ number_format($this->getViewData()['total_customers']) }}</div>
                    <div class="text-xs text-white/80">Pelanggan</div>
                </div>

                <!-- Pembayaran Hari Ini -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-8 h-8 bg-green-400 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <span class="text-xs text-white/70">Hari Ini</span>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ number_format($this->getViewData()['payments_today']) }}</div>
                    <div class="text-xs text-white/80">Pembayaran</div>
                </div>

                <!-- Pendapatan Hari Ini -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <span class="text-xs text-white/70">Hari Ini</span>
                    </div>
                    <div class="text-lg font-bold text-white">Rp {{ number_format($this->getViewData()['revenue_today'] / 1000, 0) }}K</div>
                    <div class="text-xs text-white/80">Pendapatan</div>
                </div>

                <!-- Invoice Tertunda -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-8 h-8 bg-red-400 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs text-white/70">Tertunda</span>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ number_format($this->getViewData()['pending_invoices']) }}</div>
                    <div class="text-xs text-white/80">Invoice</div>
                </div>
            </div>

            <!-- Monthly Summary -->
            <div class="mt-6 pt-6 border-t border-white/20">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                            <span class="text-white/80">Pembayaran Bulan Ini:</span>
                            <span class="text-white font-semibold">{{ number_format($this->getViewData()['payments_this_month']) }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                            <span class="text-white/80">Pendapatan:</span>
                            <span class="text-white font-semibold">Rp {{ number_format($this->getViewData()['revenue_this_month'] / 1000000, 1) }}M</span>
                        </div>
                    </div>
                    <div class="text-white/60 text-xs">
                        {{ $this->getViewData()['current_month'] }}
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
