@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">Правила</h1>
        <a href="{{ route('admin.rules.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">
            Добавить правило
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 bg-green-100 text-green-800 px-3 py-2 rounded text-sm">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2 px-3">ID</th>
                    <th class="text-left py-2 px-3">Название</th>
                    <th class="text-left py-2 px-3">Баллы</th>
                    <th class="text-left py-2 px-3">Тип</th>
                    <th class="text-left py-2 px-3">Активно</th>
                    <th class="text-left py-2 px-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rules as $rule)
                    <tr class="border-b last:border-0">
                        <td class="py-2 px-3">{{ $rule->id }}</td>
                        <td class="py-2 px-3">{{ $rule->name }}</td>
                        <td class="py-2 px-3">
                            @if ($rule->points > 0)
                                <span class="text-green-600">+{{ $rule->points }}</span>
                            @else
                                <span class="text-red-600">{{ $rule->points }}</span>
                            @endif
                        </td>
                        <td class="py-2 px-3">{{ $rule->type === 'reward' ? 'Награда' : 'Штраф' }}</td>
                        <td class="py-2 px-3">{{ $rule->is_active ? 'Да' : 'Нет' }}</td>
                        <td class="py-2 px-3 text-right space-x-2">
                            <a href="{{ route('admin.rules.edit', $rule) }}" class="text-indigo-600 text-sm">Редактировать</a>
                            <form action="{{ route('admin.rules.destroy', $rule) }}" method="POST" class="inline" onsubmit="return confirm('Удалить правило?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-sm">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-3 px-3 text-center text-gray-500">Правил пока нет.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $rules->links() }}
    </div>
@endsection
