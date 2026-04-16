@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="card" style="max-width: 420px; margin: 30px auto;">
        <h1>Login</h1>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div style="margin-bottom: 12px;">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
                @error('email')<div class="muted" style="color:#b91c1c;">{{ $message }}</div>@enderror
            </div>
            <div style="margin-bottom: 12px;">
                <label>Password</label>
                <input type="password" name="password" required>
                @error('password')<div class="muted" style="color:#b91c1c;">{{ $message }}</div>@enderror
            </div>
            <div style="margin-bottom: 12px;">
                <label><input type="checkbox" name="remember" value="1" style="width:auto;"> Remember me</label>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
    </div>
@endsection
