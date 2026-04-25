@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Новый пользователь</h1>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4 max-w-xl">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Имя</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="border rounded w-full px-3 py-2">
            @error('name') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="border rounded w-full px-3 py-2">
            @error('email') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Пароль</label>
            <input type="password" name="password"
                   class="border rounded w-full px-3 py-2">
            @error('password') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Роль</label>
                <select name="role" class="border rounded w-full px-3 py-2">
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(old('role') === $role)>{{ $role }}</option>
                    @endforeach
                </select>
                @error('role') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="flex items-center mt-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" class="mr-2" checked>
                    Активен
                </label>
            </div>
        </div>

        <div class="flex space-x-2">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">
                Сохранить
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600">Отмена</a>
        </div>
    </form>
@endsection
