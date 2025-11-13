<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data x-cloak>
<head x-init="
    $watch('darkMode', val => {
        localStorage.setItem('darkMode', val);
        setTimeout(() => location.reload(), 100);
    });
    if (darkMode) document.documentElement.classList.add('dark');
"
>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('darkMode', {
            on: localStorage.getItem('darkMode') === 'true',
            toggle() {
                this.on = !this.on;
                localStorage.setItem('darkMode', this.on);
                document.documentElement.classList.toggle('dark', this.on);
            }
        });
        // Set initial state
        if (Alpine.store('darkMode').on) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    });
</script>

    
</head>
<body class="h-full antialiased bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    {{ $slot }}
</body>
<script src="https://unpkg.com/alpinejs" defer></script>

</html>
