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

            <x-next-button :url="route('artisan_command')"/>
        </div>

        <h1 class="flex justify-center mt-16 text-xl font-semibold dark:text-white">
            <b>{{ __('messages.title2') }}</b>
        </h1>

        <form action="{{ route('messages.store') }}" method="POST" class="mt-16">
            @csrf
            <div class="flex justify-center">
                <label for="message" class="dark:text-white">
                    {{ __('messages.message') }}:
                </label>
                <input type="text" name="message"
                       class="ml-4 form-control dark:text-white bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl"
                       required>
            </div>
            <div class="mt-6 flex justify-center">
                <button type="submit"
                        class="dark:text-white p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                    {{ __('messages.add_a_message') }}
                </button>
                <a href="{{ route('messages.process') }}"
                   class="ml-4 dark:text-white p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                    {{ __('messages.process_the_message') }}
                </a>
            </div>
        </form>

        <div class="flex justify-center mt-16">
            <table class="table table-striped dark:text-white">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.message') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.creation_date') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($messages as $message)
                    <x-row-table-message :message="$message"/>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
