@extends('layouts.app')

@section('title', 'Мой класс')

@section('page-header')
    <h1 class="page-title">Класс {{ $homeroomClass->name }}</h1>
    <p class="page-subtitle">Классный руководитель: список учеников, баллы и история дисциплины</p>
@endsection

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <span class="stat-label">Учеников</span>
            <span class="stat-value">{{ $homeroomClass->students->count() }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Средний балл</span>
            <span class="stat-value">
                @php
                    $avg = $homeroomClass->students->avg('current_points');
                @endphp
                {{ $homeroomClass->students->isEmpty() ? '—' : number_format($avg, 1) }}
            </span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Действия</span>
            <a href="{{ route('teacher.dashboard') }}" class="btn-secondary mt-2 text-xs">Выдать баллы</a>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-border">
            <h2 class="font-semibold text-slate-900">Ученики класса</h2>
        </div>

        @if ($homeroomClass->students->isEmpty())
            <div class="px-6 py-12 text-center text-slate-500 text-sm">В классе пока нет учеников.</div>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ученик</th>
                            <th>Email</th>
                            <th>Баллы</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($homeroomClass->students as $idx => $student)
                            <tr>
                                <td class="text-slate-400 font-medium">{{ $idx + 1 }}</td>
                                <td class="font-semibold text-slate-800">{{ $student->user?->name ?? '—' }}</td>
                                <td class="text-slate-500 text-xs">{{ $student->user?->email ?? '—' }}</td>
                                <td>
                                    @if ($student->current_points >= 0)
                                        <span class="points-plus">{{ $student->current_points }}</span>
                                    @else
                                        <span class="points-minus">{{ $student->current_points }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('teacher.homeroom.student', $student) }}"
                                       class="text-xs font-medium text-brand-600 hover:underline">
                                        История →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
