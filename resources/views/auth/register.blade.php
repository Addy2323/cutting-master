<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ADDO.NET</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f3f4f6;
        }
        .register-container {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="register-container bg-white p-8 rounded-lg shadow-2xl w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="{{ asset('uploads/images/logo/2.PNG') }}" alt="Logo" class="mx-auto h-24 w-24 mb-4 rounded-full object-cover border-4 border-blue-200 shadow">
                <h2 class="text-2xl font-bold text-gray-800">Create Account</h2>
                <p class="text-gray-600">Join our community today</p>
            </div>

            <!-- Registration Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                
                <!-- Name Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="name">
                        <i class="fas fa-user mr-2"></i>Full Name
                    </label>
                    <input 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition duration-150"
                        id="name" 
                        type="text" 
                        name="name" 
                        required 
                        autofocus
                        placeholder="Enter your full name"
                    >
                </div>

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
                        placeholder="Create a password"
                    >
                </div>

                <!-- Confirm Password Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="password_confirmation">
                        <i class="fas fa-lock mr-2"></i>Confirm Password
                    </label>
                    <input 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition duration-150"
                        id="password_confirmation" 
                        type="password" 
                        name="password_confirmation" 
                        required
                        placeholder="Confirm your password"
                    >
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-center">
                    <input type="checkbox" id="terms" name="terms" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                        I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">Terms and Conditions</a>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150">
                    <i class="fas fa-user-plus mr-2"></i>Create Account
                </button>
            </form>

            <!-- Login Link -->
            <div class="text-center mt-6">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="w-full py-4 text-center bg-white shadow-lg mt-8">
        <p class="text-gray-600">&copy; {{ date('Y') }} ADDO.NET. All Rights Reserved.</p>
        <p class="text-sm text-gray-500 mt-1">Empowering Digital Solutions</p>
    </footer>
</body>
</html>