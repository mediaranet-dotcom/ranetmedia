<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            💰 Pembayaran Terbaru
        </x-slot>

        <div class="space-y-3">
            @forelse($this->getViewData()['payments'] as $payment)
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $payment->invoice->customer->name ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $payment->invoice->customer->customer_number ?? 'N/A' }} •
                                {{ $payment->payment_date->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0 text-right">
                    <p class="text-sm font-semibold text-green-600 dark:text-green-400">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                    </p>
                </div>
            </div>
            @empty
            <div class="text-center py-6">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Belum ada pembayaran</p>
            </div>
            @endforelse
        </div>

        @if($this->getViewData()['payments']->count() > 0)
        <div class="mt-4 text-center">
            <a href="{{ route('filament.admin.resources.payments.index') }}"
                class="text-sm text-primary-600 hover:text-primary-500 font-medium">
                Lihat Semua Pembayaran →
            </a>
        </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>