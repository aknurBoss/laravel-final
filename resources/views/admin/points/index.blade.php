@extends('layouts.app')
@section('content')
    <h1 class="text-2xl font-semibold mb-4">Выдача баллов</h1>

    <div class="mb-4 flex gap-2">
        <a href="{{ route('admin.points', ['mode' => '1']) }}" class="text-sm {{ $mode === '1' ? 'font-semibold' : '' }}">Правило → Ученики</a>
        <a href="{{ route('admin.points', ['mode' => '2']) }}" class="text-sm {{ $mode === '2' ? 'font-semibold' : '' }}">Ученик → Правила</a>
    </div>

    @if ($mode === '1')
        <form method="POST" action="{{ route('admin.points.assign') }}" class="space-y-3 mb-6">
            @csrf
            <input type="hidden" name="mode" value="1">

            <div>
                <label class="block text-sm mb-1">Правило</label>
                <select name="rule_id" class="border rounded px-2 py-1 w-full">
                    @foreach ($rules as $rule)
                        <option value="{{ $rule->id }}">{{ $rule->name }} ({{ $rule->points }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm mb-1">Ученики</label>
                @foreach ($classes as $class)
                    @if ($class->students->isNotEmpty())
                        <div class="mb-2">
                            <strong>{{ $class->name }}</strong>
                            <div class="pl-2">
                                @foreach ($class->students as $student)
                                    <label class="block text-sm">
                                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}">
                                        {{ $student->user?->name ?? '—' }} ({{ $student->current_points }} б)
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div>
                <label class="block text-sm mb-1">Комментарий</label>
                <textarea name="comment" rows="2" class="border rounded px-2 py-1 w-full">{{ old('comment') }}</textarea>
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">Назначить</button>
        </form>
    @else
        <form method="POST" action="{{ route('admin.points.assign') }}" class="space-y-3 mb-6">
            @csrf
            <input type="hidden" name="mode" value="2">

            <div>
                <label class="block text-sm mb-1">Ученик</label>
                <select name="student_id" class="border rounded px-2 py-1 w-full">
                    @foreach ($allStudents as $student)
                        <option value="{{ $student->id }}">{{ $student->user?->name ?? '—' }} ({{ $student->current_points }} б)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm mb-1">Правила</label>
                @foreach ($rules as $rule)
                    <label class="block text-sm">
                        <input type="checkbox" name="rule_ids[]" value="{{ $rule->id }}">
                        {{ $rule->name }} ({{ $rule->points }})
                    </label>
                @endforeach
            </div>

            <div>
                <label class="block text-sm mb-1">Комментарий</label>
                <textarea name="comment" rows="2" class="border rounded px-2 py-1 w-full">{{ old('comment') }}</textarea>
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">Назначить</button>
        </form>
    @endif

    <h2 class="text-lg font-semibold mb-2">История назначений</h2>
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2 px-3">Время</th>
                    <th class="text-left py-2 px-3">Ученик</th>
                    <th class="text-left py-2 px-3">Класс</th>
                    <th class="text-left py-2 px-3">Кем</th>
                    <th class="text-left py-2 px-3">Правило</th>
                    <th class="text-left py-2 px-3">Баллы</th>
                    <th class="text-left py-2 px-3">Комментарий</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($history as $record)
                    <tr class="border-b last:border-0">
                        <td class="py-2 px-3">{{ $record->created_at->format('d.m H:i') }}</td>
                        <td class="py-2 px-3">{{ $record->student?->user?->name ?? '—' }}</td>
                        <td class="py-2 px-3">{{ $record->student?->schoolClass?->name ?? '—' }}</td>
                        <td class="py-2 px-3">{{ $record->teacher?->name ?? '—' }}</td>
                        <td class="py-2 px-3">{{ $record->rule?->name ?? '—' }}</td>
                        <td class="py-2 px-3">{{ $record->points }}</td>
                        <td class="py-2 px-3">{{ $record->comment ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-3 px-3 text-center text-gray-500">Нет записей.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
