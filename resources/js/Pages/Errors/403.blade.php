<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Icon -->
            <div class="mb-4">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                    <i class="bx bx-lock-alt text-5xl text-red-500"></i>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Access Denied</h1>
            <p class="text-gray-600 mb-6">
                You do not have permission to access this page.
            </p>

            <!-- Permission Details -->
            @if(isset($permission))
                <div class="bg-gray-50 rounded-lg p-3 mb-6 text-sm">
                    <p class="text-gray-600">
                        <span class="font-medium">Required Permission:</span>
                        <span class="text-red-500">{{ $permission }}</span>
                    </p>
                </div>
            @endif

            @if(isset($message))
                <div class="bg-gray-50 rounded-lg p-3 mb-6 text-sm">
                    <p class="text-gray-600">{{ $message }}</p>
                </div>
            @endif

            <!-- User Info -->
            @auth
                <div class="bg-gray-50 rounded-lg p-3 mb-6 text-sm flex items-center justify-center gap-3">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                        {{ auth()->user()->name ? collect(explode(' ', auth()->user()->name))->map(fn($n) => $n[0])->join('') : '?' }}
                    </div>
                    <div class="text-left">
                        <p class="font-medium text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-gray-500 text-xs">{{ auth()->user()->email }}</p>
                        <p class="text-gray-400 text-xs">Roles: {{ auth()->user()->getRoleNames()->join(', ') }}</p>
                    </div>
                </div>
            @endauth

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button onclick="window.history.back()" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    <i class="bx bx-arrow-back mr-2"></i>
                    Go Back
                </button>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                        <i class="bx bx-log-out mr-2"></i>
                        Logout
                    </button>
                </form>
                <a href="/dashboard" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                    <i class="bx bx-home mr-2"></i>
                    Dashboard
                </a>
            </div>

            <p class="mt-6 text-xs text-gray-400">
                If you believe this is an error, please contact your system administrator.
            </p>
        </div>
    </div>
</body>
</html>