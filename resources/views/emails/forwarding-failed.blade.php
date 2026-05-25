@component('mail::message')
# Application forwarding failed

A submission could not be delivered to the Aporta backend after all retries.

**Submission ID:** {{ $submissionId }}

**Reason:**
```
{{ $reason }}
```

The full payload is preserved in the `failed_jobs` table.

```
php artisan queue:failed
php artisan queue:retry <id>
```

@endcomponent
