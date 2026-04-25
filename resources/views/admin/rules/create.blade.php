@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Новое правило</h1>

    <form action="{{ route('admin.rules.store') }}" method="POST" class="space-y-4 max-w-xl">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Название</label>
            <input type="text" name="name" value="{{ old('name') }}" class="border rounded w-full px-3 py-2">
            @error('name') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Описание</label>
            <textarea name="description" rows="3" class="border rounded w-full px-3 py-2">{{ old('description') }}</textarea>
            @error('description') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Баллы</label>
                <input type="number" name="points" value="{{ old('points') }}" class="border rounded w-full px-3 py-2">
                @error('points') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Тип</label>
                <select name="type" class="border rounded w-full px-3 py-2">
                    <option value="reward" @selected(old('type') === 'reward')>Награда</option>
                    <option value="penalty" @selected(old('type') === 'penalty')>Штраф</option>
                </select>
                @error('type') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="flex items-center mt-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" class="mr-2" checked>
                    Активно
                </label>
            </div>
        </div>

        <div class="flex space-x-2">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">Сохранить</button>
            <a href="{{ route('admin.rules.index') }}" class="text-sm text-gray-600">Отмена</a>
        </div>
    </form>
@endsection
