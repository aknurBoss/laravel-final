@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Аккаунты</h1>
            <p class="text-xs text-gray-400 mt-0.5">Для добавления ученика/учителя используйте разделы «Ученики» и «Учителя»</p>
        </div>
        <div class="flex-1 md:flex-none md:w-auto">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Поиск по имени/email"
                       class="border rounded px-3 py-1 text-sm w-full md:w-64">
                <button type="submit" class="bg-gray-200 px-3 py-1 rounded text-sm">Найти</button>
            </form>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-gray-700 text-white px-4 py-2 rounded text-sm">
            + Добавить аккаунт
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b">
                <th class="text-left py-2 px-3">Имя</th>
                <th class="text-left py-2 px-3">Email</th>
                <th class="text-left py-2 px-3">Роль</th>
                <th class="text-left py-2 px-3">Активен</th>
                <th class="text-left py-2 px-3"></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                @php $role = $user->role->value ?? $user->role; @endphp
                <tr class="border-b last:border-0">
                    <td class="py-2 px-3">{{ $user->name }}</td>
                    <td class="py-2 px-3">{{ $user->email }}</td>
                    <td class="py-2 px-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                @if($role === 'admin') bg-purple-100 text-purple-700
                                @elseif($role === 'teacher') bg-blue-100 text-blue-700
                                @else bg-green-100 text-green-700
                                @endif">
                                {{ $role }}
                            </span>
                    </td>
                    <td class="py-2 px-3">{{ $user->is_active ? 'Да' : 'Нет' }}</td>
                    <td class="py-2 px-3 text-right space-x-3">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 text-sm">Редактировать</a>
                        @if (!auth()->check() || auth()->id() !== $user->id)
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Удалить аккаунт «{{ $user->name }}»?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-sm">Удалить</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-4 px-3 text-center text-gray-500">Пользователей пока нет.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
