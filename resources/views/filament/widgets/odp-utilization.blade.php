<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            📊 ODP Port Utilization
        </x-slot>

        <x-slot name="description">
            Monitor port usage across all ODPs to identify capacity issues
        </x-slot>

        <div class="space-y-6">
            <!-- Overall Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['totalOdps'] }}</div>
                    <div class="text-sm text-blue-700">Total ODPs</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['activeOdps'] }}</div>
                    <div class="text-sm text-green-700">Active ODPs</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['usedPorts'] }}/{{ $stats['totalPorts'] }}</div>
                    <div class="text-sm text-purple-700">Ports Used</div>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['overallUtilization'] }}%</div>
                    <div class="text-sm text-orange-700">Overall Utilization</div>
                </div>
            </div>

            <!-- Utilization Ranges -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="flex items-center space-x-2 p-3 bg-red-50 rounded-lg border border-red-200">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <div>
                        <div class="font-semibold text-red-700">{{ $utilizationRanges['critical'] }}</div>
                        <div class="text-xs text-red-600">Critical (>90%)</div>
                    </div>
                </div>
                <div class="flex items-center space-x-2 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <div>
                        <div class="font-semibold text-yellow-700">{{ $utilizationRanges['high'] }}</div>
                        <div class="text-xs text-yellow-600">High (70-90%)</div>
                    </div>
                </div>
                <div class="flex items-center space-x-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <div>
                        <div class="font-semibold text-blue-700">{{ $utilizationRanges['medium'] }}</div>
                        <div class="text-xs text-blue-600">Medium (40-70%)</div>
                    </div>
                </div>
                <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                    <div>
                        <div class="font-semibold text-gray-700">{{ $utilizationRanges['low'] }}</div>
                        <div class="text-xs text-gray-600">Low (<40%)</div>
                    </div>
                </div>
            </div>

            <!-- Top Utilized ODPs -->
            <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔥 Highest Utilization ODPs</h4>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($topUtilizedOdps as $odp)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium text-gray-900">{{ $odp['name'] }}</span>
                                    <span class="text-xs text-gray-500">({{ $odp['code'] }})</span>
                                    @if($odp['status'] === 'active')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($odp['status']) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    📍 {{ $odp['area'] }} • 👥 {{ $odp['customer_count'] }} customers
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $odp['used_ports'] }}/{{ $odp['total_ports'] }} ports
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $odp['utilization'] }}% used
                                    </div>
                                </div>
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $odp['utilization'] > 90 ? 'bg-red-500' : ($odp['utilization'] > 70 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                         style="width: {{ $odp['utilization'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            No ODP data available
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
