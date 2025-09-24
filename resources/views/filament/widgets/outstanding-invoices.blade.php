<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            ⚠️ Invoice Tertunggak
        </x-slot>

        <x-slot name="headerEnd">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Total: Rp {{ number_format($this->getViewData()['totalOutstanding'], 0, ',', '.') }}
            </div>
        </x-slot>

        <div class="space-y-3">
            @forelse($this->getViewData()['invoices'] as $invoice)
                @php
                    $daysOverdue = now()->diffInDays($invoice->due_date);
                    $urgencyColor = $daysOverdue > 30 ? 'red' : ($daysOverdue > 7 ? 'orange' : 'yellow');
                @endphp
                
                <div class="flex items-center justify-between p-3 bg-{{ $urgencyColor }}-50 dark:bg-{{ $urgencyColor }}-900/20 rounded-lg border border-{{ $urgencyColor }}-200 dark:border-{{ $urgencyColor }}-800">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-{{ $urgencyColor }}-100 dark:bg-{{ $urgencyColor }}-900 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-{{ $urgencyColor }}-600 dark:text-{{ $urgencyColor }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $invoice->customer->name ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $invoice->customer->customer_number ?? 'N/A' }} • 
                                    Invoice: {{ $invoice->invoice_number }}
                                </p>
                                <p class="text-xs text-{{ $urgencyColor }}-600 dark:text-{{ $urgencyColor }}-400 font-medium">
                                    Terlambat {{ $daysOverdue }} hari
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-sm font-semibold text-{{ $urgencyColor }}-700 dark:text-{{ $urgencyColor }}-300">
                            Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Jatuh tempo: {{ $invoice->due_date->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-green-600 dark:text-green-400 font-medium">
                        🎉 Tidak ada invoice tertunggak!
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Semua pelanggan sudah membayar tepat waktu
                    </p>
                </div>
            @endforelse
        </div>

        @if($this->getViewData()['invoices']->count() > 0)
            <div class="mt-4 text-center">
                <a href="{{ route('filament.admin.resources.invoices.index') }}" 
                   class="text-sm text-primary-600 hover:text-primary-500 font-medium">
                    Lihat Semua Invoice →
                </a>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
