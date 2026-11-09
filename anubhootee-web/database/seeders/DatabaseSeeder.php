<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * php artisan migrate:fresh --seed
     *
     * The admin login comes from ADMIN_EMAIL / ADMIN_PASSWORD in .env.
     * DemoContentSeeder adds placeholder sections, stories and pages (local testing only).
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@anubhootee.test')],
            ['name' => env('ADMIN_NAME', 'Anubhootee Team'), 'password' => env('ADMIN_PASSWORD', 'change-me-now')],
        );

        if (app()->environment('local')) {
            $this->call(DemoContentSeeder::class);

            // Depth-effect cutouts for the demo photos (needs Node.js; skipped quietly without it).
            try {
                $this->command?->info('Making depth-effect cutouts…');
                \Illuminate\Support\Facades\Artisan::call('anubhootee:depth', [], $this->command?->getOutput());
            } catch (\Throwable $e) {
                $this->command?->warn('Depth cutouts skipped: '.$e->getMessage());
            }
        }
    }
}
