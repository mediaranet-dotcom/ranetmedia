@php
    $record = $getRecord();
    $services = $record->services()->with(['customer', 'package'])->get();
    $usedPorts = $services->pluck('odp_port')->filter()->toArray();
    $totalPorts = $record->total_ports;
@endphp

<div class="space-y-6">
    <!-- Port Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="text-2xl font-bold text-blue-600">{{ $totalPorts }}</div>
            <div class="text-sm text-blue-700">Total Ports</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="text-2xl font-bold text-green-600">{{ count($usedPorts) }}</div>
            <div class="text-sm text-green-700">Used Ports</div>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div class="text-2xl font-bold text-gray-600">{{ $totalPorts - count($usedPorts) }}</div>
            <div class="text-sm text-gray-700">Available Ports</div>
        </div>
        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
            <div class="text-2xl font-bold text-orange-600">{{ $totalPorts > 0 ? round((count($usedPorts) / $totalPorts) * 100, 1) : 0 }}%</div>
            <div class="text-sm text-orange-700">Utilization</div>
        </div>
    </div>

    <!-- Port Grid Visualization -->
    <div class="bg-white p-6 rounded-lg border border-gray-200">
        <h4 class="font-semibold text-gray-900 mb-4">🔌 Port Layout</h4>
        
        <div class="grid gap-3" style="grid-template-columns: repeat({{ min($totalPorts, 8) }}, 1fr);">
            @for($port = 1; $port <= $totalPorts; $port++)
                @php
                    $service = $services->where('odp_port', $port)->first();
                    $isUsed = in_array($port, $usedPorts);
                @endphp
                
                <div class="relative group">
                    <div class="w-16 h-16 rounded-lg border-2 flex items-center justify-center font-bold text-sm transition-all duration-200 hover:scale-105 cursor-pointer
                        {{ $isUsed 
                            ? 'bg-green-100 border-green-500 text-green-700 hover:bg-green-200' 
                            : 'bg-gray-100 border-gray-300 text-gray-500 hover:bg-gray-200' 
                        }}">
                        {{ $port }}
                    </div>
                    
                    @if($isUsed && $service)
                        <!-- Tooltip for used port -->
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10 whitespace-nowrap">
                            <div class="font-semibold">{{ $service->customer->name }}</div>
                            <div>📦 {{ $service->package->name }}</div>
                            @if($service->fiber_cable_color)
                                <div>🎨 {{ $service->fiber_cable_color }}</div>
                            @endif
                            @if($service->signal_strength)
                                <div>📶 {{ $service->signal_strength }} dBm</div>
                            @endif
                            <!-- Arrow -->
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                        </div>
                    @else
                        <!-- Tooltip for available port -->
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-700 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10 whitespace-nowrap">
                            <div>Port {{ $port }} - Available</div>
                            <!-- Arrow -->
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-700"></div>
                        </div>
                    @endif
                </div>
            @endfor
        </div>

        <!-- Legend -->
        <div class="flex items-center justify-center space-x-6 mt-6 pt-4 border-t border-gray-200">
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-green-100 border-2 border-green-500 rounded"></div>
                <span class="text-sm text-gray-600">Used Port</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-gray-100 border-2 border-gray-300 rounded"></div>
                <span class="text-sm text-gray-600">Available Port</span>
            </div>
        </div>
    </div>

    <!-- Connected Customers List -->
    @if($services->count() > 0)
        <div class="bg-white p-6 rounded-lg border border-gray-200">
            <h4 class="font-semibold text-gray-900 mb-4">👥 Connected Customers</h4>
            
            <div class="space-y-3">
                @foreach($services->sortBy('odp_port') as $service)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                {{ $service->odp_port }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $service->customer->name }}</div>
                                <div class="text-sm text-gray-600">📞 {{ $service->customer->phone }}</div>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">{{ $service->package->name }}</div>
                            <div class="text-xs text-gray-500">
                                @if($service->fiber_cable_color)
                                    🎨 {{ $service->fiber_cable_color }}
                                @endif
                                @if($service->signal_strength)
                                    • 📶 {{ $service->signal_strength }} dBm
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                {{ $service->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($service->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-gray-50 p-8 rounded-lg border border-gray-200 text-center">
            <div class="text-gray-500">
                <div class="text-4xl mb-2">🔌</div>
                <div class="font-medium">No customers connected</div>
                <div class="text-sm">All ports are available for new connections</div>
            </div>
        </div>
    @endif
</div>
