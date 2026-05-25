<?php

namespace App\Jobs;

use App\Mail\ForwardingFailed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Forwards a validated application payload to the Aporta backend.
 *
 * The full payload — including submission_id and submitted_at — is serialized
 * with the job, so retries replay the exact same body. This is what makes the
 * backend's idempotency guarantee work.
 *
 * Retry policy: 5xx and network errors trigger exponential backoff; 4xx fails
 * the job immediately (the payload is broken — retrying won't help).
 */
class ForwardApplicationToBackend implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public int $tries = 15;

	public int $timeout = 30;

	public function __construct(public array $payload) {}

	/**
	 * Exponential backoff with jitter (seconds): ~30s, 1m, 2m, 5m, 15m, 30m, 1h, 2h, 4h, 8h, 12h, 24h.
	 * After that the job is marked failed and the engineering alert fires.
	 *
	 * @return array<int, int>
	 */
	public function backoff(): array
	{
		return [30, 60, 120, 300, 900, 1800, 3600, 7200, 14400, 28800, 43200, 86400];
	}

	public function handle(): void
	{
		$response = Http::withToken(config('aporta.intake_api_key'))
			->acceptJson()
			->timeout(20)
			->post(config('aporta.intake_url'), $this->payload);

		// 201 = first submission, 200 = idempotent replay. Both are success.
		if ($response->status() === 201 || $response->status() === 200) {
			Log::info('Aporta intake forwarded', [
				'submission_id' => $this->payload['submission_id'] ?? null,
				'status' => $response->status(),
				'reference_number' => $response->json('data.reference_number'),
			]);

			return;
		}

		// 4xx = permanent failure. Push to failed_jobs immediately, do not retry.
		if ($response->clientError()) {
			$this->fail(new \RuntimeException(
				"Aporta backend rejected payload: {$response->status()} {$response->body()}"
			));

			return;
		}

		// 5xx / network → throw so Laravel retries with backoff().
		throw new \RuntimeException(
			"Aporta backend transient error: {$response->status()} {$response->body()}"
		);
	}

	public function failed(Throwable $e): void
	{
		$submissionId = $this->payload['submission_id'] ?? 'unknown';

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
