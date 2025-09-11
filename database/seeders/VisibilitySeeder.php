<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visibility;

class VisibilitySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['public','followers','draft'] as $code) {
            Visibility::firstOrCreate(['code' => $code]);
        }
    }
}