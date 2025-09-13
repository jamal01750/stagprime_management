<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Login | StagPrime Cost Management</title>

    </head>
    <body>
        <div class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Login</h2>
       

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="email"  required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50" />
                @if($errors -> has('email'))
                    <p style="color:red">{{ $errors-> first('email') }}</p>
                @endif
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50" />
                @if($errors -> has('password'))
                    <p style="color:red">{{ $errors-> first('password') }}</p>
                @endif
            </div>
   

            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                Login
            </button>
        </form>
        <!-- <p class="mt-6 text-center text-gray-600 text-sm">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </p> -->
    </div>
</div>
        
        <script src="https://cdn.tailwindcss.com"></script>
    </body>
</html>




