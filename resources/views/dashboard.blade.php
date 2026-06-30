<x-app-layout>
    <x-slot name="header">
        <style>
            /* Prevent double scrollbars - only main content scrolls */
            html, body {
                overflow: hidden !important;
                height: 100%;
                margin: 0;
                padding: 0;
            }
            /* Ensure the app layout container doesn't add extra scroll */
            .app-layout-container {
                height: 100vh;
                overflow: hidden;
            }
            /* Ensure sidebar doesn't cause horizontal scroll */
            aside {
                overflow-x: hidden;
            }
        </style>
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="flex h-screen bg-gray-50 overflow-hidden app-layout-container" x-data="{ sidebarOpen: false }" style="height: 100vh; max-height: 100vh;">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" 
             class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-sm transition-opacity lg:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;"
             x-show="sidebarOpen">
        </div>

        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-40 w-72 bg-gray-900 text-white shadow-2xl transform transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 flex-shrink-0 overflow-y-auto overflow-x-hidden"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Sidebar Header -->
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-xl font-bold tracking-wide">SmartStudent</h1>
                <p class="text-xs text-gray-400 mt-1">AI‑Secure Portal</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1">
                <!-- Main Links -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('profile.show') }}" 
                   class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('profile.*') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                </a>

                <a href="{{ route('admin.security-logs') }}" 
                   class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.security-logs') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Security Log
                </a>

                <!-- Divider -->
                <div class="pt-4 mt-4 border-t border-gray-700">
                    <span class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Quick Actions</span>
                </div>

                <!-- Quick Actions -->
                <a href="{{ route('admin.suspicious-activities') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Review Activities
                </a>

                <a href="{{ route('admin.audit-log') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Audit Log
                </a>

                <a href="{{ route('admin.risk-report') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Risk Report
                </a>
                
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    User Management
                </a>
                
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center space-x-3">
                    <img class="w-10 h-10 rounded-full" src="{{ auth()->user()->profile_photo_url }}" alt="">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">Super Admin</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Hamburger Button -->
        <div class="lg:hidden fixed top-4 left-4 z-50">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md bg-gray-900 text-white shadow-lg hover:bg-gray-800 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto px-4 py-6 md:px-6 lg:px-8 w-full max-w-7xl mx-auto" style="overflow-y: auto; height: 100vh;">
            <!-- Page Header with Stats Bar -->
            <div class="mb-6 md:mb-8">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">AI‑Powered Login Detection Dashboard</h2>
                        <p class="text-sm text-gray-500 mt-1">Your security overview at a glance</p>
                    </div>
                    <!-- Stats Bar (mobile-friendly) -->
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            Total: {{ $stats['todayLogins'] ?? 0 }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Successful: {{ ($stats['todayLogins'] ?? 0) - ($stats['suspiciousAttempts'] ?? 0) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            Suspicious: {{ $stats['suspiciousAttempts'] ?? 0 }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stats Cards (fully responsive grid) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
                <!-- Total Logins Today -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3 md:mb-4">
                        <div class="p-2 md:p-3 bg-blue-50 rounded-xl">
                            <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $stats['todayLogins'] ?? 0 }}</h3>
                    <p class="text-xs md:text-sm text-gray-500 mt-1">Total Logins Today</p>
                </div>

                <!-- Suspicious Attempts -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3 md:mb-4">
                        <div class="p-2 md:p-3 bg-red-50 rounded-xl">
                            <svg class="w-5 h-5 md:w-6 md:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $stats['suspiciousAttempts'] ?? 0 }}</h3>
                    <p class="text-xs md:text-sm text-gray-500 mt-1">Suspicious Attempts</p>
                </div>

                <!-- Average Risk Score -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3 md:mb-4">
                        <div class="p-2 md:p-3 bg-yellow-50 rounded-xl">
                            <svg class="w-5 h-5 md:w-6 md:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium px-2 py-1 rounded-full 
                            {{ ($stats['avgRiskScore'] ?? 0) < 30 ? 'text-green-600 bg-green-50' : (($stats['avgRiskScore'] ?? 0) < 70 ? 'text-yellow-600 bg-yellow-50' : 'text-red-600 bg-red-50') }}">
                            {{ ($stats['avgRiskScore'] ?? 0) < 30 ? 'Low' : (($stats['avgRiskScore'] ?? 0) < 70 ? 'Medium' : 'High') }}
                        </span>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['avgRiskScore'] ?? 0, 1) }}%</h3>
                    <p class="text-xs md:text-sm text-gray-500 mt-1">Avg Risk Score</p>
                </div>

                <!-- Pending Reviews -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3 md:mb-4">
                        <div class="p-2 md:p-3 bg-purple-50 rounded-xl">
                            <svg class="w-5 h-5 md:w-6 md:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        @if(($stats['pendingReviews'] ?? 0) > 0)
                            <a href="{{ route('admin.suspicious-activities') }}" class="text-xs font-medium text-purple-600 hover:underline">Review</a>
                        @endif
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $stats['pendingReviews'] ?? 0 }}</h3>
                    <p class="text-xs md:text-sm text-gray-500 mt-1">Pending Reviews</p>
                </div>
            </div>

            <!-- Main Content Grid (responsive) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
                <!-- Left Column (2/3 on large screens) -->
                <div class="lg:col-span-2 space-y-6 md:space-y-8">
                    <!-- Real-time Monitoring -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-base md:text-lg font-semibold text-gray-900">Real‑time Monitoring</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                                Live
                            </span>
                        </div>
                        <div class="p-4 md:p-6">
                            <livewire:suspicious-login-monitor />
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                        <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-100">
                            <h3 class="text-base md:text-lg font-semibold text-gray-900">Recent Activities</h3>
                        </div>
                        <div class="p-4 md:p-6">
                            @livewire('recent-activities')
                        </div>
                    </div>
                </div>

                <!-- Right Column (1/3 on large screens) -->
                <div class="space-y-6 md:space-y-8">
                    <!-- AI Detection Panel -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4">AI Detection</h3>
                        <livewire:ai-detection-panel />
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>