<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Real-Time Web Chat Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .input-focus {
            transition: all 0.3s ease;
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
        <!-- Animated Background Shapes -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-0 left-0 w-72 h-72 bg-blue-400 bg-opacity-30 rounded-full blur-2xl animate-pulse"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-400 bg-opacity-30 rounded-full blur-2xl animate-pulse"></div>
            <div class="absolute top-1/2 left-1/2 w-40 h-40 bg-pink-400 bg-opacity-20 rounded-full blur-2xl animate-pulse"></div>
        </div>
        <div class="w-full max-w-md z-10">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-block bg-white bg-opacity-80 backdrop-blur-lg rounded-full p-6 mb-4 shadow-lg">
                    <i class="fas fa-cube text-5xl text-blue-600 animate-spin"></i>
                </div>
                <h1 class="text-4xl font-extrabold text-white mb-2 drop-shadow-lg tracking-tight">Real-Time Web Chat Application</h1>
                <p class="text-blue-100 text-lg">Real-Time Web Chat Application</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white bg-opacity-80 backdrop-blur-lg rounded-2xl login-card overflow-hidden shadow-2xl border border-blue-100">

                <!-- Tabs -->
                <div class="flex border-b">
                    <button class="flex-1 py-4 px-6 bg-blue-600 text-white font-semibold transition rounded-tl-2xl" id="login-tab">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </button>
                    <a href="/register" class="flex-1 py-4 px-6 text-gray-600 hover:bg-gray-50 transition font-semibold border-b-2 border-transparent rounded-tr-2xl">
                        <i class="fas fa-user-plus mr-2"></i> Daftar
                    </a>
                </div>

                <!-- Login Form -->
                <form action="/login" method="POST" class="p-8 space-y-6">
                    @csrf

                    <!-- Flash Messages -->
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 p-4 mb-6 rounded-lg flex items-start gap-3">
                            <i class="fas fa-exclamation-circle mt-1 flex-shrink-0"></i>
                            <div>
                                <p class="font-semibold">Login Gagal</p>
                                <ul class="text-sm mt-2 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 p-4 mb-6 rounded-lg flex items-start gap-3">
                            <i class="fas fa-times-circle mt-1 flex-shrink-0"></i>
                            <div>
                                <p class="font-semibold">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Email Field -->
                    <div class="relative mb-4">
                        <input type="email" name="email" value="{{ old('email') }}" required class="peer w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 input-focus transition pl-12 bg-white bg-opacity-70" placeholder=" " autocomplete="email">
                        <label class="absolute left-10 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none transition-all duration-200 peer-focus:-top-3 peer-focus:text-xs peer-focus:text-blue-600 peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400">Email</label>
                        <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-blue-400"></i>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="relative mb-4">
                        <input type="password" name="password" id="password" required class="peer w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 input-focus transition pl-12 bg-white bg-opacity-70" placeholder=" " autocomplete="current-password">
                        <label class="absolute left-10 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none transition-all duration-200 peer-focus:-top-3 peer-focus:text-xs peer-focus:text-blue-600 peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400">Password</label>
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-blue-400"></i>
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="password-toggle"></i>
                        </button>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CAPTCHA Section -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-image mr-2 text-blue-600"></i> Verifikasi CAPTCHA
                        </label>
                        <div class="mb-3 flex items-center gap-2">
                            <div class="flex-1 bg-gray-100 p-3 rounded-lg border-2 border-gray-300">
                                <img id="captcha-image" src="{{ $captcha_image }}" alt="CAPTCHA" class="max-h-16 mx-auto">
                            </div>
                            <button type="button" onclick="refreshCaptcha()" class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" title="Refresh CAPTCHA">
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>
                        <input type="text" name="captcha" id="captcha-input" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 input-focus transition uppercase bg-white bg-opacity-70" placeholder="Masukkan 6 karakter dari gambar" maxlength="6">
                        @error('captcha')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-6 flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="rounded border-gray-300 text-blue-600 cursor-pointer accent-blue-600">
                        <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">Ingat saya</label>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-3 rounded-xl hover:from-blue-700 hover:to-purple-700 transition flex items-center justify-center gap-2 shadow-lg text-lg">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-blue-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white bg-opacity-80 text-blue-500 rounded-full">atau</span>
                        </div>
                    </div>

                    <!-- Register Link -->
                    <p class="text-center text-blue-600 text-sm">
                        Belum punya akun? 
                        <a href="/register" class="font-bold underline">Daftar di sini</a>
                    </p>
                </form>

                <!-- Info Footer -->
                <div class="bg-blue-50 bg-opacity-80 px-8 py-4 border-t border-blue-100">
                    <p class="text-xs text-blue-700 text-center">
                        <strong>Demo Admin:</strong><br>
                        Email: admin1@mail.com | Password: password123
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-blue-100 text-sm drop-shadow">
                    &copy; 2025 Real-Time Web Chat Application. Semua hak dilindungi. Created by ridzidev.
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const toggle = document.getElementById('password-toggle');
            if (input.type === 'password') {
                input.type = 'text';
                toggle.classList.remove('fa-eye');
                toggle.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                toggle.classList.remove('fa-eye-slash');
                toggle.classList.add('fa-eye');
            }
        }
        function refreshCaptcha() {
            fetch('/refresh-captcha')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('captcha-image').src = data.image;
                    document.getElementById('captcha-input').value = '';
                    document.getElementById('captcha-input').focus();
                })
                .catch(error => console.error('Error:', error));
        }
        // Focus on captcha input
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('captcha-input').focus();
        });
    </script>
</body>
</html>
