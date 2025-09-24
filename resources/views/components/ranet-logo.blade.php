@props(['height' => '2.5rem'])

<div {{ $attributes->merge(['class' => 'flex items-center']) }}>
    <svg width="200" height="80" viewBox="0 0 200 80" xmlns="http://www.w3.org/2000/svg" style="height: {{ $height }}; width: auto;">
        <!-- Background -->
        <rect width="200" height="80" fill="#f59e0b" rx="8"/>
        
        <!-- Network Icon -->
        <g transform="translate(15, 20)">
            <!-- Central Hub -->
            <circle cx="20" cy="20" r="6" fill="white"/>
            
            <!-- Connection Lines -->
            <line x1="20" y1="20" x2="5" y2="5" stroke="white" stroke-width="2"/>
            <line x1="20" y1="20" x2="35" y2="5" stroke="white" stroke-width="2"/>
            <line x1="20" y1="20" x2="5" y2="35" stroke="white" stroke-width="2"/>
            <line x1="20" y1="20" x2="35" y2="35" stroke="white" stroke-width="2"/>
            
            <!-- End Nodes -->
            <circle cx="5" cy="5" r="3" fill="white"/>
            <circle cx="35" cy="5" r="3" fill="white"/>
            <circle cx="5" cy="35" r="3" fill="white"/>
            <circle cx="35" cy="35" r="3" fill="white"/>
        </g>
        
        <!-- Text -->
        <text x="70" y="35" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="white">RANET</text>
        <text x="70" y="55" font-family="Arial, sans-serif" font-size="12" fill="white">Internet Provider</text>
    </svg>
</div>
