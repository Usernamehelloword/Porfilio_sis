@extends('admin.layout')

@section('title', 'Message — '.$message->name)
@section('page_title', 'Message')

@section('topbar_actions')
    <a class="btn-ghost" href="{{ route('admin.messages.index') }}">← All Messages</a>
@endsection

@section('content')
<div class="card">
    <header class="card-head">
        <h3>{{ $message->name }}</h3>
        <span class="badge badge-{{ $message->status }}">{{ ucfirst($message->status) }}</span>
    </header>

    <ul class="p-facts">
        <li><span class="k">EMAIL</span><span><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></span></li>
        <li><span class="k">PROJECT TYPE</span><span>{{ $message->project_type ?? '—' }}</span></li>
        <li><span class="k">BUDGET</span><span>{{ $message->budget ?? '—' }}</span></li>
        <li><span class="k">RECEIVED</span><span>{{ $message->created_at->format('F d, Y — H:i') }}</span></li>
    </ul>

    <div class="message-body">
        <p>{{ $message->message }}</p>
    </div>

    <div class="filter-bar">
        @if ($message->status === 'new')
            <form method="POST" action="{{ route('admin.messages.read', $message) }}">@csrf<button class="btn-ghost" type="submit">Mark as Read</button></form>
        @endif
        @if ($message->status !== 'replied')
            <form method="POST" action="{{ route('admin.messages.replied', $message) }}">@csrf<button class="btn-ghost" type="submit">Mark as Replied</button></form>
        @endif
        <a class="btn-ghost" href="mailto:{{ $message->email }}?subject=Re: Your inquiry to {{ \App\Support\Settings::get('studio_name') }}">Reply by Email</a>
        <form method="POST" action="{{ route('admin.messages.archive', $message) }}">@csrf<button class="btn-ghost" type="submit">Archive</button></form>
        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Are you sure you want to delete this message?">
            @csrf @method('DELETE')
            <button class="btn-danger" type="submit">Delete</button>
        </form>
    </div>
</div>
@endsection
