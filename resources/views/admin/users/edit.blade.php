@extends('layouts.app')
@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-500 hover:text-indigo-600 flex items-center gap-1">
            Назад к аккаунтам
        </a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">Редактировать аккаунт</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-xl">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            @php $inp = 'w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500'; @endphp

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Имя</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="{{ $inp }}">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="{{ $inp }}">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Новый пароль <span class="text-slate-400 font-normal">(оставьте пустым если не меняете)</span></label>
                <input type="password" name="password" class="{{ $inp }}">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Роль</label>
                    <select name="role" class="{{ $inp }}">
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(old('role', $user->role->value ?? $user->role) === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center mt-7">
                    <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600"
                            @checked(old('is_active', $user->is_active))>
                        Активен
                    </label>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors">Сохранить</button>
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl text-sm text-slate-600 hover:bg-slate-100 transition-colors">Отмена</a>
            </div>
        </form>
    </div>

@endsection
