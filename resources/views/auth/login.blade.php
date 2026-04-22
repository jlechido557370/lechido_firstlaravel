@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<style>
    .auth-card {
        max-width: 460px; margin: 20px auto;
        background: var(--card-grad); border: 1.5px solid var(--border);
        border-radius: 16px; box-shadow: var(--shadow-md);
        overflow: hidden;
    }
    .auth-card-header {
        padding: 32px 36px 28px;
        border-bottom: 1px solid var(--border);
        background: var(--off);
    }
    .auth-card-header h1 {
        font-size: 26px; font-weight: 600; color: var(--black);
        letter-spacing: -.02em; margin-bottom: 4px;
    }
    .auth-card-header p { font-size: 14px; color: var(--muted); }
    .auth-card-body { padding: 32px 36px; }

    .form-group { margin-bottom: 20px; }
    .form-label {
        display: block; font-size: 13px; font-weight: 600;
        color: var(--black); margin-bottom: 7px; letter-spacing: .01em;
    }
    .form-error { color: #dc2626; font-size: 12.5px; margin-top: 5px; display: flex; align-items: center; gap: 5px; }
    [data-theme="dark"] .form-error { color: #f87171; }
    .form-error::before { content: '⚠'; font-size: 11px; }

    .input-wrap { position: relative; }
    .input-wrap input {
        width: 100%; padding: 11px 14px; border: 1.5px solid var(--border);
        background: var(--off); font-family: var(--font-sans); font-size: 14.5px;
        color: var(--black); outline: none; border-radius: 9px;
        transition: border-color .18s, box-shadow .18s, background .18s;
        appearance: auto; -webkit-appearance: auto;
    }
    .input-wrap input:focus {
        border-color: var(--black); box-shadow: 0 0 0 4px var(--focus-ring);
        background: var(--white);
    }
    .input-wrap input.has-error { border-color: #dc2626; }
    .input-wrap input[type="password"] { padding-right: 52px; }

    .pw-toggle {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        background: none; border: none; color: var(--muted); font-size: 12px;
        font-family: var(--font-mono); cursor: pointer; padding: 4px 6px;
        border-radius: 5px; letter-spacing: .04em;
        transition: color .15s, background .15s; box-shadow: none;
    }
    .pw-toggle:hover { color: var(--black); background: var(--mid); opacity: 1; transform: translateY(-50%); box-shadow: none; }

    /* Remember me checkbox */
    .checkbox-row {
        display: flex; align-items: center; gap: 10px; cursor: pointer;
        font-size: 13.5px; color: var(--black); font-weight: 400;
    }
    .checkbox-row input[type="checkbox"] {
        width: 17px !important; height: 17px !important;
        min-width: 17px !important; padding: 0 !important; margin: 0 !important;
        cursor: pointer; flex-shrink: 0;
        accent-color: var(--black);
        appearance: auto !important; -webkit-appearance: auto !important;
    }

    .auth-submit-btn {
        width: 100%; padding: 13px; background: var(--black); color: var(--white);
        border: 1.5px solid var(--black); border-radius: 9px;
        font-size: 14.5px; font-weight: 600; font-family: var(--font-sans);
        cursor: pointer; letter-spacing: .02em; margin-top: 6px;
        transition: opacity .15s, transform .15s, box-shadow .15s;
    }
    .auth-submit-btn:hover { opacity: .87; transform: translateY(-1px); box-shadow: var(--shadow-md); }
    .auth-submit-btn:active { transform: translateY(0); box-shadow: none; }

    .auth-footer-text {
        text-align: center; font-size: 13.5px; color: var(--muted); margin-top: 20px;
    }
    .auth-footer-text a { color: var(--black); font-weight: 600; border-bottom: 1.5px solid var(--border); padding-bottom: 1px; transition: border-color .15s; }
    .auth-footer-text a:hover { border-color: var(--black); opacity: 1; }
</style>

<div class="auth-card">
    <div class="auth-card-header">
        <h1>Welcome back</h1>
        <p>Sign in to your .Library account.</p>
    </div>
    <div class="auth-card-body">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="login">Username or Email</label>
                <div class="input-wrap">
                    <input type="text" id="login" name="login" value="{{ old('login') }}"
                           required autocomplete="username"
                           class="{{ $errors->has('login') ? 'has-error' : '' }}"
                           placeholder="Enter username or email">
                </div>
                @error('login')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="loginPassword">Password</label>
                <div class="input-wrap">
                    <input type="password" id="loginPassword" name="password"
                           required autocomplete="current-password"
                           class="{{ $errors->has('password') ? 'has-error' : '' }}"
                           placeholder="Your password">
                    <button type="button" class="pw-toggle" onclick="togglePassword('loginPassword', this)">Show</button>
                </div>
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="checkbox-row">
                    <input type="checkbox" name="remember" value="1">
                    Remember me for 30 days
                </label>
            </div>

            <button type="submit" class="auth-submit-btn">Sign In</button>
        </form>

        <p class="auth-footer-text">Don't have an account? <a href="{{ route('register') }}">Create one</a></p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(inputId, btn) {
    var input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.textContent = input.type === 'password' ? 'Show' : 'Hide';
}
</script>
@endpush
