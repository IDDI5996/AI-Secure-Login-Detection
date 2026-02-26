<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="AI-powered suspicious login detection system for securing user authentication activities">
    <title>AI Login Detection System - Secure Your Authentication</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🛡️</text></svg>">
    
    <!-- Vite CSS -->
    @vite(['resources/css/app.css'])
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .gradient-bg {
            background: linear-gradient(-45deg, #3b82f6, #2563eb, #1d4ed8, #1e40af);
            background-size: 400% 400%;
            animation: gradient-shift 15s ease infinite;
        }
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        .glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        .glass-morphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .text-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300" x-data="{ scrolled: false, mobileMenu: false }" 
         @scroll.window="scrolled = window.scrollY > 10">
        <div :class="scrolled ? 'glass-morphism shadow-lg' : 'bg-transparent'" 
             class="transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <span class="text-xl font-bold bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                                AISecure
                            </span>
                            <span class="text-xs font-medium text-gray-500 block">Login Detection</span>
                        </div>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#features" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200">
                            Features
                        </a>
                        <a href="#how-it-works" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200">
                            How It Works
                        </a>
                        <a href="#pricing" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200">
                            Pricing
                        </a>
                        <a href="#contact" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200">
                            Contact
                        </a>
                    </div>

                    <!-- Auth Buttons -->
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="{{ route('login') }}" 
                           class="px-5 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors duration-200">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" 
                           class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg hover:shadow-lg hover:shadow-primary-500/25 transition-all duration-300">
                            Get Started Free
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenu" x-transition class="md:hidden border-t border-gray-200">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="#features" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
                        Features
                    </a>
                    <a href="#how-it-works" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
                        How It Works
                    </a>
                    <a href="#pricing" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
                        Pricing
                    </a>
                    <a href="#contact" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
                        Contact
                    </a>
                    <div class="pt-4 border-t border-gray-200">
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" 
                           class="block px-3 py-2 mt-2 rounded-lg text-white bg-primary-600 hover:bg-primary-700 text-center">
                            Get Started Free
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 md:pt-40 md:pb-28 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-20 left-10 w-72 h-72 bg-gradient-to-r from-primary-400/20 to-primary-600/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-gradient-to-l from-primary-400/20 to-primary-600/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <!-- Animated Badge -->
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-primary-50 to-blue-50 border border-primary-100 mb-8 glow">
                    <span class="w-2 h-2 bg-primary-500 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm font-medium text-primary-700">AI-Powered Security Platform</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight mb-6">
                    <span class="block">Secure Your</span>
                    <span class="text-gradient">Authentication Activities</span>
                    <span class="block">with AI Intelligence</span>
                </h1>

                <!-- Subheading -->
                <p class="text-xl md:text-2xl text-gray-600 max-w-3xl mx-auto mb-10">
                    Advanced suspicious login detection system that learns user behavior patterns and prevents unauthorized access in real-time.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
                    <a href="{{ route('register') }}" 
                       class="px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl hover:shadow-xl hover:shadow-primary-500/30 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-center">
                            <span>Start Free Trial</span>
                            <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </div>
                    </a>
                    <a href="#demo" 
                       class="px-8 py-4 text-lg font-semibold text-primary-600 bg-white border-2 border-primary-200 rounded-xl hover:border-primary-300 hover:bg-primary-50 transition-all duration-300">
                        <div class="flex items-center justify-center">
                            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Watch Demo</span>
                        </div>
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-2xl mx-auto">
                    <div class="text-center p-6 bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200/50">
                        <div class="text-3xl font-bold text-primary-600 mb-2">99.9%</div>
                        <div class="text-sm text-gray-600">Detection Accuracy</div>
                    </div>
                    <div class="text-center p-6 bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200/50">
                        <div class="text-3xl font-bold text-primary-600 mb-2">50ms</div>
                        <div class="text-sm text-gray-600">Real-time Response</div>
                    </div>
                    <div class="text-center p-6 bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200/50">
                        <div class="text-3xl font-bold text-primary-600 mb-2">24/7</div>
                        <div class="text-sm text-gray-600">Active Monitoring</div>
                    </div>
                    <div class="text-center p-6 bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200/50">
                        <div class="text-3xl font-bold text-primary-600 mb-2">1000+</div>
                        <div class="text-sm text-gray-600">Protected Logins</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Dashboard Preview -->
        <div class="max-w-6xl mx-auto px-4 mt-20 relative">
            <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-gray-200/50 float-animation">
                <div class="bg-gradient-to-r from-gray-900 to-gray-800 p-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-50 to-white p-8">
                    <!-- Mock Dashboard Content -->
                    <div class="grid grid-cols-4 gap-6 mb-8">
                        <div class="col-span-4 md:col-span-1 bg-gradient-to-br from-primary-500 to-primary-600 text-white p-6 rounded-xl">
                            <div class="text-3xl font-bold">1,247</div>
                            <div class="text-sm opacity-90">Today's Logins</div>
                        </div>
                        <div class="col-span-4 md:col-span-1 bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-xl">
                            <div class="text-3xl font-bold">12</div>
                            <div class="text-sm opacity-90">Suspicious</div>
                        </div>
                        <div class="col-span-4 md:col-span-1 bg-gradient-to-br from-yellow-500 to-yellow-600 text-white p-6 rounded-xl">
                            <div class="text-3xl font-bold">98.7%</div>
                            <div class="text-sm opacity-90">Accuracy</div>
                        </div>
                        <div class="col-span-4 md:col-span-1 bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-xl">
                            <div class="text-3xl font-bold">0.3s</div>
                            <div class="text-sm opacity-90">Avg Response</div>
                        </div>
                    </div>
                    <!-- AI Risk Visualization -->
                    <div class="relative h-4 bg-gradient-to-r from-green-400 via-yellow-400 to-red-500 rounded-full overflow-hidden">
                        <div class="absolute left-1/4 top-0 w-2 h-6 -mt-1 bg-white rounded-full shadow-lg"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gradient-to-b from-white to-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-primary-50 text-primary-700 font-medium mb-4">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Powerful Features
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Advanced Security <span class="text-gradient">Features</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Comprehensive protection with cutting-edge AI technology
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group p-8 bg-white rounded-2xl border border-gray-200 hover:border-primary-300 hover:shadow-2xl hover:shadow-primary-500/10 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">AI Behavior Analysis</h3>
                    <p class="text-gray-600 mb-6">
                        Machine learning algorithms analyze user behavior patterns to detect anomalies and prevent unauthorized access.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Real-time pattern recognition
                        </li>
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Adaptive learning algorithms
                        </li>
                    </ul>
                </div>

                <!-- Feature 2 -->
                <div class="group p-8 bg-white rounded-2xl border border-gray-200 hover:border-primary-300 hover:shadow-2xl hover:shadow-primary-500/10 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Real-time Monitoring</h3>
                    <p class="text-gray-600 mb-6">
                        Continuous monitoring of login activities with instant alerts and automated response mechanisms.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Live dashboard updates
                        </li>
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            WebSocket notifications
                        </li>
                    </ul>
                </div>

                <!-- Feature 3 -->
                <div class="group p-8 bg-white rounded-2xl border border-gray-200 hover:border-primary-300 hover:shadow-2xl hover:shadow-primary-500/10 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Multi-factor Verification</h3>
                    <p class="text-gray-600 mb-6">
                        Multiple verification methods including 2FA, email codes, and security questions for suspicious activities.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Multiple verification options
                        </li>
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Custom security policies
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-64 bg-white/5 rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                Ready to Secure Your Authentication?
            </h2>
            <p class="text-xl text-white/90 mb-10 max-w-2xl mx-auto">
                Join thousands of companies protecting their users with AI-powered login detection.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" 
                   class="px-8 py-4 text-lg font-semibold text-primary-600 bg-white rounded-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    Start Free 14-Day Trial
                </a>
                <a href="#contact" 
                   class="px-8 py-4 text-lg font-semibold text-white border-2 border-white/30 rounded-xl hover:bg-white/10 transition-all duration-300">
                    Schedule a Demo
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <span class="text-xl font-bold">AISecure</span>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm">
                        AI-powered suspicious login detection system for securing user authentication activities.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-lg mb-6">Product</h4>
                    <ul class="space-y-3">
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#how-it-works" class="text-gray-400 hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="#pricing" class="text-gray-400 hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#api" class="text-gray-400 hover:text-white transition-colors">API</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-lg mb-6">Company</h4>
                    <ul class="space-y-3">
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#careers" class="text-gray-400 hover:text-white transition-colors">Careers</a></li>
                        <li><a href="#blog" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-lg mb-6">Legal</h4>
                    <ul class="space-y-3">
                        <li><a href="#privacy" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#terms" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#security" class="text-gray-400 hover:text-white transition-colors">Security</a></li>
                        <li><a href="#compliance" class="text-gray-400 hover:text-white transition-colors">Compliance</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} AISecure Login Detection System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Vite JS -->
    @vite(['resources/js/app.js'])
    
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Parallax effect
        document.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.float-animation');
            if (parallax) {
                parallax.style.transform = `translateY(${scrolled * 0.05}px)`;
            }
        });
    </script>
</body>
</html>