@extends('layouts.app')
@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.students.index') }}" class="text-sm text-slate-500 hover:text-indigo-600 flex items-center gap-1">
            Назад к ученикам
        </a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">Редактировать ученика</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-xl">
        <form action="{{ route('admin.students.update', $student) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            @php $inp = 'w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500'; @endphp

            <div class="border-b border-slate-100 pb-4 mb-1">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-3">Данные аккаунта</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Имя</label>
                        <input type="text" name="name" value="{{ old('name', $student->user?->name) }}" class="{{ $inp }}">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $student->user?->email) }}" class="{{ $inp }}">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-3">Класс и баллы</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Класс</label>
                        <select name="class_id" class="{{ $inp }}" required>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}" @selected(old('class_id', $student->class_id) == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Текущие баллы</label>
                        <input type="number" name="current_points" value="{{ old('current_points', $student->current_points) }}" min="0" class="{{ $inp }}">
                        @error('current_points') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors">Сохранить</button>
                <a href="{{ route('admin.students.index') }}" class="px-5 py-2.5 rounded-xl text-sm text-slate-600 hover:bg-slate-100 transition-colors">Отмена</a>
            </div>
        </form>
    </div>

@endsection
