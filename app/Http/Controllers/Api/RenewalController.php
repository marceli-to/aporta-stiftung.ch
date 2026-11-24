<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RenewApplicationRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Form in renewal mode: proxies the prefill and the update to the
 * Interessenten backend. Unlike new applications, the update is forwarded
 * synchronously — the applicant needs to know right away whether it was
 * saved or whether the link has expired.
 */
class RenewalController extends Controller
{
	public function show(string $token): JsonResponse
	{
		return $this->forward('get', $token);
	}

	public function update(RenewApplicationRequest $request, string $token): JsonResponse
	{
		return $this->forward('put', $token, $request->validated() + ['co_applicant' => null]);
	}

	private function forward(string $method, string $token, array $payload = []): JsonResponse
	{
		if (! preg_match('/^\d+\.\d+\.[a-f0-9]{40}$/', $token)) {
			return $this->noStore(response()->json(['error' => 'invalid'], 410));
		}

		try {
			$response = Http::withToken(config('aporta.intake_api_key'))
				->acceptJson()
				->timeout(20)
				->{$method}(rtrim(config('aporta.renewals_url'), '/').'/'.$token, $payload);
		} catch (ConnectionException $e) {
			Log::error('Aporta renewal: backend unreachable', ['message' => $e->getMessage()]);

			return $this->unavailable();
		}

		return match (true) {
			$response->successful() => $this->noStore(response()->json($response->json(), 200)),
			$response->status() === 410 => $this->noStore(response()->json(['error' => 'invalid'], 410)),
			$response->status() === 422 => response()->json([
				'message' => 'Validation failed',
				'errors' => $response->json('errors', []),
			], 422),
			default => $this->unavailable($response),
		};
	}

	private function unavailable(?Response $response = null): JsonResponse
	{
		if ($response) {
			Log::error('Aporta renewal: backend error', ['status' => $response->status(), 'body' => $response->body()]);
		}

		return response()->json(['error' => 'unavailable'], 503);
	}

	/**
	 * The responses carry personal data: keep them out of every cache.
	 */
	private function noStore(JsonResponse $response): JsonResponse
	{
		return $response->header('Cache-Control', 'no-store, private');
	}
}
