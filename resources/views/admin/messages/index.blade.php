@extends('admin.layout')

@section('title', 'Messages')
@section('page_title', 'Messages')

@section('content')
<div class="filter-bar">
    <a class="badge {{ ! request('status') ? 'badge-live' : '' }}" href="{{ route('admin.messages.index') }}">All ({{ $counts['all'] }})</a>
    <a class="badge {{ request('status') === 'new' ? 'badge-live' : '' }}" href="{{ route('admin.messages.index', ['status' => 'new']) }}">New ({{ $counts['new'] }})</a>
    <a class="badge {{ request('status') === 'read' ? 'badge-live' : '' }}" href="{{ route('admin.messages.index', ['status' => 'read']) }}">Read ({{ $counts['read'] }})</a>
    <a class="badge {{ request('status') === 'replied' ? 'badge-live' : '' }}" href="{{ route('admin.messages.index', ['status' => 'replied']) }}">Replied ({{ $counts['replied'] }})</a>
    <a class="badge {{ request('status') === 'archived' ? 'badge-live' : '' }}" href="{{ route('admin.messages.index', ['status' => 'archived']) }}">Archived ({{ $counts['archived'] }})</a>
</div>

<form class="filter-bar" method="GET" action="{{ route('admin.messages.index') }}">
    @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <input type="text" name="q" placeholder="Search messages…" value="{{ request('q') }}">
    <button class="btn-ghost" type="submit">Search</button>
</form>

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Name</th><th>Project Type</th><th>Message</th><th>Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach ($messages as $message)
                    <tr class="{{ $message->status === 'new' ? 'is-unread' : '' }}">
                        <td>
                            <a href="{{ route('admin.messages.show', $message) }}"><strong>{{ $message->name }}</strong></a>
                            <small>{{ $message->email }}</small>
                        </td>
                        <td>{{ $message->project_type ?? '—' }}</td>
                        <td>{{ Str::limit($message->message, 70) }}</td>
                        <td>{{ $message->created_at->format('M d, Y') }}</td>
                        <td><span class="badge badge-{{ $message->status }}">{{ ucfirst($message->status) }}</span></td>
                        <td class="cell-actions">
                            <a class="act" href="{{ route('admin.messages.show', $message) }}">Open</a>
                            @if ($message->status === 'new')
                                <form method="POST" action="{{ route('admin.messages.read', $message) }}">
                                    @csrf
                                    <button class="act" type="submit">Mark as Read</button>
                                </form>
                            @endif
                            @if ($message->status !== 'archived')
                                <form method="POST" action="{{ route('admin.messages.archive', $message) }}">
                                    @csrf
                                    <button class="act" type="submit">Archive</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Are you sure you want to delete this message?">
                                @csrf
                                @method('DELETE')
                                <button class="act act-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{ $messages->links() }}
@endsection
