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

            <x-next-button :url="route('task6')"/>
        </div>

        <h1 class="flex justify-center mt-16 text-xl font-semibold dark:text-white">
            <b>{{ __('messages.title5') }}</b>
        </h1>

        <div class="mt-16">
            <form id="animalForm" action="{{ route('animal.sound') }}" method="POST">
                @csrf
                <div class="flex justify-center">
                    <label for="animal" class="dark:text-white">
                        {{ __('messages.choose_an_animal') }}:
                    </label>
                    <select id="animal" name="type"
                            class="ml-4 form-control dark:text-white bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl">
                        <option value="dog">{{ __('messages.dog') }}</option>
                        <option value="cat">{{ __('messages.cat') }}</option>
                        <option value="mouse">{{ __('messages.mouse') }}</option>
                        <option value="snake">{{ __('messages.snake') }}</option>
                        <option value="lion">{{ __('messages.lion') }}</option>
                    </select>
                </div>
                <div class="mt-6 flex justify-center">
                    <button type="submit"
                            class="dark:text-white p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        {{ __('messages.play_sound') }}
                    </button>
                </div>
            </form>

            <audio id="animalSound" controls style="display: none;"></audio>
        </div>
    </div>
</div>
</body>
<script>
    document.getElementById('animalForm').addEventListener('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.sound) {
                    let audio = document.getElementById('animalSound');
                    audio.src = data.sound;
                    audio.play();
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>
</html>
