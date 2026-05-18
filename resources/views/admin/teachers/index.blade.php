@extends('layouts.app')

@section('title', 'Учителя')

@section('page-header')
    <h1 class="page-title">Учителя</h1>
    <p class="page-subtitle">Обычные учителя выдают баллы; классный руководитель ведёт свой класс</p>
@endsection

@section('content')
    <div class="flex flex-col md:flex-row md:justify-end md:items-center mb-6 gap-3">
        <form method="GET" action="{{ route('admin.teachers.index') }}" class="flex gap-2 flex-1 md:max-w-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Поиск по имени/email" class="form-input">
            <button type="submit" class="btn-secondary">Найти</button>
        </form>
        <a href="{{ route('admin.teachers.create') }}" class="btn-primary shrink-0">+ Добавить учителя</a>
    </div>

    <div class="card overflow-hidden">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Тип</th>
                    <th>Класс</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teachers as $teacher)
                    <tr>
                        <td class="font-medium text-slate-800">{{ $teacher->user?->name ?? '—' }}</td>
                        <td class="text-slate-600">{{ $teacher->user?->email ?? '—' }}</td>
                        <td>
                            @if ($teacher->is_homeroom_teacher)
                                <span class="badge bg-sky-100 text-sky-800">Классный руководитель</span>
                            @else
                                <span class="badge-neutral">Учитель</span>
                            @endif
                        </td>
                        <td class="text-slate-600">{{ $teacher->homeroomClass?->name ?? '—' }}</td>
                        <td class="text-right space-x-2 whitespace-nowrap">
                            @if ($teacher->user)
                                <a href="{{ route('admin.history.teacher', $teacher->user) }}" class="text-xs text-slate-500 hover:underline">История</a>
                            @endif
                            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="text-xs text-brand-600 font-medium hover:underline">Изменить</a>
                            <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Удалить учителя?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 font-medium hover:underline">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-500">Учителей пока нет.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $teachers->links() }}</div>
@endsection
