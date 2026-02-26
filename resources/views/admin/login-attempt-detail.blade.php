<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Login Attempt Details') }}
            </h2>
            <a href="{{ route('admin.audit-log') }}" class="text-sm text-blue-600 hover:text-blue-900">
                ← Back to Audit Log
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($loginAttempt)
                        <div class="space-y-6">
                            <!-- User Info -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Name</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $loginAttempt->user->name ?? 'Unknown' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Email</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $loginAttempt->user->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Attempt Details -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Attempt Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Status</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $loginAttempt->is_successful ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $loginAttempt->is_successful ? 'Successful' : 'Failed' }}
                                        </span>
                                        @if($loginAttempt->is_suspicious)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Suspicious
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Risk Score</p>
                                        @php
                                            $riskScore = $loginAttempt->risk_score * 100;
                                            $riskColor = $riskScore >= 80 ? 'text-red-600' : 
                                                        ($riskScore >= 60 ? 'text-yellow-600' : 'text-green-600');
                                        @endphp
                                        <p class="text-sm font-medium {{ $riskColor }}">{{ number_format($riskScore, 1) }}%</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Attempted At</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $loginAttempt->attempted_at->format('M d, Y H:i:s') }}</p>
                                        <p class="text-xs text-gray-500">{{ $loginAttempt->attempted_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Technical Details -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Technical Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">IP Address</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $loginAttempt->ip_address }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Location</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $loginAttempt->location ?? 'Unknown' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Device Type</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $loginAttempt->device_type ?? 'Unknown' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Browser</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $loginAttempt->browser ?? 'Unknown' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">User Agent</p>
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $loginAttempt->user_agent ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Verification Details (if available) -->
                            @if($loginAttempt->verificationAttempt)
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Verification Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Verification Method</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $loginAttempt->verificationAttempt->verification_method ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Verification Status</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $loginAttempt->verificationAttempt->is_successful ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $loginAttempt->verificationAttempt->is_successful ? 'Successful' : 'Failed' }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Response Time</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $loginAttempt->verificationAttempt->response_time_ms ?? 'N/A' }} ms</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Risk Factors (if available) -->
                            @if($loginAttempt->risk_factors)
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Risk Factors</h3>
                                <div class="space-y-2">
                                    @if(is_array($loginAttempt->risk_factors))
                                        @foreach($loginAttempt->risk_factors as $factor)
                                            <div class="flex items-center">
                                                <span class="inline-block w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                                                <span class="text-sm text-gray-700">{{ $factor }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-sm text-gray-500">No risk factors recorded.</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">Login attempt not found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>