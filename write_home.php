<?php
$content = <<<'BLADE'
@extends('layouts.app')
@section('title', 'Home — dotLibrary')

@section('content')

{{-- ── HERO BANNER ── --}}
<div class="hero-glass" style="border-radius:24px;padding:56px 52px;margin-bottom:32px;position:relative;overflow:hidden;border:1px solid rgba(148,163,184,.18);animation:heroFadeIn .7s cubic-bezier(0.4,0,0.2,1) both;">
    <div style="position:absolute;top:-100px;right:-60px;width:340px;height:340px;border-radius:50%;border:1.5px solid rgba(255,255,255,.05);pointer-events:none;animation:float 8s ease-in-out infinite;"></div>
    <div style="position:absolute;top:-50px;right:20px;width:200px;height:200px;border-radius:50%;border:1.5px solid rgba(255,255,255,.07);pointer-events:none;animation:float 6s ease-in-out infinite reverse;"></div>
    <div style="position:absolute;bottom:-80px;left:180px;width:260px;height:260px;border-radius:50%;border:1.5px solid rgba(255,255,255,.04);pointer-events:none;animation:float 10s ease-in-out infinite;"></div>
    <div style="position:relative;z-index:1;max-width:680px;">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);padding:6px 16px;border-radius:999px;margin-bottom:22px;backdrop-filter:blur(8px);">
            <span style="width:8px;height:8px;border-radius:50%;background:#4ade80;display:inline-block;box-shadow:0 0 8px #4ade80;animation:pulse 2.2s infinite;"></span>
            <span style="font-size:11px;font-family:var(--font-mono);letter-spacing:.1em;color:rgba(255,255,255,.75);text-transform:uppercase;">Open · Davao City</span>
        </div>
        <h1 style="font-family:var(--font-disp);font-size:clamp(48px,7vw,80px);color:#ffffff;line-height:.9;letter-spacing:.02em;margin-bottom:20px;font-weight:400;">.Library</h1>
        <p style="font-size:17px;color:rgba(255,255,255,.7);line-height:1.8;margin-bottom:32px;max-width:520px;">A modern digital library for Davao City. Browse thousands of titles, borrow books instantly, and manage your reading life — all in one place.</p>
        <div style="display:flex;gap:14px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('books.catalogue') }}" style="display:inline-flex;align-items:center;gap:8px;background:#ffffff;color:#0f172a;font-size:14px;font-weight:600;padding:12px 24px;border-radius:10px;text-decoration:none;box-shadow:0 4px 16px rgba(0,0,0,0.15);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                Browse Catalogue
            </a>
            @guest
            <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.9);font-size:14px;padding:12px 24px;border-radius:10px;border:1px solid rgba(255,255,255,.2);text-decoration:none;backdrop-filter:blur(4px);">
                Create Free Account
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
            @endguest
            @auth
            <a href="{{ route('user.dashboard') }}" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.9);font-size:14px;padding:12px 24px;border-radius:10px;border:1px solid rgba(255,255,255,.2);text-decoration:none;backdrop-filter:blur(4px);">
                My Dashboard
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
            @endauth
        </div>
    <div style="position:absolute;right:52px;bottom:40px;text-align:right;display:none;" class="hero-hours">
        <div style="font-size:10px;font-family:var(--font-mono);letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.45);margin-bottom:5px;">Library Hours</div>
        <div style="font-size:15px;color:rgba(255,255,255,.7);">Mon – Fri &nbsp; 8am – 8pm</div>
        <div style="font-size:12px;color:rgba(255,255,255,.45);margin-top:4px;font-family:var(--font-mono);">Bolton St, Poblacion, Davao</div>
</div>

