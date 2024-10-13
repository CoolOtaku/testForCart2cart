<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="antialiased">
<div
    class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
    <div class="max-w-7xl mx-auto p-6 lg:p-8">
        <div class="grid grid-cols-1 md:grid-cols-2">
            @include('app.icon')

            <x-next-button :url="route('animal.index')"/>
        </div>

        <h1 class="flex justify-center mt-16 text-xl font-semibold dark:text-white">
            <b>{{ __('messages.title4') }}</b>
        </h1>

        <div class="mt-16">
            <form action="{{ route('shorten.shorten') }}" method="POST">
                @csrf
                <div class="flex justify-center">
                    <label for="url" class="dark:text-white">
                        {{ __('messages.enter_the_url') }}:
                    </label>
                    <input type="url" name="original_url" id="url"
                           class="ml-4 form-control dark:text-white bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl"
                           required>
                </div>
                <div class="mt-6 flex justify-center">
                    <button type="submit"
                            class="dark:text-white p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        {{ __('messages.to_shorten') }}
                    </button>
                </div>
            </form>

            @if(session('short_url'))
                <p class="mt-6 flex justify-center">
                <div class="self-center dark:text-white">{{ __('messages.abbreviated_url') }}:</div>
                <a href="{{ session('short_url') }}" target="_blank"
                   class="dark:text-white p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                    {{ session('short_url') }}
                </a>
                </p>
            @endif
        </div>
    </div>
</div>
</body>
</html>
