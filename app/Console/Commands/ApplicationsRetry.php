<?php

namespace App\Console\Commands;

use App\Jobs\ForwardApplicationToBackend;
use App\Support\ApplicationStore;
use Illuminate\Console\Command;

class ApplicationsRetry extends Command
{
    protected $signature = 'applications:retry {--id= : Submission ID to retry directly (skips the picker)}';

    protected $description = 'Re-dispatch a failed application submission to the backend.';

    public function handle(ApplicationStore $store): int
    {
        $id = $this->option('id');

        if (! $id) {
            $id = $this->pickFailed($store);
            if (! $id) {
                return self::SUCCESS;
            }
        }

        $payload = $store->load($id);
        if (! $payload) {
            $this->error("Submission {$id} not found in storage/app/json.");

            return self::FAILURE;
        }

        ForwardApplicationToBackend::dispatch($payload);
        $this->info("Re-dispatched submission {$id}.");

        return self::SUCCESS;
    }

    private function pickFailed(ApplicationStore $store): ?string
    {
        $failed = $store->listFailed();

        if (empty($failed)) {
            $this->info('No failed submissions to retry.');

            return null;
        }

        $labels = [];
        foreach ($failed as $entry) {
            $sid = $entry['submission_id'] ?? '?';
            $name = trim(
                ($entry['main_applicant']['first_name'] ?? '').' '.
                ($entry['main_applicant']['last_name'] ?? '')
            );
            $when = $entry['submitted_meta']['submitted_at'] ?? '?';
            $labels[$sid] = trim("{$sid} — ".($name !== '' ? $name : '(no name)')." ({$when})");
        }

        $choice = $this->choice('Select a failed application to retry:', array_values($labels));

        return array_search($choice, $labels, true) ?: null;
    }
}
