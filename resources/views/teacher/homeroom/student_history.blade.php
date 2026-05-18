@extends('layouts.app')

@section('title', 'История ученика')

@section('page-header')
    <h1 class="page-title">{{ $student->user?->name ?? 'Ученик' }}</h1>
    <p class="page-subtitle">
        Класс {{ $student->schoolClass?->name }} ·
        Текущие баллы:
        <strong class="{{ $student->current_points >= 0 ? 'text-merit' : 'text-demerit' }}">
            {{ $student->current_points }}
        </strong>
    </p>
@endsection

@section('content')
    <a href="{{ route('teacher.homeroom') }}" class="inline-flex text-sm text-brand-600 hover:underline mb-6">← Назад к классу</a>

    <div class="card overflow-hidden">
        @if ($history->isEmpty())
            <div class="px-6 py-12 text-center text-slate-500 text-sm">История пока пуста.</div>
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
                                <td class="text-xs text-slate-500 whitespace-nowrap">{{ $record->created_at->format('d.m.Y H:i') }}</td>
                                <td>{{ $record->teacher?->name ?? 'Админ' }}</td>
                                <td>{{ $record->rule?->description ?? $record->rule?->name }}</td>
                                <td>
                                    @if ($record->points > 0)
                                        <span class="points-plus">+{{ $record->points }}</span>
                                    @else
                                        <span class="points-minus">{{ $record->points }}</span>
                                    @endif
                                </td>
                                <td class="text-xs text-slate-500">{{ $record->comment ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
