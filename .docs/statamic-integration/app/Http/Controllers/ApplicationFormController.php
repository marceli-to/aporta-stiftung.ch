<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitApplicationRequest;
use App\Jobs\ForwardApplicationToBackend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class ApplicationFormController extends Controller
{
	/**
	 * Receive the public application form, validate, attach intake metadata,
	 * dispatch the forwarding job, and return the "danke" view.
	 *
	 * Visitor-facing password gating is enforced by the middleware stack on
	 * the route definition (see routes/web.example.php).
	 */
	public function store(SubmitApplicationRequest $request): RedirectResponse
	{
		$payload = $request->validated();

		// Intake metadata — frozen here; the same values are sent on every retry
		// because the payload is serialized once with the job.
		$payload['submission_id'] = (string) Str::ulid();
		$payload['submitted_meta'] = [
			'ip' => $request->ip(),
			'user_agent' => substr((string) $request->userAgent(), 0, 512),
			'submitted_at' => now()->toIso8601String(),
		];

		ForwardApplicationToBackend::dispatch($payload);

		// Optional: send the visitor confirmation email here. It's about
		// "we received your submission" — don't gate it on the forward
		// completing.
		// Mail::to($payload['main_applicant']['email'])->send(new ApplicationReceived);

		return redirect()
			->route('application.thanks')
			->with('submission_id', $payload['submission_id']);
	}

	public function thanks()
	{
		return view('applications.thanks');
	}
}
