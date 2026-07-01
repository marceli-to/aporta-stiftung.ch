<?php

namespace App\Console\Commands;

use App\Support\SampleTransformer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Batch-transform every legacy sample in `.sample-data/` into the new nested
 * application contract and write one file per record — the same shape the
 * backend receives at POST /api/v1/applications. Use the output to seed the
 * new application's database.
 *
 *   php artisan applications:export
 *   php artisan applications:export --raw --output=seed/real
 */
class ApplicationsExport extends Command
{
    protected $signature = 'applications:export
        {--output=seed/applications : Target directory, relative to storage/app, for one {submission_id}.json per record}
        {--raw : Keep real applicant data (skip PII anonymisation)}
        {--limit= : Only export the first N samples (handy for a quick smoke test)}';

    protected $description = 'Transform every .sample-data/ legacy sample into the new application contract and write seed files.';

    public function handle(SampleTransformer $transformer): int
    {
        $dir = base_path('.sample-data');
        if (! is_dir($dir)) {
            $this->error('.sample-data directory not found.');

            return self::FAILURE;
        }

        $files = glob($dir.'/*.json') ?: [];
        sort($files);
        if (empty($files)) {
            $this->error('No sample files in .sample-data.');

            return self::FAILURE;
        }

        if ($limit = $this->option('limit')) {
            $files = array_slice($files, 0, (int) $limit);
        }

        $this->info('Fetching lookups from '.config('aporta.lookups_url').' …');
        $lookups = $transformer->fetchLookups();
        if ($lookups === null) {
            $this->error('Lookups fetch failed — is the backend reachable? Check APORTA_LOOKUPS_URL.');

            return self::FAILURE;
        }

        $anonymise = ! $this->option('raw');
        $outputDir = trim((string) $this->option('output'), '/');
        $disk = Storage::disk('local');

        $written = 0;
        $skipped = 0;
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        foreach ($files as $file) {
            $bar->advance();

            $legacy = json_decode((string) file_get_contents($file), true);
            if (! is_array($legacy)) {
                $skipped++;
                continue;
            }

            $payload = $transformer->transform($legacy, $lookups);
            if ($anonymise) {
                $transformer->anonymise($payload);
            }

            $payload['submission_id'] = (string) Str::ulid();
            $payload['submitted_meta'] = [
                'ip' => '127.0.0.1',
                'user_agent' => 'applications:export (seed)',
                'submitted_at' => $this->submittedAt(basename($file)),
            ];

            $disk->put(
                "{$outputDir}/{$payload['submission_id']}.json",
                json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
            $written++;
        }

        $bar->finish();
        $this->newLine(2);

        $path = storage_path('app/'.$outputDir);
        $this->info("Wrote {$written} record(s) to {$path}".($skipped ? " ({$skipped} unreadable, skipped)" : '').'.');
        $this->line($anonymise
            ? 'PII was anonymised. Use --raw to keep real applicant data.'
            : 'Real applicant data was kept (--raw). Files contain PII.');

        return self::SUCCESS;
    }

    /**
     * Sample filenames end in `-DD-MM-YYYY-HH-MM-SS.json`. Recover that as the
     * submission timestamp so seed data keeps a realistic date spread; fall
     * back to now() when the pattern is absent.
     */
    private function submittedAt(string $filename): string
    {
        if (preg_match('/-(\d{2})-(\d{2})-(\d{4})-(\d{2})-(\d{2})-(\d{2})\.json$/', $filename, $m)) {
            [, $d, $mo, $y, $h, $min, $s] = $m;

            return \Illuminate\Support\Carbon::create((int) $y, (int) $mo, (int) $d, (int) $h, (int) $min, (int) $s)
                ->toIso8601String();
        }

        return now()->toIso8601String();
    }
}
