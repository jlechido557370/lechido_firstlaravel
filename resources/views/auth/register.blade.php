@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="card" style="max-width: 480px; margin: 30px auto;">
        <h1>Create Account</h1>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div style="margin-bottom: 12px;">
                <label>Username <span class="muted" style="font-size:12px;">(letters, numbers, underscores only)</span></label>
                <input type="text" name="username" value="{{ old('username') }}" required autocomplete="username" pattern="[a-zA-Z0-9_]+" minlength="3" maxlength="30">
                @error('username')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom: 12px;">
                <label>Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name">
                @error('name')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom: 12px;">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                @error('email')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom: 12px;">
                <label>Password</label>
                <div style="position:relative;">
                    <input type="password" name="password" id="regPassword" required autocomplete="new-password" minlength="8" style="padding-right:44px;">
                    <button type="button" onclick="togglePassword('regPassword', this)" style="position:absolute;right:0;top:0;bottom:0;width:40px;background:transparent;border:none;color:#6b7280;font-size:13px;">Show</button>
                </div>
                @error('password')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom: 12px;">
                <label>Confirm Password</label>
                <div style="position:relative;">
                    <input type="password" name="password_confirmation" id="regPasswordConfirm" required autocomplete="new-password" style="padding-right:44px;">
                    <button type="button" onclick="togglePassword('regPasswordConfirm', this)" style="position:absolute;right:0;top:0;bottom:0;width:40px;background:transparent;border:none;color:#6b7280;font-size:13px;">Show</button>
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <label>Gender</label>
                <div style="display:flex; gap:20px; margin-top:6px; flex-wrap:wrap;">
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-weight:normal;">
                        <input type="radio" name="gender" value="male" {{ old('gender') === 'male' ? 'checked' : '' }} style="width:auto;">
                        Male
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-weight:normal;">
                        <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }} style="width:auto;">
                        Female
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-weight:normal;">
                        <input type="radio" name="gender" value="prefer_not_to_say" {{ old('gender') === 'prefer_not_to_say' ? 'checked' : '' }} style="width:auto;">
                        Prefer not to say
                    </label>
                </div>
                @error('gender')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display:flex; align-items:flex-start; gap:8px; cursor:pointer; font-weight:normal; line-height:1.4;">
                    <input type="checkbox" name="terms" value="1" id="termsCheckbox" style="width:auto; margin-top:2px;" {{ old('terms') ? 'checked' : '' }}>
                    <span>I agree to the <a href="#terms-modal" onclick="showTerms(event)">Terms of Service and Privacy Policy</a></span>
                </label>
                @error('terms')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
            </div>

            <button type="submit">Create Account</button>
        </form>
        <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>

    <!-- Terms of Service Modal -->
    <div id="terms-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:9998; overflow-y:auto; padding:20px;">
        <div style="background:white; max-width:680px; margin:40px auto; border-radius:8px; padding:28px; position:relative;">
            <button onclick="closeTerms()" style="position:absolute; top:12px; right:12px; width:auto; padding:4px 10px; background:#6b7280; border-color:#6b7280;">Close</button>
            <h2 style="margin-top:0;">Terms of Service</h2>
            <p style="color:#6b7280; font-size:13px;">Last updated: {{ date('F d, Y') }}</p>

            <h3>1. Acceptance of Terms</h3>
            <p>By registering and using this Library Management System ("the Service"), you agree to be bound by these Terms of Service. If you do not agree to these terms, you may not use the Service.</p>

            <h3>2. User Accounts</h3>
            <p>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You must provide accurate, current, and complete information during registration. You may not impersonate any person or entity.</p>

            <h3>3. Borrowing and Returns</h3>
            <p>Users may borrow up to 5 books at a time. Each borrow period is 10 days. Overdue books incur a fine of PHP 5.00 per day. Outstanding fines must be paid before books can be returned. We reserve the right to suspend accounts with repeated violations.</p>

            <h3>4. Book Publishing</h3>
            <p>Users may submit up to 2 books per day for publication. All submissions are subject to review by our staff before being published. We reserve the right to reject submissions that contain inappropriate, inaccurate, or copyrighted content without proper attribution. By submitting content, you grant us a non-exclusive license to display and distribute it through the Service.</p>

            <h3>5. User Conduct</h3>
            <p>You agree not to use the Service to upload or transmit harmful, offensive, or illegal content. You agree not to use the messaging system to spam, harass, or contact other users without their consent. Users who have disabled direct messages must not be contacted through alternative means.</p>

            <h3>6. Privacy</h3>
            <p>We collect information you provide during registration including your name, username, email, and optional gender. We use cookies to maintain your session and improve your experience. You may control non-essential cookie usage through our cookie consent banner. We do not sell your personal data to third parties.</p>

            <h3>7. Payments and Fees</h3>
            <p>All fine payments are processed through our designated payment gateway. Payment records are maintained for accounting purposes. Fees are non-refundable once confirmed unless due to a system error on our part.</p>

            <h3>8. Intellectual Property</h3>
            <p>The Service and its original content (excluding user-submitted content) are and will remain the exclusive property of the Library. Our trademarks may not be used in connection with any product or service without prior written consent.</p>

            <h3>9. Termination</h3>
            <p>We reserve the right to terminate or suspend access to the Service immediately, without prior notice, for conduct that we believe violates these Terms or is harmful to other users, us, or third parties, or for any other reason at our sole discretion.</p>

            <h3>10. Limitation of Liability</h3>
            <p>The Service is provided on an "as is" and "as available" basis. We make no warranties, expressed or implied, and disclaim all other warranties. In no event shall we be liable for any indirect, incidental, special, or consequential damages.</p>

            <h3>11. Changes to Terms</h3>
            <p>We reserve the right to modify these terms at any time. Continued use of the Service after changes constitutes your acceptance of the updated terms.</p>

            <h3>12. Contact</h3>
            <p>For questions about these Terms, contact the library administration through the messaging system or in person at the library.</p>

            <button onclick="acceptTerms()" style="background:#111827; margin-top:16px;">I Agree - Close</button>
        </div>
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
function showTerms(e) {
    e.preventDefault();
    document.getElementById('terms-modal').style.display = 'block';
}
function closeTerms() {
    document.getElementById('terms-modal').style.display = 'none';
}
function acceptTerms() {
    document.getElementById('termsCheckbox').checked = true;
    closeTerms();
}
</script>
@endpush