<div>
    <!-- Quick Action Buttons -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <button wire:click="lockSystemModal" 
                class="p-4 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors duration-200">
            <div class="flex items-center">
                <div class="bg-red-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="font-medium text-gray-900">Lock System</h4>
                    <p class="text-sm text-gray-500">Temporarily disable all logins</p>
                </div>
            </div>
        </button>
        
        <button wire:click="blockIpModal"
                class="p-4 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 transition-colors duration-200">
            <div class="flex items-center">
                <div class="bg-orange-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="font-medium text-gray-900">Block IP</h4>
                    <p class="text-sm text-gray-500">Block suspicious IP addresses</p>
                </div>
            </div>
        </button>
        
        <button wire:click="generateReportModal"
                class="p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors duration-200">
            <div class="flex items-center">
                <div class="bg-blue-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="font-medium text-gray-900">Generate Report</h4>
                    <p class="text-sm text-gray-500">Create security reports</p>
                </div>
            </div>
        </button>
    </div>
    
    <!-- Currently Blocked IPs -->
    <div class="mb-6">
        <h4 class="text-sm font-semibold text-gray-900 mb-3">Recently Blocked IPs</h4>
        @if(count($blockedIps) > 0)
            <div class="space-y-2">
                @foreach($blockedIps as $ip)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <span class="font-medium text-gray-900">{{ $ip->ip_address }}</span>
                            <p class="text-xs text-gray-500">{{ $ip->reason }}</p>
                        </div>
                        <div class="text-xs text-gray-500">
                            Blocked {{ $ip->blocked_at->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 text-center py-4">No IPs currently blocked</p>
        @endif
    </div>
    
    <!-- Modals will go here -->
</div>