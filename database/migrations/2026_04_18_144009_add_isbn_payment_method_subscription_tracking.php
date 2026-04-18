<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add ISBN-13 and ISBN-10 to books
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'isbn_13')) {
                $table->string('isbn_13', 20)->nullable()->after('isbn');
            }
            if (!Schema::hasColumn('books', 'isbn_10')) {
                $table->string('isbn_10', 20)->nullable()->after('isbn_13');
            }
        });

        // Add payment_method to payments
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('status');
            }
        });

        // Add subscription tracking to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'subscription_plan')) {
                $table->string('subscription_plan', 20)->nullable()->after('subscription_expires_at');
            }
            if (!Schema::hasColumn('users', 'subscription_amount')) {
                $table->decimal('subscription_amount', 10, 2)->nullable()->after('subscription_plan');
            }
            if (!Schema::hasColumn('users', 'subscription_paid_at')) {
                $table->timestamp('subscription_paid_at')->nullable()->after('subscription_amount');
            }
        });

        // Populate isbn_13 and isbn_10 from existing isbn field
        foreach (DB::table('books')->get() as $book) {
            $raw = preg_replace('/[^0-9X]/', '', $book->isbn ?? '');
            if (strlen($raw) === 13) {
                $isbn10 = $this->convertIsbn13to10($raw);
                DB::table('books')->where('id', $book->id)->update([
                    'isbn_13' => $raw,
                    'isbn_10' => $isbn10,
                ]);
            }
        }

        // Fix duplicate or wrong cover_image URLs for manga/comics
        // These were confirmed duplicates or placeholders in the original seeder
        $coverFixes = [
            // Chainsaw Man had Jujutsu Kaisen cover
            'MANGA-CSM-001'   => 'https://covers.openlibrary.org/b/isbn/9781974720699-M.jpg',
            // Spy x Family had Dragon Ball cover
            'MANGA-SXF-001'   => 'https://covers.openlibrary.org/b/isbn/9781974720491-M.jpg',
            // Blue Exorcist had Dragon Ball cover
            'MANGA-BE-001'    => 'https://covers.openlibrary.org/b/isbn/9781421540320-M.jpg',
            // Fairy Tail had Bleach cover
            'MANGA-FT-001'    => 'https://covers.openlibrary.org/b/isbn/9780345501066-M.jpg',
            // Black Clover had MHA cover
            'MANGA-BC-001'    => 'https://covers.openlibrary.org/b/isbn/9781421587189-M.jpg',
            // Additional manga all had Bleach cover
            'MANGA-HQ-001'    => 'https://covers.openlibrary.org/b/isbn/9781421587660-M.jpg',
            'MANGA-SD-001'    => 'https://covers.openlibrary.org/b/isbn/9781421514055-M.jpg',
            'MANGA-NGE-001'   => 'https://covers.openlibrary.org/b/isbn/9781591164609-M.jpg',
            'MANGA-CB-001'    => 'https://covers.openlibrary.org/b/isbn/9781591161578-M.jpg',
            'MANGA-RH-001'    => 'https://covers.openlibrary.org/b/isbn/9781591161141-M.jpg',
            'MANGA-CCS-001'   => 'https://covers.openlibrary.org/b/isbn/9781591164456-M.jpg',
            'MANGA-SM-001'    => 'https://covers.openlibrary.org/b/isbn/9781935429746-M.jpg',
            'MANGA-YYH-001'   => 'https://covers.openlibrary.org/b/isbn/9781591164579-M.jpg',
            'MANGA-FB-001'    => 'https://covers.openlibrary.org/b/isbn/9781931514972-M.jpg',
            'MANGA-OHSHC-001' => 'https://covers.openlibrary.org/b/isbn/9781421500423-M.jpg',
            // Comics had generic placeholder text — give them Open Library lookups via real ISBNs
            'COMIC-SPIDEY-001'=> 'https://covers.openlibrary.org/b/isbn/9780785107316-M.jpg',
            'COMIC-BAT-001'   => 'https://covers.openlibrary.org/b/isbn/9781563893599-M.jpg',
            'COMIC-WTCH-001'  => 'https://covers.openlibrary.org/b/isbn/9781779501127-M.jpg',
            'COMIC-VFV-001'   => 'https://covers.openlibrary.org/b/isbn/9781401208417-M.jpg',
            'COMIC-SAND-001'  => 'https://covers.openlibrary.org/b/isbn/9781401284770-M.jpg',
            'COMIC-MAUS-001'  => 'https://covers.openlibrary.org/b/isbn/9780679748403-M.jpg',
            'COMIC-PRSP-001'  => 'https://covers.openlibrary.org/b/isbn/9780375714573-M.jpg',
            'COMIC-SAGA-001'  => 'https://covers.openlibrary.org/b/isbn/9781607066019-M.jpg',
            'COMIC-XMN-001'   => 'https://covers.openlibrary.org/b/isbn/9780785163336-M.jpg',
            'COMIC-DKR-001'   => 'https://covers.openlibrary.org/b/isbn/9781563893421-M.jpg',
        ];

        foreach ($coverFixes as $isbn => $coverUrl) {
            DB::table('books')->where('isbn', $isbn)->update(['cover_image' => $coverUrl]);
        }
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['isbn_13', 'isbn_10']);
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['subscription_plan', 'subscription_amount', 'subscription_paid_at']);
        });
    }

    private function convertIsbn13to10(string $isbn13): ?string
    {
        if (strlen($isbn13) !== 13) return null;
        $isbn9 = substr($isbn13, 3, 9);
        $sum   = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $isbn9[$i] * (10 - $i);
        }
        $check = (11 - ($sum % 11)) % 11;
        return $isbn9 . ($check === 10 ? 'X' : (string) $check);
    }
};