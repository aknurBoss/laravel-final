@extends('layouts.app')

@section('title', $semester->name)

@section('page-header')
    <h1 class="page-title">Архив: {{ $semester->name }}</h1>
    <p class="page-subtitle">
        {{ $semester->started_at->format('d.m.Y') }} — {{ $semester->closed_at?->format('d.m.Y') }}
        @if ($semester->closedBy)
            · закрыл {{ $semester->closedBy->name }}
        @endif
    </p>
@endsection

@section('content')
    <a href="{{ route('admin.semesters.index') }}" class="inline-flex text-sm text-brand-600 hover:underline mb-6">← Все семестры</a>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
        <div class="stat-card">
            <span class="stat-label">Учеников</span>
            <span class="stat-value">{{ $semester->students_count }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Записей</span>
            <span class="stat-value">{{ $semester->records_count }}</span>
        </div>
        <div class="stat-card border-emerald-100">
            <span class="stat-label text-emerald-700">Мериты</span>
            <span class="stat-value text-merit">+{{ $semester->total_merits }}</span>
        </div>
        <div class="stat-card border-red-100">
            <span class="stat-label text-red-700">Демериты</span>
            <span class="stat-value text-demerit">−{{ $semester->total_demerits }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                <h2 class="font-semibold text-slate-900">Итоговый рейтинг (топ-50)</h2>
            </div>
            <div class="overflow-x-auto max-h-[28rem] overflow-y-auto">
                <table class="data-table">
                    <thead class="sticky top-0 bg-slate-50">
                        <tr>
                            <th>#</th>
                            <th>Ученик</th>
                            <th>Класс</th>
                            <th>Баллы</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($snapshots as $row)
                            <tr>
                                <td class="text-slate-400 font-medium">{{ $row->global_rank }}</td>
                                <td class="font-medium">{{ $row->student_name }}</td>
                                <td class="text-slate-500">{{ $row->class_name ?? '—' }}</td>
                                <td>
                                    @if ($row->final_points >= 0)
                                        <span class="points-plus">{{ $row->final_points }}</span>
                                    @else
                                        <span class="points-minus">{{ $row->final_points }}</span>
                                    @endif
                                    @if ($row->class_rank)
                                        <span class="text-xs text-slate-400 block">в классе #{{ $row->class_rank }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500">Нет данных.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-border">
                <h2 class="font-semibold text-slate-900">Последние операции семестра</h2>
            </div>
            @if ($latestHistory->isEmpty())
                <div class="px-6 py-12 text-center text-slate-500 text-sm">Записей нет.</div>
            @else
                <div class="overflow-x-auto max-h-[28rem] overflow-y-auto">
                    <table class="data-table">
                        <thead class="sticky top-0 bg-slate-50">
                            <tr>
                                <th>Дата</th>
                                <th>Ученик</th>
                                <th>Баллы</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestHistory as $record)
                                <tr>
                                    <td class="text-xs text-slate-500 whitespace-nowrap">{{ $record->created_at->format('d.m H:i') }}</td>
                                    <td class="text-sm">{{ $record->student?->user?->name ?? '—' }}</td>
                                    <td>
                                        @if ($record->points > 0)
                                            <span class="points-plus">+{{ $record->points }}</span>
                                        @else
                                            <span class="points-minus">{{ $record->points }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
