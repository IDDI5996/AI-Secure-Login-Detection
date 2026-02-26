<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Security Dashboard') }}
            </h2>
            <div class="flex items-center space-x-4">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900">
                        <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                            <span class="text-primary-600 font-semibold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </span>
                        </div>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            My Profile
                        </a>
                        <a href="{{ route('user.security') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Security Settings
                        </a>
                        <a href="{{ route('user.activity') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            My Activity
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome & Security Status -->
            <div class="mb-8">
                <div class="bg-gradient-to-r from-primary-50 to-blue-50 border border-primary-200 rounded-xl p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h3>
                            <p class="text-gray-600 mt-2">Your account security is being monitored 24/7 by our AI system.</p>
                            <div class="flex items-center mt-4 space-x-4">
                                @if(auth()->user()->two_factor_enabled)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        2FA Enabled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                        Enable 2FA
                                    </span>
                                @endif
                                
                                @if($suspiciousCount > 0)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                        {{ $suspiciousCount }} Suspicious Activities
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        No Suspicious Activities
                                    </span>
                                @endif

                                @if($failedAttempts > 0)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $failedAttempts }} Failed Logins
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <a href="{{ route('user.security') }}" 
                               class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Security Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Recent Login Activity -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Login Activity</h3>
                                <a href="{{ route('user.activity') }}" class="text-sm text-primary-600 hover:text-primary-700">
                                    View All Activity
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @forelse($recentLogins as $login)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                @if($login->is_suspicious)
                                                    <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                        </svg>
                                                    </div>
                                                @elseif($login->is_successful)
                                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $login->city ?? 'Unknown' }}, {{ $login->country ?? 'Unknown' }}
                                                    @if($login->ip_address)
                                                        <span class="text-xs text-gray-500 ml-2">({{ $login->ip_address }})</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $login->device_type ?? 'Unknown device' }} • {{ $login->browser ?? 'Unknown browser' }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ $login->attempted_at->format('M d, Y h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if($login->is_suspicious)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Suspicious
                                                </span>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Risk: {{ number_format($login->risk_score * 100, 1) }}%
                                                </p>
                                            @elseif($login->is_successful)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Successful
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Failed
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No recent login activity found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Security Health Check -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Security Health Check</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-6">
                                <!-- Password Strength -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Password Strength</p>
                                            <p class="text-sm text-gray-500">Last changed {{ $passwordAge ?? 'recently' }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        @if($passwordStrength == 'strong')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                Strong
                                            </span>
                                        @elseif($passwordStrength == 'medium')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                Medium
                                            </span>
                                            <a href="{{ route('profile.show') }}" class="block text-xs text-primary-600 hover:text-primary-700 mt-1">
                                                Improve
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                Weak
                                            </span>
                                            <a href="{{ route('profile.show') }}" class="block text-xs text-primary-600 hover:text-primary-700 mt-1">
                                                Change Now
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Two-Factor Authentication -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full @if(auth()->user()->two_factor_enabled) bg-green-100 @else bg-yellow-100 @endif flex items-center justify-center mr-4">
                                            <svg class="h-5 w-5 @if(auth()->user()->two_factor_enabled) text-green-600 @else text-yellow-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Two-Factor Authentication</p>
                                            <p class="text-sm text-gray-500">Add extra security to your account</p>
                                        </div>
                                    </div>
                                    <div>
                                        @if(auth()->user()->two_factor_enabled)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                Enabled
                                            </span>
                                        @else
                                            <a href="{{ route('two-factor.enable') }}" 
                                               class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                                                Enable 2FA
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Email Verification -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full @if(auth()->user()->hasVerifiedEmail()) bg-green-100 @else bg-red-100 @endif flex items-center justify-center mr-4">
                                            <svg class="h-5 w-5 @if(auth()->user()->hasVerifiedEmail()) text-green-600 @else text-red-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Email Verification</p>
                                            <p class="text-sm text-gray-500">Verify your email address</p>
                                        </div>
                                    </div>
                                    <div>
                                        @if(auth()->user()->hasVerifiedEmail())
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                Verified
                                            </span>
                                        @else
                                            <form method="POST" action="{{ route('verification.send') }}">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 hover:bg-red-200">
                                                    Verify Email
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-8">
                    <!-- Your Devices -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Your Devices</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @if(is_array($devices) && count($devices) > 0)
                                    @foreach($devices as $device)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                                            <div class="flex items-center">
                                                @if($device['device_type'] === 'Mobile')
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                @endif
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900">{{ $device['device_type'] ?? 'Unknown' }}</p>
                                                    <p class="text-xs text-gray-500">{{ $device['browser'] ?? 'Unknown' }} on {{ $device['platform'] ?? 'Unknown' }}</p>
                                                    @if(isset($device['current']) && $device['current'])
                                                        <span class="text-xs text-green-600 font-medium">Current Device</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500">
                                                @if(isset($device['last_used']))
                                                    {{ \Carbon\Carbon::parse($device['last_used'])->diffForHumans() }}
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No registered devices found.</p>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-6">
                                <a href="{{ route('profile.sessions') }}" 
                                   class="block w-full text-center text-sm font-medium text-primary-600 hover:text-primary-700 py-2 border border-primary-200 rounded-lg hover:bg-primary-50 transition duration-150">
                                    Manage Devices
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('user.security') }}" 
                               class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-150 group">
                                <div class="flex items-center">
                                    <div class="bg-primary-100 p-2 rounded-lg group-hover:bg-primary-200">
                                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Update Password</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            <a href="{{ route('user.activity') }}" 
                               class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-150 group">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-lg group-hover:bg-blue-200">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">View Activity Log</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            @if(auth()->user()->two_factor_enabled)
                                <a href="{{ route('two-factor.disable') }}" 
                                   class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-150 group">
                                    <div class="flex items-center">
                                        <div class="bg-yellow-100 p-2 rounded-lg group-hover:bg-yellow-200">
                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-700">Disable 2FA</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('two-factor.enable') }}" 
                                   class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-150 group">
                                    <div class="flex items-center">
                                        <div class="bg-green-100 p-2 rounded-lg group-hover:bg-green-200">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-700">Enable 2FA</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @endif

                            <a href="{{ route('profile.show') }}" 
                               class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-150 group">
                                <div class="flex items-center">
                                    <div class="bg-purple-100 p-2 rounded-lg group-hover:bg-purple-200">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Edit Profile</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Security Tips -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Security Tips for You</h3>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Enable Two-Factor Authentication</p>
                                    <p class="text-sm text-gray-600 mt-1">Add an extra layer of security to your account.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Review Your Login History</p>
                                    <p class="text-sm text-gray-600 mt-1">Regularly check for unfamiliar login attempts.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Update Your Security Questions</p>
                                    <p class="text-sm text-gray-600 mt-1">Keep your security questions up to date.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Use Strong Passwords</p>
                                    <p class="text-sm text-gray-600 mt-1">Change your password every 90 days.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>