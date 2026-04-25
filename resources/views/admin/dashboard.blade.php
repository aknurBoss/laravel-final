@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Админ панель</h1>

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('admin.students.index') }}" class="text-indigo-600 hover:underline">Ученики</a>
        <a href="{{ route('admin.teachers.index') }}" class="text-indigo-600 hover:underline">Учителя</a>
        <a href="{{ route('admin.classes.index') }}" class="text-indigo-600 hover:underline">Классы</a>
        <a href="{{ route('admin.rules.index') }}" class="text-indigo-600 hover:underline">Правила</a>
        <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:underline">Аккаунты</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold text-sm text-gray-500 mb-1">Студентов</h2>
            <div class="text-2xl font-bold">{{ $totalStudents }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold text-sm text-gray-500 mb-1">Учителей</h2>
            <div class="text-2xl font-bold">{{ $totalTeachers }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold text-sm text-gray-500 mb-1">Положительные баллы</h2>
            <div class="text-2xl font-bold text-green-600">+{{ $sumPositivePoints }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold text-sm text-gray-500 mb-1">Отрицательные баллы</h2>
            <div class="text-2xl font-bold text-red-600">-{{ $sumNegativePoints }}</div>
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <div class="flex justify-between items-center mb-3">
            <h2 class="font-semibold">Последние операции ({{ $totalAssignments }})</h2>
        </div>

        @if ($latestHistory->isEmpty())
            <p class="text-sm text-gray-500">Пока нет записей.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 pr-2">Время</th>
                        <th class="text-left py-2 pr-2">Ученик</th>
                        <th class="text-left py-2 pr-2">Класс</th>
                        <th class="text-left py-2 pr-2">Учитель</th>
                        <th class="text-left py-2 pr-2">Правило</th>
                        <th class="text-left py-2 pr-2">Баллы</th>
                        <th class="text-left py-2 pr-2">Комментарий</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($latestHistory as $record)
                        <tr class="border-b last:border-0">
                            <td class="py-2 pr-2 whitespace-nowrap">
                                {{ $record->created_at->format('d.m H:i') }}
                            </td>
                            <td class="py-2 pr-2">
                                {{ $record->student?->user?->name ?? '—' }}
                            </td>
                            <td class="py-2 pr-2">
                                {{ $record->student?->schoolClass?->name ?? 'Без класса' }}
                            </td>
                            <td class="py-2 pr-2">
                                {{ $record->teacher?->name ?? 'Admin' }}
                            </td>
                            <td class="py-2 pr-2">
                                {{ $record->rule?->description ?? $record->rule?->name }}
                            </td>
                            <td class="py-2 pr-2">
                                @if ($record->points > 0)
                                    <span class="text-green-600">+{{ $record->points }}</span>
                                @else
                                    <span class="text-red-600">{{ $record->points }}</span>
                                @endif
                            </td>
                            <td class="py-2 pr-2">
                                {{ $record->comment }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