{{-- ── FLOATING STATS ── --}}
<div class="reveal" style="display:flex;gap:28px;flex-wrap:wrap;align-items:flex-start;margin-bottom:40px;padding:8px 4px;">
    @php $statItems = [
        ['label'=>'Total Books','value'=>$stats['total_books'],'icon'=>'<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>'],
        ['label'=>'Available','value'=>$stats['available_books'],'icon'=>'<polyline points="20 6 9 17 4 12"/>'],
        ['label'=>'Active Borrows','value'=>$stats['active_borrows'],'icon'=>'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
        ['label'=>'Readers','value'=>$stats['members'],'icon'=>'<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
    ]; @endphp
    @foreach($statItems as $i => $st)
    <div style="flex:1;min-width:140px;max-width:200px;animation:fadeInUp .5s ease {{ $i * 0.08 }}s both;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:36px;height:36px;border-radius:10px;background:var(--accent-soft);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8">{!! $st['icon'] !!}</svg>
            </div>
            <span style="font-size:11px;color:var(--muted);font-family:var(--font-mono);letter-spacing:.06em;text-transform:uppercase;">{{ $st['label'] }}</span>
        </div>
        <div style="font-family:var(--font-disp);font-size:36px;color:var(--black);line-height:1;letter-spacing:.02em;">{{ number_format($st['value']) }}</div>
    @endforeach
</div>

{{-- ── HOW IT WORKS ── --}}
<div class="reveal" style="margin-bottom:48px;padding:8px 0;">
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:36px;">
        <h2 style="margin:0;font-size:22px;font-weight:600;color:var(--black);">How It Works</h2>
        <div style="flex:1;height:1px;background:linear-gradient(to right, var(--border), transparent);"></div>
        <span style="font-size:12px;color:var(--muted);font-family:var(--font-mono);">Free to join · No credit card</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:32px;position:relative;">
        <div style="position:absolute;top:20px;left:10%;right:10%;height:2px;background:linear-gradient(to right, transparent, var(--border), transparent);display:none;" class="timeline-line"></div>
        @php $steps = [
            ['n'=>'01','title'=>'Create an Account','desc'=>'Sign up for free in seconds. No credit card required to browse or register.','color'=>'#4f6ef7'],
            ['n'=>'02','title'=>'Browse & Discover','desc'=>'Search our full catalogue by title, author, genre, or ISBN. Filter by availability.','color'=>'#10b981'],
            ['n'=>'03','title'=>'Borrow Instantly','desc'=>'Free users borrow up to 5 books for 10 days. Subscribers get up to 25 books.','color'=>'#f59e0b'],
            ['n'=>'04','title'=>'Read or Return','desc'=>'Read books online with a subscription, or return them to borrow new ones.','color'=>'#ec4899'],
        ]; @endphp
        @foreach($steps as $step)
        <div class="how-step" style="position:relative;text-align:center;padding-top:8px;">
            <div style="width:44px;height:44px;border-radius:50%;background:{{ $step['color'] }}15;border:2px solid {{ $step['color'] }}40;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;position:relative;z-index:1;">
                <span style="font-family:var(--font-mono);font-size:13px;font-weight:600;color:{{ $step['color'] }};">{{ $step['n'] }}</span>
            </div>
            <div style="font-weight:600;font-size:15px;margin-bottom:8px;color:var(--black);">{{ $step['title'] }}</div>
            <div style="font-size:13.5px;color:var(--muted);line-height:1.7;max-width:240px;margin:0 auto;">{{ $step['desc'] }}</div>
        @endforeach
    </div>

{{-- ── LIBRARY AT A GLANCE ── --}}
<div class="reveal" style="margin-bottom:48px;">
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:32px;">
        <h2 style="margin:0;font-size:22px;font-weight:600;color:var(--black);">Library at a Glance</h2>
        <div style="flex:1;height:1px;background:linear-gradient(to right, var(--border), transparent);"></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;">
        <div style="padding:28px 32px;border-radius:20px;background:linear-gradient(135deg, var(--glass-bg) 0%, var(--off) 100%);border:1px solid var(--glass-border);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:var(--bg-mesh-1);opacity:.5;filter:blur(30px);"></div>
            <div style="position:relative;z-index:1;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <span style="font-size:11px;color:var(--muted);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:.08em;">Community</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div style="font-family:var(--font-disp);font-size:42px;color:var(--black);line-height:1;margin-bottom:8px;">{{ number_format($stats['total_users']) }}</div>
                <div style="font-size:13px;color:var(--muted);">Total registered readers</div>
                <div style="margin-top:16px;height:6px;border-radius:99px;background:var(--mid);overflow:hidden;">
                    <div style="width:{{ min(100, ($stats['active_subscribers'] / max($stats['total_users'],1)) * 100) }}%;height:100%;border-radius:99px;background:linear-gradient(90deg, var(--accent), #8b5cf6);"></div>
                <div style="margin-top:8px;font-size:11px;color:var(--muted);font-family:var(--font-mono);">{{ $stats['active_subscribers'] }} active subscribers</div>
        </div>
        <div style="padding:28px 32px;border-radius:20px;background:linear-gradient(135deg, var(--glass-bg) 0%, var(--off) 100%);border:1px solid var(--glass-border);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:var(--bg-mesh-2);opacity:.5;filter:blur(30px);"></div>
            <div style="position:relative;z-index:1;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <span style="font-size:11px;color:var(--muted);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:.08em;">Activity</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div style="font-family:var(--font-disp);font-size:42px;color:var(--black);line-height:1;margin-bottom:8px;">{{ number_format($stats['total_borrows_all']) }}</div>
                <div style="font-size:13px;color:var(--muted);">Total borrows all-time</div>
                <div style="margin-top:16px;display:flex;gap:4px;align-items:flex-end;height:32px;">
                    @php $barHeights = [40, 65, 45, 80, 55, 70, 90, 60, 75, 50]; @endphp
                    @foreach($barHeights as $h)
                    <div style="flex:1;height:{{ $h }}%;border-radius:2px;background:linear-gradient(to top, #10b981, #34d399);opacity:.7;"></div>
                    @endforeach
                </div>
                <div style="margin-top:8px;font-size:11px;color:var(--muted);font-family:var(--font-mono);">{{ $stats['active_borrows'] }} currently active</div>
        </div>
        <div style="padding:28px 32px;border-radius:20px;background:linear-gradient(135deg, var(--glass-bg) 0%, var(--off) 100%);border:1px solid var(--glass-border);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:var(--bg-mesh-3);opacity:.5;filter:blur(30px);"></div>
            <div style="position:relative;z-index:1;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <span style="font-size:11px;color:var(--muted);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:.08em;">Engagement</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.8"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <div style="display:flex;gap:24px;">
                    <div>
                        <div style="font-family:var(--font-disp);font-size:32px;color:var(--black);line-height:1;">{{ number_format($stats['total_reviews']) }}</div>
                        <div style="font-size:12px;color:var(--muted);margin-top:4px;">Reviews</div>
                    <div>
                        <div style="font-family:var(--font-disp);font-size:32px;color:var(--black);line-height:1;">{{ number_format($stats['total_bookmarks']) }}</div>
                        <div style="font-size:12px;color:var(--muted);margin-top:4px;">Bookmarks</div>
                </div>
                <div style="margin-top:16px;display:flex;align-items:center;gap:12px;">
                    <svg width="48" height="48" viewBox="0 0 48 48">
                        <circle cx="24" cy="24" r="20" fill="none" stroke="var(--mid)" stroke-width="4"/>
                        <circle cx="24" cy="24" r="20" fill="none" stroke="#f59e0b" stroke-width="4" stroke-linecap="round" stroke-dasharray="{{ min(125, ($stats['total_reservations'] / max($stats['total_borrows_all'],1)) * 125) }} 125" transform="rotate(-90 24 24)"/>
                    </svg>
                    <div>
                        <div style="font-size:13px;color:var(--black);font-weight:500;">{{ $stats['total_reservations'] }} reservations</div>
                        <div style="font-size:11px;color:var(--muted);">When books are in demand</div>
                </div>
        </div>
        <div style="padding:28px 32px;border-radius:20px;background:linear-gradient(135deg, var(--glass-bg) 0%, var(--off) 100%);border:1px solid var(--glass-border);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:rgba(236,72,153,0.15);opacity:.5;filter:blur(30px);"></div>
            <div style="position:relative;z-index:1;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <span style="font-size:11px;color:var(--muted);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:.08em;">Growth</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="1.8"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
                <div style="font-family:var(--font-disp);font-size:42px;color:var(--black);line-height:1;margin-bottom:8px;">+{{ $stats['books_this_month'] }}</div>
                <div style="font-size:13px;color:var(--muted);">Books added this month</div>
                <div style="margin-top:16px;padding:12px 16px;border-radius:12px;background:rgba(236,72,153,0.06);border:1px solid rgba(236,72,153,0.12);">
                    <div style="font-size:12px;color:var(--muted);line-height:1.6;">Our collection keeps growing. Readers have submitted <strong style="color:var(--black);">{{ $stats['books_this_month'] > 0 ? $stats['books_this_month'] : 'many' }}</strong> new titles this month.</div>
            </div>
    </div>

{{-- ── MEMBERSHIP TIERS ── --}}
@auth
@if(auth()->user()->isSubscribed())
<div class="reveal" style="margin-bottom:32px;">
    <div style="background:linear-gradient(135deg,#111827 0%,#1a2744 100%);border-radius:20px;padding:36px 40px;position:relative;overflow:hidden;border:1px solid rgba(107,138,255,0.25);box-shadow:0 12px 40px rgba(79,110,247,0.18);">
        <div style="position:absolute;top:-50px;right:-50px;width:180px;height:180px;border-radius:50%;border:1px solid rgba(255,255,255,.06);pointer-events:none;"></div>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;">
            <div>
                <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(107,138,255,.15);border:1px solid rgba(107,138,255,.3);padding:4px 14px;border-radius:999px;margin-bottom:14px;">
                    <span style="color:#6b8aff;font-size:14px;">✦</span>
                    <span style="font-size:11px;font-family:var(--font-mono);letter-spacing:.08em;color:rgba(255,255,255,.7);text-transform:uppercase;">Active Subscriber</span>
                </div>
                <div style="font-size:24px;font-weight:600;color:#ffffff;margin-bottom:6px;">You have full access</div>
                <div style="font-size:14px;color:rgba(255,255,255,.62);line-height:1.6;">Borrow up to 25 books · Publish up to 50 books · ✦ badge on your profile</div>
            <a href="{{ route('subscription.index') }}" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);color:#fff;font-size:14px;font-weight:500;padding:12px 24px;border-radius:10px;border:1px solid rgba(255,255,255,.2);text-decoration:none;white-space:nowrap;flex-shrink:0;">
                Manage Subscription
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
</div>
@else
<div class="reveal" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(270px,1fr));gap:20px;margin-bottom:32px;">
    <div class="card" style="padding:32px 36px;">
        <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:.12em;color:var(--muted);text-transform:uppercase;margin-bottom:14px;">Free</div>
        <div style="font-size:32px;font-weight:600;margin-bottom:4px;color:var(--black);">₱0<span style="font-size:15px;font-weight:400;color:var(--muted);"> / forever</span></div>
        <div style="height:1px;background:var(--border);margin:22px 0;"></div>
        <ul style="list-style:none;padding:0;margin:0 0 26px;">
            @foreach(['Borrow up to 5 books at once','10-day loan period','Submit 2 books/day','Full catalogue access','Ratings & reviews'] as $f)
            <li style="display:flex;align-items:center;gap:10px;font-size:14px;color:var(--black);padding:6px 0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $f }}
            </li>
            @endforeach
        </ul>
        <a href="{{ route('register') }}" class="btn-outline" style="display:block;text-align:center;padding:12px;border-radius:10px;font-size:14px;text-decoration:none;font-weight:500;">Get Started Free</a>
    </div>
    <div style="background:linear-gradient(135deg,#111827 0%,#0b1220 100%);border-radius:20px;padding:32px 36px;position:relative;overflow:hidden;border:1px solid rgba(107,138,255,.2);box-shadow:0 12px 40px rgba(79,110,247,0.15);">
        <div style="position:absolute;top:-40px;right:-40px;width:130px;height:130px;border-radius:50%;border:1px solid rgba(255,255,255,.06);pointer-events:none;"></div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:.12em;color:rgba(255,255,255,.55);text-transform:uppercase;">Subscriber</div>
            <span style="background:rgba(107,138,255,.2);border:1px solid rgba(107,138,255,.35);color:#6b8aff;font-size:10px;font-family:var(--font-mono);padding:3px 9px;border-radius:5px;letter-spacing:.06em;">✦ PRO</span>
        </div>
        <div style="font-size:32px;font-weight:600;margin-bottom:4px;color:#ffffff;">₱99<span style="font-size:15px;font-weight:400;color:rgba(255,255,255,.62);"> / month</span></div>
        <div style="font-size:12px;color:rgba(255,255,255,.5);margin-bottom:0;font-family:var(--font-mono);">or ₱999/year (save ₱189)</div>
        <div style="height:1px;background:rgba(255,255,255,.12);margin:22px 0;"></div>
        <ul style="list-style:none;padding:0;margin:0 0 26px;">
            @foreach(['Borrow up to 25 books at once','Publish up to 50 books','✦ badge on your profile','Priority support','Read books online','Unlimited submissions'] as $f)
            <li style="display:flex;align-items:center;gap:10px;font-size:14px;color:rgba(255,255,255,.84);padding:6px 0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(107,138,255,.8)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $f }}
            </li>
            @endforeach
        </ul>
        <a href="{{ route('subscription.index') }}" style="display:block;text-align:center;padding:12px;border-radius:10px;font-size:14px;text-decoration:none;font-weight:600;background:#ffffff;color:#0f172a;">Subscribe Now</a>
    </div>
