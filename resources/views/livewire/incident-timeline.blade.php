<div>
    @if($loading)
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Loading incident timeline...</span>
        </div>
    @elseif(count($incidents) === 0)
        <div class="text-center py-12">
            <div class="text-green-500 mb-3">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">No Recent Incidents</h3>
            <p class="text-sm text-gray-500">All systems secure. No incidents in the last 24 hours.</p>
        </div>
    @else
        <div class="relative">
            <!-- Vertical timeline line -->
            <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>
            
            <div class="space-y-6">
                @foreach($incidents as $incident)
                    <div class="relative flex items-start group hover:bg-gray-50 p-2 rounded-lg transition-colors duration-200">
                        <!-- Timeline dot -->
                        <div class="absolute left-4 flex items-center justify-center w-4 h-4 bg-white rounded-full border-2 
                            @if($incident['risk_color'] === 'red') border-red-500
                            @elseif($incident['risk_color'] === 'orange') border-orange-500
                            @elseif($incident['risk_color'] === 'yellow') border-yellow-500
                            @else border-green-500 @endif z-10">
                            <div class="w-1.5 h-1.5 rounded-full 
                                @if($incident['risk_color'] === 'red') bg-red-500
                                @elseif($incident['risk_color'] === 'orange') bg-orange-500
                                @elseif($incident['risk_color'] === 'yellow') bg-yellow-500
                                @else bg-green-500 @endif">
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="ml-12 flex-1">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="text-xl">{{ $incident['icon'] }}</span>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900">{{ $incident['user'] }}</h4>
                                            <p class="text-xs text-gray-500">{{ $incident['email'] }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                            @if($incident['risk_color'] === 'red') bg-red-100 text-red-800
                                            @elseif($incident['risk_color'] === 'orange') bg-orange-100 text-orange-800
                                            @elseif($incident['risk_color'] === 'yellow') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ $incident['risk_percentage'] }}% Risk
                                        </span>
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                            @if($incident['status_color'] === 'yellow') bg-yellow-100 text-yellow-800
                                            @elseif($incident['status_color'] === 'blue') bg-blue-100 text-blue-800
                                            @elseif($incident['status_color'] === 'green') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($incident['status']) }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-sm font-medium text-gray-900 mb-1">{{ $incident['type'] }}</p>
                                    <p class="text-sm text-gray-600 mb-2">{{ $incident['description'] }}</p>
                                    
                                    @if(!empty($incident['detection_reasons']))
                                        <div class="flex flex-wrap gap-1 mb-3">
                                            @foreach($incident['detection_reasons'] as $reason)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $reason }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <!-- Risk score bar -->
                                    <div class="mb-3">
                                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                            <span>Risk Level</span>
                                            <span class="font-medium">{{ $incident['risk_percentage'] }}%</span>
                                        </div>
                                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full 
                                                @if($incident['risk_color'] === 'red') bg-red-500
                                                @elseif($incident['risk_color'] === 'orange') bg-orange-500
                                                @elseif($incident['risk_color'] === 'yellow') bg-yellow-500
                                                @else bg-green-500 @endif" 
                                                style="width: {{ $incident['risk_percentage'] }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Time and actions -->
                                <div class="ml-4 text-right">
                                    <div class="text-xs text-gray-500 mb-3">
                                        <div class="font-medium">{{ $incident['time'] }}</div>
                                        <div>{{ $incident['date'] }}</div>
                                    </div>
                                    
                                    @if($incident['status'] === 'pending')
                                        <div class="space-y-1">
                                            <button 
                                                wire:click="markAsReviewed({{ $incident['id'] }})"
                                                class="text-xs px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200">
                                                Mark Reviewed
                                            </button>
                                            <button 
                                                wire:click="markAsFalsePositive({{ $incident['id'] }})"
                                                class="text-xs px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors duration-200">
                                                False Positive
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Livewire notifications -->
    @script
    <script>
        Livewire.on('notify', (event) => {
            // Simple notification
            alert(event.message);
        });
    </script>
    @endscript
</div>