# Integration brief — Statamic-side changes for `interessenten.aporta-stiftung.ch`

Hand-off doc for the team / LLM working on the Statamic project (Statamic = Laravel + Vue, with the public Wohnungsinteressenten form embedded in a page).

A new backend at `interessenten.aporta-stiftung.ch` will own all application data and provide the staff back-office. The public Vue form stays where it is; this doc describes everything that has to change on the Statamic side to integrate.

---

## 1. Big-picture flow

```
Visitor  ─enters shared password─►  Statamic (password gate, unchanged)
              │
              │  ─fill form─►  POST to Statamic Laravel
                                       │
                                       │ validate + dispatch queued job
                                       ▼
                              show "danke" screen
                                       │
                                       │ (Laravel job, async)
                                       ▼
                   POST https://interessenten.aporta-stiftung.ch/api/v1/applications
                                       │
                                       ▼
                   Our backend stores it, notifies staff
```

Three key shifts from the existing setup:

1. **The visitor-facing password gate stays.** It's a Statamic-side access control and is unrelated to anything in this integration. Don't remove it, don't change it.
2. **Statamic is no longer the system of record for applications.** Once the queued job successfully forwards a submission, the data lives in our backend. Statamic just brokers the handoff.
3. **The visitor's "danke" screen does NOT include a reference number.** Our backend issues that asynchronously. The confirmation should say something like "Ihre Anmeldung ist eingegangen, wir melden uns." (Or you keep your own internal reference and surface that — your call. Just don't promise our reference number.)

---

## 2. The contract with the new backend

### 2.1 Endpoint

```
POST https://interessenten.aporta-stiftung.ch/api/v1/applications
Content-Type: application/json
Authorization: Bearer <APORTA_INTAKE_API_KEY>
```

The API key is a static long random string. Get it from the backend team and store in `.env` as `APORTA_INTAKE_API_KEY`. Same key across retries; rotate by coordinating with the backend team (they hold the SHA-256 hash).

### 2.2 Request body

Full nested payload. Sample:

```jsonc
{
  // Idempotency key: generate once when the visitor submits, reuse on every retry.
  // ULID preferred, UUID v4 also accepted. 16–48 chars.
  "submission_id": "01HZN5K2X3M9P7R4Q8VWBC6TFG",

  // Captured by Statamic at the moment of submission. Same values on every retry.
  "submitted_meta": {
    "ip": "85.0.123.45",
    "user_agent": "Mozilla/5.0 …",
    "submitted_at": "2026-05-22T10:22:00Z"
  },

  "main_applicant": {
    "salutation": "herr",                       // slug: frau|herr|other
    "first_name": "Reda",
    "last_name": "Laâfif",
    "street": "Römergasse",
    "street_number": "5",
    "postal_code": "8001",
    "city": "Zürich",
    "birth_date": "2000-01-30",                 // ISO date
    "marital_status": "single",                 // slug
    "nationality": "CH",                        // ISO 3166-1 alpha-2 slug
    "place_of_origin": "Luzern",                // required iff nationality === "CH"
    "residence_permit": null,                   // required iff nationality !== "CH"
    "swiss_residence_since": null,              // required iff nationality !== "CH"
    "mobile_phone": "+41763694020",             // E.164
    "landline_phone": null,                     // E.164 or null
    "email": "redalaafif@gmail.com",            // lowercased
    "occupation": "Kommunikation / Fotografie & Student",
    "employment_status": "employed",            // slug
    "debt_enforcement_last_2y": false,

    "employer": {                                // required iff employment_status === "employed"
      "name": "Gelateria di Berna / Swiss",
      "workload_percent": 80,                    // 0–100
      "annual_income_bracket": "30k_40k"         // slug
    },

    "current_housing": {                         // always present
      "tenant_role": "main_tenant",              // slug: main_tenant|subtenant
      "terminated_by_landlord": false,
      "termination_reason": null,                // required iff terminated_by_landlord === true
      "landlord_name": "JUWO",
      "landlord_contact_person": "Francy Kamm",
      "landlord_phone": "+41442982047",
      "rent_duration": "less_than_1_year",       // slug
      "previous_landlord": "JUWO"                // required iff rent_duration === "less_than_1_year"
    }
  },

  "shares_apartment": false,
  "co_applicant": null,                          // or full block, see below

  "housing_wish": {
    "earliest_move_in": "2025-08-01",            // ISO date, >= today
    "max_gross_rent": "1600.00",                 // decimal string, 1200–20000 CHF
    "wants_balcony": true,                       // renamed from legacy rent_pref_nobalcony_yn
    "wants_elevator": false,                     // renamed from legacy rent_pref_noelevator
    "districts": ["kreis_4", "kreis_5"],         // slugs from /lookups
    "floors": ["hochparterre","floor_1","floor_2","floor_3","floor_4","floor_5","floor_6"],
    "rooms": ["rooms_2_0"]                       // slug + size resolves via /lookups
  },

  "household_info": {
    "total_persons": 1,                          // must equal adults_count + children_count
    "adults_count": 1,                           // >= 1
    "children_count": 0,
    "all_children_live_constantly": null,        // only if children_count > 0
    "plays_music": false,
    "musical_instruments": null,                 // required iff plays_music === true
    "has_pets": false,
    "pets_description": null,                    // required iff has_pets === true; rejected if matches /Hund|Hunde|Dog/i
    "remarks": "…"
  },

  "children": []                                  // exactly children_count entries
}
```

