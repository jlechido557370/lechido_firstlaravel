@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<style>
    .auth-card {
        max-width: 520px; margin: 20px auto;
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
    .form-label-hint { font-size: 11.5px; font-weight: 400; color: var(--muted); margin-left: 5px; }
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
    .input-wrap input.has-error:focus { box-shadow: 0 0 0 4px rgba(220,38,38,.12); }
    .input-wrap input[type="password"] { padding-right: 52px; }

    .pw-toggle {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        background: none; border: none; color: var(--muted); font-size: 12px;
        font-family: var(--font-mono); cursor: pointer; padding: 4px 6px;
        border-radius: 5px; letter-spacing: .04em;
        transition: color .15s, background .15s; box-shadow: none;
    }
    .pw-toggle:hover { color: var(--black); background: var(--mid); opacity: 1; transform: translateY(-50%); box-shadow: none; }

    /* Gender radio group */
    .radio-group { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 4px; }
    .radio-option {
        display: flex; align-items: center; gap: 8px; cursor: pointer;
        padding: 9px 14px; border: 1.5px solid var(--border); border-radius: 9px;
        background: var(--off); transition: border-color .18s, background .15s;
        font-size: 13.5px; font-weight: 400; color: var(--black);
        user-select: none;
    }
    .radio-option:hover { border-color: var(--black); background: var(--white); }
    .radio-option input[type="radio"] {
        width: 16px !important; height: 16px !important;
        min-width: 16px !important; padding: 0 !important;
        margin: 0 !important; cursor: pointer; flex-shrink: 0;
        accent-color: var(--black);
        appearance: auto !important; -webkit-appearance: auto !important;
    }
    .radio-option:has(input:checked) {
        border-color: var(--black); background: var(--black); color: var(--white);
    }

    /* Checkbox for terms */
    .checkbox-option {
        display: flex; align-items: flex-start; gap: 10px; cursor: pointer;
        padding: 14px 16px; border: 1.5px solid var(--border); border-radius: 9px;
        background: var(--off); transition: border-color .18s, background .15s;
        font-size: 13.5px; font-weight: 400; color: var(--black); line-height: 1.5;
    }
    .checkbox-option:hover { border-color: var(--black); background: var(--white); }
    .checkbox-option input[type="checkbox"] {
        width: 17px !important; height: 17px !important;
        min-width: 17px !important; padding: 0 !important;
        margin-top: 1px; cursor: pointer; flex-shrink: 0;
        accent-color: var(--black);
        appearance: auto !important; -webkit-appearance: auto !important;
    }
    .checkbox-option:has(input:checked) { border-color: var(--black); }

    .auth-submit-btn {
        width: 100%; padding: 13px; background: var(--black); color: var(--white);
        border: 1.5px solid var(--black); border-radius: 9px;
        font-size: 14.5px; font-weight: 600; font-family: var(--font-sans);
        cursor: pointer; letter-spacing: .02em;
        transition: opacity .15s, transform .15s, box-shadow .15s;
    }
    .auth-submit-btn:hover { opacity: .87; transform: translateY(-1px); box-shadow: var(--shadow-md); }
    .auth-submit-btn:active { transform: translateY(0); box-shadow: none; }

    .auth-footer-text {
        text-align: center; font-size: 13.5px; color: var(--muted); margin-top: 20px;
    }
    .auth-footer-text a { color: var(--black); font-weight: 600; border-bottom: 1.5px solid var(--border); padding-bottom: 1px; transition: border-color .15s; }
    .auth-footer-text a:hover { border-color: var(--black); opacity: 1; }

    /* Terms modal */
    .terms-modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,.6); z-index: 9998;
        overflow-y: auto; padding: 20px;
        backdrop-filter: blur(4px);
    }
    .terms-modal-overlay.open { display: block; }
    .terms-modal {
        background: var(--white); max-width: 680px; margin: 40px auto;
        border-radius: 14px; padding: 32px; position: relative;
        border: 1.5px solid var(--border); box-shadow: var(--shadow-lg);
    }
    .terms-modal h2 { margin-top: 0; font-size: 22px; font-weight: 600; letter-spacing: -.02em; margin-bottom: 4px; }
    .terms-modal h3 { font-size: 15px; font-weight: 600; margin: 20px 0 6px; color: var(--black); }
    .terms-modal p { color: var(--muted); font-size: 13.5px; line-height: 1.75; }
    .terms-close-btn {
        position: absolute; top: 14px; right: 14px;
        background: var(--off); border: 1.5px solid var(--border); color: var(--black);
        padding: 5px 12px; border-radius: 7px; font-size: 12.5px; cursor: pointer;
        font-family: var(--font-mono); transition: background .15s; box-shadow: none;
    }
    .terms-close-btn:hover { background: var(--mid); opacity: 1; transform: none; box-shadow: none; }
    .terms-accept-btn {
        background: var(--black); color: var(--white); border: none;
        padding: 11px 24px; border-radius: 8px; margin-top: 20px;
        font-size: 14px; font-weight: 600; cursor: pointer; font-family: var(--font-sans);
    }
    .terms-accept-btn:hover { opacity: .85; }

    .divider { height: 1px; background: var(--border); margin: 24px 0; }
</style>

