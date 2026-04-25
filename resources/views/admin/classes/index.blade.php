@extends('layouts.app')
@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Классы</h1>
        <a href="{{ route('admin.classes.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
            + Добавить класс
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                <tr>
                    <th class="text-left px-6 py-3 font-medium">Класс</th>
                    <th class="text-left px-4 py-3 font-medium">Учеников</th>
                    <th class="text-right px-6 py-3 font-medium">Действия</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                @forelse ($classes as $class)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3 font-semibold text-slate-800">{{ $class->name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {{ $class->students_count }} уч.
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.classes.edit', $class) }}"
                                   class="text-xs text-indigo-600 px-2.5 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors font-medium">
                                    Изменить
                                </a>
                                <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Удалить класс «{{ $class->name }}»?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-red-500 px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition-colors font-medium">
                                        Удалить
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-slate-400 text-sm">Классов пока нет.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($classes->hasPages())
        <div class="mt-4">{{ $classes->links() }}</div>
    @endif

@endsection
<?php