When a co-applicant is present, `co_applicant` is an object with the same fields as `main_applicant` **plus**:

```jsonc
"co_applicant": {
  "relationship_to_main": "life_partner",       // slug: spouse|registered_partner|life_partner|roommate
  "same_address_as_main": true,
  // street/street_number/postal_code/city OMITTED when same_address_as_main === true,
  // REQUIRED when same_address_as_main === false
  ...
}
```

Children when present:

```jsonc
"children": [
  { "position": 1, "birth_year": 2020 },
  { "position": 2, "birth_year": 2022 }
]
```

v1 supports at most one co-applicant. The backend will reject more.

### 2.3 Responses

| Status | Meaning | Action |
|---|---|---|
| `201 Created` | New application stored. Body: `{ data: { reference_number, status: "opened", opened_at } }`. | Mark the queued payload as done. |
| `200 OK` | Idempotent replay — this `submission_id` was already stored. Body identical to the original 201. | Treat as success. Mark queued payload done. Do not send a duplicate user-facing confirmation. |
| `401 Unauthorized` | Missing or malformed `Authorization` header. | Alert engineering. Do **not** retry. |
| `403 Forbidden` | Wrong API key. | Alert engineering. Do **not** retry. |
| `413 Payload Too Large` | Body > 128 KB. | Alert. Do **not** retry. |
| `422 Unprocessable Entity` | Validation failed (contract drift, missing required field, bad slug, etc). Body: `{ message, errors }`. | Log full payload + errors. Alert engineering. Do **not** retry — the payload is broken; retrying won't help. Preserve the payload for forensic review. |
| `429 Too Many Requests` | Rate limited. | Back off and retry. |
| `5xx` or network error | Transient. | Exponential backoff retry. |

### 2.4 Retry rules

- **Use the same `submission_id` on every retry.** Idempotency depends on this.
- **Use the same `submitted_meta.submitted_at`** — wall-clock at the original submission, not at retry time. Our `opened_at` is derived from it.
- Retry on `5xx`, `429`, and network failures. Exponential backoff with jitter (e.g. 30s, 2m, 10m, 1h, 6h, 24h). Cap attempts at ~24h total; after that, alert + park.
- Do **not** retry on `4xx`. They mean the request itself is wrong; replaying it won't change anything.

---

## 3. Form changes (Vue)

The current form lives at `Register.vue`. Field names need to align with the contract above. Reference the existing implementation for field-by-field renames; the table below covers the non-obvious ones.

### 3.1 Field renames

