<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">AI Detection Engine</h3>
        <p class="text-sm text-gray-500 mt-1">Real-time risk analysis</p>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Risk Score Visualization -->
            <div class="space-y-4">
                <h4 class="font-medium text-gray-700">Risk Score Analysis</h4>
                
                <!-- Radial Progress -->
                <div class="relative flex items-center justify-center">
                    <svg class="w-48 h-48 transform -rotate-90">
                        <!-- Background Circle -->
                        <circle cx="96" cy="96" r="84" 
                                stroke="#e5e7eb" stroke-width="12" fill="none" />
                        
                        <!-- Progress Circle -->
                        <circle cx="96" cy="96" r="84" 
                                stroke="{{ $riskScore >= 80 ? '#ef4444' : ($riskScore >= 60 ? '#f59e0b' : '#10b981') }}"
                                stroke-width="12" fill="none"
                                stroke-dasharray="528" 
                                stroke-dashoffset="{{ 528 - (528 * $riskScore / 100) }}" 
                                stroke-linecap="round" />
                    </svg>
                    
                    <!-- Center Text -->
                    <div class="absolute text-center">
                        <div class="text-4xl font-bold 
                            {{ $riskScore >= 80 ? 'text-red-600' : 
                               ($riskScore >= 60 ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ number_format($riskScore, 1) }}%
                        </div>
                        <div class="text-sm text-gray-500 mt-1">Risk Score</div>
                    </div>
                </div>
                
                <!-- Risk Level Indicator -->
                <div class="text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $riskScore >= 80 ? 'bg-red-100 text-red-800' : 
                           ($riskScore >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                        @if($riskScore >= 80)
                            <svg class="mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            High Risk
                        @elseif($riskScore >= 60)
                            <svg class="mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Medium Risk
                        @else
                            <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Low Risk
                        @endif
                    </span>
                    <p class="text-xs text-gray-500 mt-2">
                        Last analyzed: {{ $lastAnalysisTime->diffForHumans() }}
                    </p>
                </div>
            </div>
            
            <!-- Detection Factors -->
            <div>
                <h4 class="font-medium text-gray-700 mb-4">Detection Factors</h4>
                
                <div class="space-y-4">
                    <!-- Location -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 {{ $this->getStatusColor($locationStatus) }}" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="{{ $this->getStatusIcon($locationStatus) }}" />
                            </svg>
                            <span class="ml-3 text-sm font-medium text-gray-700">Location</span>
                        </div>
                        <span class="text-sm {{ $this->getStatusColor($locationStatus) }}">
                            {{ $locationStatus }}
                        </span>
                    </div>
                    
                    <!-- Device -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 {{ $this->getStatusColor($deviceStatus) }}" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="{{ $this->getStatusIcon($deviceStatus) }}" />
                            </svg>
                            <span class="ml-3 text-sm font-medium text-gray-700">Device</span>
                        </div>
                        <span class="text-sm {{ $this->getStatusColor($deviceStatus) }}">
                            {{ $deviceStatus }}
                        </span>
                    </div>
                    
                    <!-- Time Pattern -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 {{ $this->getStatusColor($timeStatus) }}" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="{{ $this->getStatusIcon($timeStatus) }}" />
                            </svg>
                            <span class="ml-3 text-sm font-medium text-gray-700">Time Pattern</span>
                        </div>
                        <span class="text-sm {{ $this->getStatusColor($timeStatus) }}">
                            {{ $timeStatus }}
                        </span>
                    </div>
                    
                    <!-- Login Velocity -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 {{ $this->getStatusColor($velocityStatus) }}" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="{{ $this->getStatusIcon($velocityStatus) }}" />
                            </svg>
                            <span class="ml-3 text-sm font-medium text-gray-700">Login Velocity</span>
                        </div>
                        <span class="text-sm {{ $this->getStatusColor($velocityStatus) }}">
                            {{ $velocityStatus }}
                        </span>
                    </div>
                    
                    <!-- IP Reputation -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 {{ $this->getStatusColor($ipStatus) }}" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="{{ $this->getStatusIcon($ipStatus) }}" />
                            </svg>
                            <span class="ml-3 text-sm font-medium text-gray-700">IP Reputation</span>
                        </div>
                        <span class="text-sm {{ $this->getStatusColor($ipStatus) }}">
                            {{ $ipStatus }}
                        </span>
                    </div>
                </div>
                
                <!-- Analysis Button -->
                <div class="mt-6">
                    <button wire:click="analyzeSample" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Run New Analysis
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>