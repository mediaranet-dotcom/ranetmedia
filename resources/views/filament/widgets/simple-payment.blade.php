<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            💰 Payment Overview
        </x-slot>

        <div class="space-y-6">
            <!-- Payment Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Total Revenue -->
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
                        <p class="text-lg font-bold text-green-600 dark:text-green-400">
                            Rp {{ number_format($this->getViewData()['totalPayments'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400">{{ number_format($this->getViewData()['paymentCount']) }} payments</p>
                    </div>
                </div>

                <!-- Today Revenue -->
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Today Revenue</p>
                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($this->getViewData()['todayPayments'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400">{{ number_format($this->getViewData()['todayCount']) }} payments</p>
                    </div>
                </div>

                <!-- Paid Invoices -->
                <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-lg border border-emerald-200 dark:border-emerald-800">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Paid Invoices</p>
                        <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                            {{ number_format($this->getViewData()['paidInvoices']) }}
                        </p>
                        <p class="text-xs text-gray-400">of {{ number_format($this->getViewData()['totalInvoices']) }} total</p>
                    </div>
                </div>

                <!-- Unpaid Invoices -->
                <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg border border-orange-200 dark:border-orange-800">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unpaid Invoices</p>
                        <p class="text-lg font-bold text-orange-600 dark:text-orange-400">
                            {{ number_format($this->getViewData()['unpaidInvoices']) }}
                        </p>
                        <p class="text-xs text-gray-400">need attention</p>
                    </div>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="bg-gray-50 dark:bg-gray-900/20 p-4 rounded-lg border border-gray-200 dark:border-gray-800">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Recent Payments</h3>
                
                @if($this->getViewData()['recentPayments']->count() > 0)
                    <div class="space-y-2">
                        @foreach($this->getViewData()['recentPayments'] as $payment)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $payment->customer->name ?? 'Unknown Customer' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 'No date' }} • 
                                        {{ $payment->payment_method ?? 'Unknown method' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-green-600 dark:text-green-400">
                                        Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                        No recent payments found
                    </p>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('filament.admin.resources.payments.index') }}" 
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    View All Payments
                </a>
                
                <a href="{{ route('filament.admin.resources.invoices.index') }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Manage Invoices
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
