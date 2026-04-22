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
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root,
        [data-theme="light"] {
            --bg-page:          linear-gradient(150deg, #fafaf8 0%, #f2f2ee 100%);
            --black:            #0d0d0c;
            --white:            #ffffff;
            --card-bg:          rgba(255,255,255,0.9);
            --card-grad:        linear-gradient(145deg, #ffffff 0%, #f9f9f6 100%);
            --off:              #f3f3f0;
            --mid:              #e5e5e1;
            --muted:            #7a7a75;
            --border:           #d8d8d3;
            --nav-bg:           rgba(255,255,255,0.95);
            --shadow-sm:        0 1px 4px rgba(0,0,0,.06);
            --shadow-md:        0 4px 20px rgba(0,0,0,.10);
            --shadow-lg:        0 16px 48px rgba(0,0,0,.14);
            --font-sans:        'DM Sans', system-ui, sans-serif;
            --font-mono:        'DM Mono', monospace;
            --font-disp:        'Bebas Neue', sans-serif;
            --book-card-bg:     #ffffff;
            --book-card-border: #e2e2dd;
            --book-title-color: #0d0d0c;
        }

        [data-theme="dark"] {
            --bg-page:          linear-gradient(150deg, #0d0d0c 0%, #131311 100%);
            --black:            #f0f0ec;
            --white:            #161614;
            --card-bg:          rgba(22,22,20,0.9);
            --card-grad:        linear-gradient(145deg, #1c1c1a 0%, #161614 100%);
            --off:              #1c1c1a;
            --mid:              #252523;
            --muted:            #7a7a72;
            --border:           #2e2e2b;
            --nav-bg:           rgba(13,13,12,0.97);
            --shadow-sm:        0 1px 4px rgba(0,0,0,.3);
            --shadow-md:        0 4px 20px rgba(0,0,0,.45);
            --shadow-lg:        0 16px 48px rgba(0,0,0,.65);
            --book-card-bg:     #1c1c1a;
            --book-card-border: #2e2e2b;
            --book-title-color: #f0f0ec;
        }

        html { font-size: 16px; -webkit-font-smoothing: antialiased; }
        body {
            font-family: var(--font-sans);
            background: var(--bg-page);
            background-attachment: fixed;
            color: var(--black);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a { color: inherit; text-decoration: none; }
        a:hover { opacity: .65; }

        .wrap { max-width: 1280px; margin: 0 auto; padding: 0 36px; }
        .page-body { flex: 1; }

        /* ── FLASH ── */
        .flash { padding: 13px 18px; font-size: 14px; border-left: 3px solid var(--black); margin-bottom: 20px; border-radius: 0 8px 8px 0; }
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
            box-shadow: 0 0 0 3px rgba(13,13,12,.06);
        }
        [data-theme="dark"] .search-form:focus-within {
            box-shadow: 0 0 0 3px rgba(240,240,236,.06);
            background: var(--off);
        }
        .search-form input {
            background: transparent; border: none; outline: none;
            padding: 0 14px; font-size: 13.5px; height: 40px;
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
        .nav-links { display: flex; align-items: center; gap: 26px; font-size: 14px; }
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
        .nav-icon-btn > button:hover { color: var(--black); background: var(--off); opacity: 1; }
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
            transform: translateY(-8px) scale(0.98);
            position: absolute; right: 0; top: calc(100% + 12px);
            min-width: 310px; max-width: 350px;
            background: var(--white); border: 1px solid var(--border);
            box-shadow: var(--shadow-lg); z-index: 200;
            border-radius: 14px; overflow: hidden;
            transition: opacity .22s ease, transform .22s ease, visibility 0s linear .22s;
        }
        .icon-dropdown-panel.open {
            visibility: visible; opacity: 1;
            transform: translateY(0) scale(1);
            transition: opacity .22s ease, transform .22s ease, visibility 0s;
        }
        .icon-dropdown-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 16px; border-bottom: 1px solid var(--border);
            font-size: 11px; font-weight: 600; letter-spacing: .08em;
            text-transform: uppercase; font-family: var(--font-mono); color: var(--muted);
        }
        .icon-dropdown-header a { font-size: 12px; font-weight: 400; color: var(--black); letter-spacing: 0; text-transform: none; opacity: .7; }
        .icon-dropdown-header a:hover { opacity: 1; }
        .icon-dropdown-item {
            padding: 11px 16px; border-bottom: 1px solid var(--mid);
            font-size: 13px; line-height: 1.45; position: relative;
            transition: background .12s;
        }
        .icon-dropdown-item:hover { background: var(--off); }
        .icon-dropdown-item.unread { background: var(--off); border-left: 3px solid var(--black); padding-left: 13px; }
        .icon-dropdown-item a { color: var(--black); display: block; }
        .icon-dropdown-item a:hover { opacity: .7; }
        .icon-dropdown-empty { padding: 24px 16px; color: var(--muted); font-size: 13px; text-align: center; }
        .icon-dropdown-footer { padding: 11px 16px; border-top: 1px solid var(--border); text-align: center; font-size: 12px; }
        .icon-dropdown-footer a { color: var(--black); font-weight: 500; opacity: .8; }
        .icon-dropdown-footer a:hover { opacity: 1; }

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
        .profile-menu-name { font-size: 13px; color: var(--black); max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
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
            font-size: 13px; font-family: var(--font-sans);
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
        }
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
            color: rgba(255,255,255,.6); font-size: 14px; cursor: pointer;
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
        .drawer-user-name { font-weight: 600; font-size: 14px; color: var(--black); }
        .drawer-user-role { font-size: 10px; color: var(--muted); font-family: var(--font-mono); letter-spacing: .08em; text-transform: uppercase; margin-top: 2px; }

        .drawer-section-title {
            font-size: 9px; font-weight: 700; letter-spacing: .12em;
            text-transform: uppercase; padding: 14px 20px 6px;
            color: var(--muted); border-bottom: 1px solid var(--mid);
            font-family: var(--font-mono); background: var(--off);
            display: block;
        }
        .drawer-link {
            display: block; padding: 11px 20px;
            color: var(--black); border-bottom: 1px solid var(--mid);
            font-size: 13.5px; background: var(--white);
            transition: background .12s, color .12s, padding-left .18s;
        }
        .drawer-link:hover { background: var(--off); padding-left: 26px; opacity: 1; }
        .drawer-link.active { background: var(--off); }
        .drawer-link-sub { background: var(--black) !important; color: var(--white) !important; font-weight: 500; }
        .drawer-link-sub:hover { opacity: .85 !important; padding-left: 20px !important; }
        .drawer-link-danger { color: #dc2626 !important; }
        [data-theme="dark"] .drawer-link-danger { color: #f87171 !important; }
        .drawer-link-danger:hover { background: rgba(220,38,38,.06) !important; }

        .drawer-auth { display: flex; gap: 10px; padding: 16px 20px; border-bottom: 1px solid var(--border); background: var(--white); }
        .drawer-auth a { flex: 1; text-align: center; padding: 10px; font-size: 13px; border: 1.5px solid var(--border); color: var(--black); background: transparent; border-radius: 8px; transition: background .15s; }
        .drawer-auth a:hover { background: var(--off); opacity: 1; }
        .drawer-auth .btn-signup { background: var(--black); color: var(--white) !important; border-color: var(--black); font-weight: 500; }
        .drawer-auth .btn-signup:hover { opacity: .85; }

        /* Browse dropdown in drawer */
        .drawer-browse-toggle {
            display: flex; align-items: center; justify-content: space-between;
            padding: 11px 20px; color: var(--black); border-bottom: 1px solid var(--mid);
            font-size: 13.5px; background: var(--white); cursor: pointer;
            transition: background .12s, color .12s; user-select: none;
        }
        .drawer-browse-toggle:hover { background: var(--off); opacity: 1; }
        .drawer-browse-toggle svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; transition: transform .22s ease; flex-shrink: 0; color: var(--muted); }
        .drawer-browse-toggle.open svg { transform: rotate(180deg); }
        .drawer-browse-sub {
            display: none; background: var(--off); border-bottom: 1px solid var(--mid);
        }
        .drawer-browse-sub.open { display: block; }
        .drawer-browse-sub a {
            display: block; padding: 9px 20px 9px 32px;
            color: var(--muted); font-size: 13px; border-bottom: 1px solid var(--mid);
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
        .card { background: var(--card-grad); border: 1px solid var(--border); padding: 28px; margin-bottom: 20px; box-shadow: var(--shadow-sm); border-radius: 14px; transition: background .25s, border-color .25s; }
        .card h1 { font-size: 28px; font-weight: 500; margin-bottom: 8px; color: var(--black); }
        .card h2 { font-size: 19px; font-weight: 500; margin-bottom: 18px; color: var(--black); }

        .grid { display: grid; gap: 18px; }
        .grid-4 { grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); }
        .grid-2 { grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); }
        .grid-3 { grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); }
        .stats { font-size: 34px; font-weight: 300; margin: 10px 0 0; font-family: var(--font-disp); letter-spacing: .02em; color: var(--black); }

        /* ── BOOK CARDS ── */
        .book-card {
            cursor: pointer;
            border: 1px solid var(--book-card-border);
            border-radius: 10px; overflow: hidden;
            background: var(--book-card-bg);
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
            display: block; color: inherit;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: var(--black);
            opacity: 1;
        }
        .book-card img { width: 100%; height: 200px; object-fit: cover; display: block; background: var(--mid); }
        .book-card-body { padding: 12px; }
        .book-card-title {
            font-weight: 600; font-size: 13.5px;
            overflow: hidden; display: -webkit-box;
            -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            margin-bottom: 4px; color: var(--book-title-color); line-height: 1.4;
        }
        .book-card-author { color: var(--muted); font-size: 12px; margin-bottom: 8px; }
        .book-card-badges { display: flex; flex-wrap: wrap; gap: 4px; }

        /* ── FORMS ── */
        input, textarea, select {
            width: 100%; box-sizing: border-box; padding: 10px 13px;
            border: 1.5px solid var(--border); background: var(--off);
            font-family: var(--font-sans); font-size: 14px; color: var(--black);
            outline: none; border-radius: 8px; -webkit-appearance: none;
            transition: border-color .15s, box-shadow .15s;
        }
        input:focus, textarea:focus, select:focus {
            border-color: var(--black); background: var(--white);
            box-shadow: 0 0 0 3px rgba(13,13,12,.07);
        }
        [data-theme="dark"] input:focus, [data-theme="dark"] textarea:focus, [data-theme="dark"] select:focus { box-shadow: 0 0 0 3px rgba(240,240,236,.07); }
        input::placeholder, textarea::placeholder { color: var(--muted); }
        input[type="checkbox"], input[type="radio"] { width: 16px !important; height: 16px !important; min-width: 16px !important; padding: 0; cursor: pointer; flex-shrink: 0; accent-color: var(--black); vertical-align: middle; }
        input[type="checkbox"] { border-radius: 3px; }
        input[type="radio"] { border-radius: 50%; }
        input[type="file"] { background: transparent; border: 1.5px dashed var(--border); padding: 10px; cursor: pointer; border-radius: 8px; }

        button { padding: 10px 22px; border: 1.5px solid var(--black); background: var(--black); color: var(--white); font-family: var(--font-sans); font-size: 14px; cursor: pointer; letter-spacing: .02em; border-radius: 8px; transition: opacity .15s, transform .1s; }
        button:hover { opacity: .82; }
        button:active { transform: scale(0.98); }
        .btn-outline { background: transparent; color: var(--black); border: 1.5px solid var(--border); }
        .btn-outline:hover { border-color: var(--black); opacity: 1; }
        .btn-danger { background: #dc2626; border-color: #dc2626; color: #fff; }
        .btn-danger:hover { opacity: .85; }

        /* ── TABLE ── */
        table { width: 100%; border-collapse: collapse; }
        th { font-size: 11px; letter-spacing: .08em; text-transform: uppercase; font-family: var(--font-mono); font-weight: 500; padding: 11px 14px; border-bottom: 2px solid var(--black); text-align: left; color: var(--muted); }
        td { padding: 12px 14px; border-bottom: 1px solid var(--mid); font-size: 14px; vertical-align: top; color: var(--black); }
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
        label { font-size: 14px; font-weight: 500; margin-bottom: 6px; display: block; color: var(--black); }

        /* ── SUBSCRIPTION MODAL ── */
        .sub-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: 9000; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .sub-modal-overlay.open { display: flex; }
        .sub-modal { background: var(--white); border: 1px solid var(--border); padding: 40px; max-width: 460px; width: 90%; box-shadow: var(--shadow-lg); border-radius: 16px; color: var(--black); }
        .sub-modal h2 { font-size: 24px; font-weight: 500; margin-bottom: 14px; color: var(--black); }
        .sub-modal p { color: var(--muted); font-size: 14px; line-height: 1.75; margin-bottom: 26px; }
        .sub-modal-actions { display: flex; gap: 10px; }
        .sub-modal-actions a { flex: 1; text-align: center; padding: 12px; font-size: 13px; border: 1.5px solid var(--border); color: var(--black); border-radius: 8px; transition: background .15s; }
        .sub-modal-actions a:hover { background: var(--off); opacity: 1; }
        .btn-subscribe { background: var(--black); color: var(--white) !important; border-color: var(--black) !important; }

        /* ── COOKIE ── */
        #cookie-banner { position: fixed; bottom: 0; left: 0; right: 0; background: rgba(13,13,12,.96); backdrop-filter: blur(8px); color: rgba(255,255,255,.75); padding: 18px 24px; z-index: 9999; border-top: 1px solid #333; font-size: 13px; }
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

        /* ── FOOTER ── */
        .footer { background: var(--card-bg); border-top: 1px solid var(--border); margin-top: auto; backdrop-filter: blur(8px); }
        .footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 52px; padding: 68px 0 52px; }
        .footer-brand-name { font-family: var(--font-disp); font-size: 19px; letter-spacing: .08em; color: var(--black); display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .footer-brand-icon { width: 30px; height: 30px; border: 1.5px solid var(--black); display: flex; align-items: center; justify-content: center; font-size: 11px; font-family: var(--font-mono); flex-shrink: 0; border-radius: 6px; }
        .footer-brand-desc { font-size: 13px; color: var(--muted); line-height: 1.8; max-width: 230px; }
        .footer-col-title { font-size: 10px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--black); font-family: var(--font-mono); margin-bottom: 20px; }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 11px; }
        .footer-links a { font-size: 14px; color: var(--muted); transition: color .15s, padding-left .15s; }
        .footer-links a:hover { color: var(--black); opacity: 1; padding-left: 4px; }
        .footer-info-item { margin-bottom: 18px; }
        .footer-info-label { font-size: 9px; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); font-family: var(--font-mono); margin-bottom: 3px; }
        .footer-info-value { font-size: 14px; color: var(--black); }
        .footer-divider { border: none; border-top: 1px solid var(--border); margin: 0; }
        .footer-bottom { padding: 22px 0 0; }
        .footer-bottom-bar { display: flex; justify-content: space-between; align-items: center; }
        .footer-copy { font-size: 12px; color: var(--muted); font-family: var(--font-mono); letter-spacing: .04em; }
        .footer-big-word { font-family: var(--font-disp); font-size: clamp(80px, 14vw, 200px); line-height: .85; color: var(--black); text-align: right; user-select: none; display: block; margin-top: 8px; opacity: .05; }

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
        * { transition: background-color .2s ease, border-color .2s ease, color .2s ease; }
        .book-card { transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease !important; }
        .side-drawer { transition: left 0.3s cubic-bezier(0.4,0,0.2,1) !important; }
        .icon-dropdown-panel, .profile-menu-panel { transition: opacity .22s ease, transform .22s ease, visibility 0s linear .22s !important; }
        .icon-dropdown-panel.open, .profile-menu-panel.open { transition: opacity .22s ease, transform .22s ease, visibility 0s !important; }
        .drawer-link { transition: background .12s, padding-left .18s !important; }
    </style>
