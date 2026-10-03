@extends('admin.layout')

@section('title', 'Dashboard')

@section('page_title', 'Dashboard')

@section('topbar_actions')
    <a class="btn-ghost" href="{{ route('admin.projects.create') }}">+ Add Project</a>
    <a class="btn-ghost" href="{{ route('admin.journal.create') }}">+ Add Article</a>
@endsection

@section('content')
<div class="dash-greeting">
    <h2>Good {{ \Illuminate\Support\Carbon::now()->format('H') < 12 ? 'Morning' : (\Illuminate\Support\Carbon::now()->format('H') < 18 ? 'Afternoon' : 'Evening') }}, Admin</h2>
    <p>Architecture Portfolio Management</p>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <span class="stat-num">{{ $projectCount }}</span>
        <span class="stat-label">Projects</span>
    </div>
    <div class="stat-card is-accent">
        <span class="stat-num">{{ $publishedCount }}</span>
        <span class="stat-label">Published</span>
    </div>
    <div class="stat-card">
        <span class="stat-num">{{ $draftCount }}</span>
        <span class="stat-label">Drafts</span>
    </div>
    <div class="stat-card">
        <span class="stat-num">{{ $serviceCount }}</span>
        <span class="stat-label">Services</span>
    </div>
    <div class="stat-card {{ $newMessageCount > 0 ? 'is-alert' : '' }}">
        <span class="stat-num">{{ $messageCount }}</span>
        <span class="stat-label">Messages{{ $newMessageCount ? ' — '.$newMessageCount.' new' : '' }}</span>
    </div>
    <div class="stat-card">
        <span class="stat-num">{{ $articleCount }}</span>
        <span class="stat-label">Journal Articles</span>
    </div>
</div>

<div class="dash-grid">
    <section class="card">
        <header class="card-head">
            <h3>Recent Projects</h3>
            <a class="text-sm" href="{{ route('admin.projects.index') }}">All projects →</a>
        </header>
        <ul class="mini-list">
            @forelse ($recentProjects as $project)
                <li>
                    <a href="{{ route('admin.projects.edit', $project) }}">
                        <strong>{{ $project->name }}</strong>
                        <span class="badge {{ $project->is_published ? 'badge-live' : 'badge-draft' }}">{{ $project->is_published ? 'Published' : 'Draft' }}</span>
                        <em>{{ $project->category?->name }}</em>
                    </a>
                </li>
            @empty
                <li class="empty">No projects yet.</li>
            @endforelse
        </ul>
    </section>

    <section class="card">
        <header class="card-head">
            <h3>Recent Messages</h3>
            <a class="text-sm" href="{{ route('admin.messages.index') }}">All messages →</a>
        </header>
        <ul class="mini-list">
            @forelse ($recentMessages as $message)
                <li>
                    <a href="{{ route('admin.messages.show', $message) }}">
                        <strong>{{ $message->name }}</strong>
                        <span class="badge badge-{{ $message->status }}">{{ ucfirst($message->status) }}</span>
                        <em>{{ Str::limit($message->message, 60) }}</em>
                    </a>
                </li>
            @empty
                <li class="empty">No messages yet.</li>
            @endforelse
        </ul>
    </section>

    <section class="card">
        <header class="card-head">
            <h3>Recent Articles</h3>
            <a class="text-sm" href="{{ route('admin.journal.index') }}">All articles →</a>
        </header>
        <ul class="mini-list">
            @forelse ($recentArticles as $article)
                <li>
                    <a href="{{ route('admin.journal.edit', $article) }}">
                        <strong>{{ $article->title }}</strong>
                        <span class="badge {{ $article->status === 'published' ? 'badge-live' : 'badge-draft' }}">{{ ucfirst($article->status) }}</span>
                        <em>{{ $article->category }}</em>
                    </a>
                </li>
            @empty
                <li class="empty">No articles yet.</li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
