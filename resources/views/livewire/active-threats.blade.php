
<div>
    @if($loading)
        <div class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading threats...</span>
        </div>
    @elseif(count($threats) === 0)
        <div class="text-center py-8">
            <div class="text-green-500 mb-2">
                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No Active Threats</h3>
            <p class="text-sm text-gray-500">All systems are secure. No high-risk threats detected.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($threats as $threat)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($threat['risk_color'] === 'red') bg-red-100 text-red-800
                                    @elseif($threat['risk_color'] === 'orange') bg-orange-100 text-orange-800
                                    @elseif($threat['risk_color'] === 'yellow') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ $threat['risk_percentage'] }}% Risk
                                </span>
                                <span class="text-xs font-medium text-gray-500">{{ $threat['type'] }}</span>
                            </div>
                            
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">{{ $threat['user'] }}</h4>
                            <p class="text-sm text-gray-600 mb-2">{{ $threat['description'] }}</p>
                            
                            @if(!empty($threat['detection_reasons']))
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach($threat['detection_reasons'] as $reason)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $reason }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ $threat['time'] }}</span>
                                <button wire:click="markAsReviewed({{ $threat['id'] }})" 
                                        class="text-blue-600 hover:text-blue-800 font-medium">
                                    Mark as Reviewed
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>