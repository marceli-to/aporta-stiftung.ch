<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterAuthRequest;
use App\Http\Requests\SubmitApplicationRequest;
use App\Jobs\ForwardApplicationToBackend;
use Illuminate\Support\Str;

class FormController extends Controller
{
    /**
     * Page-level password gate. Visitor enters the shared password to unlock the form UI.
     * The returned token is used by the SPA to gate rendering only; the submission endpoint
     * no longer validates it (the new backend uses a server-side bearer key instead).
     */
    public function authenticate(RegisterAuthRequest $request)
    {
        $password = $request->input('password');
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
     * dispatch the forwarding job, and return immediately.
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
