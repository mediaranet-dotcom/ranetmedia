<div class="p-4 min-w-[300px]">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-bold text-gray-900">{{ $info['name'] }}</h3>
        <span class="px-2 py-1 text-xs font-semibold rounded-full 
            @if($info['status'] === 'Active') bg-green-100 text-green-800
            @elseif($info['status'] === 'Maintenance') bg-yellow-100 text-yellow-800
            @elseif($info['status'] === 'Damaged') bg-red-100 text-red-800
            @else bg-gray-100 text-gray-800
            @endif">
            {{ $info['status'] }}
        </span>
    </div>
    
    <div class="space-y-2 text-sm">
        <div class="flex justify-between">
            <span class="text-gray-600">Code:</span>
            <span class="font-medium">{{ $info['code'] }}</span>
        </div>
        
        <div class="flex justify-between">
            <span class="text-gray-600">Type:</span>
            <span class="font-medium">{{ $info['odp_type'] }}</span>
        </div>
        
        <div class="flex justify-between">
            <span class="text-gray-600">Port Usage:</span>
            <span class="font-medium 
                @if((float)str_replace('%', '', $info['utilization']) > 80) text-red-600
                @elseif((float)str_replace('%', '', $info['utilization']) > 60) text-yellow-600
                @else text-green-600
                @endif">
                {{ $info['port_usage'] }} ({{ $info['utilization'] }})
            </span>
        </div>
        
        <div class="flex justify-between">
            <span class="text-gray-600">Condition:</span>
            <span class="font-medium 
                @if($info['condition'] === 'Excellent') text-green-600
                @elseif($info['condition'] === 'Good') text-blue-600
                @elseif($info['condition'] === 'Fair') text-yellow-600
                @else text-red-600
                @endif">
                {{ $info['condition'] }}
            </span>
        </div>
        
        <div class="pt-2 border-t border-gray-200">
            <div class="text-gray-600 text-xs mb-1">Location:</div>
            <div class="text-gray-800">{{ $info['address'] }}</div>
            <div class="text-gray-600 text-xs">{{ $info['area'] }}, {{ $info['district'] }}</div>
        </div>
        
        @if($info['manufacturer'])
        <div class="flex justify-between">
            <span class="text-gray-600">Manufacturer:</span>
            <span class="font-medium">{{ $info['manufacturer'] }}</span>
        </div>
        @endif
        
        <div class="pt-2 border-t border-gray-200 text-xs text-gray-600">
            <div class="flex justify-between">
                <span>Installed:</span>
                <span>{{ $info['installation_date'] ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Last Maintenance:</span>
                <span>{{ $info['last_maintenance'] }}</span>
            </div>
        </div>
    </div>
    
    <div class="mt-3 pt-3 border-t border-gray-200">
        <a href="/admin/odps/{{ $info['id'] ?? '' }}/edit" 
           class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit ODP
        </a>
    </div>
</div>
