<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Discipline Diary')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>
@php
    $role = auth()->check() ? (auth()->user()->role->value ?? auth()->user()->role) : null;
    $roleLabels = ['admin' => 'Администратор', 'teacher' => 'Учитель', 'student' => 'Ученик'];
    $roleLabel = $roleLabels[$role] ?? $role;
    $rolePillClass = match ($role) {
        'admin' => 'role-pill-admin',
        'teacher' => 'role-pill-teacher',
        'student' => 'role-pill-student',
        default => 'role-pill',
    };
    $homeRoute = match ($role) {
        'admin' => route('admin.dashboard'),
        'teacher' => route('teacher.dashboard'),
        'student' => route('student.dashboard'),
        default => route('login'),
    };

    $teacherProfile = ($role === 'teacher' && auth()->check())
        ? \App\Models\Teacher::with('homeroomClass')->where('user_id', auth()->id())->first()
        : null;
@endphp

<div class="app-shell">
    @auth
        <aside class="app-sidebar hidden lg:flex">
            <div class="px-5 py-6 border-b border-border">
                <a href="{{ $homeRoute }}" class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600 text-white font-bold text-sm shadow-sm">DD</div>
                    <div>
                        <div class="font-bold text-slate-900 leading-tight">Discipline Diary</div>
                        <div class="text-xs text-slate-500">Мерит / Демерит</div>
                    </div>
                </a>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @if ($role === 'admin')
                    <p class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Управление</p>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
                        Обзор
                    </a>
                    <a href="{{ route('admin.points') }}" class="nav-link {{ request()->routeIs('admin.points*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Выдача баллов
                    </a>
                    <a href="{{ route('admin.students.index') }}" class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-4a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Ученики
                    </a>
                    <a href="{{ route('admin.teachers.index') }}" class="nav-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 7l9-5-9-5-9 5 9 5z"/></svg>
                        Учителя
                    </a>
                    <a href="{{ route('admin.classes.index') }}" class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3m2 0h2M9 7h1m-1 4h1m4-4h1m-1 4h1"/></svg>
                        Классы
                    </a>
                    <a href="{{ route('admin.rules.index') }}" class="nav-link {{ request()->routeIs('admin.rules.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Правила
                    </a>
                @elseif ($role === 'teacher')
                    <a href="{{ route('teacher.dashboard') }}" class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Выдача баллов
                    </a>
                    <a href="{{ route('teacher.assignments') }}" class="nav-link {{ request()->routeIs('teacher.assignments') ? 'active' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Мои назначения
                    </a>
                    @if ($teacherProfile?->is_homeroom_teacher)
                        <a href="{{ route('teacher.homeroom') }}" class="nav-link {{ request()->routeIs('teacher.homeroom*') ? 'active' : '' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3m2 0h2M9 7h1m-1 4h1m4-4h1m-1 4h1"/></svg>
                            Мой класс
                            @if ($teacherProfile->homeroomClass)
                                <span class="ml-auto text-xs text-slate-400">{{ $teacherProfile->homeroomClass->name }}</span>
                            @endif
                        </a>
                    @endif
                @elseif ($role === 'student')
                    <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Мой профиль
                    </a>
                @endif

                @if ($role !== 'teacher')
                    <p class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Связь</p>
                    <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Уведомления
                    </a>
                @endif
            </nav>

            <div class="p-4 border-t border-border">
                <div class="rounded-xl bg-slate-50 p-3">
                    <div class="text-sm font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</div>
                    <span class="{{ $rolePillClass }} mt-2">{{ $roleLabel }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn-ghost w-full text-xs py-2">Выйти</button>
                    </form>
                </div>
            </div>
        </aside>
    @endauth

    <div class="app-main">
        @auth
            <header class="app-topbar">
                <div class="min-w-0 flex-1">
                    @hasSection('page-header')
                        @yield('page-header')
                    @else
                        <h1 class="page-title truncate">@yield('title', 'Discipline Diary')</h1>
                    @endif
                </div>
                <div class="flex items-center gap-3 lg:hidden">
                    <span class="{{ $rolePillClass }}">{{ $roleLabel }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 font-medium">Выйти</button>
                    </form>
                </div>
            </header>

            <div class="lg:hidden border-b border-border bg-white px-4 py-2 overflow-x-auto flex gap-2 text-sm shrink-0">
                @if ($role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600' }}">Обзор</a>
                    <a href="{{ route('admin.points') }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.points*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600' }}">Баллы</a>
                    <a href="{{ route('admin.students.index') }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.students.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600' }}">Ученики</a>
                    <a href="{{ route('admin.rules.index') }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.rules.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600' }}">Правила</a>
                @elseif ($role === 'teacher')
                    <a href="{{ route('teacher.dashboard') }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg {{ request()->routeIs('teacher.dashboard') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600' }}">Баллы</a>
                    <a href="{{ route('teacher.assignments') }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg {{ request()->routeIs('teacher.assignments') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600' }}">Назначения</a>
                    @if ($teacherProfile?->is_homeroom_teacher)
                        <a href="{{ route('teacher.homeroom') }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg {{ request()->routeIs('teacher.homeroom*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600' }}">Мой класс</a>
                    @endif
                @else
                    <a href="{{ route('student.dashboard') }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg bg-brand-50 text-brand-700 font-semibold">Профиль</a>
                    <a href="{{ route('notifications.index') }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg text-slate-600">Уведомления</a>
                @endif
            </div>
        @endauth

        <main class="@auth app-content @else min-h-screen @endauth">
            @if (session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
