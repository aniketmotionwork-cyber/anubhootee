<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Generates depth-effect cutouts (the photo's subject on a transparent background)
 * for every section and story cover that doesn't have one yet.
 *
 *   php artisan anubhootee:depth            new covers only
 *   php artisan anubhootee:depth --force    redo all
 *
 * Needs Node.js (runs scripts/depth-cutouts.mjs). The first run downloads the
 * BiRefNet Lite model (about 200 MB) from Hugging Face.
 */
class GenerateDepthCutouts extends Command
{
    protected $signature = 'anubhootee:depth {--force : Regenerate cutouts that already exist}';

    protected $description = 'Create depth-effect subject cutouts for hero photos';

    public function handle(): int
    {
        $node = (new ExecutableFinder)->find('node');
        if (! $node) {
            $this->error('Node.js was not found. Run this on a computer with Node.js, then upload storage/app/public/depth.');

            return self::FAILURE;
        }

        $disk = Storage::disk('public');
        $records = collect()
            ->concat(Category::whereNotNull('cover')->get())
            ->concat(Post::whereNotNull('cover')->get())
            ->filter(fn ($r) => $this->option('force') || ! $r->cover_fg || ! $disk->exists($r->cover_fg));

        if ($records->isEmpty()) {
            $this->info('Every cover already has a cutout.');

            return self::SUCCESS;
        }

        // Several records can share one photo: process each photo once.
        $jobs = [];
        foreach ($records as $r) {
            if (! $disk->exists($r->cover)) {
                $this->warn("Missing photo for {$r->getTable()} #{$r->id}: {$r->cover}");

                continue;
            }
            $out = 'depth/'.pathinfo($r->cover, PATHINFO_FILENAME).'-'.substr(md5($r->cover), 0, 8).'.webp';
            $jobs[$r->cover] = $out;
        }

        $this->info('Finding subjects in '.count($jobs).' photo(s)…');
        $args = [$node, base_path('scripts/depth-cutouts.mjs')];
        foreach ($jobs as $in => $out) {
            $args[] = $disk->path($in);
            $args[] = $disk->path($out);
        }

        $results = [];
        $process = new Process($args, base_path(), null, null, 1800);
        $process->run(function ($type, $buffer) use (&$results) {
            foreach (preg_split('/\r?\n/', trim($buffer)) as $line) {
                $row = json_decode($line, true);
                if (is_array($row) && isset($row['in'])) {
                    $results[realpath($row['in']) ?: $row['in']] = $row;
                    $name = basename($row['in']);
                    isset($row['error'])
                        ? $this->error("  {$name}: {$row['error']}")
                        : $this->line("  {$name}: subject covers ".round($row['coverage'] * 100).'% of the photo');
                }
            }
        });

        if (! $process->isSuccessful() && ! $results) {
            $this->error(trim($process->getErrorOutput()) ?: 'The cutout script failed.');

            return self::FAILURE;
        }

        $saved = 0;
        foreach ($records as $r) {
            $row = $results[realpath($disk->path($r->cover)) ?: $disk->path($r->cover)] ?? null;
            if (! $row || isset($row['error'])) {
                continue;
            }
            $r->forceFill(['cover_fg' => $jobs[$r->cover], 'cover_fg_coverage' => $row['coverage']])->saveQuietly();
            $saved++;
        }

        $this->info("Done: {$saved} cover(s) updated.");

        return self::SUCCESS;
    }
}
