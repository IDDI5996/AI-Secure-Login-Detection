<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">Verify Your Identity</h2>
                <p class="mt-2 text-sm text-gray-600">
                    A 6‑digit verification code has been sent to your email.
                </p>
            </div>

            @if(session('warning'))
                <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 rounded-r">
                    {{ session('warning') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-r">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(isset($risk_score))
                <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-800">
                    <strong>Risk Score:</strong> {{ $risk_score }}%
                    @if(!empty($reasons))
                        <br><span class="text-xs">({{ implode(', ', $reasons) }})</span>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('verify.email') }}" class="mt-6 space-y-6">
                @csrf
                <input type="hidden" name="login_attempt_id" value="{{ $login_attempt_id ?? '' }}">
                <input type="hidden" name="verification_token" value="{{ $verification_token ?? '' }}">

                <div>
                    <label for="verification_code" class="block text-sm font-medium text-gray-700">Verification Code</label>
                    <input id="verification_code" name="verification_code" type="text" maxlength="6" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-center text-2xl tracking-widest"
                           placeholder="000000">
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Verify
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Didn't receive the email? <a href="#" class="text-primary-600 hover:underline">Resend</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>