@extends('layouts.app')

@section('title', 'Обзор')

@section('page-header')
    <h1 class="page-title">Панель администратора</h1>
    <p class="page-subtitle">Сводка по школе: участники, активность и последние назначения баллов</p>
@endsection

@section('content')
    <div class="demo-banner">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-brand-700 mb-1">Сценарий для инвестора</p>
                <h2 class="text-lg font-bold text-slate-900">Три роли — один продукт</h2>
                <p class="text-sm text-slate-600 mt-1 max-w-xl">
                    Покажите: настройку правил (админ) → быструю выдачу баллов (учитель) → мотивацию ученика через рейтинг и уведомления.
                </p>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <a href="{{ route('admin.points') }}" class="btn-primary">Выдать баллы</a>
                <a href="{{ route('admin.rules.index') }}" class="btn-secondary">Правила</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <div class="stat-card">
            <span class="stat-label">Учеников</span>
            <span class="stat-value">{{ $totalStudents }}</span>
            <a href="{{ route('admin.students.index') }}" class="text-xs font-medium text-brand-600 hover:underline mt-1">Управление →</a>
        </div>
        <div class="stat-card">
            <span class="stat-label">Учителей</span>
            <span class="stat-value">{{ $totalTeachers }}</span>
            <a href="{{ route('admin.teachers.index') }}" class="text-xs font-medium text-brand-600 hover:underline mt-1">Управление →</a>
        </div>
        <div class="stat-card border-emerald-100 bg-gradient-to-br from-white to-merit-soft">
            <span class="stat-label text-emerald-700">Мериты (всего)</span>
            <span class="stat-value text-merit">+{{ $sumPositivePoints }}</span>
            <span class="badge-merit mt-1 w-fit">Поощрения</span>
        </div>
        <div class="stat-card border-red-100 bg-gradient-to-br from-white to-demerit-soft">
            <span class="stat-label text-red-700">Демериты (всего)</span>
            <span class="stat-value text-demerit">−{{ $sumNegativePoints }}</span>
            <span class="badge-demerit mt-1 w-fit">Взыскания</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="card-padded lg:col-span-1">
            <h3 class="font-semibold text-slate-900 mb-4">Ценность для школы</h3>
            <ul class="space-y-3 text-sm text-slate-600">
                <li class="flex gap-3">
                    <span class="text-brand-600">✓</span>
                    Единые правила дисциплины для всех классов
                </li>
                <li class="flex gap-3">
                    <span class="text-brand-600">✓</span>
                    История каждого решения — кто, когда, за что
                </li>
                <li class="flex gap-3">
                    <span class="text-brand-600">✓</span>
                    Уведомления родителям и ученикам (в разработке API)
                </li>
                <li class="flex gap-3">
                    <span class="text-brand-600">✓</span>
                    Рейтинги мотивируют здоровую конкуренцию
                </li>
            </ul>
        </div>
        <div class="card lg:col-span-2 overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="font-semibold text-slate-900">Последние операции</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Всего назначений: {{ $totalAssignments }}</p>
                </div>
            </div>

            @if ($latestHistory->isEmpty())
                <div class="px-6 py-16 text-center">
                    <p class="text-slate-500 text-sm">Пока нет записей.</p>
                    <a href="{{ route('admin.points') }}" class="btn-primary mt-4 inline-flex">Создать первое назначение</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Время</th>
                                <th>Ученик</th>
                                <th>Класс</th>
                                <th>Учитель</th>
                                <th>Правило</th>
                                <th>Баллы</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestHistory as $record)
                                <tr>
                                    <td class="text-xs text-slate-500 whitespace-nowrap">{{ $record->created_at->format('d.m H:i') }}</td>
                                    <td class="font-medium text-slate-800">{{ $record->student?->user?->name ?? '—' }}</td>
                                    <td class="text-slate-500">{{ $record->student?->schoolClass?->name ?? '—' }}</td>
                                    <td class="text-slate-600">{{ $record->teacher?->name ?? 'Админ' }}</td>
                                    <td class="text-slate-700 max-w-[12rem] truncate">{{ $record->rule?->description ?? $record->rule?->name }}</td>
                                    <td>
                                        @if ($record->points > 0)
                                            <span class="points-plus">+{{ $record->points }}</span>
                                        @else
                                            <span class="points-minus">{{ $record->points }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.points.cancel', $record) }}"
                                              onsubmit="return confirm('Отменить назначение?');">
                                            @csrf
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Отменить</button>
                                        </form>
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
