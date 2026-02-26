<div class="fixed top-4 right-4 z-50 w-full max-w-sm space-y-4">
    @foreach($alerts as $alert)
        <div x-data="{ show: true }" 
             x-show="show"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="rounded-lg shadow-lg overflow-hidden border {{ $this->alertClasses($alert['type']) }}">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="{{ $this->iconForType($alert['type']) }}" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium">{{ $alert['title'] }}</p>
                        <p class="mt-1 text-sm opacity-90">{{ $alert['message'] }}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false; $wire.dismissAlert('{{ $alert['id'] }}')" 
                                class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            @if($alert['duration'] > 0)
                <div class="h-1 w-full bg-current opacity-25">
                    <div x-data="{ width: 100 }" 
                         x-init="setInterval(() => { if(width > 0) width -= 0.5 }, {{ $alert['duration'] / 200 }})"
                         class="h-full bg-current transition-all duration-{{ $alert['duration'] }}"
                         :style="`width: ${width}%`">
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>

<script>
    // Listen for WebSocket events and show alerts
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Echo !== 'undefined') {
            // Suspicious login alerts
            Echo.private('admin.suspicious-logins')
                .listen('SuspiciousLoginDetected', (e) => {
                    Livewire.emit('alertAdded', 'danger', 
                        '🚨 Suspicious Login Detected',
                        `${e.user} from ${e.location}`
                    );
                });
            
            // User verification alerts
            Echo.private('user.{{ auth()->id() }}.verifications')
                .listen('VerificationRequired', (e) => {
                    Livewire.emit('alertAdded', 'warning',
                        '⚠️ Verification Required',
                        e.message
                    );
                });
        }
        
        // Listen for browser notifications permission
        if ('Notification' in window && Notification.permission === 'default') {
            Livewire.emit('alertAdded', 'info',
                'Enable Notifications',
                'Click here to enable real-time notifications for suspicious activities.',
                10000
            );
            
            // Add click handler to request permission
            document.addEventListener('click', function requestPermission(e) {
                if (e.target.closest('[x-show]')) {
                    Notification.requestPermission().then(permission => {
                        if (permission === 'granted') {
                            Livewire.emit('alertAdded', 'success',
                                'Notifications Enabled',
                                'You will now receive real-time alerts.'
                            );
                        }
                    });
                    document.removeEventListener('click', requestPermission);
                }
            });
        }
    });
</script>