</head>
<body>

<!-- Subscription Modal -->
<div class="sub-modal-overlay" id="subModalOverlay">
    <div class="sub-modal">
        <h2>Upgrade to Subscribe</h2>
        <p>
            Free users can borrow up to 5 books at a time and submit 2 books per day.<br><br>
            With a subscription (99 PHP/month or 999 PHP/year): borrow up to 25 books, publish up to 50 books, and get a <strong>+</strong> badge next to your username.
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
            <div class="drawer-user-role">{{ ucfirst(auth()->user()->role) }}</div>
        </div>
    </div>

    <span class="drawer-section-title">My Library</span>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="drawer-link">Admin Dashboard</a>
    @elseif(auth()->user()->isStaff())
        <a href="{{ route('staff.dashboard') }}" class="drawer-link">Staff Dashboard</a>
    @else
        <a href="{{ route('subscription.index') }}" class="drawer-link drawer-link-sub">
            {{ auth()->user()->isSubscribed() ? 'Manage Subscription' : 'Subscribe' }}
        </a>
        <a href="{{ route('user.dashboard') }}" class="drawer-link">Dashboard</a>
        <a href="{{ route('books.bookmarks') }}" class="drawer-link">Bookmarks / Lists</a>
        <a href="{{ route('user.ratings') }}" class="drawer-link">Ratings</a>
        <a href="{{ route('user.following') }}" class="drawer-link">Following</a>
        <a href="{{ route('user.publish') }}" class="drawer-link">Publish a Book</a>
        <a href="{{ route('user.submissions') }}" class="drawer-link">My Submissions</a>
    @endif
    <a href="{{ route('messages.index') }}" class="drawer-link">Messages @if($unreadMessageCount > 0)<span style="font-family:var(--font-mono);font-size:11px;color:#888;">({{ $unreadMessageCount }})</span>@endif</a>
    <a href="{{ route('notifications.index') }}" class="drawer-link">Notifications @if($unreadNotificationCount > 0)<span style="font-family:var(--font-mono);font-size:11px;color:#888;">({{ $unreadNotificationCount }})</span>@endif</a>
    <a href="{{ route('user.profile') }}" class="drawer-link">My Profile</a>
    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
        @csrf
        <button type="submit" class="drawer-link drawer-link-danger" style="width:100%;text-align:left;background:none;border:none;border-bottom:1px solid #1a1a18;padding:11px 20px;font-size:13.5px;cursor:pointer;font-family:var(--font-sans);border-radius:0;display:block;">Logout</button>
    </form>
    @endauth

    <span class="drawer-section-title">Browse</span>
    <a href="{{ route('home') }}" class="drawer-link">Home</a>
    <a href="{{ route('books.catalogue') }}" class="drawer-link">Catalogue</a>

    {{-- Sort filters dropdown --}}
    <div class="drawer-browse-toggle" id="sortToggle" onclick="toggleBrowseSection('sortSub','sortToggle')">
        <span>Sort by</span>
        <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
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
        <span>Subjects</span>
        <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
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
                            <div id="notifList"><div class="icon-dropdown-empty">Loading&hellip;</div></div>
                            <div class="icon-dropdown-footer"><a href="{{ route('notifications.index') }}">View all &rarr;</a></div>
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
                            <div id="msgList"><div class="icon-dropdown-empty">Loading&hellip;</div></div>
                            <div class="icon-dropdown-footer"><a href="{{ route('messages.index') }}">Open inbox &rarr;</a></div>
                        </div>
                    </div>

                    <div class="profile-menu" id="profileMenuWrap">
                        <button type="button" class="profile-toggle" onclick="toggleDropdown('profileMenuPanel','profileMenuWrap',event)">
                            <img src="{{ auth()->user()->avatarUrl() }}" alt="avatar" class="avatar-sm">
                            <span class="profile-menu-name">{{ auth()->user()->badgedName() }}</span>
                        </button>
                        <div class="profile-menu-panel" id="profileMenuPanel">
                            @if(auth()->user()->role === 'user')
                                <a href="{{ route('subscription.index') }}" class="sub-link">
                                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    @if(auth()->user()->isSubscribed()) Subscribed <span class="sub-badge">ACTIVE</span>
                                    @else Subscribe <span class="sub-badge">NEW</span>
                                    @endif
                                </a>
                            @endif
                            <span class="pm-section-label">Account</span>
                            <a href="{{ route('user.profile') }}"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>My Profile</a>
                            @if(auth()->user()->role === 'user')
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
                    <div class="footer-brand-icon" style="font-size:9px; letter-spacing:.03em;">IVO.</div>
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
                        @if(auth()->user()->role === 'user')
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
                <span class="footer-copy">&copy; 2022 &ndash; 2026 dotLibrary &nbsp;|&nbsp; All Rights Reserved</span>
            </div>
            <div class="footer-big-word">.Library</div>
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
});

function openDrawer()  { document.getElementById('sideDrawer').classList.add('open'); document.getElementById('drawerOverlay').classList.add('open'); }
function closeDrawer() { document.getElementById('sideDrawer').classList.remove('open'); document.getElementById('drawerOverlay').classList.remove('open'); }

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
});
</script>
</body>
</html>