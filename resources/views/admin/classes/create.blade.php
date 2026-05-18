@extends('layouts.app')

@section('title', 'Новый класс')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.classes.index') }}" class="text-sm text-slate-500 hover:text-brand-600">← Классы</a>
        <h1 class="page-title mt-2">Новый класс</h1>
    </div>

    <div class="card-padded max-w-lg">
        <form action="{{ route('admin.classes.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="form-label">Название класса</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="9А" class="form-input" required autofocus>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">Классный руководитель <span class="text-slate-400 font-normal">(необязательно)</span></label>
                <select name="homeroom_teacher_id" class="form-select">
                    <option value="">— Не назначен —</option>
                    @foreach ($teachers as $t)
                        <option value="{{ $t->id }}" @selected(old('homeroom_teacher_id') == $t->id)
                                @disabled($t->homeroomClass !== null)>
                            {{ $t->user?->name ?? '—' }}
                            @if ($t->homeroomClass)
                                (уже ведёт {{ $t->homeroomClass->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('homeroom_teacher_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Сохранить</button>
                <a href="{{ route('admin.classes.index') }}" class="btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
@endsection
