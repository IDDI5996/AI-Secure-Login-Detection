<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Smart Student Portal - Access your courses, materials, and academic resources in one place">
    <title>Smart Student Portal - Your Academic Hub</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📚</text></svg>">
    
    <!-- Vite CSS -->
    @vite(['resources/css/app.css'])
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        .animate-fade-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        .animate-scale {
            animation: scaleIn 0.4s ease-out forwards;
        }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-bg-alt {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .text-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
        }
        .feature-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 via-white to-gray-100">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300" x-data="{ scrolled: false, mobileMenu: false }" 
         @scroll.window="scrolled = window.scrollY > 10">
        <div :class="scrolled ? 'glass-card shadow-lg' : 'bg-transparent'" 
             class="transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-xl gradient-bg flex items-center justify-center shadow-lg">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <span class="text-xl font-bold text-gradient-primary">Smart Student Portal</span>
                            <span class="text-xs text-gray-500 block">Your Academic Hub</span>
                        </div>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#features" class="text-gray-700 hover:text-purple-600 font-medium transition-colors duration-200">
                            Features
                        </a>
                        <a href="#about" class="text-gray-700 hover:text-purple-600 font-medium transition-colors duration-200">
                            About
                        </a>
                        <a href="#resources" class="text-gray-700 hover:text-purple-600 font-medium transition-colors duration-200">
                            Resources
                        </a>
                        <a href="#faq" class="text-gray-700 hover:text-purple-600 font-medium transition-colors duration-200">
                            FAQ
                        </a>
                    </div>

                    <!-- Auth Buttons -->
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="{{ route('login') }}" 
                           class="px-5 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors duration-200">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" 
                           class="px-5 py-2.5 text-sm font-medium text-white gradient-bg rounded-lg hover:shadow-lg hover:shadow-purple-500/25 transition-all duration-300">
                            Sign Up
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenu" x-transition class="md:hidden border-t border-gray-200 bg-white">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="#features" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Features</a>
                    <a href="#about" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">About</a>
                    <a href="#resources" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Resources</a>
                    <a href="#faq" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">FAQ</a>
                    <div class="pt-4 border-t border-gray-200">
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Sign In</a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 mt-2 rounded-lg text-white gradient-bg text-center">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-32 pb-20 md:pt-40 md:pb-28">
        <!-- Animated Background -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-20 left-10 w-72 h-72 bg-purple-200/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-pink-200/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-full max-w-4xl bg-gradient-to-r from-purple-100/10 to-pink-100/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <!-- Animated Badge -->
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-100 mb-8 animate-scale">
                    <span class="w-2 h-2 bg-purple-500 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm font-medium text-purple-700">🎓 Welcome to Your Academic Hub</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight mb-6 animate-fade-up">
                    <span class="block text-gray-900">Smart</span>
                    <span class="text-gradient-primary">Student Portal</span>
                </h1>

                <!-- Subheading -->
                <p class="text-xl md:text-2xl text-gray-600 max-w-3xl mx-auto mb-8 animate-fade-up delay-100">
                    Access your courses, materials, and academic resources in one place.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16 animate-fade-up delay-200">
                    <a href="{{ route('register') }}" 
                       class="px-8 py-4 text-lg font-semibold text-white gradient-bg rounded-xl hover:shadow-xl hover:shadow-purple-500/30 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span>Get Started</span>
                        </div>
                    </a>
                    <a href="#features" 
                       class="px-8 py-4 text-lg font-semibold text-purple-600 bg-white border-2 border-purple-200 rounded-xl hover:border-purple-300 hover:bg-purple-50 transition-all duration-300">
                        <div class="flex items-center justify-center">
                            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Explore Features</span>
                        </div>
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-3xl mx-auto animate-fade-up delay-300">
                    <div class="text-center p-6 bg-white rounded-2xl shadow-lg border border-gray-100 hover-lift">
                        <div class="text-3xl font-bold text-purple-600 mb-2">50+</div>
                        <div class="text-sm text-gray-600">Course Materials</div>
                    </div>
                    <div class="text-center p-6 bg-white rounded-2xl shadow-lg border border-gray-100 hover-lift">
                        <div class="text-3xl font-bold text-purple-600 mb-2">24/7</div>
                        <div class="text-sm text-gray-600">Access Available</div>
                    </div>
                    <div class="text-center p-6 bg-white rounded-2xl shadow-lg border border-gray-100 hover-lift">
                        <div class="text-3xl font-bold text-purple-600 mb-2">100%</div>
                        <div class="text-sm text-gray-600">Free Access</div>
                    </div>
                    <div class="text-center p-6 bg-white rounded-2xl shadow-lg border border-gray-100 hover-lift">
                        <div class="text-3xl font-bold text-purple-600 mb-2">Secure</div>
                        <div class="text-sm text-gray-600">Platform</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gradient-to-b from-white to-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-purple-50 text-purple-700 font-medium mb-4">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    What We Offer
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Everything You Need to <span class="text-gradient-primary">Succeed</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Access all your academic resources in one modern, easy-to-use platform.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group p-8 bg-white rounded-2xl border border-gray-200 hover:border-purple-300 hover:shadow-2xl transition-all duration-300 hover-lift">
                    <div class="w-14 h-14 rounded-xl feature-icon flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Course Materials</h3>
                    <p class="text-gray-600">
                        Access lecture notes, assignments, and study resources for all your courses in one centralized location.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-8 bg-white rounded-2xl border border-gray-200 hover:border-purple-300 hover:shadow-2xl transition-all duration-300 hover-lift">
                    <div class="w-14 h-14 rounded-xl feature-icon flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.66 0 3-4.03 3-9s-1.34-9-3-9m0 18c-1.66 0-3-4.03-3-9s1.34-9 3-9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Secure Login</h3>
                    <p class="text-gray-600">
                        Your account is protected with advanced security features including 2FA and suspicious login detection.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-8 bg-white rounded-2xl border border-gray-200 hover:border-purple-300 hover:shadow-2xl transition-all duration-300 hover-lift">
                    <div class="w-14 h-14 rounded-xl feature-icon flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Search Materials</h3>
                    <p class="text-gray-600">
                        Quickly find the resources you need with our powerful search functionality across all course materials.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="group p-8 bg-white rounded-2xl border border-gray-200 hover:border-purple-300 hover:shadow-2xl transition-all duration-300 hover-lift">
                    <div class="w-14 h-14 rounded-xl feature-icon flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Smart Notifications</h3>
                    <p class="text-gray-600">
                        Stay updated with real-time notifications about new materials, announcements, and important updates.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="group p-8 bg-white rounded-2xl border border-gray-200 hover:border-purple-300 hover:shadow-2xl transition-all duration-300 hover-lift">
                    <div class="w-14 h-14 rounded-xl feature-icon flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Activity Tracking</h3>
                    <p class="text-gray-600">
                        Monitor your login history and account activity to ensure your account remains secure.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="group p-8 bg-white rounded-2xl border border-gray-200 hover:border-purple-300 hover:shadow-2xl transition-all duration-300 hover-lift">
                    <div class="w-14 h-14 rounded-xl feature-icon flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">24/7 Access</h3>
                    <p class="text-gray-600">
                        Access your learning materials anytime, anywhere, on any device with our responsive platform.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="animate-fade-up">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-purple-50 text-purple-700 font-medium mb-6">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        About Us
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">
                        Your Digital Learning <span class="text-gradient-primary">Companion</span>
                    </h2>
                    <p class="text-lg text-gray-600 mb-6">
                        This portal is designed to help students easily access educational content and manage their learning experience online. We provide a centralized platform where students can find all their course materials, stay updated with notifications, and track their academic progress.
                    </p>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <span class="text-sm font-medium text-yellow-800">⚠️ Security Notice</span>
                        </div>
                        <p class="text-sm text-yellow-700 mt-1 ml-7">
                            This is a student academic portal. Do not enter sensitive personal passwords used elsewhere.
                        </p>
                    </div>
                    <div class="flex space-x-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-gray-600">Easy Access</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-gray-600">Secure Platform</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-gray-600">Free Resources</span>
                        </div>
                    </div>
                </div>
                <div class="relative animate-fade-up delay-200">
                    <div class="gradient-bg rounded-2xl p-8 shadow-2xl">
                        <div class="bg-white rounded-xl p-6">
                            <div class="flex items-center space-x-3 mb-6">
                                <div class="w-12 h-12 rounded-full gradient-bg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">Quick Stats</h4>
                                    <p class="text-sm text-gray-500">Platform Overview</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Course Materials Available</span>
                                        <span class="font-semibold text-purple-600">85%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="gradient-bg h-2 rounded-full" style="width: 85%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Student Satisfaction</span>
                                        <span class="font-semibold text-purple-600">94%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="gradient-bg h-2 rounded-full" style="width: 94%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Active Users</span>
                                        <span class="font-semibold text-purple-600">78%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="gradient-bg h-2 rounded-full" style="width: 78%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Resources Section -->
    <section id="resources" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Available <span class="text-gradient-primary">Resources</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Access a wide range of learning materials for your courses.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover-lift">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Lecture Notes</h3>
                    </div>
                    <p class="text-sm text-gray-600">Comprehensive notes from all your courses, updated regularly by lecturers.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover-lift">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Assignments</h3>
                    </div>
                    <p class="text-sm text-gray-600">Access and submit assignments directly through the platform.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover-lift">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Past Papers</h3>
                    </div>
                    <p class="text-sm text-gray-600">Practice with past examination papers and marking schemes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Frequently Asked <span class="text-gradient-primary">Questions</span>
                </h2>
                <p class="text-xl text-gray-600">
                    Find answers to common questions about the portal.
                </p>
            </div>

            <div class="space-y-4" x-data="{ open: null }">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="open = open === 1 ? null : 1" class="w-full text-left p-5 bg-white hover:bg-gray-50 transition flex justify-between items-center">
                        <span class="font-semibold text-gray-900">How do I access course materials?</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="open === 1 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 1" x-collapse class="p-5 pt-0 text-gray-600">
                        After logging in, navigate to the Student Portal where you'll find all your enrolled courses. Click on any course to view and download available materials.
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="open = open === 2 ? null : 2" class="w-full text-left p-5 bg-white hover:bg-gray-50 transition flex justify-between items-center">
                        <span class="font-semibold text-gray-900">Is my account secure?</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="open === 2 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 2" x-collapse class="p-5 pt-0 text-gray-600">
                        Yes! We implement advanced security features including suspicious login detection, two-factor authentication, and regular security audits to protect your account.
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="open = open === 3 ? null : 3" class="w-full text-left p-5 bg-white hover:bg-gray-50 transition flex justify-between items-center">
                        <span class="font-semibold text-gray-900">Can I access materials on mobile?</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="open === 3 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 3" x-collapse class="p-5 pt-0 text-gray-600">
                        Absolutely! The platform is fully responsive and works seamlessly on all devices - desktop, tablet, or mobile phone.
                    </div>
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
                Ready to Start Learning?
            </h2>
            <p class="text-xl text-white/90 mb-10 max-w-2xl mx-auto">
                Join thousands of students already using our platform to access course materials.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" 
                   class="px-8 py-4 text-lg font-semibold text-purple-600 bg-white rounded-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    Create Account
                </a>
                <a href="{{ route('login') }}" 
                   class="px-8 py-4 text-lg font-semibold text-white border-2 border-white/30 rounded-xl hover:bg-white/10 transition-all duration-300">
                    Sign In
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div>
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 rounded-xl gradient-bg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <span class="text-xl font-bold">Smart Student Portal</span>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Your academic hub for accessing course materials and managing your learning experience.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-lg mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#faq" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="#resources" class="text-gray-400 hover:text-white transition-colors">Resources</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-lg mb-6">University</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">UDOM</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Academic Year 2025/2026</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Library</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Student Support</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-lg mb-6">Connect</h4>
                    <ul class="space-y-3">
                        <li>
                            <a href="#" class="flex items-center text-gray-400 hover:text-white transition-colors">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                </svg>
                                GitHub
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center text-gray-400 hover:text-white transition-colors">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                                Twitter
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center text-gray-400 hover:text-white transition-colors">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.23 0H1.77C0.79 0 0 0.78 0 1.77v20.46C0 23.22 0.79 24 1.77 24h20.46c0.98 0 1.77-0.78 1.77-1.77V1.77C24 0.78 23.21 0 22.23 0zM7.08 20.31H3.55V8.97h3.53v11.34zM5.31 7.42c-1.13 0-2.04-0.92-2.04-2.04 0-1.13 0.91-2.04 2.04-2.04s2.04 0.91 2.04 2.04c0 1.13-0.91 2.04-2.04 2.04zM20.31 20.31h-3.53v-5.46c0-1.36-0.48-2.29-1.68-2.29-0.92 0-1.46 0.62-1.7 1.22-0.09 0.21-0.11 0.5-0.11 0.79v5.74h-3.53V8.97h3.53v1.5c0.47-0.73 1.32-1.77 3.21-1.77 2.34 0 4.1 1.53 4.1 4.82v6.79z"/>
                                </svg>
                                LinkedIn
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} Smart Student Portal. All rights reserved.</p>
                <p class="mt-2 text-xs">This is a student academic portal. Do not enter sensitive personal passwords used elsewhere.</p>
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

        // Parallax effect on hero
        document.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const heroContent = document.querySelector('.hero-content');
            if (heroContent) {
                heroContent.style.transform = `translateY(${scrolled * 0.3}px)`;
            }
        });
    </script>
</body>
</html>