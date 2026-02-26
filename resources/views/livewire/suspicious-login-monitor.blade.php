<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Real-time Monitoring</h3>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-500">Risk Threshold:</span>
                <div class="flex items-center">
                    <input type="range" min="0" max="100" wire:model="riskThreshold" 
                           class="w-32" id="riskThreshold">
                    <span class="ml-2 text-sm font-medium text-gray-700">{{ $riskThreshold }}%</span>
                </div>
                <button wire:click="loadRealtimeData" 
                        class="p-2 text-gray-500 hover:text-gray-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        @if(count($realtimeData) > 0)
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @foreach($realtimeData as $activity)
                    @php
                        $riskColor = $activity['risk_score'] >= 80 ? 'bg-red-100 border-red-300' : 
                                    ($activity['risk_score'] >= 60 ? 'bg-yellow-100 border-yellow-300' : 'bg-blue-100 border-blue-300');
                        $textColor = $activity['risk_score'] >= 80 ? 'text-red-800' : 
                                    ($activity['risk_score'] >= 60 ? 'text-yellow-800' : 'text-blue-800');
                    @endphp
                    
                    <div class="border rounded-lg p-4 {{ $riskColor }} border-l-4" 
                         style="border-left-color: {{ $activity['risk_score'] >= 80 ? '#ef4444' : 
                                                ($activity['risk_score'] >= 60 ? '#f59e0b' : '#3b82f6') }}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $textColor }} bg-white">
                                        {{ number_format($activity['risk_score'], 1) }}% Risk
                                    </span>
                                    <span class="ml-2 text-sm font-medium text-gray-700">
                                        {{ $activity['user'] }}
                                    </span>
                                    <span class="ml-2 text-sm text-gray-500">
                                        {{ $activity['time'] }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">
                                    {{ $activity['type'] }} • {{ $activity['location'] }}
                                </p>
                                @if(is_array($activity['reasons']))
                                    <div class="mt-2">
                                        @foreach($activity['reasons'] as $reason)
                                            <span class="inline-block bg-white bg-opacity-50 rounded-full px-3 py-1 text-xs font-semibold text-gray-700 mr-2 mb-2">
                                                {{ $reason }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <button wire:click="markAsReviewed({{ $activity['id'] }})"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    Mark Reviewed
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No suspicious activities</h3>
                <p class="mt-1 text-sm text-gray-500">All login attempts appear normal.</p>
            </div>
        @endif
        
        <!-- WebSocket Integration Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Check if Echo is available (from laravel-echo)
                if (typeof Echo !== 'undefined') {
                    Echo.private('admin.suspicious-logins')
                        .listen('SuspiciousLoginDetected', (e) => {
                            // Dispatch Livewire event
                            Livewire.emit('newSuspiciousActivity');
                            
                            // Show browser notification
                            if (Notification.permission === 'granted') {
                                new Notification('🚨 Suspicious Login Detected', {
                                    body: `${e.user} from ${e.location}`,
                                    icon: '/favicon.ico'
                                });
                            }
                            
                            // Play alert sound (optional)
                            const audio = new Audio('/notification.mp3');
                            audio.play().catch(e => console.log('Audio play failed:', e));
                        });
                }
            });
        </script>
    </div>
</div>