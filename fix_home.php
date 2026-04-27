<?php
$path = 'c:/Users/Admin/Desktop/laravel/lechido_firstlaravel/resources/views/home.blade.php';
$content = file_get_contents($path);

$brokenPos = strrpos($content, "or \xe2\x82\xb1999/year (save \xe2\x82\xb1189)</");
if ($brokenPos === false) {
    $brokenPos = strrpos($content, "or \xc3\xa2\xc2\x82\xc2\xb1999/year");
}

if ($brokenPos !== false) {
    $before = substr($content, 0, $brokenPos);
    $lineStart = strrpos($before, "\n");
    if ($lineStart === false) $lineStart = 0;
    $fixed = substr($content, 0, $lineStart);
} else {
    $fixed = $content;
}

$tail = <<<'EOF'
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

{{-- Browse Books section continues unchanged below --}}
<div class="reveal" style="padding:4px 0;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <h2 style="margin:0;font-size:20px;font-weight:600;color:var(--black);">Browse Books</h2>
        <a href="{{ route('books.catalogue') }}" style="font-size:13px;color:var(--muted);display:flex;align-items:center;gap:5px;transition:color .15s;" onmouseover="this.style.color='var(--black)'" onmouseout="this.style.color='var(--muted)'">
            Full Catalogue
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
    </div>
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

{{-- About This Project --}}
<div class="reveal" style="margin-top:60px;padding:60px 0;border-top:1px solid var(--border);">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:48px;align-items:center;">
        <div>
            <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);margin-bottom:14px;">About This Project</div>
            <h2 style="font-size:28px;font-weight:600;margin-bottom:16px;color:var(--black);line-height:1.2;">Built for readers.<br>Designed for discovery.</h2>
            <p style="font-size:15px;color:var(--muted);line-height:1.8;margin-bottom:20px;">
                .Library is a modern digital library platform built for Davao City. Our mission is to make books accessible to everyone through technology — whether you want to borrow physical copies, read online, or discover your next favorite story.
            </p>
            <div style="display:flex;flex-wrap:wrap;gap:20px;margin-bottom:24px;">
                <div>
                    <div style="font-size:22px;font-weight:600;color:var(--black);font-family:var(--font-disp);">{{ number_format($stats['total_books']) }}</div>
                    <div style="font-size:11px;color:var(--muted);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:.06em;">Books Catalogued</div>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:600;color:var(--black);font-family:var(--font-disp);">{{ number_format($stats['members']) }}</div>
                    <div style="font-size:11px;color:var(--muted);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:.06em;">Active Readers</div>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:600;color:var(--black);font-family:var(--font-disp);">{{ number_format($stats['total_borrows_all']) }}</div>
                    <div style="font-size:11px;color:var(--muted);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:.06em;">Borrows Recorded</div>
                </div>
            </div>
            <p style="font-size:13px;color:var(--muted);line-height:1.7;">
                <strong style="color:var(--black);">Fun fact:</strong> The average .Library member borrows 4 books per month. Our most popular genre is Fiction, followed by Technology and History.
            </p>
        </div>
        <div style="position:relative;">
            <div style="position:absolute;inset:-20px;background:linear-gradient(135deg,var(--bg-mesh-1),var(--bg-mesh-2));border-radius:24px;opacity:.6;filter:blur(20px);"></div>
            <div style="position:relative;background:var(--glass-bg);backdrop-filter:blur(16px);border:1px solid var(--glass-border);border-radius:20px;padding:32px;box-shadow:var(--glass-shadow);">
                <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);margin-bottom:16px;">Powered by</div>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    @foreach(['Laravel','PHP','MySQL','Tailwind CSS','JavaScript','Google Books API'] as $tech)
                        <span style="display:inline-block;padding:6px 14px;background:var(--off);border:1px solid var(--border);border-radius:999px;font-size:13px;color:var(--black);">{{ $tech }}</span>
                    @endforeach
                </div>
                <div style="height:1px;background:var(--border);margin:20px 0;"></div>
                <div style="font-size:13px;color:var(--muted);line-height:1.7;">
                    Open for contributions. Have feedback or want to report an issue? Reach out through the library staff or open a discussion.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

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
@keyframes bookCardGlow {
    0% { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    50% { box-shadow: 0 16px 40px rgba(79,110,247,0.16), 0 4px 12px rgba(0,0,0,0.06); }
    100% { box-shadow: 0 8px 24px rgba(0,0,0,0.10); }
}

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
EOF;

file_put_contents($path, $fixed . $tail);
echo "Done. File size: " . strlen($fixed . $tail) . " bytes\n";

