# Statamic Integration — Drop-in Code Stubs

Companion to `docs/Statamic-Integration.md` in the backend repo. This directory contains ready-to-copy PHP and JS code for the Statamic-side changes needed to integrate with the new Aporta backend at `interessenten.aporta-stiftung.ch`.

Copy the directory wholesale into a working location, point an LLM (or a human) at it together with `docs/Statamic-Integration.md`, and have them merge the files into the Statamic project. The files here are skeletons matched to the integration contract — they are not Statamic-specific, so paths and namespaces may need slight adjustment.

---

## File map

```
statamic-integration/
├── README.md                                          ← you are here
├── config/
│   └── aporta.php                                     → Statamic: config/aporta.php
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── ApplicationFormController.php          → app/Http/Controllers/ApplicationFormController.php
│   │   └── Requests/
│   │       └── SubmitApplicationRequest.php           → app/Http/Requests/SubmitApplicationRequest.php
│   ├── Jobs/
│   │   └── ForwardApplicationToBackend.php            → app/Jobs/ForwardApplicationToBackend.php
│   └── Mail/
│       └── ForwardingFailed.php                       → app/Mail/ForwardingFailed.php
├── resources/
│   ├── js/
│   │   ├── composables/
│   │   │   └── useLookups.js                          → resources/js/composables/useLookups.js
│   │   └── ApplicationForm.skeleton.vue               → reference only; adapt Register.vue
│   └── views/
│       ├── applications/
│       │   └── thanks.blade.php                       → resources/views/applications/thanks.blade.php
│       └── emails/
│           └── forwarding-failed.blade.php            → resources/views/emails/forwarding-failed.blade.php
└── routes/
    └── web.example.php                                → merge into routes/web.php
```

---

## What each file does

### `config/aporta.php`
Holds the backend endpoint URLs, the bearer API key, and the engineering alert email. Companion `.env` keys are listed at the top of the file.

### `app/Jobs/ForwardApplicationToBackend.php`
The forwarding job. Serializes the full payload (so retries replay the exact same body, preserving `submission_id` and `submitted_at`), retries on 5xx and network errors with exponential backoff, fails immediately on 4xx, and emails engineering when it permanently fails.

### `app/Http/Requests/SubmitApplicationRequest.php`
Validates the incoming form payload before forwarding. Mirrors the backend's `StoreRequest` shape but is intentionally lenient on slug values (those are owned by the backend and revalidated there). Includes the inline "no dogs" check via a `not_regex` rule on `pets_description` — the backend does *not* re-validate this.

Phone numbers are normalized to E.164 in `prepareForValidation` using `libphonenumber`. Add the dep to Statamic if it's not there:

```
composer require giggsey/libphonenumber-for-php
```

### `app/Http/Controllers/ApplicationFormController.php`
Receives the validated form, attaches intake metadata (`submission_id`, `submitted_meta`), dispatches the job, and redirects to the "danke" view. Does **not** block on the forwarding job — the visitor sees the thank-you screen immediately.

### `app/Mail/ForwardingFailed.php` + view
The engineering alert email fired from `ForwardApplicationToBackend::failed()`.

### `resources/js/composables/useLookups.js`
Vue composable that fetches `/api/v1/lookups`. Exposes `loading`, `error`, `ready`, `load()`, and `activeOnly(key)`. Filters out entries with `active: false`. **The form must not render dropdowns until `ready` is true; if `error` is set, show the "momentan nicht verfügbar" state instead of falling back to bundled data.**

### `resources/js/ApplicationForm.skeleton.vue`
A reference skeleton showing how to wire `useLookups`, gate rendering, assemble the payload shape, and POST to the Laravel controller. **Don't drop this in as-is** — adapt the existing `Register.vue` using these patterns. The skeleton covers the structural pieces (lookups gate, payload shape, no-dogs check, CSRF, error mapping).

### `routes/web.example.php`
Route definitions for the form page, submission endpoint, and thank-you page. Merge into the project's `routes/web.php`. The visitor-facing password gate (whatever middleware Statamic currently uses for it) **stays in place** — don't remove or rename it.

### `resources/views/applications/thanks.blade.php`
Minimal thank-you view. Do **not** echo a reference number here; the backend assigns it asynchronously.

---

## Setup checklist (Statamic side)

1. **Copy files** into their target locations per the map above.
2. **Add `.env` entries** (see top of `config/aporta.php`).
3. **Install libphonenumber** if not already present: `composer require giggsey/libphonenumber-for-php`.
4. **Make sure the queue worker is running** in production — `php artisan queue:work` under supervisor/systemd, or Horizon. The default `sync` driver defeats the async forwarding model.
5. **Set the lookups URL on the JS side.** The composable reads `window.APORTA_LOOKUPS_URL`. Inject it in the layout:
   ```blade
   <script>window.APORTA_LOOKUPS_URL = @json(config('aporta.lookups_url'));</script>
   ```
6. **Adapt `Register.vue`** using the patterns in `ApplicationForm.skeleton.vue`. The field renames are documented in `docs/Statamic-Integration.md` §3.1.
7. **Run the testing checklist** in `docs/Statamic-Integration.md` §8 before pointing the form at production.

---

## Things this directory deliberately doesn't include

- **A full replacement for `Register.vue`.** The existing form is large and project-specific; reworking it field-by-field is the integration work itself.
- **The visitor-facing password gate.** That's an existing Statamic concern, untouched by this integration.
- **CORS configuration on the backend.** Already handled there.
- **Slug whitelists in the form request.** The backend owns slug values and revalidates everything; duplicating the list here would create silent drift when slugs are added or retired.

---

## When the contract changes

`docs/Statamic-Integration.md` in the backend repo is the source of truth. If the contract evolves, that doc is updated and the backend team notifies. Re-sync this directory against the doc when that happens.
