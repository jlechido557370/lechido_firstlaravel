<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix wrong covers (ISBNs that OpenLibrary maps to incorrect books)
     * and missing covers (books with no cover on OpenLibrary).
     *
     * Covers come from two sources:
     *  - OpenLibrary: https://covers.openlibrary.org/b/isbn/{isbn}-L.jpg
     *  - Google Books: https://books.google.com/books/content?vid=ISBN:{isbn}&printsec=frontcover&img=1&zoom=1&source=gbs_api
     *
     * Google Books is used as the fallback when OpenLibrary returns the wrong image.
     */
    public function up(): void
    {
        $fixes = [
            // ── MANGA: wrong ISBNs in seeder caused wrong OpenLibrary covers ──────────

            // Demon Slayer Vol. 1 — seeder isbn 9781974700189 maps to wrong cover
            'Demon Slayer Vol. 1' => [
                'isbn_13'     => '9781974700523',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781974700523-L.jpg',
            ],
            // Bleach Vol. 1 — showed Basara Vol. 5 cover
            'Bleach Vol. 1' => [
                'isbn_13'     => '9781591164920',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591164920-L.jpg',
            ],
            // Hunter x Hunter Vol. 1 — showed Beyblade cover
            'Hunter x Hunter Vol. 1' => [
                'isbn_13'     => '9781591167969',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591167969-L.jpg',
            ],
            // Jujutsu Kaisen Vol. 1 — showed Persona 5 image
            'Jujutsu Kaisen Vol. 1' => [
                'isbn_13'     => '9781974710027',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781974710027-L.jpg',
            ],
            // Vinland Saga Vol. 1 — showed Sherlock Bones cover
            'Vinland Saga Vol. 1' => [
                'isbn_13'     => '9781612624204',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781612624204-L.jpg',
            ],
            // Spy x Family Vol. 1 — grey placeholder
            'Spy x Family Vol. 1' => [
                'isbn_13'     => '9781974717897',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781974717897-L.jpg',
            ],
            // Slam Dunk Vol. 1 — grey placeholder
            'Slam Dunk Vol. 1' => [
                'isbn_13'     => '9781421519432',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781421519432-L.jpg',
            ],
            // Fruits Basket Vol. 1 — grey placeholder
            'Fruits Basket Vol. 1' => [
                'isbn_13'     => '9781931514972',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781931514972-L.jpg',
            ],
            // Cardcaptor Sakura Vol. 1 — grey placeholder
            'Cardcaptor Sakura Vol. 1' => [
                'isbn_13'     => '9781591164456',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591164456-L.jpg',
            ],

            // ── REGULAR BOOKS: OpenLibrary had no cover for these ISBNs ─────────────
            // Falling back to Google Books which has better coverage.

            // Me Before You — grey placeholder
            'Me Before You' => [
                'cover_image' => 'https://books.google.com/books/content?vid=ISBN:9780143124542&printsec=frontcover&img=1&zoom=1&source=gbs_api',
            ],
            // To Kill a Mockingbird — grey placeholder
            'To Kill a Mockingbird' => [
                'cover_image' => 'https://books.google.com/books/content?vid=ISBN:9780061935466&printsec=frontcover&img=1&zoom=1&source=gbs_api',
            ],
            // You Don't Know JS — grey placeholder
            "You Don't Know JS" => [
                'cover_image' => 'https://books.google.com/books/content?vid=ISBN:9781491904244&printsec=frontcover&img=1&zoom=1&source=gbs_api',
            ],
        ];

        foreach ($fixes as $title => $data) {
            $update = ['cover_image' => $data['cover_image']];
            if (isset($data['isbn_13'])) {
                $update['isbn_13'] = $data['isbn_13'];
            }
            DB::table('books')->where('title', $title)->update($update);
        }
    }

    public function down(): void
    {
        // Revert to the original (incorrect) OpenLibrary URLs from the seeder
        $reverts = [
            'Demon Slayer Vol. 1'      => ['isbn_13' => '9781974700189', 'cover' => 'https://covers.openlibrary.org/b/isbn/9781974700189-L.jpg'],
            'Bleach Vol. 1'            => ['isbn_13' => '9781591162469', 'cover' => 'https://covers.openlibrary.org/b/isbn/9781591162469-L.jpg'],
            'Hunter x Hunter Vol. 1'   => ['isbn_13' => '9781591167938', 'cover' => 'https://covers.openlibrary.org/b/isbn/9781591167938-L.jpg'],
            'Jujutsu Kaisen Vol. 1'    => ['isbn_13' => '9781974714704', 'cover' => 'https://covers.openlibrary.org/b/isbn/9781974714704-L.jpg'],
            'Vinland Saga Vol. 1'      => ['isbn_13' => '9781612624464', 'cover' => 'https://covers.openlibrary.org/b/isbn/9781612624464-L.jpg'],
            'Spy x Family Vol. 1'      => ['isbn_13' => '9781974717965', 'cover' => 'https://covers.openlibrary.org/b/isbn/9781974717965-L.jpg'],
            'Slam Dunk Vol. 1'         => ['isbn_13' => '9781421519487', 'cover' => 'https://covers.openlibrary.org/b/isbn/9781421519487-L.jpg'],
            'Fruits Basket Vol. 1'     => ['isbn_13' => '9781598160871', 'cover' => 'https://covers.openlibrary.org/b/isbn/9781598160871-L.jpg'],
            'Cardcaptor Sakura Vol. 1' => ['isbn_13' => '9781595820402', 'cover' => 'https://covers.openlibrary.org/b/isbn/9781595820402-L.jpg'],
        ];

        foreach ($reverts as $title => $data) {
            DB::table('books')->where('title', $title)->update([
                'isbn_13'     => $data['isbn_13'],
                'cover_image' => $data['cover'],
            ]);
        }
    }
};