@extends('admin.layout')

@section('title', 'Admin Users')
@section('page_title', 'Admin Users')

@section('content')
<div class="dash-grid">
    <section class="card">
        <h3 class="card-title">Add Administrator</h3>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field"><label>NAME</label><input type="text" name="name" required></div>
                <div class="field"><label>EMAIL</label><input type="email" name="email" required></div>
                <div class="field"><label>PASSWORD</label><input type="password" name="password" required minlength="8"></div>
                <div class="field">
                    <label>ROLE</label>
                    <select name="role">
                        @foreach ($roles as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <label class="check"><input type="checkbox" name="is_active" value="1" checked> Active</label>
            <button class="btn-primary" type="submit">Create Administrator</button>
        </form>
    </section>

    <section class="card">
        <h3 class="card-title">Roles</h3>
        <ul class="role-list">
            <li><strong>Super Admin</strong><span>Full access — including administrators, appearance and system settings.</span></li>
            <li><strong>Editor</strong><span>Manages all content, plus appearance and settings. Cannot manage administrators.</span></li>
            <li><strong>Content Manager</strong><span>Manages projects, journal, services and media only.</span></li>
        </ul>
    </section>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="form-grid inline-form">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $user->name }}">
                                <input type="email" name="email" value="{{ $user->email }}">
                                <select name="role">
                                    @foreach ($roles as $key => $label)
                                        <option value="{{ $key }}" @selected($user->role === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <input type="password" name="password" placeholder="New password (optional)">
                                <label class="check"><input type="checkbox" name="is_active" value="1" @checked($user->is_active)> Active</label>
                                <button class="act" type="submit">Save</button>
                            </form>
                        </td>
                        <td></td><td></td><td></td>
                        <td>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Are you sure you want to delete this administrator?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="act act-danger" type="submit">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
