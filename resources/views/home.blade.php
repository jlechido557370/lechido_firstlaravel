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
    animation: heroFadeIn .35s ease both;
">
    <div style="position:relative;z-index:1;max-width:680px;">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);padding:6px 14px;border-radius:999px;margin-bottom:20px;">
            <span style="width:7px;height:7px;border-radius:50%;background:#4ade80;display:inline-block;"></span>
            <span style="font-size:11px;font-family:var(--font-mono);letter-spacing:.08em;color:rgba(255,255,255,.72);text-transform:uppercase;">Open · Davao City</span>
        </div>

        <h1 style="font-family:var(--font-disp);font-size:clamp(42px,6.5vw,72px);color:#ffffff;line-height:.95;letter-spacing:.01em;margin-bottom:18px;font-weight:400;">
            .Library
        </h1>
        <p style="font-size:16px;color:rgba(255,255,255,.72);line-height:1.75;margin-bottom:28px;max-width:520px;">
            A modern digital library for readers everywhere. Browse thousands of titles, borrow books instantly, and manage your reading life — all in one place.
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

{{-- ── MEMBERSHIP TIERS ── --}}
@auth
@if(auth()->user()->isSubscribed())
{{-- Subscribed user: show manage subscription banner --}}
<div class="reveal" style="margin-bottom:24px;">
    <div style="background:linear-gradient(135deg,#111827 0%,#1a2744 100%);border-radius:16px;padding:32px 36px;position:relative;overflow:hidden;border:1px solid rgba(107,138,255,0.25);box-shadow:0 8px 32px rgba(79,110,247,0.18);">
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
{{-- Non-subscribed: show pricing tiers --}}
<div class="reveal tier-grid">
    {{-- Free --}}
    <div class="tier-card tier-card-free">
        <div class="tier-label">Free</div>
        <div class="tier-price">₱0<span> / forever</span></div>
        <div class="tier-divider"></div>
        <ul>
            @foreach(['Borrow up to 5 books at once','10-day loan period','Submit 2 books/day','Full catalogue access','Ratings & reviews'] as $f)
            <li class="tier-feature">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $f }}
            </li>
            @endforeach
        </ul>
        <a href="{{ route('register') }}" class="tier-btn">Get Started Free</a>
    </div>
    {{-- Subscriber --}}
    <div class="tier-card tier-card-pro">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div class="tier-label">Subscriber</div>
            <span style="background:rgba(107,138,255,.2);border:1px solid rgba(107,138,255,.35);color:#6b8aff;font-size:10px;font-family:var(--font-mono);padding:3px 9px;border-radius:5px;letter-spacing:.06em;">✦ PRO</span>
        </div>
        <div class="tier-price">₱99<span> / month</span></div>
        <div class="tier-divider"></div>
        <ul>
            @foreach(['Borrow up to 25 books at once','Publish up to 50 books','✦ badge on your profile','Priority support','Read books online','Unlimited submissions'] as $f)
            <li class="tier-feature">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(107,138,255,.8)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $f }}
            </li>
            @endforeach
        </ul>
        <a href="{{ route('subscription.index') }}" class="tier-btn">Subscribe Now</a>
    </div>
