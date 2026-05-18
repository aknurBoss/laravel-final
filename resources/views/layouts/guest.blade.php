<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Вход') — Discipline Diary</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
                        colors: {
                            brand: { 50: '#eef2ff', 100: '#e0e7ff', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 900: '#312e81' },
                            merit: { DEFAULT: '#059669', soft: '#ecfdf5' },
                            demerit: { DEFAULT: '#dc2626', soft: '#fef2f2' },
                        }
                    }
                }
            };
        </script>
        <style type="text/tailwindcss">
            @layer components {
                .btn-primary { @apply inline-flex w-full items-center justify-center rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2; }
                .form-label { @apply mb-1.5 block text-sm font-medium text-slate-700; }
                .form-input { @apply w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20; }
                .alert-error { @apply mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800; }
            }
        </style>
    @endif
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased">
    @yield('content')
</body>
</html>
