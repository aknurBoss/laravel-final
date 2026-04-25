@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-3">
        <h1 class="text-2xl font-semibold">Учителя</h1>
        <div class="flex-1 md:flex-none md:w-auto">
            <form method="GET" action="{{ route('admin.teachers.index') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Поиск по имени/email"
                       class="border rounded px-3 py-1 text-sm w-full md:w-64">
                <button type="submit" class="bg-gray-200 px-3 py-1 rounded text-sm">Найти</button>
            </form>
        </div>
        <a href="{{ route('admin.teachers.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">
            + Добавить учителя
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b">
                <th class="text-left py-2 px-3">Имя</th>
                <th class="text-left py-2 px-3">Email</th>
                <th class="text-left py-2 px-3">Предмет</th>
                <th class="text-left py-2 px-3">Активен</th>
                <th class="text-left py-2 px-3"></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($teachers as $teacher)
                <tr class="border-b last:border-0">
                    <td class="py-2 px-3">{{ $teacher->user?->name ?? '—' }}</td>
                    <td class="py-2 px-3">{{ $teacher->user?->email ?? '—' }}</td>
                    <td class="py-2 px-3">{{ $teacher->subject ?? '—' }}</td>
                    <td class="py-2 px-3">{{ $teacher->user?->is_active ? 'Да' : 'Нет' }}</td>
                    <td class="py-2 px-3 text-right space-x-3">
                        @if ($teacher->user)
                            <a href="{{ route('admin.history.teacher', $teacher->user) }}" class="text-gray-500 text-sm">История</a>
                        @endif
                        <a href="{{ route('admin.teachers.edit', $teacher) }}" class="text-indigo-600 text-sm">Редактировать</a>
                        <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" class="inline"
                              onsubmit="return confirm('Удалить учителя «{{ $teacher->user?->name }}»?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 text-sm">Удалить</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-4 px-3 text-center text-gray-500">
                        Учителей пока нет.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $teachers->links() }}</div>
@endsection
