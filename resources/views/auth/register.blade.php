@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="card" style="max-width: 420px; margin: 30px auto;">
        <h1>Register</h1>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div style="margin-bottom: 12px;">
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
                @error('name')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
            </div>
            <div style="margin-bottom: 12px;">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
                @error('email')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
            </div>
            <div style="margin-bottom: 12px;">
                <label>Password</label>
                <input type="password" name="password" required>
                @error('password')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
            </div>
            <div style="margin-bottom: 12px;">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <button type="submit">Create Account</button>
        </form>
        <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>
@endsection