<div class="auth-card">
    <div class="auth-card-header">
        <h1>Create Account</h1>
        <p>Join .Library and start reading today.</p>
    </div>
    <div class="auth-card-body">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="username">
                    Username <span class="form-label-hint">(letters, numbers, underscores only)</span>
                </label>
                <div class="input-wrap">
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                           required autocomplete="username" pattern="[a-zA-Z0-9_]+"
                           minlength="3" maxlength="30"
                           class="{{ $errors->has('username') ? 'has-error' : '' }}"
                           placeholder="e.g. john_doe">
                </div>
                @error('username')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <div class="input-wrap">
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           required autocomplete="name"
                           class="{{ $errors->has('name') ? 'has-error' : '' }}"
                           placeholder="Your full name">
                </div>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-wrap">
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           required autocomplete="email"
                           class="{{ $errors->has('email') ? 'has-error' : '' }}"
                           placeholder="you@example.com">
                </div>
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="regPassword">Password</label>
                <div class="input-wrap">
                    <input type="password" id="regPassword" name="password"
                           required autocomplete="new-password" minlength="8"
                           class="{{ $errors->has('password') ? 'has-error' : '' }}"
                           placeholder="At least 8 characters">
                    <button type="button" class="pw-toggle" onclick="togglePassword('regPassword', this)">Show</button>
                </div>
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="regPasswordConfirm">Confirm Password</label>
                <div class="input-wrap">
                    <input type="password" id="regPasswordConfirm" name="password_confirmation"
                           required autocomplete="new-password"
                           placeholder="Re-enter your password">
                    <button type="button" class="pw-toggle" onclick="togglePassword('regPasswordConfirm', this)">Show</button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Gender</label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="gender" value="male" {{ old('gender') === 'male' ? 'checked' : '' }}>
                        Male
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }}>
                        Female
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="gender" value="prefer_not_to_say" {{ old('gender') === 'prefer_not_to_say' ? 'checked' : '' }}>
                        Prefer not to say
                    </label>
                </div>
                @error('gender')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="checkbox-option">
                    <input type="checkbox" name="terms" value="1" id="termsCheckbox" {{ old('terms') ? 'checked' : '' }}>
                    <span>I agree to the <a href="#" onclick="showTerms(event)" style="font-weight:600; border-bottom:1.5px solid var(--border); padding-bottom:1px;">Terms of Service and Privacy Policy</a></span>
                </label>
                @error('terms')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="auth-submit-btn">Create Account</button>
        </form>

        <p class="auth-footer-text">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
    </div>
</div>

<!-- Terms Modal -->
<div id="terms-modal" class="terms-modal-overlay">
    <div class="terms-modal">
        <button onclick="closeTerms()" class="terms-close-btn">Close ✕</button>
        <h2>Terms of Service</h2>
        <p style="font-size:12px; margin-bottom:0;">Last updated: {{ date('F d, Y') }}</p>

        <h3>1. Acceptance of Terms</h3>
        <p>By registering and using this Library Management System ("the Service"), you agree to be bound by these Terms of Service. If you do not agree to these terms, you may not use the Service.</p>

        <h3>2. User Accounts</h3>
        <p>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You must provide accurate, current, and complete information during registration. You may not impersonate any person or entity.</p>

        <h3>3. Borrowing and Returns</h3>
        <p>Users may borrow up to 5 books at a time. Each borrow period is 10 days. Overdue books incur a fine of PHP 5.00 per day. Outstanding fines must be paid before books can be returned. We reserve the right to suspend accounts with repeated violations.</p>

        <h3>4. Book Publishing</h3>
        <p>Users may submit up to 2 books per day for publication. All submissions are subject to review by our staff before being published. We reserve the right to reject submissions that contain inappropriate, inaccurate, or copyrighted content without proper attribution.</p>

        <h3>5. User Conduct</h3>
        <p>You agree not to use the Service to upload or transmit harmful, offensive, or illegal content. You agree not to use the messaging system to spam or harass other users.</p>

        <h3>6. Privacy</h3>
        <p>We collect information you provide during registration including your name, username, email, and optional gender. We use cookies to maintain your session and improve your experience. We do not sell your personal data to third parties.</p>

        <h3>7. Payments and Fees</h3>
        <p>All fine payments are processed through our designated payment gateway. Fees are non-refundable once confirmed unless due to a system error on our part.</p>

        <h3>8. Termination</h3>
        <p>We reserve the right to terminate or suspend access to the Service immediately, without prior notice, for conduct that we believe violates these Terms.</p>

        <h3>9. Limitation of Liability</h3>
        <p>The Service is provided on an "as is" basis. We make no warranties, expressed or implied. In no event shall we be liable for any indirect or consequential damages.</p>

        <h3>10. Contact</h3>
        <p>For questions about these Terms, contact the library administration through the messaging system or in person at the library.</p>

        <button onclick="acceptTerms()" class="terms-accept-btn">I Agree — Close</button>
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
function showTerms(e) {
    e.preventDefault();
    document.getElementById('terms-modal').classList.add('open');
}
function closeTerms() {
    document.getElementById('terms-modal').classList.remove('open');
}
function acceptTerms() {
    document.getElementById('termsCheckbox').checked = true;
    closeTerms();
}
</script>
@endpush