| Legacy field | New field | Notes |
|---|---|---|
| `salutation` (`Frau`/`Herr`/`Andere`) | `salutation` (`frau`/`herr`/`other`) | Send slug, not label. |
| `private_phone` | `mobile_phone` | E.164. |
| `work_phone` | `landline_phone` | E.164 or null. |
| `nationality` (German label `"Schweiz"`) | `nationality` (ISO alpha-2 `"CH"`) | See §4 (lookups). |
| `current_employer_name` | `employer.name` | Now nested under `employer`. |
| `current_rent_tenant_role` (`1`/`2`) | `current_housing.tenant_role` (`main_tenant`/`subtenant`) | |
| `current_rent_terminator` (`1`/`2`) | `current_housing.terminated_by_landlord` (bool) | |
| `current_renter_name` | `current_housing.landlord_name` | |
| `rent_pref_nobalcony_yn` | `housing_wish.wants_balcony` (bool) | **Name was misleading — value `1` meant "wants balcony". Don't invert.** |
| `rent_pref_noelevator` | `housing_wish.wants_elevator` (bool) | Same as above. |
| `rent_pref_min_start_date` | `housing_wish.earliest_move_in` | ISO date. |
| `accomodation_total_persons` (typo) | `household_info.total_persons` | |
| `accomodation_remarks` | `household_info.remarks` | |
| `accomodation_children_age_group` (concat blob) | `children[]` array of `{ position, birth_year }` | |
| `debt_enforcement_yn` | `debt_enforcement_last_2y` (bool) | |
| `sub_tenant_yn` + `sub_tenant_type` (multi-select 1–5) | Split: values 1–4 → `co_applicant.relationship_to_main`. Value 5 → `household_info.children_count > 0` + `children[]` rows. | The legacy "sub_tenant_type=5" (Kinder) is **not** a co-applicant; it's the children block. |

### 3.2 New fields the form must send

| Field | Source | Notes |
|---|---|---|
| `submitted_meta.ip` / `.user_agent` / `.submitted_at` | captured server-side by Statamic on submit | Don't send these from the browser. |
| `submission_id` | generated server-side by Statamic when the queued job row is created | ULID preferred. |

### 3.3 New fields the form must **not** send

