<x-guest-layout>
    <div class="min-h-screen flex">
        <!-- Left side - Illustration -->
        <div class="hidden lg:block relative w-0 flex-1">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800">
                <!-- Animated Background -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute -top-20 -left-20 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute top-1/2 -right-40 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-10 left-1/4 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                </div>

                <!-- Registration Illustration -->
                <div class="relative h-full flex items-center justify-center p-12">
                    <div class="max-w-lg">
                        <div class="text-center">
                            <!-- Animated Book Icon -->
                            <div class="relative inline-block mb-8">
                                <div class="absolute inset-0 bg-green-400/30 rounded-full animate-ping"></div>
                                <div class="relative h-32 w-32 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center border-2 border-white/30">
                                    <svg class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Benefits -->
                            <h3 class="text-2xl font-bold text-white mb-6">Join Our Learning Community</h3>
                            
                            <div class="space-y-6 text-left">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-green-400/20 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-white">Free Access</h4>
                                        <p class="text-white/80 text-sm mt-1">Access all course materials at no cost</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-blue-400/20 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-white">Study Resources</h4>
                                        <p class="text-white/80 text-sm mt-1">Lecture notes, assignments, and past papers</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-purple-400/20 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-white">Secure Platform</h4>
                                        <p class="text-white/80 text-sm mt-1">Your account is protected with advanced security</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="grid grid-cols-3 gap-4 mt-12">
                                <div class="text-center p-4 bg-white/10 backdrop-blur-sm rounded-xl">
                                    <div class="text-2xl font-bold text-white">50+</div>
                                    <div class="text-xs text-white/70 mt-1">Course Materials</div>
                                </div>
                                <div class="text-center p-4 bg-white/10 backdrop-blur-sm rounded-xl">
                                    <div class="text-2xl font-bold text-white">24/7</div>
                                    <div class="text-xs text-white/70 mt-1">Access Available</div>
                                </div>
                                <div class="text-center p-4 bg-white/10 backdrop-blur-sm rounded-xl">
                                    <div class="text-2xl font-bold text-white">100%</div>
                                    <div class="text-xs text-white/70 mt-1">Free Access</div>
                                </div>
                            </div>

                            <!-- Security Notice -->
                            <div class="mt-8 p-3 bg-yellow-500/20 backdrop-blur-sm rounded-lg border border-yellow-400/30">
                                <p class="text-xs text-yellow-100">
                                    ⚠️ This is a student academic portal. Do not enter sensitive personal passwords used elsewhere.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side - Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Logo & Header -->
                <div class="text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start mb-8">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center justify-center shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">
                                Smart Student Portal
                            </span>
                            <span class="text-xs font-medium text-gray-500 block">Your Academic Hub</span>
                        </div>
                    </div>
                    
                    <h2 class="text-3xl font-extrabold text-gray-900">
                        Create your account
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Join our learning community today
                    </p>
                </div>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-r-lg">
                        <div class="font-medium">Whoops! Something went wrong.</div>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Registration Form -->
                <div class="mt-8">
                    <div class="mt-6">
                        <form method="POST" action="{{ route('register') }}" class="space-y-6">
                            @csrf

                            <!-- Name Field -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Full name
                                </label>
                                <div class="mt-1 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input id="name" name="name" type="text" autocomplete="name" required
                                           class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition duration-200"
                                           placeholder="Modestus Ngimba" value="{{ old('name') }}">
                                </div>
                            </div>

                            <!-- Email Field -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email address
                                </label>
                                <div class="mt-1 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input id="email" name="email" type="email" autocomplete="email" required
                                           class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition duration-200"
                                           placeholder="you@university.edu" value="{{ old('email') }}">
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Password
                                </label>
                                <div class="mt-1 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <input id="password" name="password" type="password" autocomplete="new-password" required
                                           class="appearance-none block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition duration-200"
                                           placeholder="••••••••">
                                    <button type="button" onclick="togglePassword('password')" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <svg id="eye-icon-password" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <div class="password-strength-indicator"></div>
                                    <div class="grid grid-cols-2 gap-2 mt-2 text-xs text-gray-500">
                                        <div class="flex items-center">
                                            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            8+ characters
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Letters & numbers
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Confirm Password Field -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                    Confirm password
                                </label>
                                <div class="mt-1 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                           class="appearance-none block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition duration-200"
                                           placeholder="••••••••">
                                    <button type="button" onclick="togglePassword('password_confirmation')" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <svg id="eye-icon-confirm" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Terms Agreement -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="terms" name="terms" type="checkbox" required
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                </div>
                                <div class="ml-3">
                                    <label for="terms" class="text-sm text-gray-600">
                                        I agree to the
                                        <a href="#" class="font-medium text-purple-600 hover:text-purple-500">
                                            Terms of Service
                                        </a>
                                        and
                                        <a href="#" class="font-medium text-purple-600 hover:text-purple-500">
                                            Privacy Policy
                                        </a>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button type="submit"
                                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300 transform hover:-translate-y-0.5">
                                    <span class="flex items-center">
                                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                        Create Account
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Divider -->
                    <div class="mt-8">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">Or sign up with</span>
                            </div>
                        </div>
                    </div>

                    <!-- Social Registration -->
                    <div class="mt-6">
                        <a href="#" class="w-full inline-flex items-center justify-center gap-3 py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            <span>Continue with Google</span>
                        </a>
                    </div>

                    <!-- Login Link -->
                    <div class="mt-8 text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <a href="{{ route('login') }}" 
                               class="font-medium text-purple-600 hover:text-purple-500 ml-1">
                                Sign in here
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(`eye-icon-${fieldId}`);
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`;
            } else {
                input.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            }
        }

        // Password strength indicator
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function(e) {
                const password = e.target.value;
                let strength = 0;
                if (password.length >= 8) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                strength = Math.min(strength, 5);
                
                const indicator = document.querySelector('.password-strength-indicator');
                if (indicator) {
                    indicator.className = 'password-strength-indicator';
                    let width, color;
                    switch(strength) {
                        case 0: width = '20%'; color = '#ef4444'; break;
                        case 1: width = '40%'; color = '#f59e0b'; break;
                        case 2: width = '60%'; color = '#f59e0b'; break;
                        case 3: width = '80%'; color = '#10b981'; break;
                        case 4: width = '90%'; color = '#10b981'; break;
                        default: width = '100%'; color = '#10b981';
                    }
                    indicator.style.width = width;
                    indicator.style.background = color;
                }
            });
        }
    </script>

    <style>
        .password-strength-indicator {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            margin-top: 4px;
            transition: all 0.3s ease;
        }
    </style>
</x-guest-layout>