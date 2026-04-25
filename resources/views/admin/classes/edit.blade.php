@extends('layouts.app')
@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.classes.index') }}" class="text-sm text-slate-500 hover:text-indigo-600 flex items-center gap-1">
            Назад к классам
        </a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">Редактировать класс</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-sm">
        <form action="{{ route('admin.classes.update', $class) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Название класса</label>
                <input type="text" name="name" value="{{ old('name', $class->name) }}" placeholder="Например: 9А" autofocus
                       class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors">Сохранить</button>
                <a href="{{ route('admin.classes.index') }}" class="px-5 py-2.5 rounded-xl text-sm text-slate-600 hover:bg-slate-100 transition-colors">Отмена</a>
            </div>
        </form>
    </div>

@endsection
