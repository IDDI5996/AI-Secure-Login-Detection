<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Smart Student - Master academic writing skills with proven methods, templates, and expert guidance">
    <title>Smart Student - Master Academic Writing</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>✍️</text></svg>">

    <!-- Vite CSS -->
    @vite(['resources/css/app.css'])

    <style>
        /* ── Animations ── */
        @keyframes float {
            0%,
            100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-18px);
            }
        }
        @keyframes floatSlow {
            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-10px) rotate(2deg);
            }
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.92);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
        }
        @keyframes pulseRing {
            0% {
                transform: scale(0.95);
                opacity: 0.7;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.3;
            }
            100% {
                transform: scale(0.95);
                opacity: 0.7;
            }
        }
        @keyframes gradientMove {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        .animate-fade-up {
            animation: fadeInUp 0.7s ease-out forwards;
        }
        .animate-fade {
            animation: fadeIn 0.6s ease-out forwards;
        }
        .animate-scale {
            animation: scaleIn 0.5s ease-out forwards;
        }
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
        .animate-float-slow {
            animation: floatSlow 6s ease-in-out infinite;
        }
        .delay-100 {
            animation-delay: 0.1s;
        }
        .delay-200 {
            animation-delay: 0.2s;
        }
        .delay-300 {
            animation-delay: 0.3s;
        }
        .delay-400 {
            animation-delay: 0.4s;
        }
        .delay-500 {
            animation-delay: 0.5s;
        }

        /* ── Gradients ── */
        .gradient-primary {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 40%, #4f46e5 100%);
        }
        .gradient-secondary {
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
        }
        .gradient-hero {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 30%, #4f46e5 60%, #7c3aed 100%);
            background-size: 300% 300%;
            animation: gradientMove 8s ease infinite;
        }
        .gradient-accent {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .gradient-text-hero {
            background: linear-gradient(135deg, #c084fc 0%, #a78bfa 40%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Glassmorphism ── */
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .glass-dark {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-light {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* ── Hover Effects ── */
        .hover-lift {
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.35s ease;
        }
        .hover-lift:hover {
            transform: translateY(-8px) scale(1.01);
        }
        .hover-glow {
            transition: box-shadow 0.35s ease, transform 0.35s ease;
        }
        .hover-glow:hover {
            box-shadow: 0 20px 60px -12px rgba(124, 58, 237, 0.35);
            transform: translateY(-4px);
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.04);
        }

        /* ── Feature Icons ── */
        .icon-wrapper {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            box-shadow: 0 8px 24px -6px rgba(124, 58, 237, 0.35);
        }

        /* ── Stats Counter ── */
        .stat-number {
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1.1;
        }
        @media (min-width: 768px) {
            .stat-number {
                font-size: 3.5rem;
            }
        }

        /* ── Download Card ── */
        .download-card {
            background: white;
            border-radius: 1.25rem;
            border: 1px solid rgba(124, 58, 237, 0.08);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .download-card:hover {
            border-color: rgba(124, 58, 237, 0.2);
            box-shadow: 0 20px 60px -16px rgba(124, 58, 237, 0.15);
            transform: translateY(-6px);
        }
        .download-card .icon-circle {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
            color: #7c3aed;
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }
        .download-card:hover .icon-circle {
            transform: scale(1.1) rotate(-4deg);
        }

        /* ── Masterclass Card ── */
        .masterclass-card {
            background: linear-gradient(145deg, #1e1b4b 0%, #312e81 50%, #4c1d95 100%);
            border-radius: 1.5rem;
            overflow: hidden;
            position: relative;
        }
        .masterclass-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 30% 20%, rgba(124, 58, 237, 0.3) 0%, transparent 70%);
            pointer-events: none;
        }
        .masterclass-card .glow-orb {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.15) 0%, transparent 70%);
            top: -100px;
            right: -100px;
            pointer-events: none;
        }
        .masterclass-card .glow-orb-2 {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.2) 0%, transparent 70%);
            bottom: -50px;
            left: -50px;
            pointer-events: none;
        }

        /* ── YouTube Section ── */
        .youtube-stat {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .youtube-stat:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-4px);
        }

        /* ── Newsletter ── */
        .newsletter-input {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0.75rem;
            padding: 0.875rem 1.25rem;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        .newsletter-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        .newsletter-input:focus {
            outline: none;
            border-color: rgba(124, 58, 237, 0.5);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
        }

        /* ── Course Tag ── */
        .course-tag {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.025em;
            text-transform: uppercase;
            background: rgba(124, 58, 237, 0.12);
            color: #7c3aed;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #6d28d9;
        }

        /* ── Misc ── */
        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            border-radius: 9999px;
            background: rgba(124, 58, 237, 0.08);
            color: #7c3aed;
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 0.025em;
            border: 1px solid rgba(124, 58, 237, 0.1);
        }
        .section-badge svg {
            width: 14px;
            height: 14px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 8px 30px -8px rgba(124, 58, 237, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 48px -12px rgba(124, 58, 237, 0.5);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.35s ease;
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(4px);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.3);
        }
        .btn-outline-purple {
            background: transparent;
            color: #7c3aed;
            padding: 0.75rem 1.75rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.35s ease;
            border: 2px solid rgba(124, 58, 237, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        .btn-outline-purple:hover {
            background: rgba(124, 58, 237, 0.06);
            border-color: #7c3aed;
            transform: translateY(-2px);
        }

        /* ── Responsive tweaks ── */
        @media (max-width: 640px) {
            .hero-title {
                font-size: 2.5rem !important;
            }
            .hero-subtitle {
                font-size: 1.1rem !important;
            }
            .stat-number {
                font-size: 2rem !important;
            }
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">

    <!-- ============================================================
    NAVIGATION
    ============================================================ -->
    <nav class="fixed w-full z-50 transition-all duration-300" x-data="{ scrolled: false, mobileMenu: false }" @scroll.window="scrolled = window.scrollY > 20">
        <div :class="scrolled ? 'glass-card shadow-xl' : 'bg-transparent'" class="transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 md:h-20">

                    <!-- Logo -->
                    <a href="#" class="flex items-center space-x-3 group">
                        <div class="h-10 w-10 rounded-xl gradient-primary flex items-center justify-center shadow-lg shadow-purple-500/25 group-hover:shadow-purple-500/40 transition-shadow duration-300">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xl font-bold text-gray-900">Smart Student</span>
                            <span class="text-xs text-gray-500 block -mt-0.5">Academic Writing Hub</span>
                        </div>
                    </a>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#resources" class="text-gray-700 hover:text-purple-600 font-medium transition-colors duration-200 text-sm">Resources</a>
                        <a href="#masterclass" class="text-gray-700 hover:text-purple-600 font-medium transition-colors duration-200 text-sm">Masterclass</a>
                        <a href="#courses" class="text-gray-700 hover:text-purple-600 font-medium transition-colors duration-200 text-sm">Courses</a>
                        <a href="#youtube" class="text-gray-700 hover:text-purple-600 font-medium transition-colors duration-200 text-sm">YouTube</a>
                    </div>

                    <!-- Auth Buttons -->
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors duration-200">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-medium text-white gradient-primary rounded-lg hover:shadow-lg hover:shadow-purple-500/25 transition-all duration-300">
                            Get Started
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenu" x-transition class="md:hidden border-t border-gray-200 bg-white">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="#resources" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Resources</a>
                    <a href="#masterclass" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Masterclass</a>
                    <a href="#courses" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Courses</a>
                    <a href="#youtube" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">YouTube</a>
                    <div class="pt-4 border-t border-gray-200 space-y-2">
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Sign In</a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg text-white gradient-primary text-center">Get Started</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================================
    HERO SECTION
    ============================================================ -->
    <section class="relative overflow-hidden pt-28 pb-16 md:pt-36 md:pb-24 gradient-hero">
        <!-- Animated background orbs -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-pink-400/20 rounded-full blur-3xl animate-float-slow"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-400/10 rounded-full blur-3xl"></div>
            <!-- Grid pattern overlay -->
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">

                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 rounded-full glass mb-6 animate-fade-up">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm font-medium text-white/90">🎓 Helping college students across the globe</span>
                </div>

                <!-- Main Heading -->
                <h1 class="hero-title text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight mb-6 animate-fade-up delay-100">
                    <span class="text-white">Master the Skill of</span>
                    <span class="gradient-text-hero block mt-1">Academic Writing</span>
                </h1>

                <!-- Subheading -->
                <p class="hero-subtitle text-lg sm:text-xl md:text-2xl text-white/80 max-w-2xl mx-auto mb-10 animate-fade-up delay-200 leading-relaxed">
                    Learn the proven Smart Student Method that is changing the academic trajectory of college students all over the world.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-14 animate-fade-up delay-300">
                    <a href="{{ route('register') }}" class="btn-primary text-base px-8 py-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Find Out How
                    </a>
                    <a href="#masterclass" class="btn-secondary text-base px-8 py-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Watch Free Masterclass
                    </a>
                </div>

                <!-- Trust Bar -->
                <div class="flex flex-wrap items-center justify-center gap-8 md:gap-12 animate-fade-up delay-400">
                    <div class="flex items-center gap-3">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full bg-purple-300/30 border-2 border-white/30 flex items-center justify-center text-white text-xs font-bold">JD</div>
                            <div class="w-8 h-8 rounded-full bg-pink-300/30 border-2 border-white/30 flex items-center justify-center text-white text-xs font-bold">SM</div>
                            <div class="w-8 h-8 rounded-full bg-indigo-300/30 border-2 border-white/30 flex items-center justify-center text-white text-xs font-bold">AK</div>
                            <div class="w-8 h-8 rounded-full bg-purple-400/30 border-2 border-white/30 flex items-center justify-center text-white text-xs font-bold">+</div>
                        </div>
                        <span class="text-white/70 text-sm font-medium">Join 50,000+ students</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/60 text-sm">
                        <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>4.9/5 Rating</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/60 text-sm">
                        <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Free resources</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    RESOURCES / DOWNLOADS SECTION
    ============================================================ -->
    <section id="resources" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="section-badge mx-auto mb-4">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Free Resources
                </div>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Ready to improve your <span class="gradient-text">academic writing</span>?
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Download our comprehensive guides and templates to start writing better papers today.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">

                <!-- Download Card 1 -->
                <div class="download-card p-8">
                    <div class="flex items-start gap-5">
                        <div class="icon-circle flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-1">Smart Writers Guide</h3>
                            <p class="text-sm text-gray-500 mb-4">Learn the Smart Student Method that is changing the academic trajectory of college students all over the world!</p>
                            <a href="#" class="inline-flex items-center text-purple-600 font-semibold text-sm hover:text-purple-700 transition-colors">
                                Free PDF Download
                                <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Download Card 2 -->
                <div class="download-card p-8">
                    <div class="flex items-start gap-5">
                        <div class="icon-circle flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-1">APA Student Paper Template</h3>
                            <p class="text-sm text-gray-500 mb-4">Struggle with APA formatting? Download this comprehensive template for student papers and master APA formatting!</p>
                            <a href="#" class="inline-flex items-center text-purple-600 font-semibold text-sm hover:text-purple-700 transition-colors">
                                Free PDF Download
                                <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ============================================================
    MASTERCLASS SECTION
    ============================================================ -->
    <section id="masterclass" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="masterclass-card relative p-8 md:p-12 lg:p-16">
                <div class="glow-orb"></div>
                <div class="glow-orb-2"></div>

                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                    <div>
                        <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/10 text-white/80 text-sm font-medium mb-5">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Free 60 Minute Masterclass
                        </div>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">
                            Smart Writers <span class="text-purple-300">Masterclass™</span>
                        </h2>
                        <p class="text-white/80 text-lg leading-relaxed mb-8">
                            Join 100's of other students in learning the proven 4-Step Smart Student method for streamlining the process of academic writing. Not only will you learn how to write high scoring papers, you will learn how to write them <span class="text-white font-semibold">FAST</span>!
                        </p>
                        <a href="#" class="inline-flex items-center px-8 py-4 rounded-xl bg-gradient-to-r from-amber-400 to-orange-500 text-white font-bold text-base shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 transition-all duration-300 hover:-translate-y-1">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                            RESERVE YOUR SEAT
                        </a>
                    </div>
                    <div class="flex justify-center lg:justify-end">
                        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 max-w-sm w-full">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-14 h-14 rounded-full bg-purple-500/20 flex items-center justify-center text-2xl">🎓</div>
                                <div>
                                    <p class="text-white font-semibold">Learn from the</p>
                                    <p class="text-purple-300 font-bold">Smart Student on YouTube</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-3 text-center">
                                <div class="bg-white/5 rounded-xl py-3 px-2">
                                    <div class="text-white font-extrabold text-xl">100k+</div>
                                    <div class="text-white/50 text-xs uppercase tracking-wide">Students</div>
                                </div>
                                <div class="bg-white/5 rounded-xl py-3 px-2">
                                    <div class="text-white font-extrabold text-xl">13M+</div>
                                    <div class="text-white/50 text-xs uppercase tracking-wide">Views</div>
                                </div>
                                <div class="bg-white/5 rounded-xl py-3 px-2">
                                    <div class="text-white font-extrabold text-xl">120+</div>
                                    <div class="text-white/50 text-xs uppercase tracking-wide">Lessons</div>
                                </div>
                            </div>
                            <a href="#youtube" class="mt-4 block text-center text-white/70 hover:text-white text-sm font-medium transition-colors">
                                WATCH NOW →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    YOUTUBE SECTION
    ============================================================ -->
    <section id="youtube" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <div class="section-badge mx-auto mb-4">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Learn from the Smart Student on YouTube
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Free Video <span class="gradient-text">Lessons</span>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Gain access to the best support the Smart Student has to offer by enrolling in a program!
                </p>
            </div>

            <!-- YouTube Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-3xl mx-auto mb-12">
                <div class="youtube-stat">
                    <div class="text-4xl font-extrabold text-white">100k+</div>
                    <div class="text-white/60 text-sm font-medium mt-1">Students</div>
                </div>
                <div class="youtube-stat">
                    <div class="text-4xl font-extrabold text-white">13M+</div>
                    <div class="text-white/60 text-sm font-medium mt-1">Channel Views</div>
                </div>
                <div class="youtube-stat">
                    <div class="text-4xl font-extrabold text-white">120+</div>
                    <div class="text-white/60 text-sm font-medium mt-1">Video Lessons</div>
                </div>
            </div>

            <!-- Topic Tags -->
            <div class="flex flex-wrap justify-center gap-3 max-w-3xl mx-auto">
                <span class="px-4 py-2 bg-purple-50 text-purple-700 rounded-full text-sm font-medium border border-purple-100">APA 7th Style</span>
                <span class="px-4 py-2 bg-purple-50 text-purple-700 rounded-full text-sm font-medium border border-purple-100">Harvard Referencing</span>
                <span class="px-4 py-2 bg-purple-50 text-purple-700 rounded-full text-sm font-medium border border-purple-100">MLA 9th Edition</span>
                <span class="px-4 py-2 bg-purple-50 text-purple-700 rounded-full text-sm font-medium border border-purple-100">Literature Reviews</span>
                <span class="px-4 py-2 bg-purple-50 text-purple-700 rounded-full text-sm font-medium border border-purple-100">Smart Researching</span>
                <span class="px-4 py-2 bg-purple-50 text-purple-700 rounded-full text-sm font-medium border border-purple-100">Paraphrasing 101</span>
                <span class="px-4 py-2 bg-purple-50 text-purple-700 rounded-full text-sm font-medium border border-purple-100">Academic Writing</span>
            </div>

            <div class="text-center mt-10">
                <a href="#" class="btn-outline-purple">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                    WATCH NOW
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================================
    COURSES SECTION
    ============================================================ -->
    <section id="courses" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <div class="section-badge mx-auto mb-4">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Programs & Courses
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Gain access to the best support the <span class="gradient-text">Smart Student</span> has to offer
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Enroll in a program and transform your academic writing skills.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">

                <!-- Course 1: APA Made Easy -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover-lift transition-all duration-300">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-4">
                            <span class="course-tag">Bestseller</span>
                            <span class="text-2xl">📘</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">APA Made Easy</h3>
                        <p class="text-sm text-purple-600 font-semibold mb-3">Smart Student Mastery Course™</p>
                        <p class="text-gray-600 text-sm leading-relaxed mb-5">
                            Struggle with APA formatting? Student mastery program that covers the core lessons from the APA manual delivered using the Smart Student teaching style.
                        </p>
                        <div class="flex items-center gap-3 text-sm text-gray-500 mb-5">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                150-page guidebook
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Video library
                            </span>
                        </div>
                        <a href="#" class="w-full inline-flex items-center justify-center px-6 py-3 gradient-primary text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-purple-500/25 transition-all duration-300 hover:-translate-y-1">
                            PURCHASE APA MADE EASY
                            <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Course 2: Writing Academy -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover-lift transition-all duration-300">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-4">
                            <span class="course-tag" style="background:rgba(236,72,153,0.12);color:#ec4899;">Premium</span>
                            <span class="text-2xl">🎓</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">The Smart Student</h3>
                        <p class="text-sm text-pink-600 font-semibold mb-3">Writing Academy</p>
                        <p class="text-gray-600 text-sm leading-relaxed mb-5">
                            Gain lifetime access to the full signature program that will walk you through the entire academic writing process at your own pace catered to your learning style.
                        </p>
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5">
                            <p class="text-sm text-amber-800 font-medium flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Attending the free Smart Writers Masterclass is required prior to enrollment.
                            </p>
                        </div>
                        <a href="#masterclass" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-pink-500/25 transition-all duration-300 hover:-translate-y-1">
                            RESERVE YOUR SEAT
                            <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ============================================================
    NEWSLETTER SECTION
    ============================================================ -->
    <section class="py-20 gradient-primary relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-pink-400/20 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/10 text-white/90 text-sm font-medium mb-6">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Stay Updated
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                    Smart Student <span class="text-purple-200">Newsletter</span>
                </h2>
                <p class="text-white/80 text-lg mb-10 max-w-lg mx-auto">
                    Receive weekly content from the Smart Student Writing Lab
                </p>

                <form class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto">
                    <input type="text" placeholder="Your Name" class="newsletter-input" required>
                    <input type="email" placeholder="Your Email" class="newsletter-input" required>
                    <button type="submit" class="px-8 py-3 bg-white text-purple-700 font-bold rounded-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 whitespace-nowrap">
                        Submit
                    </button>
                </form>

                <p class="text-white/40 text-xs mt-6">
                    By subscribing you agree to our Privacy Policy &amp; Terms.
                </p>
            </div>
        </div>
    </section>

    <!-- ============================================================
    FOOTER
    ============================================================ -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">

                <!-- Brand -->
                <div>
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 rounded-xl gradient-primary flex items-center justify-center shadow-lg shadow-purple-500/25">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold ml-3">Smart Student</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Helping college students across the globe master the skill of academic writing.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold text-lg mb-6">Quick Links</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#resources" class="text-gray-400 hover:text-white transition-colors">Free Resources</a></li>
                        <li><a href="#masterclass" class="text-gray-400 hover:text-white transition-colors">Masterclass</a></li>
                        <li><a href="#courses" class="text-gray-400 hover:text-white transition-colors">Courses</a></li>
                        <li><a href="#youtube" class="text-gray-400 hover:text-white transition-colors">YouTube</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="font-semibold text-lg mb-6">Support</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms &amp; Conditions</a></li>
                    </ul>
                </div>

                <!-- Social -->
                <div>
                    <h4 class="font-semibold text-lg mb-6">Connect</h4>
                    <ul class="space-y-3 text-sm">
                        <li>
                            <a href="#" class="flex items-center text-gray-400 hover:text-white transition-colors">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                </svg>
                                YouTube
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

            <!-- Bottom -->
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} Smart Student. All rights reserved.</p>
                <p class="mt-2 text-xs text-gray-500">Designed for academic excellence. Empowering students worldwide.</p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Vite JS -->
    @vite(['resources/js/app.js'])

    <script>
        // ── Smooth scroll for anchor links ──
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // ── Navbar hide/show on scroll ──
        let lastScroll = 0;
        const nav = document.querySelector('nav');
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            if (currentScroll > lastScroll && currentScroll > 100) {
                // nav.style.transform = 'translateY(-100%)';
            } else {
                nav.style.transform = 'translateY(0)';
            }
            lastScroll = currentScroll;
        });

        // ── Intersection Observer for stats animation ──
        const statEls = document.querySelectorAll('.stat-number');
        if (statEls.length) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.3 });
            statEls.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.6s ease';
                observer.observe(el);
            });
        }

        console.log('🎓 Smart Student — Academic Writing Hub');
    </script>
</body>
</html>