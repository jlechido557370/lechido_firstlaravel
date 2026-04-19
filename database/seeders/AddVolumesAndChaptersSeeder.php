<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Series;
use App\Models\Book;

class AddVolumesAndChaptersSeeder extends Seeder
{
    public function run()
    {
        // ─────────────────────────────────────────────────────────────────
        // MANGA with free chapters on Manga Plus (first 3 chapters free)
        // Each entry: title => [title_id, chapters array]
        // ─────────────────────────────────────────────────────────────────
        $mangaFree = [
            'Jujutsu Kaisen'      => ['title_id' => 100001, 'chapters' => [1,2,3]],
            'Chainsaw Man'        => ['title_id' => 100025, 'chapters' => [1,2,3]],
            'Spy x Family'        => ['title_id' => 100031, 'chapters' => [1,2,3]],
            'One Piece'           => ['title_id' => 100002, 'chapters' => [1,2,3]],
            'My Hero Academia'    => ['title_id' => 100003, 'chapters' => [1,2,3]],
            'Demon Slayer'        => ['title_id' => 100009, 'chapters' => [1,2,3]],
            'Attack on Titan'     => ['title_id' => 100004, 'chapters' => [1,2,3]],
            'Death Note'          => ['title_id' => 100006, 'chapters' => [1,2,3]],
            'Fullmetal Alchemist' => ['title_id' => 100005, 'chapters' => [1,2,3]],
            'Tokyo Ghoul'         => ['title_id' => 100011, 'chapters' => [1,2,3]],
            'Naruto'              => ['title_id' => 100008, 'chapters' => [1,2,3]],
            'Bleach'              => ['title_id' => 100010, 'chapters' => [1,2,3]],
            'Hunter x Hunter'     => ['title_id' => 100018, 'chapters' => [1,2,3]],
            'Black Clover'        => ['title_id' => 100012, 'chapters' => [1,2,3]],
            'Haikyuu!!'           => ['title_id' => 100017, 'chapters' => [1,2,3]],
        ];

        // ─────────────────────────────────────────────────────────────────
        // MANGA without free online chapters (add only Volume 1 with VIZ preview)
        // ─────────────────────────────────────────────────────────────────
        $mangaOnlyVolume = [
            'Vinland Saga',
            'Berserk',
            'Sword Art Online: Aincrad',
            'Blue Exorcist',
            'Fairy Tail',
            'Slam Dunk',
            'Neon Genesis Evangelion',
            'Cowboy Bebop: Shooting Star',
            'Ranma 1/2',
            'Cardcaptor Sakura',
            'Sailor Moon',
            'YuYu Hakusho',
            'Fruits Basket',
            'Ouran High School Host Club',
        ];

        // ─────────────────────────────────────────────────────────────────
        // COMICS (add only Volume 1 with ComiXology preview link)
        // ─────────────────────────────────────────────────────────────────
        $comics = [
            'The Amazing Spider-Man: Coming Home',
            'Batman: The Long Halloween',
            'Watchmen',
            'V for Vendetta',
            'The Sandman Vol. 1: Preludes & Nocturnes',
            'Maus',
            'Persepolis',
            'Saga',
            'X-Men: Days of Future Past',
            'The Dark Knight Returns',
        ];

        // ========== 1. Process manga with free chapters ==========
        foreach ($mangaFree as $title => $data) {
            $series = Series::where('title', $title)->where('book_type', 'manga')->first();
            if (!$series) {
                $this->command->warn("Series not found: {$title}");
                continue;
            }

            // Add Volume 1
            Book::firstOrCreate(
                ['series_id' => $series->id, 'volume_number' => 1],
                [
                    'title'       => $series->title . ' Vol. 1',
                    'author'      => $series->author,
                    'book_type'   => 'manga',
                    'genre'       => 'Manga',
                    'genres'      => $series->genres,
                    'published_year' => 2020,
                    'total_copies' => 0,
                    'available_copies' => 0,
                    'description' => "Volume 1 of {$series->title}",
                    'read_url'    => null,
                    'cover_image' => $series->cover_image,
                    'is_series_container' => false,
                ]
            );

            // Add chapters 1-3
            foreach ($data['chapters'] as $ch) {
                Book::firstOrCreate(
                    ['series_id' => $series->id, 'chapter_number' => $ch],
                    [
                        'title'       => $series->title . ' Chapter ' . $ch,
                        'author'      => $series->author,
                        'book_type'   => 'manga',
                        'genre'       => 'Manga',
                        'genres'      => $series->genres,
                        'published_year' => 2020,
                        'total_copies' => 0,
                        'available_copies' => 0,
                        'description' => "Chapter {$ch} of {$series->title}",
                        'read_url'    => "https://mangaplus.shueisha.co.jp/viewer/{$data['title_id']}?chapter={$ch}",
                        'cover_image' => $series->cover_image,
                        'is_series_container' => false,
                    ]
                );
            }

            $this->command->info("✓ Added Vol.1 + Ch.1-3 for {$series->title}");
        }

        // ========== 2. Process manga with only Volume 1 ==========
        foreach ($mangaOnlyVolume as $title) {
            $series = Series::where('title', $title)->where('book_type', 'manga')->first();
            if (!$series) {
                $this->command->warn("Series not found: {$title}");
                continue;
            }

            Book::firstOrCreate(
                ['series_id' => $series->id, 'volume_number' => 1],
                [
                    'title'       => $series->title . ' Vol. 1',
                    'author'      => $series->author,
                    'book_type'   => 'manga',
                    'genre'       => 'Manga',
                    'genres'      => $series->genres,
                    'published_year' => 2020,
                    'total_copies' => 0,
                    'available_copies' => 0,
                    'description' => "Volume 1 of {$series->title}",
                    'read_url'    => "https://www.viz.com/read/manga/" . strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9 ]/', '', $series->title))) . "-volume-1",
                    'cover_image' => $series->cover_image,
                    'is_series_container' => false,
                ]
            );

            $this->command->info("✓ Added Vol.1 for {$series->title}");
        }

        // ========== 3. Process comics (Volume 1 only) ==========
        foreach ($comics as $title) {
            $series = Series::where('title', $title)->where('book_type', 'comic')->first();
            if (!$series) {
                // Try to find by exact title (some have different punctuation)
                $series = Series::where('title', 'LIKE', $title . '%')->first();
                if (!$series) {
                    $this->command->warn("Comic not found: {$title}");
                    continue;
                }
            }

            Book::firstOrCreate(
                ['series_id' => $series->id, 'volume_number' => 1],
                [
                    'title'       => $series->title . ' Vol. 1',
                    'author'      => $series->author,
                    'book_type'   => 'comic',
                    'genre'       => 'Comic',
                    'genres'      => $series->genres,
                    'published_year' => 2020,
                    'total_copies' => 0,
                    'available_copies' => 0,
                    'description' => "Volume 1 of {$series->title}",
                    'read_url'    => "https://www.comixology.com/comics-preview?title=" . urlencode($series->title),
                    'cover_image' => $series->cover_image,
                    'is_series_container' => false,
                ]
            );

            $this->command->info("✓ Added Vol.1 for {$series->title}");
        }

        $this->command->info("\n✅ All volumes and chapters added successfully!");
    }
}