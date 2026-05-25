<?php
namespace App\Http\Controllers\Api;

use App\Http\Concerns\MatchesMasterPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterAuthRequest;
use App\Http\Requests\SubmitApplicationRequest;
use App\Jobs\ForwardApplicationToBackend;
use Illuminate\Support\Str;

class FormController extends Controller
{
	use MatchesMasterPassword;

	/**
	 * Password gate. Visitor enters the shared password to unlock the form.
	 * On success the response carries a token that the SPA echoes back on
	 * submit via the X-Form-Token header; SubmitApplicationRequest::authorize()
	 * verifies it so the form cannot be bypassed by hitting the API directly.
	 *
	 * FORM_MASTER_PASSWORD in .env acts as an override that is accepted
	 * here and on submit without consuming an entry from key.json.
	 */
	public function authenticate(RegisterAuthRequest $request)
	{
		$password = $request->input('password');

		if ($this->matchesMasterPassword($password)) {
			return response()->json(['token' => config('form.master_password')]);
		}

		$key = json_decode(\Storage::disk('local')->get('key.json'), true);

		foreach ($key as $k => $v) {
			if ($v['password'] == $password && $v['used'] == false) {
				return response()->json(['token' => $v['token']]);
			}
		}

		return response()->json(['error' => 'Unauthorized'], 401);
	}

	/**
	 * Receive the public application form, validate, attach intake metadata,
	 * dispatch the forwarding job, and return immediately. Token validation
	 * happens upstream in SubmitApplicationRequest::authorize().
	 */
	public function store(SubmitApplicationRequest $request)
	{
		$payload = $request->validated();

		$payload['submission_id'] = (string) Str::ulid();
		$payload['submitted_meta'] = [
			'ip' => $request->ip(),
			'user_agent' => substr((string) $request->userAgent(), 0, 512),
			'submitted_at' => now()->toIso8601String(),
		];

		ForwardApplicationToBackend::dispatch($payload);

		return response()->json([
			'submission_id' => $payload['submission_id'],
		], 200);
	}
}
