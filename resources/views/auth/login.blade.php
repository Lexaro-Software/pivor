<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Pivor CRM</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="w-12 h-12 bg-pivor-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-2xl">P</span>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                Sign in to Pivor
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Your self-hosted CRM
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-sm rounded-xl sm:px-10 border border-gray-200 dark:border-gray-700">
                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div class="p-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-sm text-red-700 dark:text-red-300">{{ $errors->first() }}</p>
                        </div>
                    @endif

                    <div>
                        <label for="email" class="label">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}" class="input">
                    </div>

                    <div>
                        <label for="password" class="label">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="input">
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                   class="h-4 w-4 rounded border border-gray-300 text-pivor-600 focus:ring-pivor-500 dark:border-gray-600 dark:bg-gray-700">
                            <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-pivor-600 hover:bg-pivor-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pivor-500 transition-colors">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
                Powered by <a href="https://pivor.dev" class="text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">Pivor</a> &middot; Open Source CRM by Lexaro
            </p>
        </div>
    </div>
</body>
</html>
