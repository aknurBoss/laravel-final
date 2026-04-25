@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-3">
        <h1 class="text-2xl font-semibold">Ученики</h1>
        <div class="flex-1 md:flex-none md:w-auto">
            <form method="GET" action="{{ route('admin.students.index') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Поиск по имени/email"
                       class="border rounded px-3 py-1 text-sm w-full md:w-64">
                <button type="submit" class="bg-gray-200 px-3 py-1 rounded text-sm">Найти</button>
            </form>
        </div>
        <a href="{{ route('admin.students.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">
            + Добавить ученика
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b">
                <th class="text-left py-2 px-3">ID</th>
                <th class="text-left py-2 px-3">Имя</th>
                <th class="text-left py-2 px-3">Email</th>
                <th class="text-left py-2 px-3">Класс</th>
                <th class="text-left py-2 px-3">Баллы</th>
                <th class="text-left py-2 px-3"></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($students as $student)
                <tr class="border-b last:border-0">
                    <td class="py-2 px-3">{{ $student->id }}</td>
                    <td class="py-2 px-3">{{ $student->user?->name ?? '—' }}</td>
                    <td class="py-2 px-3">{{ $student->user?->email ?? '—' }}</td>
                    <td class="py-2 px-3">{{ $student->schoolClass?->name ?? 'Без класса' }}</td>
                    <td class="py-2 px-3">{{ $student->current_points }}</td>
                    <td class="py-2 px-3 text-right space-x-2">
                        <a href="{{ route('admin.history.student', $student) }}" class="text-sm text-gray-600">
                            История
                        </a>
                        <a href="{{ route('admin.students.edit', $student) }}" class="text-indigo-600 text-sm">Редактировать</a>
                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline"
                              onsubmit="return confirm('Удалить ученика из списка?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 text-sm">Удалить</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-3 px-3 text-center text-gray-500">
                        Учеников пока нет. Сначала создайте пользователя с ролью «student», затем добавьте его здесь в класс.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $students->links() }}
    </div>
@endsection
