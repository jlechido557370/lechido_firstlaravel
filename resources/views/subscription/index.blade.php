@extends('layouts.app')
@section('title', 'Subscription')
@section('content')
<div class="card">
    <h1>Library Subscription</h1>
    <p class="muted">Unlock higher limits and support the library.</p>
</div>

@if($user->isSubscribed())
    <div class="card">
        <h2>Your Subscription</h2>
        <table style="width: auto;">
            <tr>
                <td style="padding: 4px 20px 4px 0; color: #6b7280; border: none;">Status</td>
                <td style="padding: 4px 0; border: none;"><span class="badge badge-green">Active</span></td>
            </tr>
            <tr>
                <td style="padding: 4px 20px 4px 0; color: #6b7280; border: none;">Expires</td>
                <td style="padding: 4px 0; border: none;"><strong>{{ $user->subscription_expires_at?->format('F d, Y') ?? 'N/A' }}</strong></td>
            </tr>
            @if($user->subscription_plan)
            <tr>
                <td style="padding: 4px 20px 4px 0; color: #6b7280; border: none;">Plan</td>
                <td style="padding: 4px 0; border: none;">{{ ucfirst($user->subscription_plan) }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 4px 20px 4px 0; color: #6b7280; border: none;">Display name</td>
                <td style="padding: 4px 0; border: none;"><strong>{{ $user->badgedName() }}</strong></td>
            </tr>
            <tr>
                <td style="padding: 4px 20px 4px 0; color: #6b7280; border: none;">Borrow limit</td>
                <td style="padding: 4px 0; border: none;">25 books at a time</td>
            </tr>
            <tr>
                <td style="padding: 4px 20px 4px 0; color: #6b7280; border: none;">Publish limit</td>
                <td style="padding: 4px 0; border: none;">50 books total</td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>Extend Subscription</h2>
        <p class="muted">Add more time to your existing subscription.</p>
        <div class="grid grid-2">
            <div>
                <p><strong>Monthly</strong> — 99 PHP</p>
                <a href="{{ route('subscription.confirm', ['plan' => 'monthly']) }}" style="display: block; text-align: center; padding: 10px; background: #111827; color: white; border-radius: 6px; text-decoration: none;">
                    Extend 1 Month
                </a>
            </div>
            <div>
                <p><strong>Yearly</strong> — 999 PHP</p>
                <a href="{{ route('subscription.confirm', ['plan' => 'yearly']) }}" style="display: block; text-align: center; padding: 10px; background: #111827; color: white; border-radius: 6px; text-decoration: none;">
                    Extend 1 Year
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Cancel Subscription</h2>
        <p class="muted">Cancelling will immediately remove your subscriber benefits. Your account will return to the free plan (5 books, 2 daily submissions).</p>
        <form method="POST" action="{{ route('subscription.cancel') }}" onsubmit="return confirm('Are you sure you want to cancel your subscription? This cannot be undone and will remove your subscriber benefits immediately.')">
            @csrf
            <button type="submit" style="width: auto; padding: 10px 24px; background: #dc2626; border-color: #dc2626;">
                Cancel Subscription
            </button>
        </form>
    </div>

@else
    <div class="card">
        <h2>What You Get</h2>
        <p><strong>Free Account</strong></p>
        <p>Borrow up to 5 books at a time. Submit up to 2 books per day for publishing.</p>
        <p style="margin-top: 16px;"><strong>Subscriber Account</strong></p>
        <p>Borrow up to 25 books at a time. Publish up to 50 books total. A <strong>+</strong> is added to your username so others can see you support the library.</p>
        <p style="margin-top: 16px; color: #6b7280; font-size: 13px;">
            Payments are processed securely. Your subscription activates immediately after payment confirmation.
        </p>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <h2>Monthly Plan</h2>
            <p style="font-size: 24px; font-weight: bold; margin: 12px 0;">99 PHP / month</p>
            <p class="muted" style="font-size: 13px; margin-bottom: 16px;">Cancel anytime. Renew monthly.</p>
            <a href="{{ route('subscription.confirm', ['plan' => 'monthly']) }}" style="display: block; text-align: center; padding: 10px; background: #111827; color: white; border-radius: 6px; text-decoration: none;">
                Subscribe Monthly
            </a>
        </div>
        <div class="card">
            <h2>Yearly Plan</h2>
            <p style="font-size: 24px; font-weight: bold; margin: 12px 0;">999 PHP / year</p>
            <p class="muted" style="font-size: 13px; margin-bottom: 16px;">Save 2 months compared to monthly.</p>
            <a href="{{ route('subscription.confirm', ['plan' => 'yearly']) }}" style="display: block; text-align: center; padding: 10px; background: #111827; color: white; border-radius: 6px; text-decoration: none;">
                Subscribe Yearly
            </a>
        </div>
    </div>
@endif
@endsection