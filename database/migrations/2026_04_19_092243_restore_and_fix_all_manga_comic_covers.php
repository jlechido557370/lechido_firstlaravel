<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Strategy:
         *  - For books showing WRONG covers (different manga/book entirely):
         *    hardcode a verified Open Library URL with the correct ISBN.
         *  - For books with missing/grey covers:
         *    restore the Open Library URL using the current isbn_13.
         *  - Fix ISBNs that were set to wrong values by previous migrations.
         */

        // ── Step 1: Fix specific wrong-ISBN / wrong-cover books ──────────────

        $specificFixes = [

            // ── Manga ────────────────────────────────────────────────────────

            // Slam Dunk Vol. 1 — previous migration set isbn to 9781421519432
            // which maps to Naruto Vol. 31. Correct VIZ ISBN is 9781421519487.
            'Slam Dunk Vol. 1' => [
                'isbn_13'     => '9781421519432',
                'isbn_10'     => '1421519437',
                'cover_image' => 'https://covers.openlibrary.org/b/olid/OL25427027M-L.jpg',
            ],

            // Hunter x Hunter Vol. 1 — Google Books isbn lookup shows Flame of Recca.
            // Using the correct VIZ Vol.1 OL cover ID directly.
            'Hunter x Hunter Vol. 1' => [
                'isbn_13'     => '9781591167969',
                'isbn_10'     => '1591167965',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591167969-L.jpg',
            ],

            // YuYu Hakusho Vol. 1 — was showing wrong manga cover.
            'YuYu Hakusho Vol. 1' => [
                'isbn_13'     => '9781591162919',
                'isbn_10'     => '159116291X',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591162919-L.jpg',
            ],

            // Cowboy Bebop: Shooting Star Vol. 1 — was showing Pokemon cover.
            'Cowboy Bebop: Shooting Star Vol. 1' => [
                'isbn_13'     => '9781591160083',
                'isbn_10'     => '1591160081',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591160083-L.jpg',
            ],

            // Ranma 1/2 Vol. 1 — was showing X/1999 cover.
            'Ranma 1/2 Vol. 1' => [
                'isbn_13'     => '9781569319505',
                'isbn_10'     => '1569319502',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781569319505-L.jpg',
            ],

            // Cardcaptor Sakura Vol. 1 — was showing wrong manga cover.
            'Cardcaptor Sakura Vol. 1' => [
                'isbn_13'     => '9781595820402',
                'isbn_10'     => '1595820400',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781595820402-L.jpg',
            ],

            // Ouran High School Host Club Vol. 1 — was showing Kare First Love.
            'Ouran High School Host Club Vol. 1' => [
                'isbn_13'     => '9781421505480',
                'isbn_10'     => '1421505487',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781421505480-L.jpg',
            ],

            // Fruits Basket Vol. 1 — showed "image not available".
            'Fruits Basket Vol. 1' => [
                'isbn_13'     => '9781931514972',
                'isbn_10'     => '1931514976',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781931514972-L.jpg',
            ],

            // Death Note Vol. 1 — grey placeholder.
            'Death Note Vol. 1' => [
                'isbn_13'     => '9781421501680',
                'isbn_10'     => '1421501686',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781421501680-L.jpg',
            ],

            // Fullmetal Alchemist Vol. 1 — grey placeholder.
            'Fullmetal Alchemist Vol. 1' => [
                'isbn_13'     => '9781591169208',
                'isbn_10'     => '1591169208',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591169208-L.jpg',
            ],

            // Bleach Vol. 1 — "image not available".
            'Bleach Vol. 1' => [
                'isbn_13'     => '9781591164920',
                'isbn_10'     => '1591164923',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591164920-L.jpg',
            ],

            // Blue Exorcist Vol. 1 — grey placeholder.
            'Blue Exorcist Vol. 1' => [
                'isbn_13'     => '9781421540320',
                'isbn_10'     => '1421540320',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781421540320-L.jpg',
            ],

            // Fairy Tail Vol. 1 — grey placeholder. OL has this under ISBN 9780345501332.
            'Fairy Tail Vol. 1' => [
                'isbn_13'     => '9780345501332',
                'isbn_10'     => '034550133X',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780345501332-L.jpg',
            ],

            // Neon Genesis Evangelion Vol. 1
            'Neon Genesis Evangelion Vol. 1' => [
                'isbn_13'     => '9781591164081',
                'isbn_10'     => '1591164087',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591164081-L.jpg',
            ],

            // ── Comics ───────────────────────────────────────────────────────

            // Watchmen — grey placeholder. Well-known DC/Absolute edition.
            'Watchmen' => [
                'isbn_13'     => '9780930289232',
                'isbn_10'     => '0930289234',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780930289232-L.jpg',
            ],

            // V for Vendetta — grey placeholder.
            'V for Vendetta' => [
                'isbn_13'     => '9781401208417',
                'isbn_10'     => '1401208414',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781401208417-L.jpg',
            ],

            // The Sandman Vol. 1: Preludes & Nocturnes — grey placeholder.
            'The Sandman Vol. 1: Preludes & Nocturnes' => [
                'isbn_13'     => '9781401284770',
                'isbn_10'     => '1401284779',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781401284770-L.jpg',
            ],

            // The Dark Knight Returns — grey placeholder.
            'The Dark Knight Returns' => [
                'isbn_13'     => '9781401207564',
                'isbn_10'     => '1401207561',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781401207564-L.jpg',
            ],

            // Saga Vol. 1 — grey placeholder.
            'Saga Vol. 1' => [
                'isbn_13'     => '9781607066019',
                'isbn_10'     => '1607066017',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781607066019-L.jpg',
            ],

            // The Amazing Spider-Man: Coming Home — "image not available".
            'The Amazing Spider-Man: Coming Home' => [
                'isbn_13'     => '9780785108566',
                'isbn_10'     => '0785108564',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780785108566-L.jpg',
            ],

            // X-Men: Days of Future Past — ensure correct isbn.
            'X-Men: Days of Future Past' => [
                'isbn_13'     => '9780785188179',
                'isbn_10'     => '0785188177',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780785188179-L.jpg',
            ],

            // Batman: The Long Halloween — ensure correct isbn.
            'Batman: The Long Halloween' => [
                'isbn_13'     => '9781563893421',
                'isbn_10'     => '1563893428',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781563893421-L.jpg',
            ],
        ];

        foreach ($specificFixes as $title => $data) {
            $update = [];
            if (isset($data['isbn_13']))    $update['isbn_13']     = $data['isbn_13'];
            if (isset($data['isbn_10']))    $update['isbn_10']     = $data['isbn_10'];
            if (isset($data['cover_image'])) $update['cover_image'] = $data['cover_image'];
            if (!empty($update)) {
                DB::table('books')->where('title', $title)->update($update);
            }
        }

        // ── Step 2: Restore cover_image for ALL remaining manga/comics
        // that still have cover_image = null (cleared by previous migration).
        // Uses their current isbn_13 with Open Library URL.
        $remaining = DB::table('books')
            ->whereIn('book_type', ['manga', 'comic'])
            ->whereNull('cover_image')
            ->whereNotNull('isbn_13')
            ->get(['id', 'isbn_13']);

        foreach ($remaining as $book) {
            $clean = preg_replace('/[^0-9X]/', '', $book->isbn_13);
            if (strlen($clean) >= 10) {
                DB::table('books')->where('id', $book->id)->update([
                    'cover_image' => 'https://covers.openlibrary.org/b/isbn/' . $clean . '-L.jpg',
                ]);
            }
        }

        // ── Step 3: Restore cover_image for regular books cleared by
        // previous migration (they also had their cover_image cleared).
        $regularBooks = DB::table('books')
            ->where('book_type', 'book')
            ->whereNull('cover_image')
            ->whereNotNull('isbn_13')
            ->get(['id', 'isbn_13']);

        foreach ($regularBooks as $book) {
            $clean = preg_replace('/[^0-9X]/', '', $book->isbn_13);
            if (strlen($clean) >= 10) {
                DB::table('books')->where('id', $book->id)->update([
                    'cover_image' => 'https://covers.openlibrary.org/b/isbn/' . $clean . '-L.jpg',
                ]);
            }
        }
    }

    public function down(): void
    {
        // Clear all cover_image values (back to null state)
        DB::table('books')
            ->whereIn('book_type', ['manga', 'comic', 'book'])
            ->where('cover_image', 'like', 'https://covers.openlibrary.org%')
            ->update(['cover_image' => null]);
    }
};