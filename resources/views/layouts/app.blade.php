 <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'dotLibrary')</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root,
        [data-theme="light"] {
            --bg-page:          #eef0f5;
            --black:            #111827;
            --white:            #ffffff;
            --glass-bg:         rgba(255,255,255,0.65);
            --glass-bg-strong:  rgba(255,255,255,0.88);
            --glass-border:     rgba(255,255,255,0.82);
            --glass-shadow:     0 8px 32px rgba(0,0,0,0.09), 0 1.5px 0 rgba(255,255,255,0.7) inset;
            --glass-shadow-lg:  0 20px 60px rgba(0,0,0,0.13), 0 1.5px 0 rgba(255,255,255,0.7) inset;
            --card-bg:          rgba(255,255,255,0.65);
            --card-grad:        rgba(255,255,255,0.65);
            --off:              rgba(245,246,250,0.9);
            --mid:              rgba(220,222,230,0.7);
            --muted:            #6b7280;
            --border:           rgba(210,214,224,0.75);
            --nav-bg:           rgba(255,255,255,0.72);
            --shadow-sm:        0 2px 8px rgba(0,0,0,0.06);
            --shadow-md:        0 8px 28px rgba(0,0,0,0.10);
            --shadow-lg:        0 20px 60px rgba(0,0,0,0.14);
            --accent:           #4f6ef7;
            --accent-soft:      rgba(79,110,247,0.09);
            --font-sans:        'Outfit', system-ui, sans-serif;
            --font-mono:        'DM Mono', monospace;
            --font-disp:        'Bebas Neue', sans-serif;
            --book-card-bg:     rgba(255,255,255,0.82);
            --book-card-border: rgba(220,222,230,0.65);
            --book-title-color: #111827;
            --radius:           16px;
            --radius-sm:        10px;
        }

        [data-theme="dark"] {
            --bg-page:          #0c0e14;
            --black:            #e8eaf0;
            --white:            #161a24;
            --glass-bg:         rgba(22,26,36,0.72);
            --glass-bg-strong:  rgba(28,32,44,0.90);
            --glass-border:     rgba(255,255,255,0.08);
            --glass-shadow:     0 8px 32px rgba(0,0,0,0.40), 0 1px 0 rgba(255,255,255,0.04) inset;
            --glass-shadow-lg:  0 20px 60px rgba(0,0,0,0.55), 0 1px 0 rgba(255,255,255,0.04) inset;
            --card-bg:          rgba(22,26,36,0.72);
            --card-grad:        rgba(22,26,36,0.72);
            --off:              rgba(28,32,44,0.85);
            --mid:              rgba(40,45,60,0.8);
            --muted:            #6b7280;
            --border:           rgba(60,68,90,0.70);
            --nav-bg:           rgba(12,14,20,0.84);
            --shadow-sm:        0 2px 8px rgba(0,0,0,0.3);
            --shadow-md:        0 8px 28px rgba(0,0,0,0.45);
            --shadow-lg:        0 20px 60px rgba(0,0,0,0.65);
            --accent:           #6b8aff;
            --accent-soft:      rgba(107,138,255,0.12);
            --book-card-bg:     rgba(22,26,36,0.75);
            --book-card-border: rgba(50,58,80,0.70);
            --book-title-color: #e8eaf0;
            --radius:           16px;
            --radius-sm:        10px;
        }

        html { font-size: 17px; -webkit-font-smoothing: antialiased; }
        body {
            font-family: var(--font-sans);
            background: var(--bg-page);
            color: var(--black);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a { color: inherit; text-decoration: none; }
        a:hover { opacity: .65; }

        .wrap { max-width: 1320px; margin: 0 auto; padding: 0 40px; position: relative; z-index: 1; }
        .page-body { flex: 1; }

        /* ── FLASH ── */
        .flash { padding: 13px 18px; font-size: 15px; border-left: 3px solid var(--black); margin-bottom: 20px; border-radius: 0 8px 8px 0; }
        .success { background: rgba(22,163,74,.1); border-color: #16a34a; color: #15803d; }
        .error   { background: rgba(220,38,38,.1); border-color: #dc2626; color: #b91c1c; }
        .warning { background: rgba(217,119,6,.1); border-color: #d97706; color: #b45309; }
        [data-theme="dark"] .success { color: #4ade80; }
        [data-theme="dark"] .error   { color: #f87171; }
        [data-theme="dark"] .warning { color: #fbbf24; }

        /* ── NAV ── */
        .nav {
            background: var(--nav-bg);
            backdrop-filter: blur(18px) saturate(1.6);
            -webkit-backdrop-filter: blur(18px) saturate(1.6);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 100;
            transition: background .25s, border-color .25s;
        }
        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            height: 68px;
        }

        .nav-brand { display: flex; align-items: baseline; gap: 1px; flex-shrink: 0; }
        .nav-brand-dot  { font-family: var(--font-disp); font-size: 26px; color: var(--black); letter-spacing: -.02em; line-height: 1; }
        .nav-brand-name { font-family: var(--font-disp); font-size: 26px; color: var(--black); letter-spacing: .04em; line-height: 1; transition: opacity .15s; }
        .nav-brand:hover { opacity: 1; }
        .nav-brand:hover .nav-brand-name { opacity: .65; }

        /* ── SEARCH BAR ── */
        .search-form {
            display: flex; align-items: stretch;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: var(--off);
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .search-form:focus-within {
            border-color: var(--black);
            background: var(--white);
            box-shadow: none;
        }
        [data-theme="dark"] .search-form:focus-within {
            box-shadow: 0 0 0 3px rgba(240,240,236,.06);
            background: var(--off);
        }
        .search-form input {
            background: transparent; border: none; outline: none;
            padding: 0 14px; font-size: 14.5px; height: 40px;
            font-family: var(--font-sans); flex: 1; width: 0; min-width: 0;
            color: var(--black);
        }
        .search-form input::placeholder { color: var(--muted); }
        .search-form button {
            background: var(--black); color: var(--white);
            border: none; padding: 0 18px; height: 40px;
            font-size: 12px; font-family: var(--font-mono);
            letter-spacing: .07em; cursor: pointer; text-transform: uppercase;
            flex-shrink: 0; border-radius: 0;
            transition: opacity .15s;
        }
        .search-form button:hover { opacity: .82; }

        /* ── NAV LINKS ── */
        .nav-links { display: flex; align-items: center; gap: 26px; font-size: 15px; }
        .nav-links > a {
            color: var(--muted); font-weight: 400;
            position: relative; transition: color .15s;
        }
        .nav-links > a::after {
            content: ''; position: absolute; bottom: -3px; left: 0;
            width: 0; height: 1.5px; background: var(--black);
            transition: width .2s ease;
        }
        .nav-links > a:hover { color: var(--black); opacity: 1; }
        .nav-links > a:hover::after { width: 100%; }

        /* ── ICON BUTTONS (no border boxes) ── */
        .nav-icon-btn { position: relative; display: inline-flex; }
        .nav-icon-btn > button {
            background: none; border: none;
            color: var(--muted); padding: 6px;
            cursor: pointer; display: flex;
            align-items: center; justify-content: center;
            border-radius: 8px;
            transition: color .15s, background .15s;
        }
        .nav-icon-btn > button:hover { color: var(--black); background: var(--accent-soft); opacity: 1; }
        .nav-icon-btn > button svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 1.8; }
        .nav-icon-badge {
            position: absolute; top: -4px; right: -4px;
            background: var(--black); color: var(--white);
            font-size: 9px; font-weight: 700; min-width: 17px; height: 17px;
            display: flex; align-items: center; justify-content: center;
            padding: 0 3px; pointer-events: none;
            font-family: var(--font-mono); border-radius: 999px;
        }

        /* ── ANIMATED DROPDOWNS ── */
        .icon-dropdown-panel {
            visibility: hidden; opacity: 0;
            transform: translateY(-10px) scale(0.97);
            position: absolute; right: 0; top: calc(100% + 14px);
            min-width: 320px; max-width: 360px;
            background: var(--white); border: 1px solid var(--border);
            box-shadow: var(--shadow-lg); z-index: 200;
            border-radius: 16px; overflow: hidden;
            transition: opacity .25s ease, transform .25s ease, visibility 0s linear .25s;
        }
        .icon-dropdown-panel.open {
            visibility: visible; opacity: 1;
            transform: translateY(0) scale(1);
            transition: opacity .25s ease, transform .25s ease, visibility 0s;
        }
        .icon-dropdown-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 13px 16px; border-bottom: 1px solid var(--border);
            font-size: 10px; font-weight: 700; letter-spacing: .10em;
            text-transform: uppercase; font-family: var(--font-mono); color: var(--muted);
            background: var(--off); position: sticky; top: 0; z-index: 1;
        }
        .icon-dropdown-header a { font-size: 12px; font-weight: 500; color: var(--black); letter-spacing: 0; text-transform: none; opacity: .6; transition: opacity .15s; }
        .icon-dropdown-header a:hover { opacity: 1; }

        /* Scrollable list container */
        .icon-dropdown-scroll {
            max-height: 340px;
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
        }
        .icon-dropdown-scroll::-webkit-scrollbar { width: 4px; }
        .icon-dropdown-scroll::-webkit-scrollbar-track { background: transparent; }
        .icon-dropdown-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
        .icon-dropdown-scroll::-webkit-scrollbar-thumb:hover { background: var(--muted); }

        /* Fade-in animation for each item when dropdown opens */
        @keyframes dropItemIn {
            from { opacity: 0; transform: translateX(-6px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .icon-dropdown-panel.open .icon-dropdown-item {
            animation: dropItemIn .22s ease both;
        }
        .icon-dropdown-panel.open .icon-dropdown-item:nth-child(1) { animation-delay: .04s; }
        .icon-dropdown-panel.open .icon-dropdown-item:nth-child(2) { animation-delay: .08s; }
        .icon-dropdown-panel.open .icon-dropdown-item:nth-child(3) { animation-delay: .12s; }
        .icon-dropdown-panel.open .icon-dropdown-item:nth-child(4) { animation-delay: .16s; }
        .icon-dropdown-panel.open .icon-dropdown-item:nth-child(5) { animation-delay: .20s; }

        .icon-dropdown-item {
            padding: 12px 16px; border-bottom: 1px solid var(--mid);
            font-size: 14.5px; line-height: 1.45; position: relative;
            transition: background .12s, padding-left .15s;
        }
        .icon-dropdown-item:last-child { border-bottom: none; }
        .icon-dropdown-item:hover { background: var(--off); }
        .icon-dropdown-item.unread { background: var(--accent-soft); border-left: 3px solid var(--accent); padding-left: 13px; }
        .icon-dropdown-item.unread:hover { background: var(--off); }
        .icon-dropdown-item a { color: var(--black); display: block; }
        .icon-dropdown-item a:hover { opacity: .7; }
        .icon-dropdown-empty { padding: 28px 16px; color: var(--muted); font-size: 14px; text-align: center; }
        .icon-dropdown-footer { padding: 12px 16px; border-top: 1px solid var(--border); text-align: center; font-size: 12px; background: var(--off); }
        .icon-dropdown-footer a { color: var(--black); font-weight: 500; opacity: .7; transition: opacity .15s; }
        .icon-dropdown-footer a:hover { opacity: 1; }

        /* Scroll shadow indicator */
        .icon-dropdown-scroll-wrap { position: relative; }
        .icon-dropdown-scroll-wrap::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 28px;
            background: linear-gradient(to top, var(--white), transparent);
            pointer-events: none;
            opacity: 0;
            transition: opacity .2s;
        }
        .icon-dropdown-scroll-wrap.has-scroll::after { opacity: 1; }

        /* ── PROFILE MENU ── */
        .profile-menu { position: relative; }
        .profile-toggle {
            display: inline-flex; align-items: center; gap: 8px;
            background: transparent; border: 1.5px solid var(--border);
            padding: 5px 12px 5px 5px; cursor: pointer;
            font-family: var(--font-sans); border-radius: 999px;
            transition: background .15s, border-color .15s;
        }
        .profile-toggle:hover { background: var(--off); border-color: var(--black); opacity: 1; }
        .profile-menu-name { font-size: 15px; color: var(--black); max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .avatar-sm { width: 28px; height: 28px; object-fit: cover; border: 1.5px solid var(--border); border-radius: 50%; }
        .profile-menu-panel {
            visibility: hidden; opacity: 0;
            transform: translateY(-8px) scale(0.98);
            position: absolute; right: 0; top: calc(100% + 12px);
            min-width: 240px; background: var(--white);
            border: 1px solid var(--border); box-shadow: var(--shadow-lg);
            overflow: hidden; z-index: 200; border-radius: 14px;
            transition: opacity .22s ease, transform .22s ease, visibility 0s linear .22s;
        }
        .profile-menu-panel.open {
            visibility: visible; opacity: 1;
            transform: translateY(0) scale(1);
            transition: opacity .22s ease, transform .22s ease, visibility 0s;
        }
        .profile-menu-panel a, .profile-menu-panel button {
            display: flex; align-items: center; gap: 10px;
            width: 100%; text-align: left; padding: 11px 18px;
            border: 0; border-bottom: 1px solid var(--mid);
            background: var(--white); color: var(--black);
            font-size: 15px; font-family: var(--font-sans);
            cursor: pointer; border-radius: 0; transition: background .12s;
        }
        .profile-menu-panel a:hover, .profile-menu-panel button:hover { background: var(--off); opacity: 1; text-decoration: none; }
        .profile-menu-panel svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 1.8; flex-shrink: 0; opacity: .5; }
        .profile-menu-panel .danger { color: #b91c1c; }
        [data-theme="dark"] .profile-menu-panel .danger { color: #f87171; }
        .pm-section-label { padding: 8px 18px 4px; font-size: 10px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; font-family: var(--font-mono); color: var(--muted); border-bottom: 1px solid var(--mid); background: var(--off); display: block; width: 100%; }
        .sub-link { background: var(--black) !important; color: var(--white) !important; font-weight: 500; }
        .sub-link:hover { opacity: .85 !important; background: var(--black) !important; }
        .sub-link svg { opacity: .5; stroke: var(--white); }
        .sub-badge { display: inline-block; background: rgba(255,255,255,.18); color: var(--white); font-size: 9px; font-weight: 700; padding: 1px 5px; margin-left: auto; font-family: var(--font-mono); border-radius: 4px; }

        /* ── THEME TOGGLE ── */
        .theme-toggle-btn { display: flex !important; align-items: center !important; gap: 10px !important; justify-content: space-between !important; }
        .theme-toggle-track { width: 36px; height: 20px; border-radius: 999px; background: var(--mid); border: 1px solid var(--border); position: relative; flex-shrink: 0; transition: background .25s; margin-left: auto; }
        [data-theme="dark"] .theme-toggle-track { background: #555; }
        .theme-toggle-thumb { position: absolute; top: 3px; left: 3px; width: 12px; height: 12px; border-radius: 50%; background: var(--muted); transition: left .25s, background .25s; }
        [data-theme="dark"] .theme-toggle-thumb { left: 19px; background: #f0f0ec; }

        /* ── BURGER + DRAWER ── */
        .burger-btn {
            background: none; border: none; color: var(--muted);
            padding: 6px 8px; cursor: pointer; line-height: 1;
            display: flex; align-items: center; border-radius: 8px;
            transition: background .15s, color .15s;
        }
        .burger-btn:hover { background: var(--off); color: var(--black); opacity: 1; }

        .side-drawer {
            position: fixed; top: 0; left: -330px;
            width: 300px; height: 100%;
            background: var(--white);
            border-right: 1px solid var(--border);
            z-index: 999; overflow-y: auto;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-lg);
            /* Modern custom scrollbar */
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
        }
        .side-drawer::-webkit-scrollbar { width: 4px; }
        .side-drawer::-webkit-scrollbar-track { background: transparent; }
        .side-drawer::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
        .side-drawer::-webkit-scrollbar-thumb:hover { background: var(--muted); }
        .side-drawer.open { left: 0; }
        .drawer-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.45); z-index: 998;
            backdrop-filter: blur(3px);
        }
        .drawer-overlay.open { display: block; animation: overlayFadeIn .25s ease; }
        @keyframes overlayFadeIn { from { opacity: 0; } to { opacity: 1; } }

        .drawer-header {
            background: var(--black); color: var(--white);
            padding: 20px 20px 16px; display: flex;
            justify-content: space-between; align-items: center;
            border-bottom: 1px solid var(--border);
        }
        .drawer-header strong { font-family: var(--font-disp); font-size: 24px; letter-spacing: .06em; font-weight: 400; color: var(--white); }
        .drawer-close {
            background: none; border: 1px solid rgba(255,255,255,.2);
            color: rgba(255,255,255,.6); font-size: 15px; cursor: pointer;
            padding: 5px 10px; line-height: 1; border-radius: 6px;
            transition: background .15s, color .15s;
        }
        .drawer-close:hover { background: rgba(255,255,255,.1); color: #fff; opacity: 1; }

        .drawer-user-block {
            padding: 16px 20px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 13px;
            background: var(--off);
        }
        .drawer-user-block img { width: 42px; height: 42px; object-fit: cover; border: 1.5px solid var(--border); border-radius: 50%; flex-shrink: 0; }
        .drawer-user-name { font-weight: 600; font-size: 15px; color: var(--black); }
        .drawer-user-role { font-size: 10px; color: var(--muted); font-family: var(--font-mono); letter-spacing: .08em; text-transform: uppercase; margin-top: 2px; }

        .drawer-section-title {
            font-size: 9px; font-weight: 700; letter-spacing: .12em;
            text-transform: uppercase; padding: 14px 20px 6px;
            color: var(--muted); border-bottom: 1px solid var(--mid);
            font-family: var(--font-mono); background: var(--off);
            display: block;
        }
        .drawer-link {
            display: flex; align-items: center; gap: 11px; padding: 11px 20px;
            color: var(--black); border-bottom: 1px solid var(--mid);
            font-size: 14.5px; background: var(--white);
            transition: background .12s, color .12s, padding-left .18s;
        }
        .drawer-link svg { width: 15px; height: 15px; stroke: var(--muted); fill: none; stroke-width: 1.8; flex-shrink: 0; transition: stroke .12s; }
        .drawer-link:hover { background: var(--off); padding-left: 26px; opacity: 1; }
        .drawer-link:hover svg { stroke: var(--black); }
        .drawer-link.active { background: var(--off); }
        .drawer-link-sub { background: var(--black) !important; color: var(--white) !important; font-weight: 500; }
        .drawer-link-sub svg { stroke: rgba(255,255,255,.5) !important; }
        .drawer-link-sub:hover { opacity: .85 !important; padding-left: 20px !important; }
        .drawer-link-danger { color: #dc2626 !important; }
        .drawer-link-danger svg { stroke: #dc2626 !important; }
        [data-theme="dark"] .drawer-link-danger { color: #f87171 !important; }
        [data-theme="dark"] .drawer-link-danger svg { stroke: #f87171 !important; }
        .drawer-link-danger:hover { background: rgba(220,38,38,.06) !important; }

        .drawer-auth { display: flex; gap: 10px; padding: 16px 20px; border-bottom: 1px solid var(--border); background: var(--white); }
        .drawer-auth a { flex: 1; text-align: center; padding: 10px; font-size: 15px; border: 1.5px solid var(--border); color: var(--black); background: transparent; border-radius: 8px; transition: background .15s; }
        .drawer-auth a:hover { background: var(--off); opacity: 1; }
        .drawer-auth .btn-signup { background: var(--black); color: var(--white) !important; border-color: var(--black); font-weight: 500; }
        .drawer-auth .btn-signup:hover { opacity: .85; }

        /* Browse dropdown in drawer */
        .drawer-browse-toggle {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 20px; color: var(--black); border-bottom: 1px solid var(--mid);
            font-size: 14.5px; background: var(--white); cursor: pointer;
            transition: background .12s, color .12s; user-select: none;
        }
        .drawer-browse-toggle > svg:first-child { width: 15px; height: 15px; stroke: var(--muted); fill: none; stroke-width: 1.8; flex-shrink: 0; }
        .drawer-browse-toggle:hover { background: var(--off); opacity: 1; }
        .drawer-browse-toggle .chevron { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; transition: transform .22s ease; flex-shrink: 0; color: var(--muted); margin-left: auto; }
        .drawer-browse-toggle.open .chevron { transform: rotate(180deg); }
        .drawer-browse-sub {
            display: none; background: var(--off); border-bottom: 1px solid var(--mid);
        }
        .drawer-browse-sub.open { display: block; }
        .drawer-browse-sub a {
            display: block; padding: 9px 20px 9px 32px;
            color: var(--muted); font-size: 15px; border-bottom: 1px solid var(--mid);
            transition: background .12s, color .12s;
        }
        .drawer-browse-sub a:hover { background: var(--mid); color: var(--black); opacity: 1; }
        .drawer-browse-sub-title {
            padding: 8px 20px 4px 32px; font-size: 9px; font-weight: 700;
            letter-spacing: .1em; text-transform: uppercase; color: var(--muted);
            font-family: var(--font-mono);
        }

        /* ── MARK READ ── */
        .notif-mark-read-btn { position: absolute; top: 8px; right: 8px; font-size: 10px; padding: 2px 7px; background: var(--mid); border: none; color: var(--black); cursor: pointer; font-family: var(--font-mono); letter-spacing: .04em; border-radius: 4px; }
        .notif-mark-read-btn:hover { background: var(--border); opacity: 1; }

        /* ── CARDS ── */
        .card { 
            background: var(--glass-bg); 
            backdrop-filter: blur(16px) saturate(1.4); 
            -webkit-backdrop-filter: blur(16px) saturate(1.4); 
            border: 1px solid var(--glass-border); 
            padding: 32px; 
            margin-bottom: 22px; 
            box-shadow: var(--glass-shadow); 
            border-radius: 18px; 
            transition: background .25s, border-color .25s, box-shadow .3s, transform .2s ease; 
        }
        .card:hover {
            box-shadow: var(--glass-shadow-lg);
            transform: translateY(-2px);
        }
        .card h1 { font-size: 28px; font-weight: 500; margin-bottom: 8px; color: var(--black); }
        .card h2 { font-size: 19px; font-weight: 500; margin-bottom: 18px; color: var(--black); }

        .grid { display: grid; gap: 18px; }
        .grid-4 { grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); }
        .grid-2 { grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); }
        .grid-3 { grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); }
        .stats { font-size: 34px; font-weight: 300; margin: 10px 0 0; font-family: var(--font-disp); letter-spacing: .02em; color: var(--black); }

        /* ── FORMS ── */
        input, textarea, select {
            width: 100%; box-sizing: border-box; padding: 10px 13px;
            border: 1.5px solid var(--border); background: var(--off);
            font-family: var(--font-sans); font-size: 15px; color: var(--black);
            outline: none; border-radius: 8px; -webkit-appearance: none;
            transition: border-color .15s, box-shadow .15s;
        }
        input:focus, textarea:focus, select:focus {
            border-color: var(--black); background: var(--white);
            box-shadow: none;
            outline: none;
        }
        [data-theme="dark"] input:focus, [data-theme="dark"] textarea:focus, [data-theme="dark"] select:focus { box-shadow: none; }
        input::placeholder, textarea::placeholder { color: var(--muted); }
        input[type="checkbox"], input[type="radio"] { width: 16px !important; height: 16px !important; min-width: 16px !important; padding: 0; cursor: pointer; flex-shrink: 0; accent-color: var(--black); vertical-align: middle; }
        input[type="checkbox"] { border-radius: 3px; }
        input[type="radio"] { border-radius: 50%; }
        input[type="file"] { background: transparent; border: 1.5px dashed var(--border); padding: 10px; cursor: pointer; border-radius: 8px; }

        button { 
            padding: 11px 24px; 
            border: 1.5px solid var(--black); 
            background: var(--black); 
            color: var(--white); 
            font-family: var(--font-sans); 
            font-size: 14.5px; 
            cursor: pointer; 
            letter-spacing: .03em; 
            border-radius: 9px; 
            transition: opacity .18s, transform .15s ease, box-shadow .18s; 
            font-weight: 500;
        }
        button:hover { 
            opacity: .88; 
            transform: translateY(-1px); 
            box-shadow: 0 4px 12px rgba(0,0,0,0.18); 
        }
        button:active { transform: scale(0.97) translateY(0); box-shadow: none; }
        .btn-outline { 
            background: transparent; 
            color: var(--black); 
            border: 1.5px solid var(--border); 
        }
        .btn-outline:hover { 
            border-color: var(--black); 
            opacity: 1; 
            background: var(--off);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .btn-danger { 
            background: linear-gradient(135deg, #dc2626, #b91c1c); 
            border-color: #dc2626; 
            color: #fff; 
            box-shadow: 0 2px 8px rgba(220,38,38,0.25);
        }
        .btn-danger:hover { 
            opacity: 1; 
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 16px rgba(220,38,38,0.35);
        }

        /* ── TABLE ── */
        table { width: 100%; border-collapse: collapse; }
        th { font-size: 11px; letter-spacing: .08em; text-transform: uppercase; font-family: var(--font-mono); font-weight: 500; padding: 11px 14px; border-bottom: 2px solid var(--black); text-align: left; color: var(--muted); }
        td { padding: 12px 14px; border-bottom: 1px solid var(--mid); font-size: 15px; vertical-align: top; color: var(--black); }
        tr:hover td { background: var(--off); }

        /* ── MISC ── */
        .muted { color: var(--muted); }
        .badge { display: inline-block; padding: 3px 9px; font-size: 11px; background: var(--mid); color: var(--black); font-family: var(--font-mono); border-radius: 5px; }
        .badge-green  { background: rgba(22,163,74,.12); color: #15803d; }
        .badge-red    { background: rgba(220,38,38,.12); color: #b91c1c; }
        .badge-yellow { background: rgba(217,119,6,.12); color: #b45309; }
        [data-theme="dark"] .badge-green  { color: #4ade80; }
        [data-theme="dark"] .badge-red    { color: #f87171; }
        [data-theme="dark"] .badge-yellow { color: #fbbf24; }
        .row { display: flex; gap: 10px; flex-wrap: wrap; }
        .row > * { flex: 1; min-width: 160px; }
        label { font-size: 15px; font-weight: 500; margin-bottom: 6px; display: block; color: var(--black); }

        /* ── SUBSCRIPTION MODAL ── */
        .sub-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: 9000; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .sub-modal-overlay.open { display: flex; }
        .sub-modal { background: var(--white); border: 1px solid var(--border); padding: 40px; max-width: 460px; width: 90%; box-shadow: var(--shadow-lg); border-radius: 16px; color: var(--black); }
        .sub-modal h2 { font-size: 24px; font-weight: 500; margin-bottom: 14px; color: var(--black); }
        .sub-modal p { color: var(--muted); font-size: 15px; line-height: 1.75; margin-bottom: 26px; }
        .sub-modal-actions { display: flex; gap: 10px; }
        .sub-modal-actions a { flex: 1; text-align: center; padding: 12px; font-size: 15px; border: 1.5px solid var(--border); color: var(--black); border-radius: 8px; transition: background .15s; }
        .sub-modal-actions a:hover { background: var(--off); opacity: 1; }
        .btn-subscribe { background: var(--black); color: var(--white) !important; border-color: var(--black) !important; }

        /* ── COOKIE ── */
        #cookie-banner { position: fixed; bottom: 0; left: 0; right: 0; background: rgba(13,13,12,.96); backdrop-filter: blur(8px); color: rgba(255,255,255,.75); padding: 18px 24px; z-index: 9999; border-top: 1px solid #333; font-size: 15px; }
        #cookie-banner .cookie-inner { max-width: 1280px; margin: 0 auto; display: flex; gap: 16px; align-items: flex-start; flex-wrap: wrap; }
        #cookie-banner .cookie-text { flex: 1; min-width: 240px; line-height: 1.6; }
        #cookie-banner .cookie-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; flex-shrink: 0; }
        #cookie-banner button { width: auto; padding: 8px 16px; font-size: 12px; border-radius: 6px; }
        #cookie-banner .btn-accept { background: #f0f0ec; color: #0d0d0c; border-color: #f0f0ec; }
        #cookie-banner .btn-deny   { background: #2a2a28; border-color: #2a2a28; color: rgba(255,255,255,.7); }
        #cookie-banner .btn-custom { background: transparent; border: 1px solid #444; color: rgba(255,255,255,.5); }
        #cookie-customize { display: none; margin-top: 12px; padding-top: 12px; border-top: 1px solid #333; }
        #cookie-customize label { display: flex; gap: 8px; align-items: center; margin-bottom: 8px; cursor: pointer; color: rgba(255,255,255,.7); font-weight: 400; }
        #cookie-customize input[type="checkbox"] { width: auto !important; }

        /* ── CONTENT ── */
        .content-wrap { padding: 36px 0; }
        
        /* Remove unwanted focus outlines on non-interactive elements */
        *:focus:not(:focus-visible) { outline: none !important; box-shadow: none !important; }
        body:focus { outline: none !important; }
        
        /* Clean focus ring for interactive elements */
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }

        /* ── FOOTER ── */
        .footer { background: var(--glass-bg); backdrop-filter: blur(16px) saturate(1.4); border-top: 1px solid var(--glass-border); margin-top: auto; position: relative; z-index: 1; }
        .footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 52px; padding: 68px 0 52px; }
        .footer-brand-name { font-family: var(--font-disp); font-size: 19px; letter-spacing: .08em; color: var(--black); display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .footer-brand-icon { width: 34px; height: 34px; object-fit: contain; flex-shrink: 0; display: block; align-self: center; }
        .logo-light { display: inline-block; }
        .logo-dark  { display: none; }
        [data-theme="dark"] .logo-light { display: none; }
        [data-theme="dark"] .logo-dark  { display: inline-block; }
        .footer-brand-desc { font-size: 15px; color: var(--muted); line-height: 1.8; max-width: 230px; }
        .footer-col-title { font-size: 10px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--black); font-family: var(--font-mono); margin-bottom: 20px; }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 11px; }
        .footer-links a { font-size: 15px; color: var(--muted); transition: color .15s, padding-left .15s; }
        .footer-links a:hover { color: var(--black); opacity: 1; padding-left: 4px; }
        .footer-info-item { margin-bottom: 18px; }
        .footer-info-label { font-size: 9px; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); font-family: var(--font-mono); margin-bottom: 3px; }
        .footer-info-value { font-size: 15px; color: var(--black); }
        .footer-divider { border: none; border-top: 1px solid var(--border); margin: 0; }
        .footer-bottom { padding: 22px 0 0; }
        .footer-bottom-bar { display: flex; justify-content: space-between; align-items: center; }
        .footer-copy { font-size: 12px; color: var(--muted); font-family: var(--font-mono); letter-spacing: .04em; }
        .footer-big-text { font-family: var(--font-disp); font-size: 96px; letter-spacing: .04em; color: var(--black); line-height: 1; opacity: .18; user-select: none; text-align: right; flex-shrink: 0; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .search-form { flex: 1; }
            .search-form input { width: 100%; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; padding: 40px 0 32px; }
            .wrap { padding: 0 20px; }
        }
        @media (max-width: 480px) { .footer-grid { grid-template-columns: 1fr; } }

        /* ── GLOBAL TRANSITIONS ── */
        html, body, .nav, .card, .book-card, button, a, input, select, textarea {
            transition: background-color .25s ease,
                        border-color .25s ease,
                        color .25s ease,
                        box-shadow .25s ease;
        }
        .book-card { transition: transform .2s ease, box-shadow .2s ease, border-color .15s ease !important; }
        .side-drawer { transition: left 0.3s ease !important; }
        .icon-dropdown-panel, .profile-menu-panel { transition: opacity .25s ease, transform .25s ease, visibility 0s linear .25s !important; }
        .icon-dropdown-panel.open, .profile-menu-panel.open { transition: opacity .25s ease, transform .25s ease, visibility 0s !important; }
        .drawer-link { transition: background .12s, padding-left .18s ease !important; }

        /* ── BUTTONS ── */
        button, .btn-outline, .receipt-btn-primary, .receipt-btn-secondary {
            transition: opacity .15s ease, transform .15s ease, box-shadow .2s ease, background-color .2s ease, border-color .2s ease !important;
        }
        a {
            transition: opacity .15s ease, color .15s ease, padding-left .15s ease !important;
        }

        /* ── SMOOTH SCROLL REVEAL STAGGER ── */
        .reveal-stagger > * {
            opacity: 0;
            transform: translateY(12px);
            transition: opacity .4s ease, transform .4s ease;
        }
        .reveal-stagger.visible > *:nth-child(1) { transition-delay: 0s; opacity: 1; transform: translateY(0); }
        .reveal-stagger.visible > *:nth-child(2) { transition-delay: .06s; opacity: 1; transform: translateY(0); }
        .reveal-stagger.visible > *:nth-child(3) { transition-delay: .12s; opacity: 1; transform: translateY(0); }
        .reveal-stagger.visible > *:nth-child(4) { transition-delay: .18s; opacity: 1; transform: translateY(0); }
        .reveal-stagger.visible > *:nth-child(5) { transition-delay: .24s; opacity: 1; transform: translateY(0); }
        .reveal-stagger.visible > *:nth-child(6) { transition-delay: .30s; opacity: 1; transform: translateY(0); }

        /* ── PAGE ANIMATIONS ── */
        @keyframes pageIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes cardIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes navSlide { from { opacity: 0; transform: translateY(-3px); } to { opacity: 1; transform: translateY(0); } }

        .page-body { animation: pageIn .3s ease both; }
        .nav { animation: navSlide .25s ease both; }
        .card { animation: cardIn .3s ease both; }
        .card:nth-child(2) { animation-delay: .04s; }
        .card:nth-child(3) { animation-delay: .08s; }
        .card:nth-child(4) { animation-delay: .12s; }
        .card:nth-child(5) { animation-delay: .16s; }

        /* Flash messages animate in */
        .flash { animation: cardIn .25s ease both; }

        /* Sidebar drawer link stagger on open */
        .side-drawer.open .drawer-link:nth-child(1) { animation: cardIn .2s ease both; }
        .side-drawer.open .drawer-link:nth-child(2) { animation: cardIn .2s .03s ease both; }
        .side-drawer.open .drawer-link:nth-child(3) { animation: cardIn .2s .06s ease both; }
        .side-drawer.open .drawer-link:nth-child(4) { animation: cardIn .2s .09s ease both; }
        .side-drawer.open .drawer-link:nth-child(5) { animation: cardIn .2s .12s ease both; }

        /* ── SCROLL REVEAL ── */
        .reveal {
            opacity: 0;
            transform: translateY(12px);
            transition: opacity .4s ease, transform .4s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── TOAST NOTIFICATIONS ── */
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .toast {
            pointer-events: auto;
            background: var(--glass-bg-strong);
            backdrop-filter: blur(20px) saturate(1.6);
            -webkit-backdrop-filter: blur(20px) saturate(1.6);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-lg);
            padding: 14px 20px;
            border-radius: 12px;
            min-width: 280px;
            max-width: 380px;
            font-size: 14.5px;
            color: var(--black);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            opacity: 0;
            transition: transform .35s ease, opacity .25s ease;
        }
        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        .toast-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .toast-success .toast-icon { background: rgba(22,163,74,.12); color: #15803d; }
        .toast-error   .toast-icon { background: rgba(220,38,38,.12); color: #b91c1c; }
        .toast-warning .toast-icon { background: rgba(217,119,6,.12); color: #b45309; }
        [data-theme="dark"] .toast-success .toast-icon { color: #4ade80; }
        [data-theme="dark"] .toast-error   .toast-icon { color: #f87171; }
        [data-theme="dark"] .toast-warning .toast-icon { color: #fbbf24; }
        .toast-close {
            margin-left: auto;
            background: none;
            border: none;
            color: var(--muted);
            font-size: 16px;
            cursor: pointer;
            padding: 0 0 0 8px;
            line-height: 1;
            opacity: .6;
            transition: opacity .15s;
        }
        .toast-close:hover { opacity: 1; }

        /* ── LOADING SPINNER ── */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            width: 18px; height: 18px;
            border: 2px solid var(--border);
            border-top-color: var(--black);
            border-radius: 50%;
            animation: spin .6s linear infinite;
            display: inline-block;
            vertical-align: middle;
        }
        .btn-loading { position: relative; color: transparent !important; pointer-events: none; }
        .btn-loading::after {
            content: '';
            position: absolute;
            left: 50%; top: 50%;
            width: 16px; height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid rgba(255,255,255,.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .6s linear infinite;
        }
        .btn-outline.btn-loading::after {
            border-color: var(--muted);
            border-top-color: var(--black);
        }

        .book-card {
            cursor: pointer;
            border: 1px solid var(--book-card-border);
            border-radius: 12px; overflow: hidden;
            background: var(--book-card-bg);
            transition: transform .2s ease, box-shadow .2s ease, border-color .15s ease;
            display: block; color: inherit;
            position: relative;
        }
        .book-card::after {
            content: '';
            position: absolute; inset: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, transparent 60%);
            opacity: 0;
            transition: opacity .2s ease;
            pointer-events: none;
        }
        .book-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
            border-color: var(--accent);
            opacity: 1;
        }
        .book-card:hover::after { opacity: 1; }
        .book-card:active { transform: translateY(-1px); }
        .book-card img {
            width: 100%; aspect-ratio: 3 / 4; object-fit: cover; display: block;
            background: var(--mid);
            transition: transform .25s ease;
        }
        .book-card:hover img { transform: scale(1.02); }
        .book-card-body { padding: 14px; }
        .book-card-title {
            font-weight: 600; font-size: 14.5px;
            overflow: hidden; display: -webkit-box;
            -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            margin-bottom: 4px; color: var(--book-title-color); line-height: 1.4;
        }
        .book-card-author { color: var(--muted); font-size: 12px; margin-bottom: 8px; }
        .book-card-badges { display: flex; flex-wrap: wrap; gap: 4px; }

        /* ── SMOOTH IMAGE LOAD ── */
        .img-fade {
            opacity: 0;
            transition: opacity .5s ease;
        }
        .img-fade.loaded {
            opacity: 1;
        }

        /* Custom scrollbar for the whole page */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--muted); }
        html { scrollbar-width: thin; scrollbar-color: var(--border) transparent; }

        /* Soft glow on focused elements */
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
            box-shadow: 0 0 0 4px var(--accent-soft);
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--muted);
        }
        .empty-state-icon {
            width: 64px; height: 64px;
            margin: 0 auto 16px;
            background: var(--off);
            border: 1.5px dashed var(--border);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: var(--muted);
        }
        .empty-state-title { font-size: 16px; font-weight: 600; color: var(--black); margin-bottom: 6px; }
        .empty-state-desc  { font-size: 14px; line-height: 1.6; max-width: 320px; margin: 0 auto; }

        /* ── NOTIFICATION ITEMS ── */
        .notif-item {
            padding: 16px 20px;
            border-bottom: 1px solid var(--mid);
            display: flex; align-items: flex-start; gap: 14px;
            transition: background .15s;
        }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: var(--off); }
        .notif-item.read { opacity: .55; }
        .notif-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .notif-icon.overdue { background: rgba(220,38,38,.10); }
        .notif-icon.due_soon { background: rgba(217,119,6,.10); }
        .notif-icon.payment_confirmed { background: rgba(22,163,74,.10); }
        .notif-icon.default { background: var(--off); }
        .notif-body { flex: 1; min-width: 0; }
        .notif-type-label {
            font-size: 10px; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; font-family: var(--font-mono);
            margin-bottom: 3px;
        }
        .notif-type-label.overdue { color: #dc2626; }
        .notif-type-label.due_soon { color: #92400e; }
        .notif-type-label.payment_confirmed { color: #15803d; }
        .notif-type-label.default { color: var(--muted); }
        .notif-message { font-size: 14.5px; color: var(--black); line-height: 1.5; margin: 0; }
        .notif-meta { font-size: 12px; color: var(--muted); font-family: var(--font-mono); margin-top: 4px; }
        .notif-badge-new {
            background: #dc2626; color: #fff;
            font-size: 9px; font-weight: 700; padding: 2px 7px;
            border-radius: 999px; font-family: var(--font-mono);
            letter-spacing: .04em; text-transform: uppercase;
        }

        /* ── MESSAGE BUBBLES (dark-mode vars) ── */
        :root {
            --msg-mine-bg: #111827;
            --msg-mine-color: #ffffff;
            --msg-theirs-bg: #f3f4f6;
            --msg-theirs-color: #111827;
        }
        [data-theme="dark"] {
            --msg-mine-bg: #4f6ef7;
            --msg-mine-color: #ffffff;
            --msg-theirs-bg: rgba(255,255,255,.08);
            --msg-theirs-color: #e8eaf0;
        }
        .msg-bubble-inner {
            max-width: 72%;
            padding: 12px 16px;
            border-radius: 14px;
            line-height: 1.5;
            font-size: 15px;
            word-wrap: break-word;
        }
        .msg-bubble-mine {
            background: var(--msg-mine-bg);
            color: var(--msg-mine-color);
            border-bottom-right-radius: 4px;
        }
        .msg-bubble-theirs {
            background: var(--msg-theirs-bg);
            color: var(--msg-theirs-color);
            border-bottom-left-radius: 4px;
        }
        .msg-time {
            font-size: 11px;
            opacity: .55;
            margin-top: 6px;
            text-align: right;
            font-family: var(--font-mono);
        }

        /* ── SELECT ARROW (custom dropdown) ── */
        .select-arrow {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 38px !important;
            cursor: pointer;
        }
        .select-arrow::-ms-expand {
            display: none;
        }
        [data-theme="dark"] .select-arrow {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        }

        /* ── SECTION HEADER ── */
        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 18px; flex-wrap: wrap; gap: 12px;
        }
        .section-header h2 { margin: 0; font-size: 18px; font-weight: 600; }
        .section-header-link {
            font-size: 13px; color: var(--muted);
            display: flex; align-items: center; gap: 5px;
            transition: color .15s;
        }
        .section-header-link:hover { color: var(--black); opacity: 1; }

        /* ── STAT CARDS (mini) ── */
        .stat-mini {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            transition: box-shadow .2s, border-color .2s;
        }
        .stat-mini:hover { box-shadow: var(--shadow-md); border-color: var(--black); }
        .stat-mini-label {
            font-size: 10px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; font-family: var(--font-mono);
            color: var(--muted); margin-bottom: 10px;
        }
        .stat-mini-value {
            font-size: 32px; font-weight: 300;
            font-family: var(--font-disp);
            letter-spacing: .02em; color: var(--black); line-height: 1;
        }
        .stat-mini-sub { font-size: 12px; color: var(--muted); margin-top: 6px; }
    </style>
</head>
<body>

<!-- Subscription Modal -->
<div class="sub-modal-overlay" id="subModalOverlay">
    <div class="sub-modal">
        <h2>Upgrade to Subscribe</h2>
        <p>
            Free users can borrow up to 5 books at a time and submit 2 books per day.<br><br>
            With a subscription (99 PHP/month or 999 PHP/year): borrow up to 25 books, publish up to 50 books, and get a <strong>✦</strong> badge next to your username.
        </p>
        <div class="sub-modal-actions">
            <a href="{{ route('subscription.index') }}" class="btn-subscribe">View Plans</a>
            <a href="#" style="color: var(--muted);" onclick="document.getElementById('subModalOverlay').classList.remove('open'); return false;">Not Now</a>
        </div>
    </div>
</div>

<!-- Side Drawer -->
<div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
<div class="side-drawer" id="sideDrawer">
    <div class="drawer-header">
        <strong>.Library</strong>
        <button class="drawer-close" onclick="closeDrawer()">&#x2715;</button>
    </div>

    @guest
    <div class="drawer-auth">
        <a href="{{ route('login') }}">Log In</a>
        <a href="{{ route('register') }}" class="btn-signup">Sign Up</a>
    </div>
    @endguest

    @auth
    <div class="drawer-user-block">
        <img src="{{ auth()->user()->avatarUrl() }}" alt="avatar">
        <div>
            <div class="drawer-user-name">{{ auth()->user()->badgedName() }}</div>
            @php
                $drawerRoleLabel = match(auth()->user()->role) {
                    'subscribed_user' => 'Subscriber',
                    'admin' => 'Admin',
                    'staff' => 'Staff',
                    default => 'User',
                };
            @endphp
            <div class="drawer-user-role">{{ $drawerRoleLabel }}</div>
        </div>
    </div>

    @if(auth()->user()->isPatron())
        <a href="{{ route('subscription.index') }}" class="drawer-link drawer-link-sub">
            <span style="font-size:15px;line-height:1;display:inline-flex;align-items:center;justify-content:center;width:15px;flex-shrink:0;">✦</span>
            {{ auth()->user()->isSubscribed() ? 'Manage Subscription' : 'Subscribe' }}
        </a>
    @endif

    <span class="drawer-section-title">Account</span>
    <a href="{{ route('user.profile') }}" class="drawer-link"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>My Profile</a>

    <span class="drawer-section-title">My Library</span>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="drawer-link"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Admin Dashboard</a>
    @elseif(auth()->user()->isStaff())
        <a href="{{ route('staff.dashboard') }}" class="drawer-link"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Staff Dashboard</a>
    @else
        <a href="{{ route('user.dashboard') }}" class="drawer-link"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Dashboard</a>
        <a href="{{ route('books.bookmarks') }}" class="drawer-link"><svg viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>Bookmarks / Lists</a>
        <a href="{{ route('user.ratings') }}" class="drawer-link"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>Ratings</a>
        <a href="{{ route('user.following') }}" class="drawer-link"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>Following</a>
        <a href="{{ route('user.publish') }}" class="drawer-link"><svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>Publish a Book</a>
        <a href="{{ route('user.submissions') }}" class="drawer-link"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>My Submissions</a>
    @endif
    <a href="{{ route('messages.index') }}" class="drawer-link"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>Messages @if($unreadMessageCount > 0)<span style="font-family:var(--font-mono);font-size:11px;color:#888;margin-left:auto;">({{ $unreadMessageCount }})</span>@endif</a>
    <a href="{{ route('notifications.index') }}" class="drawer-link"><svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>Notifications @if($unreadNotificationCount > 0)<span style="font-family:var(--font-mono);font-size:11px;color:#888;margin-left:auto;">({{ $unreadNotificationCount }})</span>@endif</a>
    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
        @csrf
        <button type="submit" class="drawer-link drawer-link-danger" style="width:100%;text-align:left;background:none;border:none;border-bottom:1px solid var(--mid);cursor:pointer;font-family:var(--font-sans);border-radius:0;">
            <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Logout
        </button>
    </form>
    @endauth

    <span class="drawer-section-title">Browse</span>
    <a href="{{ route('home') }}" class="drawer-link"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Home</a>
    <a href="{{ route('books.catalogue') }}" class="drawer-link"><svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>Catalogue</a>

    {{-- Sort filters dropdown --}}
    <div class="drawer-browse-toggle" id="sortToggle" onclick="toggleBrowseSection('sortSub','sortToggle')">
        <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        <span>Sort by</span>
        <svg class="chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
    </div>
    <div class="drawer-browse-sub" id="sortSub">
        <a href="{{ route('home', ['sort' => 'latest']) }}">Newest Added</a>
        <a href="{{ route('home', ['sort' => 'title_asc']) }}">Title A &ndash; Z</a>
        <a href="{{ route('home', ['sort' => 'title_desc']) }}">Title Z &ndash; A</a>
        <a href="{{ route('home', ['sort' => 'author_asc']) }}">Author A &ndash; Z</a>
        <a href="{{ route('home', ['sort' => 'rating']) }}">Top Rated</a>
    </div>

    {{-- Genre filters dropdown --}}
    @php $genres = \App\Models\Book::select('genre')->distinct()->whereNotNull('genre')->whereNotIn('genre', ['Manga', 'Comic'])->where('book_type', 'book')->orderBy('genre')->pluck('genre'); @endphp
    <div class="drawer-browse-toggle" id="genreToggle" onclick="toggleBrowseSection('genreSub','genreToggle')">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <span>Subjects</span>
        <svg class="chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
    </div>
    <div class="drawer-browse-sub" id="genreSub">
        @foreach($genres as $g)
            <a href="{{ route('home', ['genre' => $g]) }}">{{ $g }}</a>
        @endforeach
    </div>
</div>

<!-- Nav -->
<nav class="nav">
    <div class="wrap">
        <div class="nav-inner">
            <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
                <button class="burger-btn" onclick="openDrawer()" title="Menu">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="2" y1="4.5" x2="16" y2="4.5"/><line x1="2" y1="9" x2="16" y2="9"/><line x1="2" y1="13.5" x2="16" y2="13.5"/></svg>
                </button>
                <a href="{{ route('home') }}" class="nav-brand">
                    <span class="nav-brand-dot">.</span><span class="nav-brand-name">Library</span>
                </a>
            </div>

            <form class="search-form" method="GET" action="{{ route('search') }}" style="flex:1;max-width:420px;">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search books or users&hellip;">
                <button type="submit">Search</button>
            </form>

            <div class="nav-links">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('books.catalogue') }}">Catalogue</a>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}">Admin</a>
                    @elseif(auth()->user()->isStaff())
                        <a href="{{ route('staff.dashboard') }}">Staff</a>
                    @else
                        <a href="{{ route('user.dashboard') }}">Dashboard</a>
                    @endif

                    <div class="nav-icon-btn" id="notifWrap">
                        <button type="button" onclick="toggleDropdown('notifPanel','notifWrap',event)" title="Notifications">
                            <svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        </button>
                        @if($unreadNotificationCount > 0)
                            <span class="nav-icon-badge" id="notifBadge">{{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}</span>
                        @endif
                        <div class="icon-dropdown-panel" id="notifPanel">
                            <div class="icon-dropdown-header">Notifications<a href="#" onclick="markAllNotifRead(event)">Mark all read</a></div>
                            <div class="icon-dropdown-scroll-wrap" id="notifScrollWrap">
                                <div class="icon-dropdown-scroll" id="notifScroll">
                                    <div id="notifList"><div class="icon-dropdown-empty">Loading&hellip;</div></div>
                                </div>
                            </div>
                            <div class="icon-dropdown-footer"><a href="{{ route('notifications.index') }}">View all notifications &rarr;</a></div>
                        </div>
                    </div>

                    <div class="nav-icon-btn" id="msgWrap">
                        <button type="button" onclick="toggleDropdown('msgPanel','msgWrap',event)" title="Messages">
                            <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </button>
                        @if($unreadMessageCount > 0)
                            <span class="nav-icon-badge" id="msgBadge">{{ $unreadMessageCount > 9 ? '9+' : $unreadMessageCount }}</span>
                        @endif
                        <div class="icon-dropdown-panel" id="msgPanel">
                            <div class="icon-dropdown-header">Messages<a href="{{ route('messages.index') }}">View all</a></div>
                            <div class="icon-dropdown-scroll-wrap" id="msgScrollWrap">
                                <div class="icon-dropdown-scroll" id="msgScroll">
                                    <div id="msgList"><div class="icon-dropdown-empty">Loading&hellip;</div></div>
                                </div>
                            </div>
                            <div class="icon-dropdown-footer"><a href="{{ route('messages.index') }}">Open inbox &rarr;</a></div>
                        </div>
                    </div>

                    <div class="profile-menu" id="profileMenuWrap">
                        <button type="button" class="profile-toggle" onclick="toggleDropdown('profileMenuPanel','profileMenuWrap',event)">
                            <img src="{{ auth()->user()->avatarUrl() }}" alt="avatar" class="avatar-sm">
                            <span class="profile-menu-name">{{ auth()->user()->badgedName() }}</span>
                        </button>
                        <div class="profile-menu-panel" id="profileMenuPanel">
                            @if(auth()->user()->isPatron())
                                <a href="{{ route('subscription.index') }}" class="sub-link">
                                    <span style="font-size:15px;line-height:1;display:inline-flex;align-items:center;justify-content:center;width:15px;flex-shrink:0;">✦</span>
                                    @if(auth()->user()->isSubscribed()) Manage Subscription <span class="sub-badge">ACTIVE</span>
                                    @else Subscribe <span class="sub-badge">NEW</span>
                                    @endif
                                </a>
                            @endif
                            <span class="pm-section-label">Account</span>
                            <a href="{{ route('user.profile') }}"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>My Profile</a>
                            @if(auth()->user()->isPatron())
                                <a href="{{ route('user.dashboard') }}"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Dashboard</a>
                                <a href="{{ route('books.bookmarks') }}"><svg viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>Bookmarks</a>
                                <a href="{{ route('user.publish') }}"><svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>Publish a Book</a>
                            @endif
                            <a href="{{ route('messages.index') }}"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>Messages @if($unreadMessageCount > 0)<span style="font-family:var(--font-mono);font-size:11px;margin-left:auto;color:var(--muted);">({{ $unreadMessageCount }})</span>@endif</a>
                            <span class="pm-section-label">Display</span>
                            <button type="button" class="theme-toggle-btn" onclick="toggleTheme()">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                                <span id="themeLabel">Dark Mode</span>
                                <div class="theme-toggle-track" style="pointer-events:none;"><div class="theme-toggle-thumb"></div></div>
                            </button>
                            <span class="pm-section-label">Session</span>
                            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" class="danger"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                    <button onclick="toggleTheme()" title="Toggle dark mode" style="background:none;border:none;color:var(--muted);padding:6px;cursor:pointer;display:flex;align-items:center;border-radius:8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
                    </button>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Page Content -->
<div class="page-body">
    <div class="wrap content-wrap">
        @if (session('success'))
            <div class="flash success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="flash error"><strong>Please fix the errors below.</strong></div>
        @endif
        @if(session('subscription_prompt'))
            <script>document.addEventListener('DOMContentLoaded',function(){document.getElementById('subModalOverlay').classList.add('open');});</script>
        @endif
        @yield('content')
    </div>
</div>



<!-- FOOTER -->
<footer class="footer">
    <div class="wrap">
        <div class="footer-grid">
            <div>
                <div class="footer-brand-name">
                    <img src="/favicon1.png" alt="logo" class="footer-brand-icon logo-light" style="width:34px;height:34px;object-fit:contain;">
                    <img src="/favicon.png" alt="logo" class="footer-brand-icon logo-dark" style="width:34px;height:34px;object-fit:contain;">
                    .Library
                </div>
                <p class="footer-brand-desc">A minimalist library management system designed for modern readers and institutions.</p>
            </div>
            <div>
                <div class="footer-col-title">Navigate</div>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('books.catalogue') }}">Browse Library</a></li>
                    @guest
                        <li><a href="{{ route('login') }}">Sign In</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @endguest
                    @auth
                        <li><a href="{{ route('user.profile') }}">My Profile</a></li>
                        @if(auth()->user()->isPatron())
                            <li><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                        @endif
                    @endauth
                </ul>
            </div>
            <div>
                <div class="footer-col-title">Categories</div>
                <ul class="footer-links">
                    @php $footerGenres = \App\Models\Book::select('genre')->distinct()->whereNotNull('genre')->whereNotIn('genre', ['Manga', 'Comic'])->where('book_type', 'book')->orderBy('genre')->limit(6)->pluck('genre'); @endphp
                    @forelse($footerGenres as $fg)
                        <li><a href="{{ route('home', ['genre' => $fg]) }}">{{ $fg }}</a></li>
                    @empty
                        <li><a href="{{ route('home') }}">Fiction</a></li>
                        <li><a href="{{ route('home') }}">Non-Fiction</a></li>
                        <li><a href="{{ route('home') }}">Biography</a></li>
                        <li><a href="{{ route('home') }}">Science</a></li>
                        <li><a href="{{ route('home') }}">Technology</a></li>
                        <li><a href="{{ route('home') }}">History</a></li>
                    @endforelse
                </ul>
            </div>
            <div>
                <div class="footer-col-title">Information</div>
                <div class="footer-info-item"><div class="footer-info-label">Hours</div><div class="footer-info-value">Mon &ndash; Fri, 8am &ndash; 8pm</div></div>
                <div class="footer-info-item">
                    <div class="footer-info-label">Contact</div>
                    <div class="footer-info-value" style="display:flex; align-items:flex-end; gap:0; flex-wrap:wrap;">
                        <div style="text-align:center; padding-right:10px; border-right:1px solid var(--border); margin-right:10px;">
                            <div style="font-size:15px; font-weight:500; letter-spacing:.02em;">+63</div>
                        </div>
                        <div style="text-align:center; padding-right:10px; border-right:1px solid var(--border); margin-right:10px;">
                            <div style="font-size:15px; font-weight:500; letter-spacing:.02em;">962</div>
                        </div>
                        <div style="text-align:center;">
                            <div style="font-size:15px; font-weight:500; letter-spacing:.02em;">612-5XXX</div>
                        </div>
                    </div>
                </div>
                <div class="footer-info-item"><div class="footer-info-label">Location</div><div class="footer-info-value">Bolton St, Poblacion District, Davao City, 8000 Davao del Sur</div></div>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="footer-bottom">
            <div class="footer-bottom-bar">
                <span class="footer-copy">&copy; 2022 &ndash; 2026 dotLibrary &nbsp;|&nbsp; All Rights Reserved &nbsp;|&nbsp; <a href="#" onclick="event.preventDefault();showToast('Open source soon!','info');" style="color:var(--muted);">Contribute</a></span>
                <span class="footer-big-text">.LIBRARY</span>
            </div>
        </div>
    </div>
</footer>

<!-- Cookie Banner -->
<div id="cookie-banner" style="display:none;">
    <div class="cookie-inner">
        <div class="cookie-text">
            This website uses cookies to ensure basic functionality and improve your experience.
            <div id="cookie-customize">
                <label><input type="checkbox" id="cookie-analytics" checked> Analytics cookies</label>
                <label><input type="checkbox" id="cookie-preferences" checked> Preference cookies</label>
            </div>
        </div>
        <div class="cookie-actions">
            <button class="btn-accept" onclick="acceptAllCookies()">Accept All</button>
            <button class="btn-custom" onclick="toggleCookieCustomize()">Customize</button>
            <button class="btn-deny" onclick="denyCookies()">Necessary Only</button>
        </div>
    </div>
</div>

@stack('scripts')

<script>
var _csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

(function(){
    var saved = localStorage.getItem('dl-theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
})();

function toggleTheme() {
    var html = document.documentElement;
    var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('dl-theme', next);
    var label = document.getElementById('themeLabel');
    if (label) label.textContent = next === 'dark' ? 'Light Mode' : 'Dark Mode';
}

document.addEventListener('DOMContentLoaded', function() {
    var theme = localStorage.getItem('dl-theme') || 'light';
    var label = document.getElementById('themeLabel');
    if (label) label.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';

    // Scroll reveal
    var revealEls = document.querySelectorAll('.reveal');
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.10 });
    revealEls.forEach(function(el) { observer.observe(el); });
});

function openDrawer()  { document.getElementById('sideDrawer').classList.add('open'); document.getElementById('drawerOverlay').classList.add('open'); }
function closeDrawer() { document.getElementById('sideDrawer').classList.remove('open'); document.getElementById('drawerOverlay').classList.remove('open'); }

function updateScrollShadow(scrollEl, wrapEl) {
    if (!scrollEl || !wrapEl) return;
    var canScroll = scrollEl.scrollHeight > scrollEl.clientHeight;
    var atBottom = scrollEl.scrollTop + scrollEl.clientHeight >= scrollEl.scrollHeight - 4;
    wrapEl.classList.toggle('has-scroll', canScroll && !atBottom);
}

function initDropdownScroll(scrollId, wrapId) {
    var el = document.getElementById(scrollId);
    var wrap = document.getElementById(wrapId);
    if (!el || !wrap) return;
    updateScrollShadow(el, wrap);
    el.addEventListener('scroll', function() { updateScrollShadow(el, wrap); });
}

function toggleBrowseSection(subId, toggleId) {
    var sub = document.getElementById(subId);
    var toggle = document.getElementById(toggleId);
    if (!sub || !toggle) return;
    var isOpen = sub.classList.contains('open');
    sub.classList.toggle('open', !isOpen);
    toggle.classList.toggle('open', !isOpen);
}

function toggleDropdown(panelId, wrapId, event) {
    event.preventDefault(); event.stopPropagation();
    var panel = document.getElementById(panelId);
    if (!panel) return;
    var isOpen = panel.classList.contains('open');
    document.querySelectorAll('.icon-dropdown-panel.open, .profile-menu-panel.open').forEach(function(p){ p.classList.remove('open'); });
    if (!isOpen) {
        panel.classList.add('open');
        if (panelId === 'notifPanel') loadNotifications();
        if (panelId === 'msgPanel')   loadMessages();
    }
}
document.addEventListener('click', function(e) {
    var ids = ['notifWrap','msgWrap','profileMenuWrap'];
    var inside = ids.some(function(id){ var el=document.getElementById(id); return el&&el.contains(e.target); });
    if (!inside) document.querySelectorAll('.icon-dropdown-panel.open, .profile-menu-panel.open').forEach(function(p){ p.classList.remove('open'); });
});

function loadNotifications() {
    @auth
    fetch('{{ route("notifications.json") }}', { headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} })
    .then(r=>r.json()).then(data=>{
        var list = document.getElementById('notifList');
        if (!data.notifications||!data.notifications.length){ list.innerHTML='<div class="icon-dropdown-empty">No notifications.</div>'; return; }
        list.innerHTML = data.notifications.map(function(n){
            var mb = !n.read ? '<button class="notif-mark-read-btn" onclick="markNotifRead('+n.id+',this)">Mark read</button>' : '';
            return '<div class="icon-dropdown-item '+(n.read?'':'unread')+'" id="notif-item-'+n.id+'">'+mb
                +'<span style="font-size:10px;font-weight:600;text-transform:uppercase;color:var(--muted);font-family:var(--font-mono);letter-spacing:.06em;">'+n.type.replace(/_/g,' ')+'</span>'
                +'<div style="margin-top:3px;color:var(--black);">'+n.message+'</div>'
                +'<div style="font-size:11px;color:var(--muted);margin-top:4px;font-family:var(--font-mono);">'+n.time+'</div></div>';
        }).join('');
        var badge=document.getElementById('notifBadge');
        if(badge){if(data.unread>0){badge.textContent=data.unread>9?'9+':data.unread;badge.style.display='flex';}else{badge.style.display='none';}}
        initDropdownScroll('notifScroll', 'notifScrollWrap');
    }).catch(function(){});
    @endauth
}
function markNotifRead(id,btn) {
    fetch('/notifications/'+id+'/read',{method:'POST',headers:{'X-CSRF-TOKEN':_csrfToken,'X-Requested-With':'XMLHttpRequest'}})
    .then(function(){ var item=document.getElementById('notif-item-'+id); if(item)item.classList.remove('unread'); if(btn)btn.remove(); loadNotifications(); });
}
function markAllNotifRead(e) {
    e.preventDefault();
    fetch('{{ route("notifications.read_all") }}',{method:'POST',headers:{'X-CSRF-TOKEN':_csrfToken,'X-Requested-With':'XMLHttpRequest'}})
    .then(function(){ loadNotifications(); });
}

function loadMessages() {
    @auth
    fetch('{{ route("messages.recent.json") }}',{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
    .then(r=>r.json()).then(data=>{
        var list=document.getElementById('msgList');
        if(!data.conversations||!data.conversations.length){list.innerHTML='<div class="icon-dropdown-empty">No messages.</div>';return;}
        list.innerHTML=data.conversations.map(function(c){
            return '<div class="icon-dropdown-item '+(c.unread>0?'unread':'')+'"><a href="'+c.url+'">'
                +'<div style="display:flex;align-items:center;gap:10px;">'
                +'<img src="'+c.avatar+'" style="width:30px;height:30px;object-fit:cover;border:1px solid var(--border);border-radius:50%;flex-shrink:0;">'
                +'<div><strong style="color:var(--black);">'+c.partner_name+'</strong>'+(c.unread>0?' <span style="font-size:11px;font-family:var(--font-mono);color:var(--muted);">('+c.unread+' new)</span>':'')
                +'<br><span style="color:var(--muted);font-size:12px;">'+c.latest_body+'</span></div></div></a></div>';
        }).join('');
        var badge=document.getElementById('msgBadge');
        if(badge){if(data.total_unread>0){badge.textContent=data.total_unread>9?'9+':data.total_unread;badge.style.display='flex';}else{badge.style.display='none';}}
        initDropdownScroll('msgScroll', 'msgScrollWrap');
    }).catch(function(){});
    @endauth
}

function getCookie(name){var m=document.cookie.match(new RegExp('(^| )'+name+'=([^;]+)'));return m?m[2]:null;}
function setCookieConsent(a,p){var e='; expires='+new Date(Date.now()+31536000000).toUTCString()+'; path=/; SameSite=Lax';document.cookie='cookie_consent=1'+e;document.cookie='cookie_analytics='+(a?'1':'0')+e;document.cookie='cookie_preferences='+(p?'1':'0')+e;document.getElementById('cookie-banner').style.display='none';}
function acceptAllCookies(){setCookieConsent(true,true);}
function denyCookies(){setCookieConsent(false,false);}
function toggleCookieCustomize(){var el=document.getElementById('cookie-customize');el.style.display=el.style.display==='block'?'none':'block';}
function saveCustomCookies(){setCookieConsent(document.getElementById('cookie-analytics').checked,document.getElementById('cookie-preferences').checked);}

document.addEventListener('DOMContentLoaded',function(){
    if(!getCookie('cookie_consent'))document.getElementById('cookie-banner').style.display='block';
    var customDiv=document.getElementById('cookie-customize');
    var saveBtn=document.createElement('button');saveBtn.textContent='Save Preferences';saveBtn.className='btn-accept';saveBtn.style.marginTop='8px';saveBtn.style.width='auto';saveBtn.style.padding='7px 14px';saveBtn.onclick=saveCustomCookies;customDiv.appendChild(saveBtn);
    document.querySelectorAll('form[data-autofilter] select').forEach(function(el){el.addEventListener('change',function(){this.closest('form').submit();});});

    // Smooth image loads
    document.querySelectorAll('img').forEach(function(img){
        img.classList.add('img-fade');
        img.addEventListener('load',function(){this.classList.add('loaded');});
        if(img.complete) img.classList.add('loaded');
    });
});

/* ── TOAST SYSTEM ── */
function showToast(message, type) {
    type = type || 'success';
    var container = document.getElementById('toastContainer');
    if (!container) return;
    var icons = { success: '✓', error: '✕', warning: '!' };
    var toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.innerHTML = '<div class="toast-icon">' + icons[type] + '</div><span>' + message + '</span><button class="toast-close">×</button>';
    container.appendChild(toast);
    requestAnimationFrame(function() { toast.classList.add('show'); });
    var remove = function() {
        toast.classList.remove('show');
        setTimeout(function(){ if(toast.parentNode) toast.parentNode.removeChild(toast); }, 400);
    };
    toast.querySelector('.toast-close').addEventListener('click', remove);
    setTimeout(remove, 5000);
}

/* ── AJAX FORM SUBMIT ── */
function ajaxFormSubmit(form, opts) {
    opts = opts || {};
    var btn = form.querySelector('[type="submit"]');
    if(btn && opts.loadingClass) btn.classList.add(opts.loadingClass);
    var action = form.action;
    var method = form.method || 'POST';
    var data = new FormData(form);
    fetch(action, { method: method, headers: { 'X-CSRF-TOKEN': _csrfToken, 'X-Requested-With': 'XMLHttpRequest' }, body: data })
    .then(function(r) { return r.text().then(function(t) { return { text: t, ok: r.ok, status: r.status }; }); })
    .then(function(res) {
        if(btn && opts.loadingClass) btn.classList.remove(opts.loadingClass);
        var parser = new DOMParser();
        var doc = parser.parseFromString(res.text, 'text/html');
        var flash = doc.querySelector('.flash.success');
        var err = doc.querySelector('.flash.error');
        if (flash) { showToast(flash.textContent.trim(), 'success'); }
        if (err)     { showToast(err.textContent.trim(), 'error'); }
        if (opts.onSuccess) opts.onSuccess(res, doc);
    }).catch(function(e) {
        if(btn && opts.loadingClass) btn.classList.remove(opts.loadingClass);
        showToast('Something went wrong. Please refresh and try again.', 'error');
    });
}

/* ── BORROW AJAX ── */
function handleBorrow(form) {
    ajaxFormSubmit(form, {
        loadingClass: 'btn-loading',
        onSuccess: function(res, doc) {
            var success = !!doc.querySelector('.flash.success');
            if (success) {
                var readBtn = document.getElementById('readBookBtn');
                if (readBtn) readBtn.style.display = '';
                form.parentNode.innerHTML = '<span class="badge">You currently have this book borrowed</span>';
            }
        }
    });
}
</script>
</body>
</html>