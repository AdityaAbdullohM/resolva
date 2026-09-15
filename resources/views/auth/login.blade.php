<x-guest-layout>
    <!-- CSS for Animations and Dynamic Background -->
    <style>
        body { margin: 0; padding: 0; overflow-x: hidden; }
        
        /* Background Gradient Animation - Light theme */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: linear-gradient(-45deg, #e0c3fc, #8ec5fc, #e0c3fc, #a1c4fd);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            z-index: -2;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating Bubbles/Shapes */
        .bubbles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }

        .bubble {
            position: absolute;
            bottom: -150px;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(3px);
            border-radius: 50%;
            animation: fly 10s infinite ease-in;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .bubble:nth-child(1) { left: 10%; width: 80px; height: 80px; animation-duration: 8s; animation-delay: 0s; }
        .bubble:nth-child(2) { left: 20%; width: 50px; height: 50px; animation-duration: 6s; animation-delay: 2s; }
        .bubble:nth-child(3) { left: 45%; width: 120px; height: 120px; animation-duration: 12s; animation-delay: 4s; }
        .bubble:nth-child(4) { left: 65%; width: 60px; height: 60px; animation-duration: 10s; animation-delay: 1s; }
        .bubble:nth-child(5) { left: 85%; width: 90px; height: 90px; animation-duration: 7s; animation-delay: 3s; }
        .bubble:nth-child(6) { left: 35%; width: 40px; height: 40px; animation-duration: 9s; animation-delay: 5s; }

        @keyframes fly {
            0% { transform: translateY(0) scale(1) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-120vh) scale(1.5) rotate(360deg); opacity: 0; }
        }

        /* Override Guest Layout Styles to make it transparent & animated */
        .min-h-screen.bg-gray-100 {
            background: transparent !important;
        }

        /* Make the card background light with glassmorphism for dark text */
        .bg-white.shadow-md {
            background: rgba(255, 255, 255, 0.85) !important; /* Light tinted glass background */
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 1.25rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1) !important;
            animation: slideUpFade 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(40px);
            color: #1f2937 !important;
        }

        @keyframes slideUpFade {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Form Elements Animation */
        .input-wrapper {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .input-wrapper:focus-within {
            transform: translateY(-2px);
        }

        /* Custom Input Styles */
        .custom-input {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-color: rgba(0, 0, 0, 0.2) !important;
            color: #1f2937 !important;
        }
        .custom-input::placeholder {
            color: rgba(0, 0, 0, 0.4) !important;
        }
        .custom-input:focus {
            border-color: #3b82f6 !important; /* blue-500 */
            box-shadow: 0 0 0 1px #3b82f6 !important;
            background-color: #ffffff !important;
        }

        /* Custom Label Styles */
        .custom-label {
            color: #374151 !important; /* gray-700 */
        }

        .btn-login {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            color: white;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            z-index: -1;
            transition: opacity 0.3s ease;
            opacity: 0;
        }
        .btn-login:hover::before {
            opacity: 1;
        }
        
        .logo-animate {
            animation: pulseLogo 3s infinite ease-in-out alternate;
        }
        
        @keyframes pulseLogo {
            0% { transform: scale(1); filter: drop-shadow(0 0 5px rgba(59, 130, 246, 0.3)); }
            100% { transform: scale(1.08); filter: drop-shadow(0 0 15px rgba(59, 130, 246, 0.6)); }
        }
    </style>

    <!-- Animated Background Elements -->
    <div class="animated-bg"></div>
    <div class="bubbles">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <!-- Login Content -->
    <div class="relative z-10 p-2 text-gray-900">
        <div class="flex justify-center mb-6">
            <a href="/" class="logo-animate block transition-transform hover:scale-110">
                <x-logo class="w-24 h-24 text-blue-600" />
            </a>
        </div>
        
        <h1 class="text-3xl font-extrabold text-center text-gray-900 mb-2 tracking-tight">Selamat Datang!</h1>
        <p class="text-center text-gray-600 mb-6 text-sm font-medium">Masuk untuk melanjutkan ke dashboard Anda</p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4 font-medium text-green-700 bg-green-100 bg-opacity-80 border border-green-300 p-3 rounded-lg" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div class="input-wrapper">
                <x-input-label for="email" :value="__('Email')" class="custom-label font-bold" />
                <x-text-input id="email" class="custom-input block mt-1 w-full rounded-md shadow-sm transition-all font-medium" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Masukkan email Anda" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 font-medium" />
            </div>

            <!-- Password -->
            <div class="input-wrapper mt-4">
                <x-input-label for="password" :value="__('Password')" class="custom-label font-bold" />
                <x-text-input id="password" class="custom-input block mt-1 w-full rounded-md shadow-sm transition-all font-medium"
                                type="password"
                                name="password"
                                required autocomplete="current-password" placeholder="Masukkan password Anda" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 font-medium" />
            </div>

            <!-- Remember Me -->
            

               
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-login w-full flex justify-center py-3 px-4 rounded-md shadow-lg text-sm font-bold uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('Log in') }}
                </button>
            </div>
            
            
        </form>
    </div>
</x-guest-layout>