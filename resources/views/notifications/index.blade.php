@extends('layouts.app')

@section('title', 'Уведомления')

@section('page-header')
    <h1 class="page-title">Уведомления</h1>
    <p class="page-subtitle">Сообщения о начислении баллов и важных событиях</p>
@endsection

@section('content')
    <div class="card overflow-hidden">
        @if ($notifications->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-4xl mb-3">🔔</p>
                <p class="text-slate-600 font-medium">Уведомлений пока нет</p>
                <p class="text-sm text-slate-500 mt-1">Они появятся после начисления баллов</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Когда</th>
                            <th>Заголовок</th>
                            <th>Текст</th>
                            <th>Статус</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $notification)
                            <tr class="{{ $notification->is_read ? '' : 'bg-brand-50/40' }}">
                                <td class="text-xs text-slate-500 whitespace-nowrap">{{ $notification->created_at->format('d.m.Y H:i') }}</td>
                                <td class="font-semibold text-slate-800">{{ $notification->title }}</td>
                                <td class="text-slate-600 max-w-md">{{ $notification->body }}</td>
                                <td>
                                    @if ($notification->is_read)
                                        <span class="badge-neutral">Прочитано</span>
                                    @else
                                        <span class="badge bg-brand-100 text-brand-700">Новое</span>
                                    @endif
                                </td>
                                <td>
                                    @if (! $notification->is_read)
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium text-brand-600 hover:underline">Прочитано</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($notifications->hasPages())
        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif
@endsection
