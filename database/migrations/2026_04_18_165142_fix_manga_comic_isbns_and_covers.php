<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add isbn_13 and isbn_10 columns if they don't exist yet
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'isbn_13')) {
                $table->string('isbn_13', 20)->nullable()->after('isbn');
            }
            if (!Schema::hasColumn('books', 'isbn_10')) {
                $table->string('isbn_10', 20)->nullable()->after('isbn_13');
            }
        });

        // Add payment_method to payments if not exists
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('status');
            }
        });

        // Add subscription tracking to users if not exists
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

        // Populate isbn_13 and isbn_10 from existing isbn for regular books
        foreach (DB::table('books')->get() as $book) {
            $raw = preg_replace('/[^0-9X]/', '', $book->isbn ?? '');
            if (strlen($raw) === 13 && is_numeric($raw)) {
                $isbn10 = $this->isbn13to10($raw);
                DB::table('books')->where('id', $book->id)->update([
                    'isbn_13' => $raw,
                    'isbn_10' => $isbn10,
                ]);
            }
        }

        // Real ISBN data for manga (English published editions)
        $mangaIsbns = [
            'MANGA-NAR-001'   => ['isbn_13' => '9781569319000', 'isbn_10' => '1569319006',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781569319000-L.jpg'],
            'MANGA-OP-001'    => ['isbn_13' => '9781569319017', 'isbn_10' => '1569319014',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781569319017-L.jpg'],
            'MANGA-DB-001'    => ['isbn_13' => '9781569319208', 'isbn_10' => '1569319200',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781569319208-L.jpg'],
            'MANGA-AOT-001'   => ['isbn_13' => '9781612620244', 'isbn_10' => '1612620248',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781612620244-L.jpg'],
            'MANGA-DS-001'    => ['isbn_13' => '9781974700523', 'isbn_10' => '1974700526',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781974700523-L.jpg'],
            'MANGA-MHA-001'   => ['isbn_13' => '9781421582696', 'isbn_10' => '1421582694',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781421582696-L.jpg'],
            'MANGA-DN-001'    => ['isbn_13' => '9781421501680', 'isbn_10' => '1421501686',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781421501680-L.jpg'],
            'MANGA-FMA-001'   => ['isbn_13' => '9781591169208', 'isbn_10' => '1591169208',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591169208-L.jpg'],
            'MANGA-BL-001'    => ['isbn_13' => '9781591164920', 'isbn_10' => '1591164923',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591164920-L.jpg'],
            'MANGA-TG-001'    => ['isbn_13' => '9781421580371', 'isbn_10' => '1421580373',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781421580371-L.jpg'],
            'MANGA-SAO-001'   => ['isbn_13' => '9780316371247', 'isbn_10' => '0316371246',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780316371247-L.jpg'],
            'MANGA-HXH-001'   => ['isbn_13' => '9781591167969', 'isbn_10' => '1591167965',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591167969-L.jpg'],
            'MANGA-VS-001'    => ['isbn_13' => '9781612624204', 'isbn_10' => '1612624200',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781612624204-L.jpg'],
            'MANGA-BRSRK-001' => ['isbn_13' => '9781593070205', 'isbn_10' => '1593070209',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781593070205-L.jpg'],
            'MANGA-JJK-001'   => ['isbn_13' => '9781974710027', 'isbn_10' => '1974710025',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781974710027-L.jpg'],
            'MANGA-CSM-001'   => ['isbn_13' => '9781974709939', 'isbn_10' => '1974709930',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781974709939-L.jpg'],
            'MANGA-SXF-001'   => ['isbn_13' => '9781974717897', 'isbn_10' => '1974717895',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781974717897-L.jpg'],
            'MANGA-BE-001'    => ['isbn_13' => '9781421540320', 'isbn_10' => '1421540320',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781421540320-L.jpg'],
            'MANGA-FT-001'    => ['isbn_13' => '9780345501066', 'isbn_10' => '034550106X',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780345501066-L.jpg'],
            'MANGA-BC-001'    => ['isbn_13' => '9781421587189', 'isbn_10' => '1421587181',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781421587189-L.jpg'],
            'MANGA-HQ-001'    => ['isbn_13' => '9781421587660', 'isbn_10' => '1421587661',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781421587660-L.jpg'],
            'MANGA-SD-001'    => ['isbn_13' => '9781421519432', 'isbn_10' => '1421519437',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781421519432-L.jpg'],
            'MANGA-NGE-001'   => ['isbn_13' => '9781569317785', 'isbn_10' => '1569317785',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781569317785-L.jpg'],
            'MANGA-CB-001'    => ['isbn_13' => '9781591161578', 'isbn_10' => '1591161576',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591161578-L.jpg'],
            'MANGA-RH-001'    => ['isbn_13' => '9781591161141', 'isbn_10' => '1591161141',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591161141-L.jpg'],
            'MANGA-CCS-001'   => ['isbn_13' => '9781591164456', 'isbn_10' => '1591164456',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591164456-L.jpg'],
            'MANGA-SM-001'    => ['isbn_13' => '9781935429746', 'isbn_10' => '1935429744',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781935429746-L.jpg'],
            'MANGA-YYH-001'   => ['isbn_13' => '9781591164579', 'isbn_10' => '1591164575',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781591164579-L.jpg'],
            'MANGA-FB-001'    => ['isbn_13' => '9781931514972', 'isbn_10' => '1931514976',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781931514972-L.jpg'],
            'MANGA-OHSHC-001' => ['isbn_13' => '9781421500423', 'isbn_10' => '1421500426',  'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781421500423-L.jpg'],
        ];

        // Real ISBN data for comics (English published editions)
        $comicIsbns = [
            'COMIC-SPIDEY-001' => ['isbn_13' => '9780785107316', 'isbn_10' => '0785107312', 'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780785107316-L.jpg'],
            'COMIC-BAT-001'    => ['isbn_13' => '9781563893599', 'isbn_10' => '1563893592', 'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781563893599-L.jpg'],
            'COMIC-WTCH-001'   => ['isbn_13' => '9781779501127', 'isbn_10' => '177950112X', 'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780930289232-L.jpg'],
            'COMIC-VFV-001'    => ['isbn_13' => '9781401208417', 'isbn_10' => '1401208414', 'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781401208417-L.jpg'],
            'COMIC-SAND-001'   => ['isbn_13' => '9781401284770', 'isbn_10' => '1401284779', 'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781401284770-L.jpg'],
            'COMIC-MAUS-001'   => ['isbn_13' => '9780679748403', 'isbn_10' => '0679748407', 'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780679748403-L.jpg'],
            'COMIC-PRSP-001'   => ['isbn_13' => '9780375714573', 'isbn_10' => '037571457X', 'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780375714573-L.jpg'],
            'COMIC-SAGA-001'   => ['isbn_13' => '9781607066019', 'isbn_10' => '1607066017', 'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781607066019-L.jpg'],
            'COMIC-XMN-001'    => ['isbn_13' => '9780785163336', 'isbn_10' => '078516333X', 'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780785163336-L.jpg'],
            'COMIC-DKR-001'    => ['isbn_13' => '9781401207564', 'isbn_10' => '1401207561', 'cover_image' => 'https://covers.openlibrary.org/b/isbn/9781401207564-L.jpg'],
        ];

        foreach (array_merge($mangaIsbns, $comicIsbns) as $fakeIsbn => $data) {
            DB::table('books')->where('isbn', $fakeIsbn)->update([
                'isbn'        => $data['isbn_13'],   // replace fake isbn with real isbn-13
                'isbn_13'     => $data['isbn_13'],
                'isbn_10'     => $data['isbn_10'],
                'cover_image' => $data['cover_image'],
            ]);
        }
    }

    public function down(): void
    {
        // Remove added columns
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'isbn_13')) $table->dropColumn('isbn_13');
            if (Schema::hasColumn('books', 'isbn_10')) $table->dropColumn('isbn_10');
        });
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_method')) $table->dropColumn('payment_method');
        });
        Schema::table('users', function (Blueprint $table) {
            foreach (['subscription_plan','subscription_amount','subscription_paid_at'] as $col) {
                if (Schema::hasColumn('users', $col)) $table->dropColumn($col);
            }
        });
    }

    private function isbn13to10(string $isbn13): ?string
    {
        if (strlen($isbn13) !== 13) return null;
        $nine = substr($isbn13, 3, 9);
        $sum  = 0;
        for ($i = 0; $i < 9; $i++) $sum += (int)$nine[$i] * (10 - $i);
        $check = (11 - ($sum % 11)) % 11;
        return $nine . ($check === 10 ? 'X' : (string)$check);
    }
};