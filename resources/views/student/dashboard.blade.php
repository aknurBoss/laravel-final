@extends('layouts.app')

@section('title', 'Мой профиль')

@section('page-header')
    <h1 class="page-title">Кабинет ученика</h1>
    <p class="page-subtitle">Ваши баллы, история и место в рейтинге школы и класса</p>
@endsection

@section('content')
    @php
        $points = $student->current_points;
        $pointsTone = $points >= 0 ? 'text-merit' : 'text-demerit';
        $pointsBg = $points >= 0 ? 'from-white to-merit-soft border-emerald-100' : 'from-white to-demerit-soft border-red-100';
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="card-padded">
            <span class="stat-label">Ученик</span>
            <p class="text-xl font-bold text-slate-900 mt-1">{{ $student->user?->name ?? '—' }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $student->user?->email }}</p>
        </div>
        <div class="card-padded">
            <span class="stat-label">Класс</span>
            <p class="text-xl font-bold text-slate-900 mt-1">{{ $student->schoolClass?->name ?? 'Без класса' }}</p>
        </div>
        <div class="stat-card bg-gradient-to-br {{ $pointsBg }}">
            <span class="stat-label">Текущие баллы</span>
            <span class="stat-value {{ $pointsTone }}">{{ $points > 0 ? '+' : '' }}{{ $points }}</span>
            <p class="text-xs text-slate-500 mt-1">
                Место в школе: <strong class="text-slate-700">#{{ $globalRank }}</strong>
                @if ($classRank)
                    · в классе: <strong class="text-slate-700">#{{ $classRank }}</strong>
                @endif
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="card lg:col-span-2 overflow-hidden">
            <div class="px-6 py-4 border-b border-border">
                <h2 class="font-semibold text-slate-900">История баллов</h2>
                <p class="text-xs text-slate-500 mt-0.5">Прозрачная лента: кто начислил и за что</p>
            </div>

            @if ($history->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-slate-500">История пока пуста.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Когда</th>
                                <th>Кто</th>
                                <th>Правило</th>
                                <th>Баллы</th>
                                <th>Комментарий</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($history as $record)
                                <tr>
                                    <td class="text-xs text-slate-500 whitespace-nowrap">{{ $record->created_at->format('d.m H:i') }}</td>
                                    <td class="text-slate-700">{{ $record->teacher?->name ?? 'Админ' }}</td>
                                    <td class="text-slate-800">{{ $record->rule?->description ?? $record->rule?->name }}</td>
                                    <td>
                                        @if ($record->points > 0)
                                            <span class="points-plus">+{{ $record->points }}</span>
                                        @else
                                            <span class="points-minus">{{ $record->points }}</span>
                                        @endif
                                    </td>
                                    <td class="text-slate-500 text-xs">{{ $record->comment ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="card-padded">
                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="text-lg">🏆</span> Топ-10 по школе
                </h3>
                @if ($topSchool->isEmpty())
                    <p class="text-xs text-slate-500">Пока нет данных.</p>
                @else
                    <ol class="space-y-2">
                        @foreach ($topSchool as $idx => $s)
                            <li class="flex items-center justify-between text-sm rounded-lg px-2 py-1.5 {{ $s->id === $student->id ? 'bg-brand-50 ring-1 ring-brand-100' : '' }}">
                                <span class="text-slate-700 truncate pr-2">
                                    <span class="font-bold text-slate-400 w-5 inline-block">#{{ $idx + 1 }}</span>
                                    {{ $s->user?->name ?? '—' }}
                                    <span class="text-xs text-slate-400">({{ $s->schoolClass?->name ?? '—' }})</span>
                                </span>
                                <span class="font-bold tabular-nums {{ $s->current_points >= 0 ? 'text-merit' : 'text-demerit' }}">{{ $s->current_points }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

            <div class="card-padded">
                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="text-lg">📋</span> Топ-10 в классе
                </h3>
                @if ($topClass->isEmpty())
                    <p class="text-xs text-slate-500">Нет класса или данных.</p>
                @else
                    <ol class="space-y-2">
                        @foreach ($topClass as $idx => $s)
                            <li class="flex items-center justify-between text-sm rounded-lg px-2 py-1.5 {{ $s->id === $student->id ? 'bg-brand-50 ring-1 ring-brand-100' : '' }}">
                                <span class="text-slate-700 truncate pr-2">
                                    <span class="font-bold text-slate-400 w-5 inline-block">#{{ $idx + 1 }}</span>
                                    {{ $s->user?->name ?? '—' }}
                                </span>
                                <span class="font-bold tabular-nums {{ $s->current_points >= 0 ? 'text-merit' : 'text-demerit' }}">{{ $s->current_points }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>
    </div>
@endsection
