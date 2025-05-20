<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ADDO.NET</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f3f4f6;
        }
        .login-container {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="login-container bg-white p-4 rounded-lg shadow-2xl w-full max-w-sm">
            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="{{ asset('uploads/images/logo/2.PNG') }}" alt="Logo" class="mx-auto h-24 w-24 mb-4 rounded-full object-cover border-4 border-blue-200 shadow">
                <h2 class="text-2xl font-bold text-gray-800">Welcome Back!</h2>
                <p class="text-gray-600">Please sign in to your account</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <!-- Email Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="email">
                        <i class="fas fa-envelope mr-2"></i>Email Address
                    </label>
                    <input 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition duration-150"
                        id="email" 
                        type="email" 
                        name="email" 
                        required 
                        autofocus
                        placeholder="Enter your email"
                    >
                </div>

                <!-- Password Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="password">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition duration-150"
                        id="password" 
                        type="password" 
                        name="password" 
                        required
                        placeholder="Enter your password"
                    >
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 transition duration-150">
                        Forgot Password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150">
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </button>
                <!-- Register Link (inside the card) -->
                <div class="text-center mt-4">
                    <a href="{{ route('register') }}" class="text-base font-semibold text-blue-700 hover:text-blue-900 transition duration-150">
                        Don't have an account? <span class="underline">Register here</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="w-full py-4 text-center bg-white shadow-lg mt-8">
        <p class="text-gray-600">&copy; {{ date('Y') }} ADDO.NET. All rights reserved.</p>
    </footer>
</body>
</html>