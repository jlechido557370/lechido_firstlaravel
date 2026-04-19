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
        .warning { background: #fef9c3; }
        .muted   { color: #6b7280; }
        .badge   { display: inline-block; padding: 4px 8px; border-radius: 999px; font-size: 12px; background: #e5e7eb; }
        .badge-green  { background: #dcfce7; }
        .badge-red    { background: #fee2e2; }
        .badge-yellow { background: #fef9c3; }
        .avatar-sm { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,.4); vertical-align: middle; }
        .burger-btn { background: none; border: none; color: white; font-size: 22px; cursor: pointer; padding: 4px 8px; width: auto; line-height: 1; }
        .burger-btn:hover { opacity: .8; }
        .side-drawer { position: fixed; top: 0; left: -320px; width: 300px; height: 100%; background: white; border-right: 1px solid #ddd; z-index: 999; overflow-y: auto; transition: left 0.25s ease; box-shadow: 2px 0 8px rgba(0,0,0,.15); }
        .side-drawer.open { left: 0; }
        .drawer-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 998; }
        .drawer-overlay.open { display: block; }
        .drawer-header { background: #111827; color: white; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; }
        .drawer-close { background: none; border: none; color: white; font-size: 20px; cursor: pointer; width: auto; padding: 2px 6px; line-height: 1; }
        .drawer-section-title { font-weight: bold; font-size: 13px; text-transform: uppercase; letter-spacing: .05em; padding: 14px 16px 6px; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
        .drawer-link { display: block; padding: 12px 16px; color: #111827; border-bottom: 1px solid #f3f4f6; text-decoration: none; font-size: 15px; }
        .drawer-link:hover { background: #f9fafb; text-decoration: none; }
        .drawer-auth { display: flex; gap: 8px; padding: 14px 16px; border-bottom: 1px solid #e5e7eb; }
        .drawer-auth a { flex: 1; text-align: center; padding: 10px; border-radius: 6px; font-size: 14px; text-decoration: none; }
        .drawer-auth .btn-login  { border: 1px solid #111827; color: #111827; }
        .drawer-auth .btn-signup { background: #111827; color: white; }
        .nav-links { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
        .search-form { display: flex; align-items: center; gap: 8px; min-width: 260px; max-width: 360px; width: 100%; }
        .search-form input { width: 100%; padding: 8px 10px; border-radius: 6px; border: 1px solid #374151; background: #fff; }
        .search-form button { width: auto; padding: 8px 12px; background: #374151; border-color: #374151; }
        .profile-menu { position: relative; }
        .profile-toggle { width: auto; padding: 6px 10px; background: transparent; border: 1px solid rgba(255,255,255,.18); display: inline-flex; align-items: center; gap: 8px; }
        .profile-toggle:hover { opacity: 1; background: rgba(255,255,255,.06); }
        .profile-menu-panel { display: none; position: absolute; right: 0; top: calc(100% + 8px); min-width: 220px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,.14); overflow: hidden; z-index: 200; }
        .profile-menu-panel.open { display: block; }
        .profile-menu-panel a, .profile-menu-panel button { display: block; width: 100%; text-align: left; padding: 10px 14px; margin: 0; border: 0; background: white; color: #111827; border-radius: 0; }
        .profile-menu-panel a:hover, .profile-menu-panel button:hover { background: #f9fafb; text-decoration: none; }
        .profile-menu-panel .danger { color: #dc2626; }
        .profile-menu-name { color: white; font-size: 14px; }
        .nav-icon-btn { position: relative; display: inline-flex; align-items: center; }
        .nav-icon-btn > button { background: none; border: 1px solid rgba(255,255,255,.18); color: white; font-size: 16px; padding: 5px 9px; width: auto; border-radius: 6px; display: flex; align-items: center; }
        .nav-icon-btn > button:hover { background: rgba(255,255,255,.1); opacity:1; }
        .nav-icon-badge { position: absolute; top: -4px; right: -4px; background: #dc2626; color: white; font-size: 10px; font-weight: bold; min-width: 16px; height: 16px; border-radius: 999px; display: flex; align-items: center; justify-content: center; padding: 0 3px; pointer-events: none; }
        .icon-dropdown-panel { display: none; position: absolute; right: 0; top: calc(100% + 8px); min-width: 300px; max-width: 340px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,.14); overflow: hidden; z-index: 200; }
        .icon-dropdown-panel.open { display: block; }
        .icon-dropdown-header { display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-weight: bold; font-size: 13px; }
        .icon-dropdown-header a { font-size: 12px; font-weight: normal; }
        .icon-dropdown-item { padding: 8px 14px; border-bottom: 1px solid #f3f4f6; font-size: 13px; line-height: 1.4; position: relative; }
        .icon-dropdown-item.unread { background: #f0f9ff; }
        .icon-dropdown-item a { color: #111827; text-decoration: none; display: block; }
        .icon-dropdown-item a:hover { text-decoration: underline; }
        .icon-dropdown-empty { padding: 16px 14px; color: #6b7280; font-size: 13px; text-align: center; }
        .icon-dropdown-footer { padding: 10px 14px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 13px; }
        .notif-mark-read-btn { position: absolute; top: 8px; right: 8px; font-size: 10px; padding: 2px 6px; background: #e5e7eb; border: none; border-radius: 4px; color: #374151; cursor: pointer; width: auto; }
        .notif-mark-read-btn:hover { background: #d1d5db; opacity: 1; }
        /* Subscription badge in profile dropdown */
        .sub-link { background: #fef9c3 !important; font-weight: bold; border-left: 3px solid #f59e0b; }
        .sub-link:hover { background: #fef08a !important; }
        .sub-badge { display: inline-block; background: #f59e0b; color: white; font-size: 10px; font-weight: bold; padding: 1px 5px; border-radius: 999px; margin-left: 4px; vertical-align: middle; }
        /* Subscription popup modal */
        .sub-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 9000; align-items: center; justify-content: center; }
        .sub-modal-overlay.open { display: flex; }
        .sub-modal { background: white; border-radius: 8px; padding: 28px; max-width: 420px; width: 90%; }
        .sub-modal h2 { margin: 0 0 8px; font-size: 18px; }
        .sub-modal p { color: #374151; font-size: 14px; line-height: 1.6; margin: 0 0 16px; }
        .sub-modal .sub-modal-actions { display: flex; gap: 8px; }
        .sub-modal .sub-modal-actions a { flex: 1; text-align: center; padding: 10px; border-radius: 6px; font-size: 14px; text-decoration: none; }
        .sub-modal .btn-subscribe { background: #111827; color: white; }
        .sub-modal .btn-cancel { border: 1px solid #d1d5db; color: #374151; }
        /* Cookie banner */
        #cookie-banner { position: fixed; bottom: 0; left: 0; right: 0; background: #111827; color: #d1d5db; padding: 16px 20px; z-index: 9999; border-top: 2px solid #374151; font-size: 14px; }
        #cookie-banner .cookie-inner { max-width: 1100px; margin: 0 auto; display: flex; gap: 16px; align-items: flex-start; flex-wrap: wrap; }
        #cookie-banner .cookie-text { flex: 1; min-width: 240px; line-height: 1.5; }
        #cookie-banner .cookie-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; flex-shrink: 0; }
        #cookie-banner button { width: auto; padding: 8px 14px; font-size: 13px; }
        #cookie-banner .btn-accept { background: #2563eb; border-color: #2563eb; }
        #cookie-banner .btn-deny   { background: #374151; border-color: #374151; }
        #cookie-banner .btn-custom { background: transparent; border: 1px solid #6b7280; color: #d1d5db; }
        #cookie-customize { display: none; margin-top: 12px; padding-top: 12px; border-top: 1px solid #374151; }
        #cookie-customize label { display: flex; gap: 8px; align-items: center; margin-bottom: 8px; cursor: pointer; }
        #cookie-customize input[type="checkbox"] { width: auto; }
    </style>
</head>
<body>

<!-- Subscription Prompt Modal -->
<div class="sub-modal-overlay" id="subModalOverlay">
    <div class="sub-modal">
        <h2>Upgrade to Subscribe</h2>
        <p>
            Free users can borrow up to 5 books at a time and submit 2 books per day.<br><br>
            With a subscription (99 PHP/month or 999 PHP/year) you get:<br>
            - Borrow up to 25 books simultaneously<br>
            - Publish up to 50 books total<br>
            - A <strong>+</strong> badge next to your username
        </p>
        <div class="sub-modal-actions">
            <a href="{{ route('subscription.index') }}" class="btn-subscribe">View Subscription Plans</a>
            <a href="#" class="btn-cancel" onclick="document.getElementById('subModalOverlay').classList.remove('open'); return false;">Not Now</a>
        </div>
    </div>
</div>

<!-- Side Drawer -->
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
        <img src="{{ auth()->user()->avatarUrl() }}" alt="avatar" style="width:44px; height:44px; border-radius:50%; object-fit:cover; border:2px solid #e5e7eb;">
        <div>
            <div style="font-weight:bold; font-size:14px;">{{ auth()->user()->badgedName() }}</div>
            <div style="font-size:12px; color:#6b7280;">{{ ucfirst(auth()->user()->role) }}</div>
        </div>
    </div>

    <div class="drawer-section-title">My Library</div>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="drawer-link">Admin Dashboard</a>
    @elseif(auth()->user()->isStaff())
        <a href="{{ route('staff.dashboard') }}" class="drawer-link">Staff Dashboard</a>
    @else
        <a href="{{ route('subscription.index') }}" class="drawer-link" style="background:#fef9c3; font-weight:bold; border-left:3px solid #f59e0b;">
            {{ auth()->user()->isSubscribed() ? 'Manage Subscription' : 'Subscribe' }}
        </a>
        <a href="{{ route('user.dashboard') }}" class="drawer-link">Dashboard</a>
        <a href="{{ route('books.bookmarks') }}" class="drawer-link">Bookmarks / Lists</a>
        <a href="{{ route('user.ratings') }}" class="drawer-link">Ratings</a>
        <a href="{{ route('user.following') }}" class="drawer-link">Following</a>
        <a href="{{ route('user.publish') }}" class="drawer-link">Publish a Book</a>
        <a href="{{ route('user.submissions') }}" class="drawer-link">My Submissions</a>
    @endif
    <a href="{{ route('messages.index') }}" class="drawer-link">
        Messages @if($unreadMessageCount > 0)({{ $unreadMessageCount }})@endif
    </a>
    <a href="{{ route('notifications.index') }}" class="drawer-link">
        Notifications @if($unreadNotificationCount > 0)({{ $unreadNotificationCount }})@endif
    </a>
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
    <a href="{{ route('home', ['sort' => 'title_asc']) }}" class="drawer-link">Browse A-Z</a>

    @php $genres = \App\Models\Book::select('genre')->distinct()->orderBy('genre')->pluck('genre'); @endphp
    <div class="drawer-section-title">Subjects</div>
    @foreach($genres as $g)
        <a href="{{ route('home', ['genre' => $g]) }}" class="drawer-link">{{ $g }}</a>
    @endforeach
</div>

<!-- Nav -->
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
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                @elseif(auth()->user()->isStaff())
                    <a href="{{ route('staff.dashboard') }}">Staff</a>
                @else
                    <a href="{{ route('user.dashboard') }}">Dashboard</a>
                @endif

                {{-- Notification Bell Dropdown --}}
                <div class="nav-icon-btn" id="notifWrap">
                    <button type="button" onclick="toggleDropdown('notifPanel', 'notifWrap', event)" title="Notifications">
                        &#128276;
                    </button>
                    @if($unreadNotificationCount > 0)
                        <span class="nav-icon-badge" id="notifBadge">{{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}</span>
                    @endif
                    <div class="icon-dropdown-panel" id="notifPanel">
                        <div class="icon-dropdown-header">
                            Notifications
                            <a href="#" onclick="markAllNotifRead(event)">Mark all read</a>
                        </div>
                        <div id="notifList"><div class="icon-dropdown-empty">Loading...</div></div>
                        <div class="icon-dropdown-footer"><a href="{{ route('notifications.index') }}">View all</a></div>
                    </div>
                </div>

                {{-- Message Dropdown --}}
                <div class="nav-icon-btn" id="msgWrap">
                    <button type="button" onclick="toggleDropdown('msgPanel', 'msgWrap', event)" title="Messages">
                        &#9993;
                    </button>
                    @if($unreadMessageCount > 0)
                        <span class="nav-icon-badge" id="msgBadge">{{ $unreadMessageCount > 9 ? '9+' : $unreadMessageCount }}</span>
                    @endif
                    <div class="icon-dropdown-panel" id="msgPanel">
                        <div class="icon-dropdown-header">
                            Messages
                            <a href="{{ route('messages.index') }}">View all</a>
                        </div>
                        <div id="msgList"><div class="icon-dropdown-empty">Loading...</div></div>
                        <div class="icon-dropdown-footer"><a href="{{ route('messages.index') }}">Open inbox</a></div>
                    </div>
                </div>

                {{-- Profile dropdown --}}
                <div class="profile-menu" id="profileMenuWrap">
                    <button type="button" class="profile-toggle" onclick="toggleDropdown('profileMenuPanel', 'profileMenuWrap', event)">
                        <img src="{{ auth()->user()->avatarUrl() }}" alt="avatar" class="avatar-sm">
                        <span class="profile-menu-name">{{ auth()->user()->badgedName() }}</span>
                    </button>
                    <div class="profile-menu-panel" id="profileMenuPanel">
                        @if(auth()->user()->role === 'user')
                            {{-- Subscription link at top, highlighted --}}
                            <a href="{{ route('subscription.index') }}" class="sub-link">
                                @if(auth()->user()->isSubscribed())
                                    Subscribed <span class="sub-badge">ACTIVE</span>
                                @else
                                    Subscribe <span class="sub-badge">NEW</span>
                                @endif
                            </a>
                        @endif
                        <a href="{{ route('user.profile') }}">My Profile</a>
                        @if(auth()->user()->role === 'user')
                            <a href="{{ route('user.ratings') }}">Ratings</a>
                            <a href="{{ route('books.bookmarks') }}">Bookmarks / Lists</a>
                            <a href="{{ route('user.following') }}">Following</a>
                            <a href="{{ route('user.publish') }}">Publish a Book</a>
                            <a href="{{ route('user.submissions') }}">My Submissions</a>
                        @endif
                        <a href="{{ route('messages.index') }}">
                            Messages @if($unreadMessageCount > 0)({{ $unreadMessageCount }})@endif
                        </a>
                        <a href="{{ route('notifications.index') }}">
                            Notifications @if($unreadNotificationCount > 0)({{ $unreadNotificationCount }})@endif
                        </a>
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

    @if(session('subscription_prompt'))
        <script>document.addEventListener('DOMContentLoaded', function(){ document.getElementById('subModalOverlay').classList.add('open'); });</script>
    @endif

    @yield('content')
</div>

<!-- Cookie Consent Banner -->
<div id="cookie-banner" style="display:none;">
    <div class="cookie-inner">
        <div class="cookie-text">
            This website uses cookies to ensure basic functionality, improve your experience, and understand how the site is used.
            Necessary cookies are always active.
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

function openDrawer() { document.getElementById('sideDrawer').classList.add('open'); document.getElementById('drawerOverlay').classList.add('open'); }
function closeDrawer() { document.getElementById('sideDrawer').classList.remove('open'); document.getElementById('drawerOverlay').classList.remove('open'); }

var openDropdown = null;
function toggleDropdown(panelId, wrapId, event) {
    event.preventDefault(); event.stopPropagation();
    var panel = document.getElementById(panelId);
    if (!panel) return;
    var isOpen = panel.classList.contains('open');
    document.querySelectorAll('.icon-dropdown-panel.open, .profile-menu-panel.open').forEach(function(p){ p.classList.remove('open'); });
    if (!isOpen) {
        panel.classList.add('open'); openDropdown = panelId;
        if (panelId === 'notifPanel') loadNotifications();
        if (panelId === 'msgPanel') loadMessages();
    } else { openDropdown = null; }
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
        if (!data.notifications || !data.notifications.length) {
            list.innerHTML = '<div class="icon-dropdown-empty">No notifications.</div>'; return;
        }
        list.innerHTML = data.notifications.map(function(n) {
            var markBtn = !n.read
                ? '<button class="notif-mark-read-btn" onclick="markNotifRead(' + n.id + ', this)">Mark read</button>'
                : '';
            return '<div class="icon-dropdown-item ' + (n.read ? '' : 'unread') + '" id="notif-item-' + n.id + '">'
                + markBtn
                + '<span style="font-size:11px;font-weight:bold;text-transform:uppercase;color:#6b7280;">' + n.type.replace(/_/g,' ') + '</span>'
                + '<div>' + n.message + '</div>'
                + '<div style="font-size:11px;color:#9ca3af;">' + n.time + '</div>'
                + '</div>';
        }).join('');
        var badge = document.getElementById('notifBadge');
        if (badge) { if (data.unread>0){badge.textContent=data.unread>9?'9+':data.unread;badge.style.display='flex';}else{badge.style.display='none';} }
    }).catch(function(){});
    @endauth
}

function markNotifRead(id, btn) {
    fetch('/notifications/' + id + '/read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': _csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
    }).then(function() {
        var item = document.getElementById('notif-item-' + id);
        if (item) { item.classList.remove('unread'); }
        if (btn) btn.remove();
        loadNotifications();
    });
}

function markAllNotifRead(e) {
    e.preventDefault();
    fetch('{{ route("notifications.read_all") }}', {
        method: 'POST', headers: { 'X-CSRF-TOKEN': _csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
    }).then(function() { loadNotifications(); });
}

function loadMessages() {
    @auth
    fetch('{{ route("messages.recent.json") }}', { headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} })
    .then(r=>r.json()).then(data=>{
        var list = document.getElementById('msgList');
        if (!data.conversations||!data.conversations.length){ list.innerHTML='<div class="icon-dropdown-empty">No messages.</div>'; return; }
        list.innerHTML = data.conversations.map(function(c){
            return '<div class="icon-dropdown-item '+(c.unread>0?'unread':'')+'"><a href="'+c.url+'">'
                +'<div style="display:flex;align-items:center;gap:8px;">'
                +'<img src="'+c.avatar+'" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0;">'
                +'<div><strong>'+c.partner_name+'</strong>'+(c.unread>0?' <span style="color:#dc2626;font-size:11px;">('+c.unread+' new)</span>':'')+'<br><span style="color:#6b7280;">'+c.latest_body+'</span></div>'
                +'</div></a></div>';
        }).join('');
        var badge=document.getElementById('msgBadge');
        if(badge){if(data.total_unread>0){badge.textContent=data.total_unread>9?'9+':data.total_unread;badge.style.display='flex';}else{badge.style.display='none';}}
    }).catch(function(){});
    @endauth
}

function getCookie(name) { var m=document.cookie.match(new RegExp('(^| )'+name+'=([^;]+)')); return m?m[2]:null; }
function setCookieConsent(a,p){ var e='; expires='+new Date(Date.now()+31536000000).toUTCString()+'; path=/; SameSite=Lax'; document.cookie='cookie_consent=1'+e; document.cookie='cookie_analytics='+(a?'1':'0')+e; document.cookie='cookie_preferences='+(p?'1':'0')+e; document.getElementById('cookie-banner').style.display='none'; }
function acceptAllCookies(){ setCookieConsent(true,true); }
function denyCookies(){ setCookieConsent(false,false); }
function toggleCookieCustomize(){ var el=document.getElementById('cookie-customize'); el.style.display=el.style.display==='block'?'none':'block'; }
function saveCustomCookies(){ setCookieConsent(document.getElementById('cookie-analytics').checked, document.getElementById('cookie-preferences').checked); }

document.addEventListener('DOMContentLoaded', function() {
    if (!getCookie('cookie_consent')) document.getElementById('cookie-banner').style.display='block';
    var customDiv=document.getElementById('cookie-customize');
    var saveBtn=document.createElement('button'); saveBtn.textContent='Save Preferences'; saveBtn.className='btn-accept'; saveBtn.style.marginTop='8px'; saveBtn.style.width='auto'; saveBtn.style.padding='7px 14px'; saveBtn.onclick=saveCustomCookies; customDiv.appendChild(saveBtn);
    document.querySelectorAll('form[data-autofilter] select').forEach(function(el){ el.addEventListener('change',function(){this.closest('form').submit();}); });
});
</script>
</body>
</html>