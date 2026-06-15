<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\SampleTransformer;
use Illuminate\Http\JsonResponse;

/**
 * Dev-only endpoint that returns a random sample from `.sample-data/`
 * transformed from the legacy schema (main_tenant_*, sub_tenant_*, …) to
 * the new nested SubmitApplicationRequest contract.
 *
 * Registered only when APP_ENV=local and FORM_DEV_SAMPLE=1. To remove the
 * feature entirely: set FORM_DEV_SAMPLE=0, or delete this file + the
 * routes/api.php entry + the autofill block in Register.vue.
 *
 * The legacy→new transform and anonymisation live in SampleTransformer, shared
 * with the `applications:export` seed exporter.
 */
class DevSampleController extends Controller
{
    public function show(SampleTransformer $transformer): JsonResponse
    {
        $dir = base_path('.sample-data');
        if (! is_dir($dir)) {
            return response()->json(['error' => '.sample-data directory not found'], 500);
        }

        $files = glob($dir.'/*.json') ?: [];
        if (empty($files)) {
            return response()->json(['error' => 'no sample files in .sample-data'], 500);
        }

        $file = $files[array_rand($files)];
        $legacy = json_decode((string) file_get_contents($file), true);
        if (! is_array($legacy)) {
            return response()->json(['error' => 'unreadable sample file'], 500);
        }

        $lookups = $transformer->fetchLookups();
        if ($lookups === null) {
            return response()->json(['error' => 'lookups fetch failed'], 502);
        }

        $payload = $transformer->transform($legacy, $lookups);
        $transformer->anonymise($payload);
        $payload['__source_file'] = basename($file);

        return response()->json($payload);
    }
}
