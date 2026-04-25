<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Discipline Diary') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            body { font-family: system-ui, sans-serif; background: #f3f4f6; margin: 0; }
            nav { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 0 1.5rem; display: flex; justify-content: space-between; align-items: center; height: 4rem; }
            nav .brand { font-size: 1.1rem; font-weight: 600; }
            nav .user-info { display: flex; align-items: center; gap: 1rem; font-size: .875rem; color: #374151; }
            nav button { background: none; border: none; color: #dc2626; cursor: pointer; font-size: .875rem; }
            nav button:hover { text-decoration: underline; }
            main { max-width: 80rem; margin: 2rem auto; padding: 0 1.5rem; }
            .card { background: #fff; border-radius: .5rem; box-shadow: 0 1px 3px rgba(0,0,0,.1); padding: 1.5rem; }
            .alert-error { background: #fee2e2; color: #b91c1c; padding: .5rem .75rem; border-radius: .25rem; margin-bottom: 1rem; font-size: .875rem; }
            .alert-success { background: #d1fae5; color: #065f46; padding: .5rem .75rem; border-radius: .25rem; margin-bottom: 1rem; font-size: .875rem; }
            label { display: block; font-size: .875rem; font-weight: 500; color: #374151; margin-bottom: .25rem; }
            input[type=email], input[type=password], input[type=text] { width: 100%; padding: .5rem .75rem; border: 1px solid #d1d5db; border-radius: .375rem; font-size: .875rem; box-sizing: border-box; }
            .form-group { margin-bottom: 1rem; }
            .btn { display: inline-block; padding: .5rem 1rem; border-radius: .375rem; font-size: .875rem; cursor: pointer; border: none; }
            .btn-primary { background: #4f46e5; color: #fff; width: 100%; }
            .btn-primary:hover { background: #4338ca; }
            .checkbox-row { display: flex; align-items: center; gap: .5rem; margin-bottom: 1rem; font-size: .875rem; }
        </style>
    @endif
</head>
<body>
    <nav>
        <div class="brand">Discipline Diary</div>
        @auth
            <div class="user-info">
                <span>{{ auth()->user()->name }} ({{ auth()->user()->role->value ?? auth()->user()->role }})</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Выйти</button>
                </form>
            </div>
        @endauth
    </nav>

    <main>
        @if (session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
