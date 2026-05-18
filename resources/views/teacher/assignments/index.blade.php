@extends('layouts.app')

@section('title', 'Мои назначения')

@section('page-header')
    <h1 class="page-title">Мои назначения</h1>
    <p class="page-subtitle">История выданных вами баллов — можно отфильтровать и отменить запись</p>
@endsection

@section('content')
    <div class="card overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-border">
            <p class="text-sm text-slate-600">
                Записей: <strong class="text-slate-900">{{ $history->count() }}</strong>
            </p>

            <form method="GET" action="{{ route('teacher.assignments') }}" class="flex flex-wrap items-end gap-2 text-xs">
                <div>
                    <label class="form-label mb-1">С</label>
                    <input type="date" name="hf" value="{{ request('hf') }}" class="form-input py-1.5">
                </div>
                <div>
                    <label class="form-label mb-1">По</label>
                    <input type="date" name="ht" value="{{ request('ht') }}" class="form-input py-1.5">
                </div>
                <div>
                    <label class="form-label mb-1">Тип</label>
                    <select name="htype" class="form-select py-1.5">
                        <option value="">Все</option>
                        <option value="reward" @selected(request('htype') === 'reward')>Награды</option>
                        <option value="penalty" @selected(request('htype') === 'penalty')>Штрафы</option>
                    </select>
                </div>
                <button type="submit" class="btn-secondary py-1.5">Применить</button>
                @if (request()->hasAny(['hf', 'ht', 'htype']))
                    <a href="{{ route('teacher.assignments') }}" class="btn-ghost py-1.5">Сбросить</a>
                @endif
            </form>
        </div>

        @if ($history->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-slate-500 text-sm mb-4">История пуста.</p>
                <a href="{{ route('teacher.dashboard') }}" class="btn-primary inline-flex">Выдать баллы</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Время</th>
                            <th>Ученик</th>
                            <th>Класс</th>
                            <th>Правило</th>
                            <th>Баллы</th>
                            <th>Комментарий</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $record)
                            <tr>
                                <td class="text-xs text-slate-500 whitespace-nowrap">{{ $record->created_at->format('d.m.Y H:i') }}</td>
                                <td class="font-medium text-slate-800">{{ $record->student?->user?->name ?? '—' }}</td>
                                <td class="text-slate-500">{{ $record->student?->schoolClass?->name ?? '—' }}</td>
                                <td class="text-slate-700">{{ $record->rule?->name ?? '—' }}</td>
                                <td>
                                    @if ($record->points > 0)
                                        <span class="points-plus">+{{ $record->points }}</span>
                                    @else
                                        <span class="points-minus">{{ $record->points }}</span>
                                    @endif
                                </td>
                                <td class="text-xs text-slate-500 max-w-[12rem] truncate">{{ $record->comment ?? '—' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('teacher.history.cancel', $record) }}"
                                          onsubmit="return confirm('Отменить это назначение?');">
                                        @csrf
                                        <button type="submit" class="text-xs text-red-500 font-medium hover:underline">
                                            Отменить
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="mt-4">
        <a href="{{ route('teacher.dashboard') }}" class="text-sm text-brand-600 hover:underline">← Выдача баллов</a>
    </div>
@endsection
