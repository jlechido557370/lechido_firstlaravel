<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Library Management System')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f7f7f7; color: #222; }
        a { color: #0b5ed7; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .container { max-width: 1100px; margin: 0 auto; padding: 20px; }
        .nav { background: #111827; color: white; position: relative; z-index: 100; }
        .nav .container { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding-top: 10px; padding-bottom: 10px; }
        .nav a { color: white; margin-left: 10px; font-size: 14px; }
        .nav a:hover { text-decoration: underline; }
        .card { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
        .grid { display: grid; gap: 16px; }
        .grid-4 { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
        .grid-2 { grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); }
        .grid-3 { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .stats { font-size: 28px; font-weight: 700; margin: 8px 0 0; }
        input, textarea, select { width: 100%; box-sizing: border-box; padding: 10px; border: 1px solid #bbb; border-radius: 6px; }
        button { width: 100%; box-sizing: border-box; padding: 10px; border: 1px solid #bbb; border-radius: 6px; background: #111827; color: white; cursor: pointer; }
        button:hover { opacity: .92; }
        .row { display: flex; gap: 8px; flex-wrap: wrap; }
        .row > * { flex: 1; min-width: 160px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; vertical-align: top; }
        .flash { padding: 12px 14px; margin-bottom: 16px; border-radius: 6px; }
        .success { background: #dcfce7; }
        .error   { background: #fee2e2; }
        .muted   { color: #6b7280; }
        .badge   { display: inline-block; padding: 4px 8px; border-radius: 999px; font-size: 12px; background: #e5e7eb; }
        .badge-green { background: #dcfce7; }
        .badge-red   { background: #fee2e2; }

        .avatar-sm {
            width: 32px; height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,.4);
            vertical-align: middle;
        }

        .burger-btn {
            background: none; border: none; color: white;
            font-size: 22px; cursor: pointer;
            padding: 4px 8px; width: auto; line-height: 1;
        }
        .burger-btn:hover { opacity: .8; }

        .side-drawer {
            position: fixed; top: 0; left: -320px;
            width: 300px; height: 100%;
            background: white; border-right: 1px solid #ddd;
            z-index: 999; overflow-y: auto;
            transition: left 0.25s ease;
            box-shadow: 2px 0 8px rgba(0,0,0,.15);
        }
        .side-drawer.open { left: 0; }

        .drawer-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.4); z-index: 998;
        }
        .drawer-overlay.open { display: block; }

        .drawer-header {
            background: #111827; color: white;
            padding: 14px 16px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .drawer-close {
            background: none; border: none; color: white;
            font-size: 20px; cursor: pointer; width: auto;
            padding: 2px 6px; line-height: 1;
        }
        .drawer-section-title {
            font-weight: bold; font-size: 13px;
            text-transform: uppercase; letter-spacing: .05em;
            padding: 14px 16px 6px; color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }
        .drawer-link {
            display: block; padding: 12px 16px;
            color: #111827; border-bottom: 1px solid #f3f4f6;
            text-decoration: none; font-size: 15px;
        }
        .drawer-link:hover { background: #f9fafb; text-decoration: none; }
        .drawer-auth { display: flex; gap: 8px; padding: 14px 16px; border-bottom: 1px solid #e5e7eb; }
        .drawer-auth a {
            flex: 1; text-align: center; padding: 10px;
            border-radius: 6px; font-size: 14px; text-decoration: none;
        }
        .drawer-auth .btn-login  { border: 1px solid #111827; color: #111827; }
        .drawer-auth .btn-signup { background: #111827; color: white; }

        .nav-links { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
        .search-form { display: flex; align-items: center; gap: 8px; min-width: 260px; max-width: 360px; width: 100%; }
        .search-form input { width: 100%; padding: 8px 10px; border-radius: 6px; border: 1px solid #374151; background: #fff; }
        .search-form button { width: auto; padding: 8px 12px; background: #374151; border-color: #374151; }

        .profile-menu { position: relative; }
        .profile-toggle {
            width: auto; padding: 6px 10px; background: transparent; border: 1px solid rgba(255,255,255,.18);
            display: inline-flex; align-items: center; gap: 8px;
        }
        .profile-toggle:hover { opacity: 1; background: rgba(255,255,255,.06); }
        .profile-menu-panel {
            display: none; position: absolute; right: 0; top: calc(100% + 8px);
            min-width: 220px; background: white; border: 1px solid #e5e7eb; border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,.14); overflow: hidden; z-index: 200;
        }
        .profile-menu-panel.open { display: block; }
        .profile-menu-panel a, .profile-menu-panel button {
            display: block; width: 100%; text-align: left; padding: 10px 14px; margin: 0;
            border: 0; background: white; color: #111827; border-radius: 0;
        }
        .profile-menu-panel a:hover, .profile-menu-panel button:hover { background: #f9fafb; text-decoration: none; }
        .profile-menu-panel .danger { color: #dc2626; }
        .profile-menu-name { color: white; font-size: 14px; }
    </style>
</head>
<body>

<div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
<div class="side-drawer" id="sideDrawer">
    <div class="drawer-header">
        <strong>Library System</strong>
        <button class="drawer-close" onclick="closeDrawer()">&#x2715;</button>
    </div>

    @guest
    <div class="drawer-auth">
        <a href="{{ route('login') }}" class="btn-login">Log In</a>
        <a href="{{ route('register') }}" class="btn-signup">Sign Up</a>
    </div>
    @endguest

    @auth
    <div style="padding: 12px 16px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 12px;">
        <img src="{{ auth()->user()->avatarUrl() }}" alt="avatar"
             style="width:44px; height:44px; border-radius:50%; object-fit:cover; border:2px solid #e5e7eb;">
        <div>
            <div style="font-weight:bold; font-size:14px;">{{ auth()->user()->name }}</div>
            <div style="font-size:12px; color:#6b7280;">{{ ucfirst(auth()->user()->role) }}</div>
        </div>
    </div>

    <div class="drawer-section-title">My Library</div>
    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}" class="drawer-link">Dashboard</a>
    @if(auth()->user()->role === 'user')
        <a href="{{ route('books.bookmarks') }}" class="drawer-link">Bookmarks</a>
        <a href="{{ route('user.ratings') }}" class="drawer-link">Ratings</a>
    @endif
    <a href="{{ route('user.profile') }}" class="drawer-link">My Profile</a>
    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
        @csrf
        <button type="submit" style="width:100%; text-align:left; background:none; color:#dc2626; border:none; border-top:1px solid #f3f4f6; border-radius:0; padding:12px 16px; font-size:15px; cursor:pointer;">
            Logout
        </button>
    </form>
    @endauth

    <div class="drawer-section-title">Browse</div>
    <a href="{{ route('home') }}" class="drawer-link">Home</a>
    <a href="{{ route('books.catalogue') }}" class="drawer-link">Catalogue</a>
    <a href="{{ route('home', ['sort' => 'latest']) }}" class="drawer-link">Newest Books</a>
    <a href="{{ route('home', ['sort' => 'title_asc']) }}" class="drawer-link">Browse A–Z</a>

    @php $genres = \App\Models\Book::select('genre')->distinct()->orderBy('genre')->pluck('genre'); @endphp
    <div class="drawer-section-title">Subjects</div>
    @foreach($genres as $g)
        <a href="{{ route('home', ['genre' => $g]) }}" class="drawer-link">{{ $g }}</a>
    @endforeach
</div>

<div class="nav">
    <div class="container">
        <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
            <button class="burger-btn" onclick="openDrawer()" title="Menu">&#9776;</button>
            <strong><a href="{{ route('home') }}" style="color:white; text-decoration:none; font-size:15px;">Library System</a></strong>
        </div>

        <form class="search-form" method="GET" action="{{ route('search') }}">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search books or users">
            <button type="submit">Search</button>
        </form>

        <div class="nav-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('books.catalogue') }}">Catalogue</a>
            @auth
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}">Dashboard</a>
                @if(auth()->user()->role === 'user')
                    <a href="{{ route('books.bookmarks') }}">Bookmarks</a>
                    <a href="{{ route('user.ratings') }}">Ratings</a>
                @endif
                <div class="profile-menu" id="profileMenuWrap">
                    <button type="button" class="profile-toggle" onclick="toggleProfileMenu(event)">
                        <img src="{{ auth()->user()->avatarUrl() }}" alt="avatar" class="avatar-sm">
                        <span class="profile-menu-name">{{ auth()->user()->name }}</span>
                    </button>
                    <div class="profile-menu-panel" id="profileMenuPanel">
                        <a href="{{ route('user.profile') }}">My Profile</a>
                        @if(auth()->user()->role === 'user')
                            <a href="{{ route('user.ratings') }}">Ratings</a>
                            <a href="{{ route('books.bookmarks') }}">Bookmarks</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="danger">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    </div>
</div>

<div class="container">
    @if (session('success'))
        <div class="flash success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="flash error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="flash error"><strong>Please fix the errors below.</strong></div>
    @endif

    @yield('content')
</div>

@stack('scripts')

<script>
    function openDrawer() {
        document.getElementById('sideDrawer').classList.add('open');
        document.getElementById('drawerOverlay').classList.add('open');
    }

    function closeDrawer() {
        document.getElementById('sideDrawer').classList.remove('open');
        document.getElementById('drawerOverlay').classList.remove('open');
    }

    function toggleProfileMenu(event) {
        event.preventDefault();
        event.stopPropagation();
        document.getElementById('profileMenuPanel').classList.toggle('open');
    }

    document.addEventListener('click', function (event) {
        var panel = document.getElementById('profileMenuPanel');
        var wrap = document.getElementById('profileMenuWrap');
        if (panel && wrap && !wrap.contains(event.target)) {
            panel.classList.remove('open');
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-autofilter] select').forEach(function (el) {
            el.addEventListener('change', function () { this.closest('form').submit(); });
        });
    });
</script>
</body>
</html>
