@extends('layouts.app')

@section('title', 'Выдача баллов')

@section('page-header')
    <h1 class="page-title">Выдача баллов</h1>
    <p class="page-subtitle">Назначение меритов и демеритов — как у учителя, с полным контролем администратора</p>
@endsection

@section('content')
<div class="flex gap-2 mb-6">
    <a href="{{ route('admin.points', ['mode' => '1']) }}"
       class="px-5 py-2 rounded-xl text-sm font-semibold transition-colors
              {{ $mode === '1' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-border hover:border-brand-300' }}">
        Правило → Ученики
    </a>
    <a href="{{ route('admin.points', ['mode' => '2']) }}"
       class="px-5 py-2 rounded-xl text-sm font-semibold transition-colors
              {{ $mode === '2' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-border hover:border-brand-300' }}">
        Ученик → Правила
    </a>
</div>

@if ($mode === '1')
<div class="card-padded mb-6">
    <h2 class="font-semibold text-slate-800 mb-0.5">Режим 1 — одно правило, несколько учеников</h2>
    <p class="text-xs text-slate-400 mb-5">Идеально для массового поощрения или взыскания класса.</p>

    <form method="POST" action="{{ route('admin.points.assign') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="mode" value="1">

        <div>
            <label class="form-label">Правило</label>
            <select name="rule_id" class="form-select" required>
                @foreach ($rules as $rule)
                    <option value="{{ $rule->id }}">{{ $rule->name }} ({{ $rule->points > 0 ? '+' : '' }}{{ $rule->points }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label">Ученики по классам</label>
            <div class="border border-border rounded-xl divide-y divide-slate-100 max-h-64 overflow-y-auto" id="students-list">
                @foreach ($classes as $class)
                    @if ($class->students->isNotEmpty())
                        <div class="p-4">
                            <p class="text-sm font-semibold text-slate-800 mb-2">{{ $class->name }}</p>
                            <div class="grid sm:grid-cols-2 gap-1">
                                @foreach ($class->students as $student)
                                    <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-slate-50 rounded-lg px-2 py-1.5">
                                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="rounded text-brand-600">
                                        <span class="text-slate-700">{{ $student->user?->name ?? '—' }}</span>
                                        <span class="text-xs text-slate-400 ml-auto">{{ $student->current_points }} б</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div>
            <label class="form-label">Комментарий <span class="text-slate-400 font-normal">(необязательно)</span></label>
            <textarea name="comment" rows="2" class="form-textarea">{{ old('comment') }}</textarea>
        </div>

        <button type="submit" class="btn-success">Назначить баллы</button>
    </form>
</div>
@else
<div class="card-padded mb-6">
    <h2 class="font-semibold text-slate-800 mb-0.5">Режим 2 — один ученик, несколько правил</h2>
    <p class="text-xs text-slate-400 mb-5">Для разбора инцидента с несколькими нарушениями.</p>

    <form method="POST" action="{{ route('admin.points.assign') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="mode" value="2">

        <div>
            <label class="form-label">Ученик</label>
            <select name="student_id" class="form-select" required>
                @foreach ($allStudents as $student)
                    <option value="{{ $student->id }}">{{ $student->user?->name ?? '—' }} ({{ $student->current_points }} б)</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label">Правила</label>
            <div class="border border-border rounded-xl p-3 space-y-1.5 max-h-56 overflow-y-auto">
                @foreach ($rules as $rule)
                    <label class="flex items-center gap-2.5 text-sm cursor-pointer hover:bg-slate-50 rounded-lg px-2 py-1">
                        <input type="checkbox" name="rule_ids[]" value="{{ $rule->id }}" class="rounded text-brand-600">
                        <span class="flex-1">{{ $rule->name }}</span>
                        @if ($rule->points > 0)
                            <span class="points-plus text-xs">+{{ $rule->points }}</span>
                        @else
                            <span class="points-minus text-xs">{{ $rule->points }}</span>
                        @endif
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="form-label">Комментарий</label>
            <textarea name="comment" rows="2" class="form-textarea">{{ old('comment') }}</textarea>
        </div>

        <button type="submit" class="btn-success">Назначить баллы</button>
    </form>
</div>
@endif

<div class="card overflow-hidden">
    <div class="px-6 py-4 border-b border-border">
        <h2 class="font-semibold text-slate-900">История назначений</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Время</th>
                    <th>Ученик</th>
                    <th>Класс</th>
                    <th>Кем</th>
                    <th>Правило</th>
                    <th>Баллы</th>
                    <th>Комментарий</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($history as $record)
                    <tr>
                        <td class="text-xs text-slate-500 whitespace-nowrap">{{ $record->created_at->format('d.m H:i') }}</td>
                        <td class="font-medium">{{ $record->student?->user?->name ?? '—' }}</td>
                        <td class="text-slate-500">{{ $record->student?->schoolClass?->name ?? '—' }}</td>
                        <td>{{ $record->teacher?->name ?? 'Админ' }}</td>
                        <td>{{ $record->rule?->name ?? '—' }}</td>
                        <td>
                            @if ($record->points > 0)
                                <span class="points-plus">+{{ $record->points }}</span>
                            @else
                                <span class="points-minus">{{ $record->points }}</span>
                            @endif
                        </td>
                        <td class="text-xs text-slate-500">{{ $record->comment ?? '—' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.points.cancel', $record) }}"
                                  onsubmit="return confirm('Отменить назначение?');">
                                @csrf
                                <button type="submit" class="text-xs text-red-500 font-medium hover:underline">Отменить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500">Нет записей.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
