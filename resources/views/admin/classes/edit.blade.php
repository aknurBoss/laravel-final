@extends('layouts.app')

@section('title', 'Редактировать класс')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.classes.index') }}" class="text-sm text-slate-500 hover:text-brand-600">← Классы</a>
        <h1 class="page-title mt-2">Класс {{ $class->name }}</h1>
    </div>

    <div class="card-padded max-w-lg">
        <form action="{{ route('admin.classes.update', $class) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="form-label">Название класса</label>
                <input type="text" name="name" value="{{ old('name', $class->name) }}" class="form-input" required autofocus>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Классный руководитель</label>
                <select name="homeroom_teacher_id" class="form-select">
                    <option value="">— Не назначен —</option>
                    @foreach ($teachers as $t)
                        @php
                            $takenElsewhere = $t->homeroomClass && $t->homeroomClass->id !== $class->id;
                        @endphp
                        <option value="{{ $t->id }}"
                                @selected(old('homeroom_teacher_id', $class->homeroom_teacher_id) == $t->id)
                                @disabled($takenElsewhere)>
                            {{ $t->user?->name ?? '—' }}
                            @if ($takenElsewhere)
                                (класс {{ $t->homeroomClass->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('homeroom_teacher_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-slate-500 mt-1.5">У каждого класса может быть только один классный руководитель.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Сохранить</button>
                <a href="{{ route('admin.classes.index') }}" class="btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
@endsection
