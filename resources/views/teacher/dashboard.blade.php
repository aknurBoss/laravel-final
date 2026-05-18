@extends('layouts.app')

@section('title', 'Выдача баллов')

@section('page-header')
    <h1 class="page-title">Кабинет учителя</h1>
    <p class="page-subtitle">Быстрое назначение меритов и демеритов — два удобных режима работы</p>
@endsection

@section('content')

{{-- Mode switcher --}}
<div class="flex gap-2 mb-6">
    <a href="{{ route('teacher.dashboard', ['mode' => '1']) }}"
       class="px-5 py-2 rounded-xl text-sm font-semibold transition-colors
              {{ $mode === '1' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:border-indigo-300' }}">
        Правило → Ученики
    </a>
    <a href="{{ route('teacher.dashboard', ['mode' => '2']) }}"
       class="px-5 py-2 rounded-xl text-sm font-semibold transition-colors
              {{ $mode === '2' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:border-indigo-300' }}">
        Ученик → Правила
    </a>
</div>

{{-- ═══ MODE 1 ═══ --}}
@if ($mode === '1')
<div class="card-padded mb-6">
    <h2 class="font-semibold text-slate-800 mb-0.5">Режим 1 — одно правило, много учеников</h2>
    <p class="text-xs text-slate-400 mb-5">Выберите правило и отметьте учеников.</p>

    <form method="POST" action="{{ route('teacher.assign') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="mode" value="1">

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Правило</label>
            @error('rule_id') <p class="text-red-500 text-xs mb-1">{{ $message }}</p> @enderror
            @if ($rules->isEmpty())
                <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm">Нет активных правил.</div>
            @else
                <div class="border border-slate-200 rounded-xl p-3 space-y-1.5 max-h-48 overflow-y-auto">
                    @foreach ($rules as $rule)
                        <label class="flex items-center gap-2.5 text-sm cursor-pointer hover:bg-slate-50 rounded-lg px-2 py-1 -mx-2">
                            <input type="radio" name="rule_id" value="{{ $rule->id }}" class="text-indigo-600"
                                   @checked(old('rule_id') == $rule->id)>
                            <span class="flex-1 text-slate-700">{{ $rule->name }}</span>
                            @if ($rule->points > 0)
                                <span class="text-emerald-600 font-semibold text-xs">+{{ $rule->points }}</span>
                            @else
                                <span class="text-red-500 font-semibold text-xs">{{ $rule->points }}</span>
                            @endif
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-sm font-medium text-slate-700">Ученики</label>
                <div class="flex gap-2">
                    <button type="button" onclick="checkAll(true)"
                            class="text-xs px-3 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">Все</button>
                    <button type="button" onclick="checkAll(false)"
                            class="text-xs px-3 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">Снять</button>
                </div>
            </div>
            @error('student_ids') <p class="text-red-500 text-xs mb-1">{{ $message }}</p> @enderror
            @php $hasStudents = $classes->sum(fn($c) => $c->students->count()) > 0; @endphp

            @if (!$hasStudents)
                <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm">Нет учеников в системе.</div>
            @else
                <div class="border border-slate-200 rounded-xl divide-y divide-slate-100" id="students-list">
                    @foreach ($classes as $class)
                        @if ($class->students->isNotEmpty())
                            <details class="group">
                                <summary class="flex items-center justify-between px-4 py-2.5 cursor-pointer select-none hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-sm text-slate-700">{{ $class->name }}</span>
                                        <span class="text-xs text-slate-400">{{ $class->students->count() }} уч.</span>
                                    </div>
                                    <span class="text-slate-400 text-sm font-bold leading-none group-open:hidden">+</span>
                                    <span class="text-slate-400 text-sm font-bold leading-none hidden group-open:inline">−</span>
                                </summary>
                                <div class="grid grid-cols-2 gap-1 px-4 pb-3 pt-1">
                                    @foreach ($class->students as $student)
                                        <label class="student-check flex items-center gap-2 text-sm cursor-pointer hover:bg-slate-50 rounded-lg px-2 py-1 -mx-2">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="text-indigo-600 rounded"
                                                   @checked(is_array(old('student_ids')) && in_array($student->id, old('student_ids')))>
                                            <span class="text-slate-700">{{ $student->user?->name ?? '—' }}</span>
                                            <span class="text-slate-400 text-xs ml-auto">{{ $student->current_points }}б</span>
                                        </label>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Комментарий <span class="text-slate-400 font-normal">(необязательно)</span></label>
            <textarea name="comment" rows="2"
                      class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('comment') }}</textarea>
        </div>

        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm">
            Назначить баллы
        </button>
    </form>
</div>

{{-- ═══ MODE 2 ═══ --}}
@else
<div class="card-padded mb-6">
    <h2 class="font-semibold text-slate-800 mb-0.5">Режим 2 — один ученик, несколько правил</h2>
    <p class="text-xs text-slate-400 mb-5">Выберите ученика и отметьте нужные правила.</p>

    <form method="POST" action="{{ route('teacher.assign') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="mode" value="2">

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Ученик</label>
            @error('student_id') <p class="text-red-500 text-xs mb-1">{{ $message }}</p> @enderror
            @if ($allStudents->isEmpty())
                <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm">Нет учеников.</div>
            @else
                <select name="student_id" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    <option value="">— Выберите ученика —</option>
                    @foreach ($allStudents as $student)
                        <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                            {{ $student->user?->name ?? '—' }}
                            ({{ $student->schoolClass?->name ?? 'Без класса' }}, {{ $student->current_points }} б)
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Правила</label>
            @error('rule_ids') <p class="text-red-500 text-xs mb-1">{{ $message }}</p> @enderror
            @if ($rules->isEmpty())
                <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm">Нет активных правил.</div>
            @else
                <div class="border border-slate-200 rounded-xl p-3 space-y-1.5 max-h-56 overflow-y-auto">
                    @foreach ($rules as $rule)
                        <label class="flex items-center gap-2.5 text-sm cursor-pointer hover:bg-slate-50 rounded-lg px-2 py-1 -mx-2">
                            <input type="checkbox" name="rule_ids[]" value="{{ $rule->id }}" class="text-indigo-600 rounded"
                                   @checked(is_array(old('rule_ids')) && in_array($rule->id, old('rule_ids')))>
                            <span class="flex-1 text-slate-700">{{ $rule->name }}</span>
                            @if ($rule->points > 0)
                                <span class="text-emerald-600 font-semibold text-xs">+{{ $rule->points }}</span>
                            @else
                                <span class="text-red-500 font-semibold text-xs">{{ $rule->points }}</span>
                            @endif
                            @if ($rule->description)
                                <span class="text-slate-400 text-xs truncate max-w-xs">{{ $rule->description }}</span>
                            @endif
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Комментарий <span class="text-slate-400 font-normal">(необязательно)</span></label>
            <textarea name="comment" rows="2"
                      class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('comment') }}</textarea>
        </div>

        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm">
            Назначить баллы
        </button>
    </form>
