@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="card">
        <h1>Profile</h1>
        <p class="muted">Update your account details and password.</p>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <h2>Update Profile</h2>
            <form method="POST" action="{{ route('user.profile.update') }}">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 12px;">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom: 12px;">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
                </div>
                <button type="submit">Save Profile</button>
            </form>
        </div>

        <div class="card">
            <h2>Change Password</h2>
            <form method="POST" action="{{ route('user.password.update') }}">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 12px;">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                    @error('current_password')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom: 12px;">
                    <label>New Password</label>
                    <input type="password" name="password" required>
                    @error('password')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom: 12px;">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation" required>
                </div>
                <button type="submit">Change Password</button>
            </form>
        </div>
    </div>

    <div class="card">
        <h2>Borrow History</h2>
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Borrowed</th>
                    <th>Returned</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($borrowHistory as $record)
                    <tr>
                        <td>{{ $record->book->title ?? 'Deleted Book' }}</td>
                        <td>{{ $record->borrowed_at?->format('M d, Y') }}</td>
                        <td>{{ $record->returned_at?->format('M d, Y') ?? '—' }}</td>
                        <td>
                            @if($record->returned_at)
                                <span class="badge badge-green">Returned</span>
                            @else
                                <span class="badge">Active</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">No borrow history yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
