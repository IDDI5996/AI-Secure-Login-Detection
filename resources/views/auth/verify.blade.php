<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500 mx-auto" />
                </a>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Verify Your Identity</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Additional verification is required for this login attempt.
                </p>
                
                <!-- Risk Indicator -->
                @if(isset($riskScore))
                    <div class="mt-4 inline-flex items-center px-4 py-2 rounded-full text-sm font-medium 
                               {{ $riskScore >= 80 ? 'bg-red-100 text-red-800' : 
                                  ($riskScore >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                        <svg class="mr-1.5 h-2 w-2 {{ $riskScore >= 80 ? 'text-red-400' : 
                                                      ($riskScore >= 60 ? 'text-yellow-400' : 'text-blue-400') }}" 
                             fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        Risk Score: {{ $riskScore }}%
                    </div>
                @endif
            </div>

            <!-- Alert for Suspicious Activity -->
            @if(isset($reasons) && count($reasons) > 0)
                <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Suspicious Activity Detected</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach($reasons as $reason)
                                        <li>{{ $reason }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Verification Methods -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Choose Verification Method</h3>
                
                <!-- 2FA Option -->
                <div x-data="{ show2fa: false }" class="border rounded-lg p-4 hover:border-primary-300 transition duration-150">
                    <button @click="show2fa = !show2fa" 
                            class="w-full flex items-center justify-between text-left">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-md bg-blue-100">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">Authenticator App</h4>
                                <p class="text-sm text-gray-500">Use Google Authenticator or similar app</p>
                            </div>
                        </div>
                        <svg :class="show2fa ? 'rotate-180' : ''" 
                             class="h-5 w-5 text-gray-400 transition-transform duration-200" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <!-- 2FA Form -->
                    <div x-show="show2fa" x-transition class="mt-4">
                        <form method="POST" action="{{ route('verify.2fa') }}">
                            @csrf
                            <input type="hidden" name="login_attempt_id" value="{{ $loginAttemptId ?? '' }}">
                            
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700">
                                    Enter 6-digit code
                                </label>
                                <div class="mt-1">
                                    <input id="code" name="code" type="text" 
                                           inputmode="numeric" pattern="[0-9]*" maxlength="6"
                                           class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md text-center text-2xl tracking-widest"
                                           placeholder="000000" required autofocus>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" 
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    Verify with 2FA
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Email Verification Option -->
                <div x-data="{ showEmail: false }" class="border rounded-lg p-4 hover:border-primary-300 transition duration-150">
                    <button @click="showEmail = !showEmail" 
                            class="w-full flex items-center justify-between text-left">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-md bg-green-100">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">Email Verification</h4>
                                <p class="text-sm text-gray-500">We'll send a code to your email</p>
                            </div>
                        </div>
                        <svg :class="showEmail ? 'rotate-180' : ''" 
                             class="h-5 w-5 text-gray-400 transition-transform duration-200" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <!-- Email Form -->
                    <div x-show="showEmail" x-transition class="mt-4">
                        <form method="POST" action="{{ route('verify.email') }}">
                            @csrf
                            <input type="hidden" name="login_attempt_id" value="{{ $loginAttemptId ?? '' }}">
                            
                            <p class="text-sm text-gray-600 mb-4">
                                A verification code will be sent to: 
                                <span class="font-medium">{{ auth()->user()->email ?? 'your email' }}</span>
                            </p>
                            
                            <button type="submit" 
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Send Verification Code
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Security Questions Option -->
                <div x-data="{ showQuestions: false }" class="border rounded-lg p-4 hover:border-primary-300 transition duration-150">
                    <button @click="showQuestions = !showQuestions" 
                            class="w-full flex items-center justify-between text-left">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-md bg-purple-100">
                                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">Security Questions</h4>
                                <p class="text-sm text-gray-500">Answer your security questions</p>
                            </div>
                        </div>
                        <svg :class="showQuestions ? 'rotate-180' : ''" 
                             class="h-5 w-5 text-gray-400 transition-transform duration-200" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <!-- Questions Form -->
                    <div x-show="showQuestions" x-transition class="mt-4">
                        <form method="POST" action="{{ route('verify.questions') }}">
                            @csrf
                            <input type="hidden" name="login_attempt_id" value="{{ $loginAttemptId ?? '' }}">
                            
                            <div class="space-y-4">
                                @foreach($securityQuestions ?? [] as $question)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ $question['question'] }}
                                        </label>
                                        <div class="mt-1">
                                            <input type="hidden" name="questions[{{ $loop->index }}][id]" value="{{ $question['id'] }}">
                                            <input type="text" name="questions[{{ $loop->index }}][answer]" 
                                                   class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                   required>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" 
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    Submit Answers
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Alternative Actions -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Not your account?
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="font-medium text-primary-600 hover:text-primary-500">
                            Sign out
                        </a>
                    </p>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alpine.js for toggling -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-guest-layout>