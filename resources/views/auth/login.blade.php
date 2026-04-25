@extends('layouts.app')

@section('content')
    <div style="max-width: 28rem; margin: 0 auto;">
        <div class="card">
            <h1 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1.5rem;">Вход в систему</h1>

            @if ($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required autofocus
                           value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input id="password" name="password" type="password" required>
                </div>

                <div class="checkbox-row">
                    <input id="remember" name="remember" type="checkbox">
                    <label for="remember" style="margin-bottom: 0;">Запомнить меня</label>
                </div>

                <button type="submit" class="btn btn-primary">Войти</button>
            </form>
        </div>
    </div>
@endsection
