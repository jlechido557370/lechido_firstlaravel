@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="card" style="max-width: 420px; margin: 30px auto;">
        <h1>Login</h1>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div style="margin-bottom: 12px;">
                <label>Username or Email</label>
                <input type="text" name="login" value="{{ old('login') }}" required autocomplete="username">
                @error('login')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
            </div>
            <div style="margin-bottom: 12px;">
                <label>Password</label>
                <div style="position:relative;">
                    <input type="password" name="password" id="loginPassword" required autocomplete="current-password" style="padding-right:44px;">
                    <button type="button" onclick="togglePassword('loginPassword', this)" style="position:absolute;right:0;top:0;bottom:0;width:40px;background:transparent;border:none;color:#6b7280;font-size:13px;">Show</button>
                </div>
                @error('password')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
            </div>
            <div style="margin-bottom: 12px;">
                <label><input type="checkbox" name="remember" value="1" style="width:auto;"> Remember me</label>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
    </div>
@endsection

@push('scripts')
<script>
function togglePassword(inputId, btn) {
    var input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = 'Hide';
    } else {
        input.type = 'password';
        btn.textContent = 'Show';
    }
}
</script>
@endpush