@endif
@else
<div class="reveal" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(270px,1fr));gap:20px;margin-bottom:32px;">
    <div class="card" style="padding:32px 36px;">
        <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:.12em;color:var(--muted);text-transform:uppercase;margin-bottom:14px;">Free</div>
        <div style="font-size:32px;font-weight:600;margin-bottom:4px;color:var(--black);">₱0<span style="font-size:15px;font-weight:400;color:var(--muted);"> / forever</span></div>
        <div style="height:1px;background:var(--border);margin:22px 0;"></div>
        <ul style="list-style:none;padding:0;margin:0 0 26px;">
            @foreach(['Borrow up to 5 books at once','10-day loan period','Submit 2 books/day','Full catalogue access','Ratings & reviews'] as $f)
            <li style="display:flex;align-items:center;gap:10px;font-size:14px;color:var(--black);padding:6px 0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $f }}
            </li>
            @endforeach
        </ul>
        <a href="{{ route('register') }}" class="btn-outline" style="display:block;text-align:center;padding:12px;border-radius:10px;font-size:14px;text-decoration:none;font-weight:500;">Get Started Free</a>
    </div>
    <div style="background:linear-gradient(135deg,#111827 0%,#0b1220 100%);border-radius:20px;padding:32px 36px;position:relative;overflow:hidden;border:1px solid rgba(107,138,255,.2);box-shadow:0 12px 40px rgba(79,110,247,0.15);">
        <div style="position:absolute;top:-40px;right:-40px;width:130px;height:130px;border-radius:50%;border:1px solid rgba(255,255,255,.06);pointer-events:none;"></div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:.12em;color:rgba(255,255,255,.55);text-transform:uppercase;">Subscriber</div>
            <span style="background:rgba(107,138,255,.2);border:1px solid rgba(107,138,255,.35);color:#6b8aff;font-size:10px;font-family:var(--font-mono);padding:3px 9px;border-radius:5px;letter-spacing:.06em;">✦ PRO</span>
        </div>
        <div style="font-size:32px;font-weight:600;margin-bottom:4px;color:#ffffff;">₱99<span style="font-size:15px;font-weight:400;color:rgba(255,255,255,.62);"> / month</span></div>
        <div style="font-size:12px;color:rgba(255,255,255,.5);margin-bottom:0;font-family:var(--font-mono);">or ₱999/year (save ₱189)</div>
        <div style="height:1px;background:rgba(255,255,255,.12);margin:22px 0;"></div>
        <ul style="list-style:none;padding:0;margin:0 0 26px;">
            @foreach(['Borrow up to 25 books at once','Publish up to 50 books','✦ badge on your profile','Priority support','Read books online','Unlimited submissions'] as $f)
            <li style="display:flex;align-items:center;gap:10px;font-size:14px;color:rgba(255,255,255,.84);padding:6px 0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(107,138,255,.8)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $f }}
            </li>
            @endforeach
        </ul>
        <a href="{{ route('subscription.index') }}" style="display:block;text-align:center;padding:12px;border-radius:10px;font-size:14px;text-decoration:none;font-weight:600;background:#ffffff;color:#0f172a;">Subscribe Now</a>
    </div>
@endauth

{{-- ── BROWSE BOOKS ── --}}
<div class="reveal" style="padding:4px 0;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <h2 style="margin:0;font-size
