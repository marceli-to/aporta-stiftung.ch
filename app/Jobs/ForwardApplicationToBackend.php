<?php

namespace App\Jobs;

use App\Mail\ForwardingFailed;
use App\Support\ApplicationStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ForwardApplicationToBackend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 15;

    public int $timeout = 30;

    public function __construct(public array $payload) {}

    public function backoff(): array
    {
        return [30, 60, 120, 300, 900, 1800, 3600, 7200, 14400, 28800, 43200, 86400];
    }

    public function handle(ApplicationStore $store): void
    {
        $submissionId = $this->payload['submission_id'] ?? null;

        $response = Http::withToken(config('aporta.intake_api_key'))
            ->acceptJson()
            ->timeout(20)
            ->post(config('aporta.intake_url'), $this->payload);

        if ($response->status() === 201 || $response->status() === 200) {
            if ($submissionId) {
                $store->markForwarded($submissionId);
            }

            Log::info('Aporta intake forwarded', [
                'submission_id' => $submissionId,
                'status' => $response->status(),
                'reference_number' => $response->json('data.reference_number'),
            ]);

            return;
        }

        if ($response->clientError()) {
            $this->fail(new \RuntimeException(
                "Aporta backend rejected payload: {$response->status()} {$response->body()}"
            ));

            return;
        }

        throw new \RuntimeException(
            "Aporta backend transient error: {$response->status()} {$response->body()}"
        );
    }

    public function failed(Throwable $e): void
    {
        $submissionId = $this->payload['submission_id'] ?? 'unknown';

        if ($submissionId !== 'unknown') {
            app(ApplicationStore::class)->markFailed($submissionId);
        }

        Log::error('Aporta intake forwarding failed permanently', [
            'submission_id' => $submissionId,
            'message' => $e->getMessage(),
        ]);

        $alertEmail = config('aporta.failure_alert_email');
        if ($alertEmail) {
            Mail::to($alertEmail)->send(new ForwardingFailed($submissionId, $e->getMessage()));
        }
    }
}
