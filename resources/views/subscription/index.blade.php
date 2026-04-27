@extends('layouts.app')
@section('title', 'Subscription')
@section('content')

<style>
    /* ── SUBSCRIPTION PAGE SPECIFIC ── */
    .sub-header {
        text-align: center;
        padding: 48px 32px 36px;
        margin-bottom: 28px;
        background: var(--glass-bg);
        backdrop-filter: blur(16px) saturate(1.4);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--glass-shadow);
        position: relative;
        overflow: hidden;
    }
    .sub-header::before {
        content: '✦';
        position: absolute;
        font-size: 180px;
        color: var(--accent);
        opacity: 0.04;
        top: -20px;
        right: 40px;
        line-height: 1;
        pointer-events: none;
    }
    .sub-header-accent {
        position: absolute;
        top: -60px;
        right: -60px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        border: 1px solid rgba(79,110,247,.08);
        pointer-events: none;
    }
    .sub-header-accent-2 {
        position: absolute;
        bottom: -40px;
        left: 60px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 1px solid rgba(79,110,247,.06);
        pointer-events: none;
    }

    .sub-status-card {
        background: var(--glass-bg);
        backdrop-filter: blur(16px) saturate(1.4);
        border: 1px solid var(--glass-border);
        border-radius: 18px;
        padding: 32px;
        margin-bottom: 20px;
        box-shadow: var(--glass-shadow);
    }

    .sub-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        margin-top: 20px;
    }
    .sub-info-cell {
        padding: 16px 20px;
        border-right: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
        background: var(--off);
    }
    .sub-info-cell:nth-child(even) { border-right: none; }
    .sub-info-cell:nth-last-child(-n+2) { border-bottom: none; }
    .sub-info-label {
        font-size: 10px;
        font-weight: 600;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--muted);
        font-family: var(--font-mono);
        margin-bottom: 5px;
    }
    .sub-info-value {
        font-size: 15px;
        font-weight: 500;
        color: var(--black);
    }

    /* Plan cards */
    .plan-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 20px; }
    @media (max-width: 640px) { .plan-grid { grid-template-columns: 1fr; } .sub-info-grid { grid-template-columns: 1fr; } .sub-info-cell { border-right: none !important; } }

    .plan-card {
        background: var(--glass-bg);
        backdrop-filter: blur(16px) saturate(1.4);
        border: 1.5px solid var(--glass-border);
        border-radius: 18px;
        padding: 32px 28px;
        box-shadow: var(--glass-shadow);
        position: relative;
        overflow: hidden;
        transition: transform .28s cubic-bezier(0.34,1.2,0.64,1), box-shadow .28s ease, border-color .2s;
    }
    .plan-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--glass-shadow-lg);
        border-color: var(--black);
    }
    .plan-card.featured {
        border-color: var(--black);
        background: var(--glass-bg-strong);
    }
    .plan-card.featured::before {
        content: 'BEST VALUE';
        position: absolute;
        top: 16px; right: -32px;
        background: var(--accent);
        color: white;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .12em;
        font-family: var(--font-mono);
        padding: 5px 40px;
        transform: rotate(45deg);
        transform-origin: center;
    }

    .plan-price {
        font-family: var(--font-disp);
        font-size: 48px;
        color: var(--black);
        line-height: 1;
        margin: 16px 0 4px;
        letter-spacing: .01em;
    }
    .plan-period {
        font-size: 12px;
        color: var(--muted);
        font-family: var(--font-mono);
        letter-spacing: .05em;
        text-transform: uppercase;
        margin-bottom: 20px;
    }
    .plan-features {
        list-style: none;
        margin-bottom: 28px;
    }
    .plan-features li {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 0;
        font-size: 14.5px;
        color: var(--black);
        border-bottom: 1px solid var(--mid);
    }
    .plan-features li:last-child { border-bottom: none; }
    .plan-features li svg { flex-shrink: 0; color: #16a34a; width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.2; }

    /* Extend subscription section */
    .extend-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    @media (max-width: 640px) { .extend-grid { grid-template-columns: 1fr; } }

    .extend-option {
        border: 1.5px solid var(--border);
        border-radius: 14px;
        padding: 22px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        text-decoration: none;
        color: var(--black);
        background: var(--off);
        transition: border-color .2s, background .2s, transform .22s cubic-bezier(0.34,1.3,0.64,1), box-shadow .2s;
    }
    .extend-option:hover {
        border-color: var(--black);
        background: var(--white);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        opacity: 1;
    }
    .extend-option-info { flex: 1; }
    .extend-option-label { font-size: 16px; font-weight: 600; color: var(--black); margin-bottom: 2px; }
    .extend-option-price { font-size: 13px; color: var(--muted); font-family: var(--font-mono); }
    .extend-option-btn {
        background: var(--black);
        color: var(--white);
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 500;
        cursor: pointer;
        white-space: nowrap;
        transition: opacity .15s, transform .15s;
    }
    .extend-option:hover .extend-option-btn { opacity: .85; transform: scale(1.03); }

    /* Benefits comparison */
    .benefits-table { width: 100%; }
    .benefits-table th { 
        font-size: 11px; letter-spacing: .08em; text-transform: uppercase; 
        font-family: var(--font-mono); padding: 10px 16px; 
        border-bottom: 2px solid var(--black); text-align: left; color: var(--muted);
    }
    .benefits-table td { 
        padding: 12px 16px; border-bottom: 1px solid var(--mid); 
        font-size: 14.5px; color: var(--black); vertical-align: middle;
    }
    .benefits-table tr:hover td { background: var(--off); }
    .check { color: #16a34a; font-size: 16px; }
    .cross { color: var(--muted); font-size: 16px; }

    /* Cancel section */
    .cancel-section {
        background: rgba(220,38,38,.04);
        border: 1px solid rgba(220,38,38,.15);
        border-radius: 16px;
        padding: 28px 32px;
    }
    [data-theme="dark"] .cancel-section {
        background: rgba(220,38,38,.06);
        border-color: rgba(220,38,38,.2);
    }
</style>

@if($user->isSubscribed())

    {{-- SUBSCRIBED VIEW --}}
    <div class="sub-header">
        <div class="sub-header-accent"></div>
        <div class="sub-header-accent-2"></div>
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(22,163,74,.1);border:1px solid rgba(22,163,74,.25);padding:6px 16px;border-radius:999px;margin-bottom:16px;">
            <span style="width:7px;height:7px;border-radius:50%;background:#16a34a;display:inline-block;box-shadow:0 0 6px #16a34a;animation:pulse 2s infinite;"></span>
            <span style="font-size:11px;font-family:var(--font-mono);letter-spacing:.09em;color:#15803d;text-transform:uppercase;font-weight:600;">Active Subscriber</span>
        </div>
        <h1 style="font-size:28px;font-weight:500;color:var(--black);margin-bottom:8px;">Your Subscription</h1>
        <p style="color:var(--muted);font-size:15px;">Thank you for supporting .Library. Here's your current plan.</p>
    </div>

    <div class="sub-status-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;flex-wrap:wrap;gap:12px;">
            <div>
                <h2 style="font-size:18px;font-weight:600;color:var(--black);margin:0 0 2px;">Plan Details</h2>
                <p style="font-size:13px;color:var(--muted);margin:0;">Your current subscription information</p>
            </div>
            <span class="badge badge-green" style="font-size:12px;padding:6px 14px;">Active</span>
        </div>

        <div class="sub-info-grid">
            <div class="sub-info-cell">
                <div class="sub-info-label">Status</div>
                <div class="sub-info-value" style="color:#16a34a;">● Active</div>
            </div>
            <div class="sub-info-cell">
                <div class="sub-info-label">Expires</div>
                <div class="sub-info-value">{{ $user->subscription_expires_at?->format('F d, Y') ?? 'N/A' }}</div>
            </div>
            <div class="sub-info-cell">
                <div class="sub-info-label">Display Name</div>
                <div class="sub-info-value">{{ $user->badgedName() }}</div>
            </div>
            @if($user->subscription_plan)
            <div class="sub-info-cell">
                <div class="sub-info-label">Plan</div>
                <div class="sub-info-value">{{ ucfirst($user->subscription_plan) }}</div>
            </div>
            @endif
            <div class="sub-info-cell">
                <div class="sub-info-label">Borrow Limit</div>
                <div class="sub-info-value">25 books at a time</div>
            </div>
            <div class="sub-info-cell">
                <div class="sub-info-label">Publish Limit</div>
                <div class="sub-info-value">50 books total</div>
            </div>
        </div>
    </div>

    <div class="sub-status-card">
        <h2 style="font-size:18px;font-weight:600;color:var(--black);margin:0 0 6px;">Extend Subscription</h2>
        <p style="color:var(--muted);font-size:14px;margin-bottom:22px;">Add more time to your existing subscription. Your new expiry date will be extended from your current end date.</p>

        <div class="extend-grid">
            <a href="{{ route('subscription.confirm', ['plan' => 'monthly']) }}" class="extend-option">
                <div class="extend-option-info">
                    <div class="extend-option-label">Monthly</div>
                    <div class="extend-option-price">99 PHP / month</div>
                </div>
                <span class="extend-option-btn">Extend 1 Month</span>
            </a>
            <a href="{{ route('subscription.confirm', ['plan' => 'yearly']) }}" class="extend-option">
                <div class="extend-option-info">
                    <div class="extend-option-label">Yearly</div>
                    <div class="extend-option-price">999 PHP / year · save 2 months</div>
                </div>
                <span class="extend-option-btn">Extend 1 Year</span>
            </a>
        </div>
    </div>

    <div class="cancel-section">
        <h2 style="font-size:17px;font-weight:600;color:#b91c1c;margin:0 0 8px;">Cancel Subscription</h2>
        <p style="color:var(--muted);font-size:14px;line-height:1.7;margin-bottom:20px;">
            Cancelling will immediately remove your subscriber benefits. Your account will return to the free plan (5 books, 2 daily submissions). This action cannot be undone.
        </p>
        <form method="POST" action="{{ route('subscription.cancel') }}" onsubmit="return confirm('Are you sure you want to cancel? This cannot be undone and removes your benefits immediately.')">
            @csrf
            <button type="submit" class="btn-danger" style="padding:11px 28px;font-size:14px;">
                Cancel Subscription
            </button>
        </form>
    </div>

@else
    {{-- NON-SUBSCRIBED VIEW --}}
    <div class="sub-header">
        <div class="sub-header-accent"></div>
        <div class="sub-header-accent-2"></div>
        <div style="display:inline-flex;align-items:center;gap:8px;background:var(--accent-soft);border:1px solid rgba(79,110,247,.2);padding:6px 16px;border-radius:999px;margin-bottom:16px;">
            <span style="font-size:14px;">✦</span>
            <span style="font-size:11px;font-family:var(--font-mono);letter-spacing:.09em;color:var(--accent);text-transform:uppercase;font-weight:600;">Library Subscription</span>
        </div>
        <h1 style="font-size:32px;font-weight:500;color:var(--black);margin-bottom:10px;">Unlock the Full Library</h1>
        <p style="color:var(--muted);font-size:16px;max-width:480px;margin:0 auto;line-height:1.7;">Borrow more, publish more, and show your support with a subscriber badge. Starting at 99 PHP/month.</p>
    </div>

    {{-- Comparison table --}}
    <div class="sub-status-card" style="margin-bottom:20px;">
        <h2 style="font-size:18px;font-weight:600;color:var(--black);margin:0 0 20px;">Free vs Subscriber</h2>
        <table class="benefits-table">
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>Free</th>
                    <th>Subscriber</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Borrow books at a time</td>
                    <td><span style="color:var(--muted);">5 books</span></td>
                    <td><strong style="color:#16a34a;">25 books</strong></td>
                </tr>
                <tr>
                    <td>Daily book submissions</td>
                    <td><span style="color:var(--muted);">2 per day</span></td>
                    <td><strong style="color:#16a34a;">Unlimited</strong></td>
                </tr>
                <tr>
                    <td>Total publish limit</td>
                    <td><span style="color:var(--muted);">—</span></td>
                    <td><strong style="color:#16a34a;">50 books</strong></td>
                </tr>
                <tr>
                    <td>Subscriber badge (✦)</td>
                    <td><span class="cross">✕</span></td>
                    <td><span class="check">✓</span></td>
                </tr>
                <tr>
                    <td>Priority support</td>
                    <td><span class="cross">✕</span></td>
                    <td><span class="check">✓</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Plan cards --}}
    <div class="plan-grid">
        <div class="plan-card">
            <div style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);font-family:var(--font-mono);">Monthly Plan</div>
            <div class="plan-price">99<span style="font-size:20px;font-family:var(--font-sans);font-weight:400;"> PHP</span></div>
            <div class="plan-period">per month · cancel anytime</div>
            <ul class="plan-features">
                <li><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>25 books borrowed at once</li>
                <li><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>50 total book publications</li>
                <li><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Subscriber ✦ badge</li>
                <li><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Flexible monthly billing</li>
            </ul>
            <a href="{{ route('subscription.confirm', ['plan' => 'monthly']) }}" style="display:flex;align-items:center;justify-content:center;gap:8px;background:var(--black);color:var(--white);padding:13px 24px;border-radius:10px;font-size:15px;font-weight:600;text-decoration:none;transition:opacity .18s,transform .18s,box-shadow .18s;width:100%;" onmouseover="this.style.opacity='.85';this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 16px rgba(0,0,0,0.2)'" onmouseout="this.style.opacity='1';this.style.transform='';this.style.boxShadow=''">
                Subscribe Monthly
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>

        <div class="plan-card featured">
            <div style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--accent);font-family:var(--font-mono);">Yearly Plan</div>
            <div class="plan-price">999<span style="font-size:20px;font-family:var(--font-sans);font-weight:400;"> PHP</span></div>
            <div class="plan-period">per year · save 2 months</div>
            <ul class="plan-features">
                <li><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>25 books borrowed at once</li>
                <li><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>50 total book publications</li>
                <li><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Subscriber ✦ badge</li>
                <li><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Best value — 2 months free</li>
            </ul>
            <a href="{{ route('subscription.confirm', ['plan' => 'yearly']) }}" style="display:flex;align-items:center;justify-content:center;gap:8px;background:var(--accent);color:white;padding:13px 24px;border-radius:10px;font-size:15px;font-weight:600;text-decoration:none;transition:opacity .18s,transform .18s,box-shadow .18s;width:100%;" onmouseover="this.style.opacity='.88';this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 16px rgba(79,110,247,0.35)'" onmouseout="this.style.opacity='1';this.style.transform='';this.style.boxShadow=''">
                Subscribe Yearly
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    <div style="text-align:center;padding:14px 0 8px;">
        <p style="color:var(--muted);font-size:13px;font-family:var(--font-mono);letter-spacing:.03em;">Payments are processed securely. Your subscription activates immediately after confirmation.</p>
    </div>

@endif
@endsection