</div>
@endif
@endauth
@guest
{{-- Guest: show both pricing tiers --}}
<div class="reveal tier-grid">
    {{-- Free --}}
    <div class="tier-card tier-card-free">
        <div class="tier-label">Free</div>
        <div class="tier-price">₱0<span> / forever</span></div>
        <div class="tier-divider"></div>
        <ul>
            @foreach(['Borrow up to 5 books at once','10-day loan period','Submit 2 books/day','Full catalogue access','Ratings & reviews'] as $f)
            <li class="tier-feature">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $f }}
            </li>
            @endforeach
        </ul>
        <a href="{{ route('register') }}" class="tier-btn">Get Started Free</a>
    </div>
    {{-- Subscriber --}}
    <div class="tier-card tier-card-pro">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div class="tier-label">Subscriber</div>
            <span style="background:rgba(107,138,255,.2);border:1px solid rgba(107,138,255,.35);color:#6b8aff;font-size:10px;font-family:var(--font-mono);padding:3px 9px;border-radius:5px;letter-spacing:.06em;">✦ PRO</span>
        </div>
        <div class="tier-price">₱99<span> / month</span></div>
        <div class="tier-divider"></div>
        <ul>
            @foreach(['Borrow up to 25 books at once','Publish up to 50 books','✦ badge on your profile','Priority support','Read books online','Unlimited submissions'] as $f)
            <li class="tier-feature">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(107,138,255,.8)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $f }}
            </li>
            @endforeach
        </ul>
        <a href="{{ route('subscription.index') }}" class="tier-btn">Subscribe Now</a>
    </div>
</div>
@endguest

