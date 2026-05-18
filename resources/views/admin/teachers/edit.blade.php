@extends('layouts.app')

@section('title', 'Редактировать учителя')

@section('content')
    @php
        $isHomeroom = old('is_homeroom_teacher', $teacher->is_homeroom_teacher);
        $selectedClassId = old('homeroom_class_id', $teacher->homeroomClass?->id);
    @endphp

    <div class="mb-6">
        <a href="{{ route('admin.teachers.index') }}" class="text-sm text-slate-500 hover:text-brand-600">← Учителя</a>
        <h1 class="page-title mt-2">Редактировать учителя</h1>
    </div>

    <div class="card-padded max-w-xl">
        <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div class="space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Аккаунт</h3>
                <div>
                    <label class="form-label">Имя</label>
                    <input type="text" name="name" value="{{ old('name', $teacher->user?->name) }}" class="form-input" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $teacher->user?->email) }}" class="form-input" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Новый пароль <span class="text-slate-400 font-normal">(необязательно)</span></label>
                    <input type="password" name="password" class="form-input">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="border-t border-border pt-5 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Роль в школе</h3>
                <label class="flex items-start gap-3 cursor-pointer rounded-xl border border-border p-4 hover:bg-slate-50">
                    <input type="checkbox" name="is_homeroom_teacher" value="1" id="is_homeroom"
                           class="mt-0.5 rounded text-brand-600"
                           @checked($isHomeroom)>
                    <span>
                        <span class="font-semibold text-slate-900 block">Классный руководитель</span>
                        <span class="text-xs text-slate-500">Управление своим классом: ученики, баллы, история.</span>
                    </span>
                </label>

                <div id="homeroom-class-wrap" class="{{ $isHomeroom ? '' : 'hidden' }}">
                    <label class="form-label">Класс</label>
                    <select name="homeroom_class_id" class="form-select">
                        <option value="">— Выберите класс —</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" @selected($selectedClassId == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('homeroom_class_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Сохранить</button>
                <a href="{{ route('admin.teachers.index') }}" class="btn-secondary">Отмена</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('is_homeroom')?.addEventListener('change', function () {
            document.getElementById('homeroom-class-wrap').classList.toggle('hidden', !this.checked);
        });
    </script>
@endsection
