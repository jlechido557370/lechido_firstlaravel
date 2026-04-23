@extends('layouts.app')
@section('title', 'Home — dotLibrary')

@section('content')

{{-- ── HERO BANNER ── --}}
<div class="hero-glass" style="
    border-radius: 20px;
    padding: 52px 48px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(148,163,184,.18);
    animation: heroFadeIn .6s ease both;
">
    {{-- Decorative rings --}}
    <div style="position:absolute;top:-80px;right:-80px;width:300px;height:300px;border-radius:50%;border:1px solid rgba(255,255,255,.04);pointer-events:none;"></div>
    <div style="position:absolute;top:-40px;right:-40px;width:180px;height:180px;border-radius:50%;border:1px solid rgba(255,255,255,.06);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-60px;left:200px;width:200px;height:200px;border-radius:50%;border:1px solid rgba(255,255,255,.03);pointer-events:none;"></div>

    <div style="position:relative;z-index:1;max-width:680px;">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);padding:6px 14px;border-radius:999px;margin-bottom:20px;">
            <span style="width:7px;height:7px;border-radius:50%;background:#4ade80;display:inline-block;box-shadow:0 0 6px #4ade80;animation:pulse 2s infinite;"></span>
            <span style="font-size:11px;font-family:var(--font-mono);letter-spacing:.08em;color:rgba(255,255,255,.72);text-transform:uppercase;">Open · Davao City</span>
        </div>

        <h1 style="font-family:var(--font-disp);font-size:clamp(42px,6.5vw,72px);color:#ffffff;line-height:.95;letter-spacing:.01em;margin-bottom:18px;font-weight:400;">
            .Library
        </h1>
        <p style="font-size:16px;color:rgba(255,255,255,.72);line-height:1.75;margin-bottom:28px;max-width:520px;">
            A modern digital library for Davao City. Browse thousands of titles, borrow books instantly, and manage your reading life — all in one place.
        </p>

        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('books.catalogue') }}" style="display:inline-flex;align-items:center;gap:8px;background:#ffffff;color:#0f172a;font-size:13.5px;font-weight:600;padding:11px 22px;border-radius:8px;text-decoration:none;transition:opacity .15s;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                Browse Catalogue
            </a>
            @guest
            <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.9);font-size:13.5px;padding:11px 22px;border-radius:8px;border:1px solid rgba(255,255,255,.18);text-decoration:none;transition:opacity .15s;">
                Create Free Account
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
            @endguest
            @auth
            <a href="{{ route('user.dashboard') }}" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.9);font-size:13.5px;padding:11px 22px;border-radius:8px;border:1px solid rgba(255,255,255,.18);text-decoration:none;transition:opacity .15s;">
                My Dashboard
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
            @endauth
        </div>
    </div>

    {{-- Hours badge --}}
    <div style="position:absolute;right:48px;bottom:36px;text-align:right;display:none;" class="hero-hours">
        <div style="font-size:10px;font-family:var(--font-mono);letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:4px;">Library Hours</div>
        <div style="font-size:14px;color:rgba(255,255,255,.72);">Mon – Fri &nbsp; 8am – 8pm</div>
        <div style="font-size:12px;color:rgba(255,255,255,.5);margin-top:3px;font-family:var(--font-mono);">Bolton St, Poblacion, Davao</div>
    </div>
</div>