{{-- Browse Books Filter Bar --}}
<div class="card reveal" style="padding:26px 30px;margin-bottom:24px;">
    <form id="browse-form" method="GET" action="{{ route('home') }}" style="display:flex;gap:14px;flex-wrap:wrap;align-items:end;">
        <div style="flex:1;min-width:200px;">
            <label style="font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;font-family:var(--font-mono);color:var(--muted);margin-bottom:7px;display:block;">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Title, author, ISBN…" style="font-size:15px;padding:11px 14px;height:44px;">
        </div>
        <div style="min-width:150px;">
            <label style="font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;font-family:var(--font-mono);color:var(--muted);margin-bottom:7px;display:block;">Genre</label>
            <select name="genre" class="select-arrow" style="font-size:15px;padding:11px 14px;height:44px;">
                <option value="">All Genres</option>
                @foreach($genres as $g)
                    <option value="{{ $g }}" {{ request('genre') == $g ? 'selected' : '' }}>{{ $g }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:150px;">
            <label style="font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;font-family:var(--font-mono);color:var(--muted);margin-bottom:7px;display:block;">Availability</label>
            <select name="availability" class="select-arrow" style="font-size:15px;padding:11px 14px;height:44px;">
                <option value="">All</option>
                <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
            </select>
        </div>
        <div style="min-width:160px;">
            <label style="font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;font-family:var(--font-mono);color:var(--muted);margin-bottom:7px;display:block;">Sort By</label>
            <select name="sort" class="select-arrow" style="font-size:15px;padding:11px 14px;height:44px;">
                <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Newest Added</option>
                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title A – Z</option>
                <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title Z – A</option>
                <option value="year_asc" {{ request('sort') == 'year_asc' ? 'selected' : '' }}>Year Ascending</option>
                <option value="year_desc" {{ request('sort') == 'year_desc' ? 'selected' : '' }}>Year Descending</option>
            </select>
        </div>
        <div style="display:flex;gap:10px;">
            <button type="submit" id="filter-btn" style="height:44px;padding:0 22px;font-size:14px;">Filter</button>
            <button type="button" id="reset-btn" class="btn-outline" style="height:44px;padding:0 18px;font-size:14px;display:flex;align-items:center;gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                Reset
            </button>
        </div>
    </form>
</div>

{{-- Browse Books section with AJAX container --}}
<div id="browse-results" class="reveal" style="padding:4px 0;">
    @include('partials.browse_results', ['books' => $books])
</div>

{{-- ── ABOUT & TECH STACK ── --}}
<div style="height:28px;"></div>

<div class="card reveal" style="padding:40px 44px; margin-bottom:24px;">
    <div style="font-size:10px; font-weight:600; letter-spacing:.14em; text-transform:uppercase; font-family:var(--font-mono); color:var(--muted); margin-bottom:16px;">About this project</div>
    <p style="font-size:16px; line-height:1.8; color:var(--black); margin-bottom:14px; max-width:720px;">
        .Library is a digital catalog and lending system designed to make it easier for people to find and borrow books. It connects physical library collections with the convenience of an online platform, so users can quickly check availability, reserve titles, and borrow them without having to go through multiple steps.
    </p>
    <p style="font-size:16px; line-height:1.8; color:var(--muted); max-width:720px;">
        It’s built mainly for students, researchers, and anyone who just wants a simpler way to access books. The goal is to keep things straightforward and no complicated forms or confusing processes, just an easy way to search for a book and get it when it’s available.
    </p>
</div>

<div class="reveal" style="margin-bottom:40px; padding:12px 0;">
    <div style="font-size:10px; font-weight:600; letter-spacing:.14em; text-transform:uppercase; font-family:var(--font-mono); color:var(--muted); margin-bottom:24px; text-align:center;">What powers this site</div>
    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:28px; max-width:520px; margin:0 auto 8px;">
        @php
        $techs = [
            ['name' => 'Laravel', 'color' => '#ff2d20', 'bg' => 'rgba(255,45,32,0.08)'],
            ['name' => 'PHP', 'color' => '#777bb4', 'bg' => 'rgba(119,123,180,0.10)'],
            ['name' => 'MySQL', 'color' => '#00758f', 'bg' => 'rgba(0,117,143,0.10)'],
            ['name' => 'HTML5', 'color' => '#e34c26', 'bg' => 'rgba(227,76,38,0.10)'],
            ['name' => 'CSS3', 'color' => '#264de4', 'bg' => 'rgba(38,77,228,0.10)'],
            ['name' => 'JavaScript', 'color' => '#f7df1e', 'bg' => 'rgba(247,223,30,0.12)'],
        ];
        @endphp
        @foreach($techs as $tech)
        <div style="text-align:center;">
            <div style="width:56px; height:56px; border-radius:12px; background:{{ $tech['bg'] }}; display:flex; align-items:center; justify-content:center; margin:0 auto 10px; font-family:var(--font-mono); font-size:14px; font-weight:700; color:{{ $tech['color'] }}; border:1px solid {{ $tech['color'] }}33;">
                {{ substr($tech['name'], 0, 2) }}
            </div>
            <div style="font-size:14px; font-weight:600; color:var(--black);">{{ $tech['name'] }}</div>
        </div>
        @endforeach
    </div>
</div>

@endsection

@push('scripts')
<style>
@keyframes heroFadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

.book-card-anim {
    opacity: 0;
    animation: fadeBookIn .3s ease forwards;
}
@keyframes fadeBookIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

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

/* Tier cards – equal sizing */
.tier-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
    gap: 18px;
    margin-bottom: 24px;
    align-items: stretch;
}
.tier-card {
    display: flex;
    flex-direction: column;
    padding: 30px 34px;
    border-radius: 18px;
    position: relative;
    overflow: hidden;
}
.tier-card > ul { flex: 1; margin: 0 0 26px; padding: 0; list-style: none; }
.tier-card > a, .tier-card > .tier-btn {
    margin-top: auto;
    display: block;
    text-align: center;
    padding: 12px;
    border-radius: 10px;
    font-size: 14px;
    text-decoration: none;
    font-weight: 500;
    transition: opacity .18s, transform .18s, box-shadow .18s;
}
.tier-card-pro {
    background: linear-gradient(135deg, #111827 0%, #0b1220 100%);
    border: 1px solid rgba(107,138,255,.2);
    box-shadow: 0 8px 32px rgba(79,110,247,0.15);
    color: #fff;
}
.tier-card-pro .tier-label { font-family: var(--font-mono); font-size: 10px; letter-spacing: .12em; color: rgba(255,255,255,.55); text-transform: uppercase; margin-bottom: 14px; }
.tier-card-pro .tier-price { font-size: 30px; font-weight: 600; margin-bottom: 4px; color: #ffffff; }
.tier-card-pro .tier-price span { font-size: 15px; font-weight: 400; color: rgba(255,255,255,.62); }
.tier-card-pro .tier-divider { height: 1px; background: rgba(255,255,255,.12); margin: 20px 0; }
.tier-card-pro .tier-feature { display: flex; align-items: center; gap: 10px; font-size: 14px; color: rgba(255,255,255,.84); padding: 6px 0; }
.tier-card-pro .tier-btn { background: #ffffff; color: #0f172a; font-weight: 600; }
.tier-card-pro .tier-btn:hover { opacity: .92; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(255,255,255,.15); }

.tier-card-free {
    background: var(--glass-bg);
    backdrop-filter: blur(16px) saturate(1.4);
    -webkit-backdrop-filter: blur(16px) saturate(1.4);
    border: 1px solid var(--glass-border);
    box-shadow: var(--glass-shadow);
}
.tier-card-free .tier-label { font-family: var(--font-mono); font-size: 10px; letter-spacing: .12em; color: var(--muted); text-transform: uppercase; margin-bottom: 14px; }
.tier-card-free .tier-price { font-size: 30px; font-weight: 600; margin-bottom: 4px; color: var(--black); }
.tier-card-free .tier-price span { font-size: 15px; font-weight: 400; color: var(--muted); }
.tier-card-free .tier-divider { height: 1px; background: var(--border); margin: 20px 0; }
.tier-card-free .tier-feature { display: flex; align-items: center; gap: 10px; font-size: 14px; color: var(--black); padding: 6px 0; }
.tier-card-free .tier-btn { background: transparent; color: var(--black); border: 1.5px solid var(--border); }
.tier-card-free .tier-btn:hover { border-color: var(--black); background: var(--off); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

/* Loading state */
#browse-results.loading { opacity: .55; pointer-events: none; transition: opacity .2s; }
</style>

<script>
(function() {
    var form = document.getElementById('browse-form');
    var results = document.getElementById('browse-results');
    var filterBtn = document.getElementById('filter-btn');
    var resetBtn = document.getElementById('reset-btn');

    if (!form || !results) return;

    function doFilter(reset) {
        if (reset) {
            form.querySelectorAll('input, select').forEach(function(el) {
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
                else el.value = '';
            });
        }

        var params = new URLSearchParams(new FormData(form));
        // Remove empty params for cleaner URLs
        for (var pair of Array.from(params.entries())) {
            if (!pair[1]) params.delete(pair[0]);
        }
        var qs = params.toString();
        var url = '{{ route("browse.ajax") }}' + (qs ? '?' + qs : '');

        results.classList.add('loading');
        if (filterBtn) filterBtn.classList.add('btn-loading');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                results.innerHTML = data.html;
                results.classList.remove('loading');
                if (filterBtn) filterBtn.classList.remove('btn-loading');

                // Re-trigger scroll reveal for new items
                var newCards = results.querySelectorAll('.book-card-anim');
                newCards.forEach(function(card, i) {
                    card.style.animationDelay = (i * 0.04) + 's';
                });

                // Update URL without reload
                var newUrl = '{{ route("home") }}' + (qs ? '?' + qs : '');
                history.pushState(null, '', newUrl);

                // Update image fade for new images
                results.querySelectorAll('img').forEach(function(img) {
                    img.classList.add('img-fade');
                    img.addEventListener('load', function() { this.classList.add('loaded'); });
                    if (img.complete) img.classList.add('loaded');
                });
            })
            .catch(function() {
                results.classList.remove('loading');
                if (filterBtn) filterBtn.classList.remove('btn-loading');
            });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        doFilter(false);
    });

    resetBtn.addEventListener('click', function() {
        doFilter(true);
    });

    // Auto-submit on select change for convenience
    form.querySelectorAll('select').forEach(function(sel) {
        sel.addEventListener('change', function() { doFilter(false); });
    });
})();
</script>
@endpush
