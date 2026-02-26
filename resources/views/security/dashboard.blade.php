<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Security Operations Center') }}
            </h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600 bg-yellow-100 px-3 py-1 rounded-full">
                    <span class="font-medium">Role:</span> 
                    <span class="text-yellow-700">
                        @if(auth()->user()->role === 'security_lead') Security Lead
                        @elseif(auth()->user()->role === 'security_analyst') Security Analyst
                        @else {{ auth()->user()->role }}
                        @endif
                    </span>
                </span>
                <button onclick="toggleSystemLock()" 
                        id="lockSystemBtn"
                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition duration-150">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Lock System
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Security Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- High Risk Activities -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-800">High Risk Activities</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['highRiskActivities'] ?? 0 }}</p>
                            <div class="mt-2">
                                <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded-full">
                                    Requires Immediate Attention
                                </span>
                            </div>
                        </div>
                        <div class="bg-red-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Reviews -->
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-orange-800">Pending Reviews</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pendingReviews'] ?? 0 }}</p>
                            <a href="{{ route('admin.suspicious-activities') }}" 
                               class="inline-flex items-center text-xs font-medium text-orange-600 hover:text-orange-700 mt-2">
                                Start Review
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        <div class="bg-orange-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Recent Alerts -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-800">24h Alerts</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['recentAlerts'] ?? 0 }}</p>
                            <div class="mt-2">
                                <span class="text-xs font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                                    Last 24 hours
                                </span>
                            </div>
                        </div>
                        <div class="bg-blue-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Threat Level -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800">Threat Level</p>
                            <div class="mt-2">
                                @if(($stats['suspiciousAttempts'] ?? 0) < 5)
                                    <span class="text-2xl font-bold text-green-600">LOW</span>
                                    <div class="text-xs text-green-600 mt-1">Normal Operations</div>
                                @elseif(($stats['suspiciousAttempts'] ?? 0) < 15)
                                    <span class="text-2xl font-bold text-yellow-600">MEDIUM</span>
                                    <div class="text-xs text-yellow-600 mt-1">Increased Monitoring</div>
                                @else
                                    <span class="text-2xl font-bold text-red-600">HIGH</span>
                                    <div class="text-xs text-red-600 mt-1">Active Threat Detected</div>
                                @endif
                            </div>
                        </div>
                        <div class="bg-gray-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Active Threats -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Active Threats</h3>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-1 animate-pulse"></span>
                                        Critical
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @if(class_exists('App\Livewire\ActiveThreats'))
                                @livewire('active-threats')
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    <p class="mt-2">Active threats monitoring</p>
                                    <p class="text-sm">Component will be available soon</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Incident Timeline -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Incident Timeline</h3>
                        </div>
                        <div class="p-6">
                            @if(class_exists('App\Livewire\IncidentTimeline'))
                                @livewire('incident-timeline')
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2">Incident timeline</p>
                                    <p class="text-sm">Component will be available soon</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-8">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Security Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <!-- Block IP Address -->
                            <button onclick="showBlockIpModal()"
                                    class="w-full flex items-center justify-between p-3 bg-red-50 hover:bg-red-100 rounded-lg transition duration-150 group">
                                <div class="flex items-center">
                                    <div class="bg-red-100 p-2 rounded-lg group-hover:bg-red-200">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Block IP Address</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>

                            <!-- Require 2FA -->
                            <button onclick="showRequire2FAModal()"
                                    class="w-full flex items-center justify-between p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition duration-150 group">
                                <div class="flex items-center">
                                    <div class="bg-yellow-100 p-2 rounded-lg group-hover:bg-yellow-200">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Require 2FA</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>

                            <!-- Lock User Account -->
                            <button onclick="showLockUserModal()"
                                    class="w-full flex items-center justify-between p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition duration-150 group">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-lg group-hover:bg-blue-200">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Lock User Account</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>

                            <!-- Generate Report -->
                            <button onclick="showGenerateReportModal()"
                                    class="w-full flex items-center justify-between p-3 bg-green-50 hover:bg-green-100 rounded-lg transition duration-150 group">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-2 rounded-lg group-hover:bg-green-200">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Generate Report</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Threat Intelligence -->
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 text-white rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4">Threat Intelligence</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-300">Known Bad IPs</span>
                                <span class="text-sm font-medium" id="badIpsCount">0</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-300">Malicious Patterns</span>
                                <span class="text-sm font-medium" id="maliciousPatternsCount">0</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-300">VPN/Proxy Detection</span>
                                <span class="text-sm font-medium text-green-400">Active</span>
                            </div>
                            <div class="pt-4 border-t border-gray-700">
                                <button onclick="showUpdateThreatDbModal()" 
                                        class="w-full text-sm font-medium text-blue-400 hover:text-blue-300">
                                    Update Threat Database
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Security Tips -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Security Tips</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <p class="text-sm text-gray-600">Review high-risk activities within 1 hour of detection.</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    <p class="text-sm text-gray-600">Always verify with users before blocking accounts.</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h1m0-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm text-gray-600">Document all security actions in the incident log.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Lock Modal -->
    <div id="systemLockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Lock System</h3>
                
                <div class="mb-4">
                    <label for="lockReason" class="block text-sm font-medium text-gray-700">Reason for locking</label>
                    <textarea id="lockReason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"></textarea>
                </div>
                
                <div class="mb-6">
                    <label for="lockDuration" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                    <input type="number" id="lockDuration" value="60" min="1" max="1440" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeSystemLockModal()" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md border">
                        Cancel
                    </button>
                    <button onclick="performSystemLock()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                        Lock System
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Block IP Modal -->
    <div id="blockIpModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Block IP Address</h3>
                
                <div class="mb-4">
                    <label for="ipAddress" class="block text-sm font-medium text-gray-700">IP Address</label>
                    <input type="text" id="ipAddress" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="192.168.1.1">
                </div>
                
                <div class="mb-4">
                    <label for="blockReason" class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea id="blockReason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"></textarea>
                </div>
                
                <div class="mb-6">
                    <label for="blockDuration" class="block text-sm font-medium text-gray-700">Duration (hours)</label>
                    <input type="number" id="blockDuration" value="24" min="1" max="720" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeBlockIpModal()" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md border">
                        Cancel
                    </button>
                    <button onclick="performBlockIp()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                        Block IP
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Require 2FA Modal -->
    <div id="require2FAModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Require 2FA for User</h3>
                
                <div class="mb-4">
                    <label for="userSearch" class="block text-sm font-medium text-gray-700">Search User</label>
                    <input type="text" id="userSearch" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm" placeholder="Search by name or email" onkeyup="searchUsers(this.value)">
                    <div id="userResults" class="mt-2 max-h-40 overflow-y-auto hidden"></div>
                </div>
                
                <div class="mb-4">
                    <label for="twofaReason" class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea id="twofaReason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm"></textarea>
                </div>
                
                <div class="mb-6">
                    <label for="selectedUserId" class="block text-sm font-medium text-gray-700">Selected User</label>
                    <input type="text" id="selectedUserId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 sm:text-sm" readonly>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeRequire2FAModal()" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md border">
                        Cancel
                    </button>
                    <button onclick="performRequire2FA()" class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 rounded-md">
                        Require 2FA
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for functionality -->
    <script>
    // Get CSRF token once
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Base API URL
    const API_BASE = '/api/security';
    
    // Modal functions
    function toggleSystemLock() {
        document.getElementById('systemLockModal').classList.toggle('hidden');
    }
    
    function closeSystemLockModal() {
        document.getElementById('systemLockModal').classList.add('hidden');
    }
    
    function showBlockIpModal() {
        document.getElementById('blockIpModal').classList.remove('hidden');
    }
    
    function closeBlockIpModal() {
        document.getElementById('blockIpModal').classList.add('hidden');
    }
    
    function showRequire2FAModal() {
        document.getElementById('require2FAModal').classList.remove('hidden');
        // Clear previous selections
        document.getElementById('userSearch').value = '';
        document.getElementById('selectedUserId').value = '';
        document.getElementById('selectedUserId').dataset.userId = '';
        document.getElementById('twofaReason').value = '';
        document.getElementById('userResults').innerHTML = '';
        document.getElementById('userResults').classList.add('hidden');
    }
    
    function closeRequire2FAModal() {
        document.getElementById('require2FAModal').classList.add('hidden');
    }
    
    // Lock User Account Modal
    function showLockUserModal() {
        // Create modal dynamically
        const modalHtml = `
            <div id="lockUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Lock User Account</h3>
                        
                        <div class="mb-4">
                            <label for="lockUserSearch" class="block text-sm font-medium text-gray-700">Search User</label>
                            <input type="text" id="lockUserSearch" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Search by name or email" onkeyup="searchUsersForLock(this.value)">
                            <div id="lockUserResults" class="mt-2 max-h-40 overflow-y-auto hidden"></div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="lockUserReason" class="block text-sm font-medium text-gray-700">Reason for Locking</label>
                            <textarea id="lockUserReason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Provide reason for locking account"></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="lockUserDuration" class="block text-sm font-medium text-gray-700">Duration (hours)</label>
                            <input type="number" id="lockUserDuration" value="24" min="1" max="720" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        
                        <div class="mb-6">
                            <label for="selectedLockUserId" class="block text-sm font-medium text-gray-700">Selected User</label>
                            <input type="text" id="selectedLockUserId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 sm:text-sm" readonly>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button onclick="closeLockUserModal()" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md border">
                                Cancel
                            </button>
                            <button onclick="performLockUser()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                                Lock Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('lockUserModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    function closeLockUserModal() {
        const modal = document.getElementById('lockUserModal');
        if (modal) {
            modal.remove();
        }
    }
    
    // Generate Report Modal
    function showGenerateReportModal() {
        const today = new Date().toISOString().split('T')[0];
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        const weekAgoStr = weekAgo.toISOString().split('T')[0];
        
        const modalHtml = `
            <div id="generateReportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Generate Security Report</h3>
                        
                        <div class="mb-4">
                            <label for="reportType" class="block text-sm font-medium text-gray-700">Report Type</label>
                            <select id="reportType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                <option value="security">Security Overview</option>
                                <option value="threats">Threat Analysis</option>
                                <option value="users">User Activity</option>
                                <option value="comprehensive">Comprehensive Report</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="reportStartDate" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" id="reportStartDate" value="${weekAgoStr}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        
                        <div class="mb-4">
                            <label for="reportEndDate" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" id="reportEndDate" value="${today}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        
                        <div class="mb-6">
                            <label for="reportFormat" class="block text-sm font-medium text-gray-700">Format</label>
                                <select id="reportFormat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                <option value="pdf">PDF</option>
                                <option value="csv">CSV</option>
                                <option value="json">JSON</option>
                            </select>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button onclick="closeGenerateReportModal()" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md border">
                                Cancel
                            </button>
                            <button onclick="performGenerateReport()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md">
                                Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        const existingModal = document.getElementById('generateReportModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    function closeGenerateReportModal() {
        const modal = document.getElementById('generateReportModal');
        if (modal) {
            modal.remove();
        }
    }
    
    // Update Threat Database Modal
    function showUpdateThreatDbModal() {
        const modalHtml = `
            <div id="updateThreatDbModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Update Threat Database</h3>
                        
                        <div class="mb-4">
                            <label for="threatType" class="block text-sm font-medium text-gray-700">Threat Type</label>
                            <select id="threatType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                <option value="ip">IP Address</option>
                                <option value="pattern">Attack Pattern</option>
                                <option value="behavior">Suspicious Behavior</option>
                                <option value="malware">Malware Signature</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="threatAction" class="block text-sm font-medium text-gray-700">Action</label>
                            <select id="threatAction" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                <option value="add">Add to Database</option>
                                <option value="update">Update Existing</option>
                                <option value="remove">Remove from Database</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="threatData" class="block text-sm font-medium text-gray-700">Threat Data (JSON)</label>
                            <textarea id="threatData" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder='{"ip": "192.168.1.1", "reason": "Brute force attack"}'></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button onclick="closeUpdateThreatDbModal()" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md border">
                                Cancel
                            </button>
                            <button onclick="performUpdateThreatDb()" class="px-4 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-md">
                                Update Database
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        const existingModal = document.getElementById('updateThreatDbModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    function closeUpdateThreatDbModal() {
        const modal = document.getElementById('updateThreatDbModal');
        if (modal) {
            modal.remove();
        }
    }
    
    // API Functions
    async function performSystemLock() {
        const reason = document.getElementById('lockReason').value;
        const duration = document.getElementById('lockDuration').value;
        
        if (!reason || !duration) {
            alert('Please provide reason and duration');
            return;
        }
        
        try {
            const response = await fetch(`${API_BASE}/lock-system`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    reason: reason,
                    duration_minutes: duration
                })
            });
            
            if (response.status === 401) {
                alert('Unauthorized. Please log in again.');
                window.location.href = '/login';
                return;
            }
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                alert('System locked successfully');
                closeSystemLockModal();
                // Update button text
                document.getElementById('lockSystemBtn').innerHTML = `
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                    Unlock System
                `;
                document.getElementById('lockSystemBtn').onclick = function() {
                    unlockSystem();
                };
            } else {
                alert(result.message || 'Failed to lock system');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error locking system: ' + error.message);
        }
    }
    
    async function unlockSystem() {
        try {
            const response = await fetch(`${API_BASE}/unlock-system`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (response.status === 401) {
                alert('Unauthorized. Please log in again.');
                window.location.href = '/login';
                return;
            }
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                alert('System unlocked successfully');
                // Update button text
                document.getElementById('lockSystemBtn').innerHTML = `
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Lock System
                `;
                document.getElementById('lockSystemBtn').onclick = toggleSystemLock;
            } else {
                alert(result.message || 'Failed to unlock system');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error unlocking system: ' + error.message);
        }
    }
    
    async function performBlockIp() {
        const ip = document.getElementById('ipAddress').value;
        const reason = document.getElementById('blockReason').value;
        const duration = document.getElementById('blockDuration').value;
        
        if (!ip || !reason || !duration) {
            alert('Please fill all fields');
            return;
        }
        
        try {
            const response = await fetch(`${API_BASE}/block-ip`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    ip_address: ip,
                    reason: reason,
                    duration_hours: duration
                })
            });
            
            if (response.status === 401) {
                alert('Unauthorized. Please log in again.');
                window.location.href = '/login';
                return;
            }
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                alert(`IP ${ip} blocked successfully. Affected attempts: ${result.affected_attempts || 0}`);
                closeBlockIpModal();
                // Update threat intelligence counts
                updateThreatCounts();
            } else {
                alert(result.message || 'Failed to block IP');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error blocking IP: ' + error.message);
        }
    }
    
    // User search function
    async function searchUsers(query, type = '2fa') {
        if (query.length < 2) {
            const resultsDiv = document.getElementById(type === 'lock' ? 'lockUserResults' : 'userResults');
            if (resultsDiv) {
                resultsDiv.classList.add('hidden');
            }
            return;
        }
        
        try {
            const response = await fetch(`/api/security/users/search?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (response.status === 401) {
                alert('Unauthorized. Please log in again.');
                window.location.href = '/login';
                return;
            }
            
            if (!response.ok) {
                // Try to get response text to see what's wrong
                const text = await response.text();
                console.error('Raw error response:', text.substring(0, 500));
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const users = await response.json();
            
            const resultsDiv = document.getElementById(type === 'lock' ? 'lockUserResults' : 'userResults');
            if (!resultsDiv) return;
            
            resultsDiv.innerHTML = '';
            
            if (!users || users.length === 0) {
                resultsDiv.innerHTML = '<div class="p-2 text-sm text-gray-500">No users found</div>';
            } else {
                users.forEach(user => {
                    const div = document.createElement('div');
                    div.className = 'p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200';
                    div.innerHTML = `
                        <div class="font-medium">${user.name}</div>
                        <div class="text-xs text-gray-500">${user.email}</div>
                        <div class="text-xs ${user.is_locked ? 'text-red-500' : 'text-green-500'}">
                            ${user.is_locked ? '🔒 Locked' : '✓ Active'}
                        </div>
                    `;
                    div.onclick = () => selectUser(user, type);
                    resultsDiv.appendChild(div);
                });
            }
            
            resultsDiv.classList.remove('hidden');
        } catch (error) {
            console.error('Error searching users:', error);
            const resultsDiv = document.getElementById(type === 'lock' ? 'lockUserResults' : 'userResults');
            if (resultsDiv) {
                resultsDiv.innerHTML = '<div class="p-2 text-sm text-red-500">Error: ' + error.message + '</div>';
                resultsDiv.classList.remove('hidden');
            }
        }
    }
    
    // Modified search function for lock user modal
    function searchUsersForLock(query) {
        searchUsers(query, 'lock');
    }
    
    // Select user function
    function selectUser(user, type = '2fa') {
        if (type === 'lock') {
            document.getElementById('selectedLockUserId').value = `${user.name} (${user.email})`;
            document.getElementById('selectedLockUserId').dataset.userId = user.id;
            document.getElementById('lockUserResults').classList.add('hidden');
        } else {
            document.getElementById('selectedUserId').value = `${user.name} (${user.email})`;
            document.getElementById('selectedUserId').dataset.userId = user.id;
            document.getElementById('userResults').classList.add('hidden');
        }
    }
    
    async function performRequire2FA() {
        const userId = document.getElementById('selectedUserId').dataset.userId;
        const reason = document.getElementById('twofaReason').value;
        
        if (!userId || !reason) {
            alert('Please select a user and provide reason');
            return;
        }
        
        try {
            const response = await fetch(`${API_BASE}/require-2fa`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    user_id: userId,
                    reason: reason
                })
            });
            
            if (response.status === 401) {
                alert('Unauthorized. Please log in again.');
                window.location.href = '/login';
                return;
            }
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                alert(`2FA required for ${result.user.name}`);
                closeRequire2FAModal();
            } else {
                alert(result.message || 'Failed to require 2FA');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error requiring 2FA: ' + error.message);
        }
    }
    
    async function performLockUser() {
        const userId = document.getElementById('selectedLockUserId').dataset.userId;
        const reason = document.getElementById('lockUserReason').value;
        const duration = document.getElementById('lockUserDuration').value;
        
        if (!userId || !reason || !duration) {
            alert('Please select a user, provide reason and duration');
            return;
        }
        
        try {
            const response = await fetch(`${API_BASE}/lock-user`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    user_id: userId,
                    reason: reason,
                    duration_hours: duration
                })
            });
            
            if (response.status === 401) {
                alert('Unauthorized. Please log in again.');
                window.location.href = '/login';
                return;
            }
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                alert(`User ${result.user.name} locked successfully`);
                closeLockUserModal();
            } else {
                alert(result.message || 'Failed to lock user account');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error locking user account: ' + error.message);
        }
    }
    
    async function performGenerateReport() {
        const reportType = document.getElementById('reportType').value;
        const startDate = document.getElementById('reportStartDate').value;
        const endDate = document.getElementById('reportEndDate').value;
        const format = document.getElementById('reportFormat').value;
        
        if (!startDate || !endDate) {
            alert('Please select start and end dates');
            return;
        }
        
        try {
            const response = await fetch(`${API_BASE}/generate-report`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    report_type: reportType,
                    start_date: startDate,
                    end_date: endDate,
                    format: format
                })
            });
            
            if (response.status === 401) {
                alert('Unauthorized. Please log in again.');
                window.location.href = '/login';
                return;
            }
            
            if (format === 'json') {
                const result = await response.json();
                if (result.success) {
                    alert('Report generated successfully. Check console for JSON data.');
                    console.log('Report Data:', result.report);
                    closeGenerateReportModal();
                } else {
                    alert(result.message || 'Failed to generate report');
                }
            } else {
                // For PDF/CSV, check if response is HTML (error)
                const contentType = response.headers.get('content-type');
                
                if (contentType && contentType.includes('application/json')) {
                    // Response is JSON (error)
                    const result = await response.json();
                    alert(result.message || 'Failed to generate report');
                    return;
                }
                
                // It's a file, trigger download
                const blob = await response.blob();
                
                // Quick check if blob is HTML
                if (blob.size < 1000) { // Small file might be error
                    const text = await blob.text();
                    if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                        alert('Error: Server returned HTML instead of file. Check console for details.');
                        console.error('Server error:', text.substring(0, 500));
                        return;
                    }
                }
                
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `security-report-${reportType}-${new Date().toISOString().split('T')[0]}.${format}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                closeGenerateReportModal();
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error generating report: ' + error.message);
        }
    }
    
    async function performUpdateThreatDb() {
        const threatType = document.getElementById('threatType').value;
        const action = document.getElementById('threatAction').value;
        const threatData = document.getElementById('threatData').value;
        
        if (!threatData) {
            alert('Please provide threat data');
            return;
        }
        
        try {
            const parsedData = JSON.parse(threatData);
            
            const response = await fetch(`${API_BASE}/update-threat-db`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    threat_type: threatType,
                    action: action,
                    data: parsedData
                })
            });
            
            if (response.status === 401) {
                alert('Unauthorized. Please log in again.');
                window.location.href = '/login';
                return;
            }
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                alert('Threat database updated successfully');
                closeUpdateThreatDbModal();
                updateThreatCounts();
            } else {
                alert(result.message || 'Failed to update threat database');
            }
        } catch (error) {
            console.error('Error:', error);
            if (error instanceof SyntaxError) {
                alert('Invalid JSON format for threat data');
            } else {
                alert('Error updating threat database: ' + error.message);
            }
        }
    }
    
    async function updateThreatCounts() {
        try {
            // Update blocked IPs count
            const blockedResponse = await fetch(`${API_BASE}/blocked-ips`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (blockedResponse.ok) {
                const blockedData = await blockedResponse.json();
                if (blockedData.success) {
                    document.getElementById('badIpsCount').textContent = blockedData.blocked_ips?.length || 0;
                }
            }
        } catch (error) {
            console.error('Error updating threat counts:', error);
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateThreatCounts();
        
        // Check if system is locked
        fetch(`${API_BASE}/system-lock-status`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.is_locked) {
                document.getElementById('lockSystemBtn').innerHTML = `
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                    Unlock System
                `;
                document.getElementById('lockSystemBtn').onclick = unlockSystem;
            }
        })
        .catch(error => {
            console.error('Error checking system lock status:', error);
        });
    });
    </script>
</x-app-layout>