- No `Authorization`, no password, no token. Browser-direct submission is not supported by the new backend. The form submits to Statamic; Statamic forwards.
- No reference number echoed on the "danke" screen (we don't have one yet at confirmation time).

---

## 4. Reference data (`/api/v1/lookups`)

Replaces all hardcoded dropdown values in the form with values fetched from the new backend.

### 4.1 Endpoint

```
GET https://interessenten.aporta-stiftung.ch/api/v1/lookups
```

- Anonymous (no auth header).
- CORS-allowed for the Statamic origin.
- Returns JSON keyed by category. **Every entry** has `slug`, `label`, `sort_order`, `active`; `rooms` adds `size` (float, in rooms). Each list is pre-sorted by `sort_order`. Example shape:

```jsonc
{
  "salutations":         [{ "slug": "frau", "label": "Frau",        "sort_order": 1, "active": true }, ...],
  "marital_statuses":    [{ "slug": "single", "label": "ledig",     "sort_order": 1, "active": true }, ...],
  "employment_statuses": [{ "slug": "employed", "label": "Angestellt", "sort_order": 1, "active": true }, ...],
  "residence_permits":   [{ "slug": "B", "label": "B",              "sort_order": 1, "active": true }, ...],
  "relationships":       [{ "slug": "spouse", "label": "Ehepartner*in", "sort_order": 1, "active": true }, ...],
  "tenant_roles":        [{ "slug": "main_tenant", "label": "Hauptmieter*in", "sort_order": 1, "active": true }, ...],
  "rent_durations":      [{ "slug": "less_than_1_year", "label": "Weniger als 1 Jahr", "sort_order": 1, "active": true }, ...],
  "income_brackets":     [{ "slug": "30k_40k", "label": "30'000–40'000", "sort_order": 3, "active": true }, ...],
  "districts":           [{ "slug": "kreis_4", "label": "Kreis 4",  "sort_order": 4, "active": true }, ...],
  "floors":              [{ "slug": "hochparterre", "label": "Hochparterre", "sort_order": 0, "active": true }, ...],
  "rooms":               [{ "slug": "rooms_2_0", "label": "2",      "sort_order": 4, "active": true, "size": 2.0 }, ...],
  "nationalities":       [{ "slug": "CH", "label": "Schweiz",       "sort_order": 1, "active": true }, ...],
  "statuses":            [{ "slug": "opened", "label": "Eröffnet",  "sort_order": 1, "active": true }, ...]
}
```

The form should filter `active: false` entries out of dropdowns. Existing applications can still reference inactive slugs (they're never deleted, only retired from new selection).

### 4.2 Caching & fetch behaviour

- Response carries `ETag` + `Cache-Control: public, max-age=86400, stale-while-revalidate=86400`.
- Form fetches on mount, sends `If-None-Match` on subsequent loads (browser handles automatically). 304 means use the cached body.
- Use `fetch(url, { credentials: 'omit' })` — this endpoint is anonymous; sending cookies will break CORS.

### 4.3 Failure mode

If the `/lookups` fetch fails, **do not render the form**. Show an error state instead:

> "Das Anmeldeformular ist momentan nicht verfügbar. Bitte versuchen Sie es in ein paar Minuten erneut."

Reasoning:

- A bundled-at-build-time fallback list creates a silent half-broken state: the form renders with stale slugs, the visitor submits, and our backend rejects the payload with 422 because a slug got deprecated. The visitor is confused and the failure is invisible until someone reads the failed_jobs queue.
- Failing loudly when the backend is down is monitorable. Half-working forms are not.
- Returning visitors within the 24h browser cache window keep working even during a brief backend outage — that's the browser's HTTP cache doing its job, no extra code needed. We rely on cache for resilience, not on a bundled snapshot.

While the fetch is in flight on first render, show a loading state — the same UX as any other API-dependent component. Don't render dropdowns with placeholder options.

### 4.4 Append-only contract

We never rename slugs once published. We may:

- Add new slugs (form sees them after next fetch).
- Mark old slugs `active: false` (filter them out of dropdowns in the form; existing applications retain them).

---

## 5. Validation

Validate client-side for UX, server-side (Statamic Laravel) for correctness. Our backend re-validates everything regardless.

### 5.1 Conditional rules

| Rule | Trigger |
|---|---|
| `place_of_origin` required | `nationality === "CH"` |
| `residence_permit` + `swiss_residence_since` required | `nationality !== "CH"` |
| `employer` block required | `employment_status === "employed"` |
| `termination_reason` required | `terminated_by_landlord === true` |
| `previous_landlord` required | `rent_duration === "less_than_1_year"` |
| `musical_instruments` required | `plays_music === true` |
| `pets_description` required | `has_pets === true` |
| Co-applicant address fields required | `co_applicant.same_address_as_main === false` |
| `children[]` length === `household_info.children_count` | always |

**Note on pets / no-dogs policy:** the house rule is "no dogs". This is enforced **on the form side only**, with a friendly inline error when the visitor types something matching `/\b(Hund|Hunde|Dog)\b/i` in `pets_description`. Our backend does **not** re-validate this — once the payload arrives, `pets_description` is accepted as free text. The form must surface this rule clearly so visitors don't submit and get a confusing rejection. If the rule is ever extended (e.g. cats, exotic pets), update the form only.

### 5.2 Format & range rules

| Field | Rule |
|---|---|
| `postal_code` | `/^\d{4}$/` for Swiss; foreign formats OK for non-Swiss applicants. |
| `mobile_phone` / `landline_phone` | Normalize to E.164 with CH default region. |
| `email` | RFC-valid; lowercase before sending. |
| `birth_date` | At least 16 years ago. |
| `max_gross_rent` | Number, 1200 ≤ x ≤ 20000. Send as decimal string `"1600.00"`. |
| `earliest_move_in` | ISO date, ≥ today at submit time. |
| `total_persons` | === `adults_count + children_count`; `adults_count >= 1`. |
| `districts` / `floors` / `rooms` | At least one entry each. |

---

## 6. Statamic Laravel side — what changes

Use Laravel's built-in queue + jobs. No custom queue table needed — `$tries`, `backoff()`, `failed()`, and the standard `failed_jobs` table cover everything.

### 6.1 The form controller

Whatever controller the form currently posts to:

1. Stays behind the existing password gate (the visitor-facing access control is unchanged).
2. Validate the payload (FormRequest with the rules in §5).
3. Build a `$payload` array that contains the full body to send to our backend, including:
   - `submission_id` — generate now with `(string) Str::ulid()`.
   - `submitted_meta` — `{ ip: $request->ip(), user_agent: $request->userAgent(), submitted_at: now()->toIso8601String() }`.
   - All the form fields from §2.2.
4. Dispatch the job: `ForwardApplicationToBackend::dispatch($payload)`.
5. (Optional) Trigger the applicant confirmation email here too — see §6.3.
6. Return the "danke" view immediately. Do not block on the forward.

### 6.2 The forwarding job

```php
class ForwardApplicationToBackend implements ShouldQueue
{
    use Queueable;

    public int $tries = 15;
    public int $timeout = 30;

    public function __construct(public array $payload) {}

    public function backoff(): array
    {
        // exponential with jitter: ~30s, 1m, 2m, 5m, 15m, 30m, 1h, 2h, 4h, 8h, 12h, 24h, …
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
            return;
        }

        // 4xx = permanent failure. Push to failed_jobs immediately, do not retry.
        if ($response->clientError()) {
            $this->fail(new \RuntimeException(
                "Backend rejected payload: {$response->status()} {$response->body()}"
            ));
            return;
        }

        // 5xx / network → throw so Laravel retries with backoff.
        throw new \RuntimeException(
            "Backend transient error: {$response->status()} {$response->body()}"
        );
    }

    public function failed(\Throwable $e): void
    {
        // Notify engineering. The serialized job in failed_jobs still has the payload.
        Mail::to(config('aporta.failure_alert_email'))
            ->send(new ForwardingFailed($this->payload['submission_id'] ?? 'unknown', $e));
    }
}
```

Why this is enough:

- **Idempotency on retry** is automatic: the job is serialized once (with `submission_id` baked in) and the same body is sent on every attempt. No state on our side to track.
- **`submitted_at` stays frozen** because it's set once in the controller and serialized with the job. A retry 6 hours later still sends the original wall-clock.
- **Failed jobs** (4xx or retries exhausted) land in `failed_jobs` with the full serialized payload — `php artisan queue:failed` shows them; `php artisan queue:retry <id>` replays one after a fix.
- **Payload retention**: the payload lives in the `jobs` table until the forward succeeds, then it's gone. For permanently-failed jobs, prune `failed_jobs` after N days via `php artisan queue:prune-failed --hours=…` on a schedule.

### 6.2.1 Queue driver

Use `database` driver as a minimum (works without extra infrastructure) or `redis` if you have it. Don't use `sync` — that defeats the async point. Make sure a queue worker is actually running (`php artisan queue:work` under supervisor / systemd / Horizon).

### 6.3 Applicant confirmation email

Sent by Statamic, not by the new backend.

- Triggered immediately after the visitor submits (don't wait for the forward to succeed — the email is about "we received your submission", not "the backend has stored it").
- Subject suggestion: "Vielen Dank für Ihre Anmeldung".
- Body: confirmation text, contact for questions. No reference number (we don't have one yet).

### 6.4 Failure alerting

The job's `failed()` method (§6.2) fires on any 4xx and after retries are exhausted. Send an email/Slack/whatever to engineering with the `submission_id` and the exception. The serialized payload is preserved in `failed_jobs` and inspectable via:

```
php artisan queue:failed
php artisan tinker
>>> DB::table('failed_jobs')->find($id)->payload  // JSON-decode to inspect
```

After a fix, replay individual jobs with `php artisan queue:retry <id>` or all of them with `queue:retry all`.

### 6.5 Environment variables

Add to Statamic `.env`:

```
APORTA_INTAKE_URL=https://interessenten.aporta-stiftung.ch/api/v1/applications
APORTA_INTAKE_API_KEY=<long-random-string-from-backend-team>
APORTA_LOOKUPS_URL=https://interessenten.aporta-stiftung.ch/api/v1/lookups
APORTA_FAILURE_ALERT_EMAIL=engineering@…
```

---

## 7. Things to NOT do

- **Don't** call `/applications` directly from the browser. The API key would leak. Always go via Statamic Laravel.
- **Don't** call a `POST /applications/authenticate` endpoint on our backend. It doesn't exist. Earlier specs proposed a shared-password + one-shot-token dance between Statamic and our backend; that's gone, replaced by the static API key. **The visitor-facing password gate on the Statamic side is a separate concern and stays unchanged.**
- **Don't** add a custom queue table or persist payloads in your own DB for the forward. Laravel's queue handles this — the job carries the payload, `failed_jobs` retains permanently-failed ones, and pruning is a one-line scheduler entry.
- **Don't** regenerate `submission_id` on retry. The whole point of idempotency depends on stability.
- **Don't** update `submitted_at` to "now" on retry. The wall-clock from the original submission is what drives our `opened_at`.
- **Don't** show our reference number on the visitor confirmation screen. We assign it asynchronously and it isn't available at confirmation time.
- **Don't** retry on 4xx. The payload is broken; replaying won't help, only adds noise.
- **Don't** send cookies on the `/lookups` fetch (`credentials: 'omit'`). Cookies break the CORS policy on the anonymous endpoint.
- **Don't** invert `wants_balcony` / `wants_elevator` when migrating from `rent_pref_nobalcony_yn` / `rent_pref_noelevator`. The legacy names were misleading; the values were already "wants X" semantics.

---

## 8. Testing checklist

Before shipping:

- [ ] Submit a fresh form against the new backend (dev). Verify the `ForwardApplicationToBackend` job runs, returns 201, and the application appears in the new back office.
- [ ] Manually re-dispatch the same job (e.g. via `tinker`: `ForwardApplicationToBackend::dispatch($previousPayload)`). Verify second call returns 200 with identical body and no duplicate appears in the back office.
- [ ] Take the new backend offline. Submit a form. Verify:
  - the visitor sees the "danke" screen normally,
  - the job sits in the `jobs` table and the queue worker logs retry attempts with exponential backoff,
  - when the backend comes back up, the next attempt succeeds and the job is removed from `jobs`.
- [ ] Take `/lookups` offline (and clear the browser cache to simulate a first-time visitor). Verify the form **refuses to render** and shows the "momentan nicht verfügbar" error message.
- [ ] Take `/lookups` offline but with a recent successful fetch in browser cache. Verify the form still renders for that visitor (HTTP cache resilience). Confirm new visitors during the same window see the error message.
- [ ] Submit with an invalid payload (e.g. unknown nationality slug). Verify the 422 response, the job lands in `failed_jobs`, and the engineering alert fires.
- [ ] Verify the visitor-facing password gate still blocks unauthenticated access to the form. (Sanity check — this should be untouched, but confirm nothing broke during refactoring.)
- [ ] Verify CORS: open the form in the browser, watch the network panel for the `/lookups` request — should be 200 with `Access-Control-Allow-Origin` set to the Statamic origin.

---

## 9. Reference

Backend project: `interessenten.aporta-stiftung.ch` (Laravel 13, MySQL).

For the full API surface (back-office endpoints, etc.) see the backend repo's `docs/requirements/02-api-surface.md`. Public-form integration is fully covered by this document — you shouldn't need anything else.

If the contract changes, the backend team will update this file and notify. Anything not described here is out of scope for v1.

---

## 10. Drop-in code stubs

The companion `statamic-integration/` directory in the backend repo contains ready-to-copy code for the Statamic side: config, form request, controller, forwarding job, failure mailable, and a Vue composable for fetching `/lookups`. See its own `README.md` for the file map and copy targets.
