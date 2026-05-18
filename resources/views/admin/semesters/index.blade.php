@extends('layouts.app')

@section('title', 'Семестры')

@section('page-header')
    <h1 class="page-title">Семестры и архив</h1>
    <p class="page-subtitle">Закрытие семестра сохраняет историю и итоговые баллы, затем сбрасывает всех учеников до {{ \App\Models\Student::startingPoints() }} баллов</p>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="card-padded lg:col-span-2 border-brand-100 bg-gradient-to-br from-white to-brand-50">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <span class="badge bg-brand-100 text-brand-700">Активный семестр</span>
                    <h2 class="text-xl font-bold text-slate-900 mt-2">{{ $activeSemester->name }}</h2>
                    <p class="text-sm text-slate-500 mt-1">Начат {{ $activeSemester->started_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                <div class="rounded-xl bg-white border border-border p-3 text-center">
                    <div class="text-2xl font-bold text-slate-900">{{ $activeStats['students'] }}</div>
                    <div class="text-xs text-slate-500 mt-1">Учеников</div>
                </div>
                <div class="rounded-xl bg-white border border-border p-3 text-center">
                    <div class="text-2xl font-bold text-slate-900">{{ $activeStats['records'] }}</div>
                    <div class="text-xs text-slate-500 mt-1">Записей</div>
                </div>
                <div class="rounded-xl bg-white border border-border p-3 text-center">
                    <div class="text-2xl font-bold text-merit">+{{ $activeStats['merits'] }}</div>
                    <div class="text-xs text-slate-500 mt-1">Мериты</div>
                </div>
                <div class="rounded-xl bg-white border border-border p-3 text-center">
                    <div class="text-2xl font-bold text-demerit">−{{ $activeStats['demerits'] }}</div>
                    <div class="text-xs text-slate-500 mt-1">Демериты</div>
                </div>
            </div>

            <div class="border-t border-border pt-5">
                <h3 class="font-semibold text-slate-900 mb-1">Закрыть семестр и начать новый</h3>
                <p class="text-xs text-slate-500 mb-4">
                    История баллов уйдёт в архив. У всех учеников баллы станут {{ \App\Models\Student::startingPoints() }}.
                    Уведомления будут очищены.
                </p>
                <form method="POST" action="{{ route('admin.semesters.close') }}"
                      class="space-y-3"
                      onsubmit="return confirm('Закрыть текущий семестр? Это действие нельзя отменить.');">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Название закрываемого семестра</label>
                            <input type="text" name="closed_name" class="form-input" required
                                   value="{{ old('closed_name', $activeSemester->name) }}">
                            @error('closed_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Название нового семестра</label>
                            <input type="text" name="new_name" class="form-input" required
                                   value="{{ old('new_name', 'Новый семестр ' . now()->format('Y')) }}"
                                   placeholder="II полугодие 2025-2026">
                            @error('new_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn-danger">Закрыть семестр и сбросить баллы</button>
                </form>
            </div>
        </div>

        <div class="card-padded">
            <h3 class="font-semibold text-slate-900 mb-3">Как это работает</h3>
            <ol class="text-sm text-slate-600 space-y-2 list-decimal list-inside">
                <li>Сохраняются итоговые баллы и рейтинг каждого ученика</li>
                <li>Вся история остаётся в архиве</li>
                <li>Открывается новый пустой семестр</li>
                <li>Баллы учеников → {{ \App\Models\Student::startingPoints() }}</li>
            </ol>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-border">
            <h2 class="font-semibold text-slate-900">Архив семестров</h2>
        </div>
        @if ($archivedSemesters->isEmpty())
            <div class="px-6 py-12 text-center text-slate-500 text-sm">Архивных семестров пока нет.</div>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Семестр</th>
                            <th>Период</th>
                            <th>Учеников</th>
                            <th>Записей</th>
                            <th>Мериты / Демериты</th>
                            <th>Закрыл</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($archivedSemesters as $semester)
                            <tr>
                                <td class="font-semibold text-slate-800">{{ $semester->name }}</td>
                                <td class="text-xs text-slate-500 whitespace-nowrap">
                                    {{ $semester->started_at->format('d.m.Y') }} —
                                    {{ $semester->closed_at?->format('d.m.Y') }}
                                </td>
                                <td>{{ $semester->students_count }}</td>
                                <td>{{ $semester->records_count }}</td>
                                <td>
                                    <span class="points-plus text-xs">+{{ $semester->total_merits }}</span>
                                    <span class="text-slate-300 mx-1">/</span>
                                    <span class="points-minus text-xs">−{{ $semester->total_demerits }}</span>
                                </td>
                                <td class="text-sm text-slate-600">{{ $semester->closedBy?->name ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('admin.semesters.show', $semester) }}"
                                       class="text-sm font-medium text-brand-600 hover:underline">Открыть архив →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
