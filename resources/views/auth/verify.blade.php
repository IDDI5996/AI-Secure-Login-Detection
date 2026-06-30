<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-100 p-4">
        <div class="w-full max-w-md animate-fadeIn">

            {{-- Main Card --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl p-8 md:p-10 border border-white/20 transition-all duration-300 hover:shadow-3xl">

                {{-- Icon --}}
                <div class="flex justify-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                {{-- Heading --}}
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Verify Your Identity</h2>
                    <p class="mt-2 text-sm text-gray-500">
                        A 6‑digit verification code has been sent to your email.
                    </p>
                </div>

                {{-- Alerts --}}
                @if(session('warning'))
                    <div class="mt-6 p-4 bg-amber-50/80 backdrop-blur-sm border-l-4 border-amber-400 text-amber-700 rounded-xl flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-sm">{{ session('warning') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mt-6 p-4 bg-red-50/80 backdrop-blur-sm border-l-4 border-red-400 text-red-700 rounded-xl flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('verify.email') }}" class="mt-8 space-y-6">
                    @csrf
                    <input type="hidden" name="login_attempt_id" value="{{ $login_attempt_id ?? '' }}">
                    <input type="hidden" name="verification_token" value="{{ $verification_token ?? '' }}">

                    {{-- Code Input --}}
                    <div>
                        <label for="verification_code" class="block text-sm font-medium text-gray-700 mb-1">
                            Verification Code
                        </label>
                        <input id="verification_code"
                               name="verification_code"
                               type="text"
                               maxlength="6"
                               required
                               autofocus
                               class="w-full px-4 py-3 text-3xl text-center font-mono tracking-[0.5em] border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition-all duration-200 placeholder:text-gray-300 placeholder:tracking-normal"
                               placeholder="000000">
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-[1.02]">
                        Verify
                    </button>
                </form>

                {{-- Resend --}}
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">
                        Didn't receive the email?
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-800 transition-colors duration-200 hover:underline">
                            Resend
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- Optional: custom animation keyframes (Tailwind v3+ supports @keyframes) --}}
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
        }
        /* Improve tracking for the code input */
        input[type="text"]::placeholder {
            letter-spacing: normal;
        }
    </style>
</x-guest-layout>