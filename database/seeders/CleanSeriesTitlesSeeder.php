<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Series;

class CleanSeriesTitlesSeeder extends Seeder
{
    public function run()
    {
        $series = Series::all();
        $count = 0;
        foreach ($series as $s) {
            $newTitle = preg_replace('/\s*Vol\.?\s*\d+\s*$/', '', $s->title);
            $newTitle = preg_replace('/\s*Volume\s*\d+\s*$/', '', $newTitle);
            if ($newTitle != $s->title) {
                $s->title = trim($newTitle);
                $s->save();
                $this->command->info("Updated: {$s->title}");
                $count++;
            }
        }
        $this->command->info("Done! Updated $count series.");
    }
}