{{-- ── STATS ROW ── --}}
<div class="grid grid-4" style="margin-bottom:24px;">
    @php $statItems = [
        ['label'=>'Total Books','value'=>$stats['total_books'],'icon'=>'<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>'],
        ['label'=>'Available Copies','value'=>$stats['available_books'],'icon'=>'<polyline points="20 6 9 17 4 12"/>'],
        ['label'=>'Active Borrows','value'=>$stats['active_borrows'],'icon'=>'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
        ['label'=>'Members','value'=>$stats['members'],'icon'=>'<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
    ]; @endphp
    @foreach($statItems as $i => $st)
    <div class="card stat-card reveal" style="animation-delay:{{ $i * 0.08 }}s; padding:24px 28px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <div style="font-size:12px;color:var(--muted);font-family:var(--font-mono);letter-spacing:.05em;text-transform:uppercase;">{{ $st['label'] }}</div>
            <div style="width:32px;height:32px;border:1px solid var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.8">{!! $st['icon'] !!}</svg>
            </div>
        </div>
        <div class="stats">{{ number_format($st['value']) }}</div>
    </div>
    @endforeach
</div>

{{-- ── HOW IT WORKS ── --}}
<div class="card stat-card reveal" style="margin-bottom:24px;padding:36px 40px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:32px;">
        <h2 style="margin:0;font-size:18px;">How It Works</h2>
        <span style="font-size:12px;color:var(--muted);font-family:var(--font-mono);">Free to join · No credit card</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:28px;">
        @php $steps = [
            ['n'=>'01','title'=>'Create an Account','desc'=>'Sign up for free in seconds. No credit card required to browse or register.','icon'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>'],
            ['n'=>'02','title'=>'Browse & Discover','desc'=>'Search our full catalogue by title, author, genre, or ISBN. Filter by availability.','icon'=>'<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>'],
            ['n'=>'03','title'=>'Borrow Instantly','desc'=>'Free users borrow up to 5 books for 10 days. Subscribers get up to 25 books.','icon'=>'<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>'],
            ['n'=>'04','title'=>'Read or Return','desc'=>'Read books online with a subscription, or return them to borrow new ones.','icon'=>'<polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>'],
        ]; @endphp
        @foreach($steps as $step)
        <div class="how-step" style="position:relative;">
            <div style="width:40px;height:40px;border:1.5px solid var(--border);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;transition:border-color .2s,background .2s;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--black)" stroke-width="1.8">{!! $step['icon'] !!}</svg>
            </div>
            <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:.12em;color:var(--muted);margin-bottom:6px;">{{ $step['n'] }}</div>
            <div style="font-weight:600;font-size:14px;margin-bottom:7px;color:var(--black);">{{ $step['title'] }}</div>
            <div style="font-size:13px;color:var(--muted);line-height:1.7;">{{ $step['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── MEMBERSHIP TIERS ── --}}
@auth
@if(auth()->user()->isSubscribed())
{{-- Subscribed user: show manage subscription banner --}}
<div class="reveal" style="margin-bottom:24px;">
    <div style="background:linear-gradient(135deg,#111827 0%,#1a2744 100%);border-radius:16px;padding:32px 36px;position:relative;overflow:hidden;border:1px solid rgba(107,138,255,0.25);box-shadow:0 8px 32px rgba(79,110,247,0.18);">
        <div style="position:absolute;top:-50px;right:-50px;width:180px;height:180px;border-radius:50%;border:1px solid rgba(255,255,255,.06);pointer-events:none;"></div>
        <div style="position:absolute;top:-20px;right:-20px;width:110px;height:110px;border-radius:50%;border:1px solid rgba(107,138,255,.12);pointer-events:none;"></div>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;">
            <div>
                <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(107,138,255,.15);border:1px solid rgba(107,138,255,.3);padding:4px 12px;border-radius:999px;margin-bottom:14px;">
                    <span style="color:#6b8aff;font-size:14px;">✦</span>
                    <span style="font-size:11px;font-family:var(--font-mono);letter-spacing:.08em;color:rgba(255,255,255,.7);text-transform:uppercase;">Active Subscriber</span>
                </div>
                <div style="font-size:22px;font-weight:600;color:#ffffff;margin-bottom:6px;">You have full access</div>
                <div style="font-size:14px;color:rgba(255,255,255,.62);line-height:1.6;">Borrow up to 25 books · Publish up to 50 books · ✦ badge on your profile</div>
            </div>
            <a href="{{ route('subscription.index') }}" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);color:#fff;font-size:14px;font-weight:500;padding:12px 22px;border-radius:10px;border:1px solid rgba(255,255,255,.2);text-decoration:none;transition:background .2s;white-space:nowrap;flex-shrink:0;">
                Manage Subscription
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</div>
@else
{{-- Non-subscribed or guest: show pricing tiers --}}
<div class="reveal" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(270px,1fr));gap:18px;margin-bottom:24px;">
    {{-- Free --}}
    <div class="card" style="padding:30px 34px;">
        <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:.12em;color:var(--muted);text-transform:uppercase;margin-bottom:14px;">Free</div>
        <div style="font-size:30px;font-weight:600;margin-bottom:4px;color:var(--black);">₱0<span style="font-size:15px;font-weight:400;color:var(--muted);"> / forever</span></div>
        <div style="height:1px;background:var(--border);margin:20px 0;"></div>
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
    {{-- Subscriber --}}
    <div style="background:linear-gradient(135deg,#111827 0%,#0b1220 100%);border-radius:16px;padding:30px 34px;position:relative;overflow:hidden;border:1px solid rgba(107,138,255,.2);box-shadow:0 8px 32px rgba(79,110,247,0.15);">
        <div style="position:absolute;top:-40px;right:-40px;width:130px;height:130px;border-radius:50%;border:1px solid rgba(255,255,255,.06);pointer-events:none;"></div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:.12em;color:rgba(255,255,255,.55);text-transform:uppercase;">Subscriber</div>
            <span style="background:rgba(107,138,255,.2);border:1px solid rgba(107,138,255,.35);color:#6b8aff;font-size:10px;font-family:var(--font-mono);padding:3px 9px;border-radius:5px;letter-spacing:.06em;">✦ PRO</span>
        </div>
        <div style="font-size:30px;font-weight:600;margin-bottom:4px;color:#ffffff;">₱99<span style="font-size:15px;font-weight:400;color:rgba(255,255,255,.62);"> / month</span></div>
        <div style="font-size:12px;color:rgba(255,255,255,.5);margin-bottom:0;font-family:var(--font-mono);">or ₱999/year (save ₱189)</div>
        <div style="height:1px;background:rgba(255,255,255,.12);margin:20px 0;"></div>
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
</div>
@endif
@else
{{-- Guest: show both tiers --}}
<div class="reveal" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(270px,1fr));gap:18px;margin-bottom:24px;">
    {{-- Free --}}
    <div class="card" style="padding:30px 34px;">
        <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:.12em;color:var(--muted);text-transform:uppercase;margin-bottom:14px;">Free</div>
        <div style="font-size:30px;font-weight:600;margin-bottom:4px;color:var(--black);">₱0<span style="font-size:15px;font-weight:400;color:var(--muted);"> / forever</span></div>
        <div style="height:1px;background:var(--border);margin:20px 0;"></div>
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
    {{-- Subscriber --}}
    <div style="background:linear-gradient(135deg,#111827 0%,#0b1220 100%);border-radius:16px;padding:30px 34px;position:relative;overflow:hidden;border:1px solid rgba(107,138,255,.2);box-shadow:0 8px 32px rgba(79,110,247,0.15);">
        <div style="position:absolute;top:-40px;right:-40px;width:130px;height:130px;border-radius:50%;border:1px solid rgba(255,255,255,.06);pointer-events:none;"></div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:.12em;color:rgba(255,255,255,.55);text-transform:uppercase;">Subscriber</div>
            <span style="background:rgba(107,138,255,.2);border:1px solid rgba(107,138,255,.35);color:#6b8aff;font-size:10px;font-family:var(--font-mono);padding:3px 9px;border-radius:5px;letter-spacing:.06em;">✦ PRO</span>
        </div>
        <div style="font-size:30px;font-weight:600;margin-bottom:4px;color:#ffffff;">₱99<span style="font-size:15px;font-weight:400;color:rgba(255,255,255,.62);"> / month</span></div>
        <div style="font-size:12px;color:rgba(255,255,255,.5);margin-bottom:0;font-family:var(--font-mono);">or ₱999/year (save ₱189)</div>
        <div style="height:1px;background:rgba(255,255,255,.12);margin:20px 0;"></div>
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
</div>
@endauth

{{-- ── BROWSE BOOKS ── --}}
<div class="reveal" style="padding:4px 0;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <h2 style="margin:0;font-size:20px;font-weight:600;color:var(--black);">Browse Books</h2>
        <a href="{{ route('books.catalogue') }}" style="font-size:13px;color:var(--muted);display:flex;align-items:center;gap:5px;transition:color .15s;" onmouseover="this.style.color='var(--black)'" onmouseout="this.style.color='var(--muted)'">
            Full Catalogue
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
    </div>

    {{-- FILTERS --}}
    <style>
        /* Custom select with chevron - no blue outlines */
        .browse-select-wrap {
            position: relative;
            flex: 1;
            min-width: 140px;
        }
        .browse-select-wrap select {
            width: 100%;
            padding-right: 36px;
            padding-left: 13px;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            background: var(--off);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            color: var(--black);
            height: 42px;
            transition: border-color .18s, background .18s;
            outline: none !important;
            box-shadow: none !important;
        }
        .browse-select-wrap select:hover {
            border-color: var(--muted);
            background: var(--white);
        }
        .browse-select-wrap select:focus {
            border-color: var(--black);
            background: var(--white);
            outline: none !important;
            box-shadow: none !important;
        }
        .browse-select-wrap .select-chevron {
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: var(--muted);
            display: flex;
            align-items: center;
            transition: transform .2s ease, color .15s;
        }
        .browse-select-wrap select:focus + .select-chevron,
        .browse-select-wrap:hover .select-chevron {
            color: var(--black);
        }
        .browse-select-wrap select:focus + .select-chevron {
            transform: translateY(-50%) rotate(180deg);
        }
        .browse-filters-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin-bottom: 18px;
        }
        .browse-search-wrap {
            flex: 2;
            min-width: 200px;
        }
        .browse-search-wrap input {
            height: 42px;
            border-radius: 10px;
            border: 1.5px solid var(--border);
            background: var(--off);
            transition: border-color .18s, background .18s;
            font-size: 14px;
            outline: none !important;
        }
        .browse-search-wrap input:hover { border-color: var(--muted); background: var(--white); }
        .browse-search-wrap input:focus { 
            border-color: var(--black); 
            background: var(--white); 
            outline: none !important; 
            box-shadow: none !important; 
        }
    </style>
    <form method="GET" action="{{ route('home') }}" data-autofilter>
        <div class="browse-filters-row">
            <div class="browse-search-wrap">
                <input type="text" name="search"
                    placeholder="Search title, author, genre, ISBN…"
                    value="{{ request('search') }}"
                    onchange="this.form.submit()">
            </div>

            <div class="browse-select-wrap">
                <select name="genre">
                    <option value="">All Genres</option>
                    @foreach($genres as $g)
                        <option value="{{ $g }}" {{ request('genre') === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
                <span class="select-chevron">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </div>

            <div class="browse-select-wrap">
                <select name="availability">
                    <option value="">Any Availability</option>
                    <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Available Now</option>
                    <option value="unavailable" {{ request('availability') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
                <span class="select-chevron">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </div>

            <div class="browse-select-wrap">
                <select name="sort">
                    <option value="latest" {{ request('sort','latest') === 'latest' ? 'selected' : '' }}>Newest Added</option>
                    <option value="year_desc" {{ request('sort') === 'year_desc' ? 'selected' : '' }}>Year: Newest First</option>
                    <option value="year_asc" {{ request('sort') === 'year_asc' ? 'selected' : '' }}>Year: Oldest First</option>
                    <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Title: A–Z</option>
                    <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>Title: Z–A</option>
                </select>
                <span class="select-chevron">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </div>

            @if(request('search') || request('genre') || request('availability') || (request('sort') && request('sort') !== 'latest'))
                <a href="{{ route('home') }}" style="padding:10px 14px; color:var(--muted); white-space:nowrap; border:1.5px solid var(--border); border-radius:10px; font-size:13px; transition:border-color .15s, color .15s;" onmouseover="this.style.borderColor='var(--black)';this.style.color='var(--black)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">Clear</a>
            @endif
        </div>
    </form>

    <p class="muted" style="margin-bottom:16px;font-size:13px;">
        Showing {{ $books->count() }} book(s)
        @if(request('genre')) in <strong>{{ request('genre') }}</strong>@endif
        @if(request('search')) matching "<strong>{{ request('search') }}</strong>"@endif
    </p>

    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:18px;">
        @forelse ($books as $book)
            <a href="{{ route('books.show', ['book' => $book->id, 'back' => request()->fullUrl()]) }}" class="book-card book-card-anim">
                <img src="{{ $book->coverUrl() }}"
                     alt="{{ $book->title }}"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='{{ $book->noImageSvg() }}'">
                <div class="book-card-body">
                    <div class="book-card-title">{{ $book->title }}</div>
                    <div class="book-card-author">{{ $book->author }}</div>
                    <div class="book-card-badges">
                        @if($book->genre)
                            <span class="badge" style="font-size:11px;">{{ $book->genre }}</span>
                        @endif
                        <span class="badge {{ $book->status === 'available' ? 'badge-green' : 'badge-red' }}" style="font-size:11px;">{{ ucfirst($book->status) }}</span>
                    </div>
                </div>
            </a>
        @empty
            <p style="grid-column:1/-1;color:var(--muted);">No books found.</p>
        @endforelse
    </div>
</div>

@push('scripts')
<style>
@keyframes heroFadeIn { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
@keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(74,222,128,.5);} 50%{box-shadow:0 0 0 8px rgba(74,222,128,0);} }

.how-step:hover > div:first-child {
    border-color: var(--black) !important;
    background: var(--off);
}
.how-step { transition: transform .2s ease; }
.how-step:hover { transform: translateY(-3px); }

.book-card-anim {
    opacity: 0;
    animation: fadeBookIn .45s ease forwards;
}
@keyframes fadeBookIn { from { opacity:0; transform:translateY(14px) scale(.98); } to { opacity:1; transform:translateY(0) scale(1); } }

@media (min-width:900px) {
    .hero-hours { display:block !important; }
}

/* Hero glass sheen */
.hero-glass {
    background: linear-gradient(135deg, rgba(15,23,42,0.92) 0%, rgba(11,18,32,0.96) 100%);
    border-radius: 20px;
    border: 1px solid rgba(107,138,255,0.15);
    box-shadow: 0 20px 60px rgba(0,0,0,0.22), inset 0 1px 0 rgba(255,255,255,0.06);
}

/* Stat cards hover */
.stat-card:hover {
    box-shadow: var(--glass-shadow-lg) !important;
    transform: translateY(-3px);
}
.stat-card { transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease; }
</style>
@endpush
@endsection