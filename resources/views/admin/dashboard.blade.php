<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('AI-Powered Login Detection Dashboard') }}
            </h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600 bg-primary-100 px-3 py-1 rounded-full">
                    <span class="font-medium">Role:</span> 
                    @if(auth()->user()->is_super_admin)
                        <span class="text-primary-700">Super Administrator</span>
                    @else
                        <span class="text-primary-700">Administrator</span>
                    @endif
                </span>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                        <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            System Settings
                        </a>
                        <a href="{{ route('admin.users') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            User Management
                        </a>
                        <a href="{{ route('admin.audit-log') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Audit Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Quick Stats -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Logins Today -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-800">Total Logins Today</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['todayLogins'] ?? 0 }}</p>
                            <div class="flex items-center mt-2">
                                <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                                    {{ $stats['activeUsers'] ?? 0 }} active users
                                </span>
                            </div>
                        </div>
                        <div class="bg-blue-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Suspicious Attempts -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-800">Suspicious Attempts</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['suspiciousAttempts'] ?? 0 }}</p>
                            <div class="mt-2">
                                <div class="w-full bg-red-200 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full" 
                                         style="width: {{ min(($stats['suspiciousAttempts'] / max($stats['todayLogins'], 1)) * 100, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-red-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Average Risk Score -->
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Avg Risk Score</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['avgRiskScore'] ?? 0, 1) }}%</p>
                            <div class="mt-2">
                                @if(($stats['avgRiskScore'] ?? 0) < 30)
                                    <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">Low Risk</span>
                                @elseif(($stats['avgRiskScore'] ?? 0) < 70)
                                    <span class="text-xs font-medium text-yellow-600 bg-yellow-100 px-2 py-1 rounded-full">Medium Risk</span>
                                @else
                                    <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded-full">High Risk</span>
                                @endif
                            </div>
                        </div>
                        <div class="bg-yellow-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Reviews -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-800">Pending Reviews</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pendingReviews'] ?? 0 }}</p>
                            <a href="{{ route('admin.suspicious-activities') }}" 
                               class="inline-flex items-center text-xs font-medium text-purple-600 hover:text-purple-700 mt-2">
                                Review Now
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        <div class="bg-purple-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Real-time Monitoring -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Real-time Monitoring</h3>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                                        Live
                                    </span>
                                    <button class="p-1 text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('suspicious-login-monitor')
                        </div>
                    </div>

                    <!-- Recent Suspicious Activities -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Suspicious Activities</h3>
                                <a href="{{ route('admin.suspicious-activities') }}" class="text-sm text-primary-600 hover:text-primary-700">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('recent-activities')
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-8">
                    <!-- System Health -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">System Health</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- AI Engine Status -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700">AI Detection Engine</span>
                                </div>
                                <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">Active</span>
                            </div>
                            
                            <!-- Database Status -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700">Database</span>
                                </div>
                                <span class="text-xs text-gray-500">Healthy</span>
                            </div>
                            
                            <!-- API Status -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700">API Services</span>
                                </div>
                                <span class="text-xs text-gray-500">All Systems OK</span>
                            </div>
                            
                            <!-- Storage -->
                            <div class="pt-4 border-t border-gray-100">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Storage Usage</span>
                                    <span class="font-medium text-gray-900">45%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-primary-600 h-2 rounded-full" style="width: 45%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('admin.audit-log') }}" 
                               class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-150 group">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-lg group-hover:bg-blue-200 transition duration-150">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">View Audit Log</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            
                            <a href="{{ route('admin.risk-report') }}" 
                               class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-150 group">
                                <div class="flex items-center">
                                    <div class="bg-yellow-100 p-2 rounded-lg group-hover:bg-yellow-200 transition duration-150">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Generate Risk Report</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            
                            @if(auth()->user()->is_super_admin)
                                <a href="{{ route('admin.settings') }}" 
                                   class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-150 group">
                                    <div class="flex items-center">
                                        <div class="bg-purple-100 p-2 rounded-lg group-hover:bg-purple-200 transition duration-150">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-700">System Settings</span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @endif
                            
                            <a href="{{ route('admin.users') }}" 
                               class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-150 group">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-2 rounded-lg group-hover:bg-green-200 transition duration-150">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0h-6m3 0a3 3 0 100-6 3 3 0 000 6z" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">User Management</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- AI Detection Panel -->
                    <div class="bg-gradient-to-br from-primary-50 to-blue-50 border border-primary-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">AI Detection Status</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Detection Accuracy</span>
                                    <span class="font-medium text-gray-900">98.7%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 98.7%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">False Positive Rate</span>
                                    <span class="font-medium text-gray-900">2.3%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 2.3%"></div>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-primary-100">
                                <div class="text-center">
                                    <button class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                        Run System Diagnostics
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>