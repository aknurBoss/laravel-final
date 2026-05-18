@extends('layouts.guest')

@section('title', 'Вход')

@section('content')
<div class="min-h-screen lg:grid lg:grid-cols-2">
    {{-- Hero / pitch for investors --}}
    <section class="relative hidden lg:flex flex-col justify-between bg-gradient-to-br from-brand-900 via-brand-700 to-brand-600 text-white p-12 overflow-hidden">
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 80%, white 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="relative">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-sm font-medium backdrop-blur">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                Демо для школы
            </div>
            <h1 class="mt-8 text-4xl font-extrabold leading-tight tracking-tight">
                Discipline Diary
            </h1>
            <p class="mt-4 text-lg text-indigo-100 max-w-md leading-relaxed">
                Цифровая система меритов и демеритов: учителя выставляют баллы по правилам, ученики видят прогресс, администрация контролирует дисциплину в реальном времени.
            </p>
        </div>

        <div class="relative space-y-4">
            <div class="rounded-2xl bg-white/10 backdrop-blur p-5 border border-white/10">
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-200 mb-3">Как это работает</p>
                <ol class="space-y-3 text-sm text-indigo-50">
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/20 text-xs font-bold">1</span>
                        <span><strong class="text-white">Админ</strong> настраивает классы, правила и пользователей</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/20 text-xs font-bold">2</span>
                        <span><strong class="text-white">Учитель</strong> выдаёт баллы за поведение за 30 секунд</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/20 text-xs font-bold">3</span>
                        <span><strong class="text-white">Ученик</strong> видит историю, рейтинг и уведомления</span>
                    </li>
                </ol>
            </div>
            <p class="text-xs text-indigo-200">Прозрачность · Мотивация · Аналитика для руководства</p>
        </div>
    </section>

    {{-- Login form --}}
    <section class="flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16">
        <div class="mx-auto w-full max-w-md">
            <div class="lg:hidden mb-8 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-600 text-white font-bold">DD</div>
                <h1 class="text-2xl font-bold text-slate-900">Discipline Diary</h1>
                <p class="text-sm text-slate-500 mt-1">Мерит / Демерит для школы</p>
            </div>

            <h2 class="text-2xl font-bold text-slate-900">Вход в систему</h2>
            <p class="mt-1 text-sm text-slate-500">Выберите роль для демонстрации проекта</p>

            @if ($errors->any())
                <div class="alert-error mt-6">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input id="email" name="email" type="email" required autofocus
                           value="{{ old('email') }}" class="form-input" placeholder="admin@example.com">
                </div>
                <div>
                    <label for="password" class="form-label">Пароль</label>
                    <input id="password" name="password" type="password" required
                           class="form-input" placeholder="••••••••">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                    <input id="remember" name="remember" type="checkbox" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    Запомнить меня
                </label>
                <button type="submit" class="btn-primary">Войти</button>
            </form>

            <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Демо-аккаунты</p>
                <div class="space-y-2 text-sm">
                    @foreach ([
                        ['label' => 'Администратор', 'email' => 'admin@example.com'],
                        ['label' => 'Классный руководитель (10А)', 'email' => 'teacher@example.com'],
                        ['label' => 'Учитель (без класса)', 'email' => 'teacher2@example.com'],
                        ['label' => 'Ученик', 'email' => 'student@example.com'],
                    ] as $demo)
                        <button type="button" onclick="fillLogin('{{ $demo['email'] }}')"
                                class="w-full text-left rounded-xl bg-white border border-slate-200 px-3 py-2.5 hover:border-brand-300 hover:bg-brand-50/50 transition-colors">
                            <span class="font-semibold text-slate-800">{{ $demo['label'] }}</span>
                            <span class="block text-xs text-slate-500 mt-0.5">{{ $demo['email'] }} · пароль: password</span>
                        </button>
                    @endforeach
                    <p class="text-xs text-slate-400 px-1 pt-1">Демо: <code class="text-slate-600">php artisan migrate:fresh --seed</code></p>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function fillLogin(email) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = 'password';
    document.getElementById('email').focus();
}
</script>
@endsection