</div>
@endif

{{-- ═══ HISTORY ═══ --}}
<div class="card overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-slate-100">
        <h2 class="font-semibold text-slate-800">Мои назначения</h2>

        <form method="GET" action="{{ route('teacher.dashboard') }}" class="flex flex-wrap items-end gap-2 text-xs">
            <input type="hidden" name="mode" value="{{ $mode }}">
            <div>
                <label class="block text-slate-500 mb-1">С</label>
                <input type="date" name="hf" value="{{ request('hf') }}"
                       class="border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-slate-500 mb-1">По</label>
                <input type="date" name="ht" value="{{ request('ht') }}"
                       class="border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-slate-500 mb-1">Тип</label>
                <select name="htype" class="border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Все</option>
                    <option value="reward" @selected(request('htype') === 'reward')>Награды</option>
                    <option value="penalty" @selected(request('htype') === 'penalty')>Штрафы</option>
                </select>
            </div>
            <button type="submit" class="bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">Применить</button>
        </form>
    </div>

    @if ($history->isEmpty())
        <div class="px-6 py-12 text-center text-slate-400 text-sm">История пуста.</div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-6 py-3 font-medium">Время</th>
                        <th class="text-left px-4 py-3 font-medium">Ученик</th>
                        <th class="text-left px-4 py-3 font-medium">Класс</th>
                        <th class="text-left px-4 py-3 font-medium">Правило</th>
                        <th class="text-left px-4 py-3 font-medium">Баллы</th>
                        <th class="text-left px-4 py-3 font-medium">Комментарий</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($history as $record)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3 text-xs text-slate-400 whitespace-nowrap">{{ $record->created_at->format('d.m H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $record->student?->user?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $record->student?->schoolClass?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $record->rule?->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-semibold">
                                @if ($record->points > 0)
                                    <span class="text-emerald-600">+{{ $record->points }}</span>
                                @else
                                    <span class="text-red-500">{{ $record->points }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-400 text-xs">{{ $record->comment ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('teacher.history.cancel', $record) }}"
                                      onsubmit="return confirm('Отменить это назначение?');">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs text-red-500 hover:text-red-700 px-2.5 py-1 rounded-lg hover:bg-red-50 transition-colors font-medium">
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

<script>
function checkAll(state) {
    document.querySelectorAll('#students-list input[type=checkbox]').forEach(cb => cb.checked = state);
}
</script>
@endsection››