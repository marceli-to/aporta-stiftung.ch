# Todo — Aporta backend integration

Working branch: `feature/aporta-backend-integration`

Source of truth for the contract: `.docs/Statamic-Integration.md`. Drop-in stubs: `.docs/statamic-integration/`.

---

## Decisions taken

- `RegisterExisting.vue` (existing-tenants form) is being **removed entirely** — no replacement on the new backend yet, so the route, FormRequest, Vue file, XML actions and `key-existing.json` all go.
- The page-level password gate (the current `authenticate()` flow against `key.json`) **stays for now**. Only the API-side token check on the submission POST is removed.
- `giggsey/libphonenumber-for-php` will be installed for E.164 phone normalization.
- Framework upgrade (Laravel 10 → 11/12, Statamic 5 → latest) is **deferred** to a separate follow-up PR after this integration ships. See §4 below.

---

## 1. Backend (PHP / Laravel) — integration

### 1.1 New files
- [ ] `config/aporta.php` — backend URL, API key, lookups URL, alert email
- [ ] `app/Jobs/ForwardApplicationToBackend.php` — queued job
  - `$tries = 15`, `$timeout = 30`
  - `backoff()` with exponential schedule (30s → 24h)
  - Success on 200/201, fail-fast on 4xx via `$this->fail()`, throw on 5xx/network for retry
  - `failed()` sends engineering alert
- [ ] `app/Mail/ForwardingFailed.php` + `resources/views/emails/forwarding-failed.blade.php`
- [ ] `app/Http/Requests/SubmitApplicationRequest.php`
  - Nested payload validation (main_applicant, co_applicant, housing_wish, household_info, children[])
  - Conditional rules per `Statamic-Integration.md` §5.1
  - E.164 phone normalization via libphonenumber in `prepareForValidation`
  - Client-side `not_regex` "no dogs" check on `pets_description`

### 1.2 Controller (`app/Http/Controllers/Api/FormController.php`)
- [ ] Replace `store()` body:
  - Drop `validateToken($request->token)`
  - Build payload with `submission_id = (string) Str::ulid()`
  - Add `submitted_meta = { ip, user_agent, submitted_at: now()->toIso8601String() }`
  - Dispatch `ForwardApplicationToBackend::dispatch($payload)`
  - Return 200 immediately (do not block)
- [ ] Remove `authenticateExisting()` and `storeExisting()` methods
- [ ] Remove `validateToken()` helper (no longer needed once `store()` doesn't call it; `authenticate()` keeps its own inline check)
- [ ] Keep `authenticate()` as-is (page-level gate)

### 1.3 Deletions
- [ ] `app/Actions/CreateXml.php`
- [ ] `app/Actions/CreateExistingXml.php`
- [ ] `app/Actions/SubmitXml.php`
- [ ] `app/Actions/SubmitXmlExisting.php`
- [ ] `app/Http/Requests/RegisterExistingStoreRequest.php`
- [ ] `app/Http/Requests/RegisterStoreRequest.php` (replaced by `SubmitApplicationRequest`)
- [ ] `resources/js/form/RegisterExisting.vue`
- [ ] `storage/app/key-existing.json` (delete on production deploy, not in repo)

### 1.4 Routes (`routes/api.php`)
- [ ] Remove `/form/register-existing/authenticate`
- [ ] Remove `/form/register-existing`
- [ ] Keep `/form/register/authenticate` (page gate)
- [ ] Repoint `/form/register` to the new flow (same URL, new controller behavior)

### 1.5 Composer / env
- [ ] `composer require giggsey/libphonenumber-for-php`
- [ ] `.env.example` and prod `.env`: add
  - `APORTA_INTAKE_URL=https://interessenten.aporta-stiftung.ch/api/v1/applications`
  - `APORTA_INTAKE_API_KEY=<from backend team>`
  - `APORTA_LOOKUPS_URL=https://interessenten.aporta-stiftung.ch/api/v1/lookups`
  - `APORTA_FAILURE_ALERT_EMAIL=<engineering address>`
  - `QUEUE_CONNECTION=database` (if not already)
- [ ] Verify `jobs` + `failed_jobs` tables exist (run `php artisan queue:table` / `queue:failed-table` + `migrate` if missing)
- [ ] Production: configure `php artisan queue:work` under supervisor/systemd

---

## 2. Frontend (Vue) — integration

### 2.1 New files
- [ ] `resources/js/form/composables/useLookups.js` — copy from stub
  - Reads `window.APORTA_LOOKUPS_URL`
  - Exposes `loading`, `error`, `ready`, `load()`, `activeOnly(key)`
  - Filters `active: false` entries
  - 304 / ETag handled automatically by browser cache

### 2.2 `resources/js/form/Register.vue` rewrite
- [ ] On mount: call `useLookups().load()`. Gate the form render on `ready === true`.
- [ ] If `error`: show "Das Anmeldeformular ist momentan nicht verfügbar..." — **no** bundled fallback.
- [ ] Replace hardcoded option arrays with `activeOnly(key)` for:
  - `salutations`, `marital_status`, `employment_status`, `nationality`,
  - `residence_permits`, `tenant_roles`, `rent_duration`, `annual_incomes` (→ `income_brackets`),
  - `districts`, `floors`, `rooms` (size resolves via lookup), `relationships`
- [ ] Restructure local form model + on-submit payload assembly per `.docs/Statamic-Integration.md` §2.2:
  - `main_applicant { ..., employer{}, current_housing{} }`
  - `co_applicant` (null or full block, with `relationship_to_main`, `same_address_as_main`)
  - `housing_wish { earliest_move_in, max_gross_rent, wants_balcony, wants_elevator, districts[], floors[], rooms[] }`
  - `household_info { total_persons, adults_count, children_count, all_children_live_constantly, plays_music, musical_instruments, has_pets, pets_description, remarks }`
  - `children[] = [{ position, birth_year }]`
- [ ] Field renames per §3.1 — notable: do **not** invert `wants_balcony` / `wants_elevator`.
- [ ] Split legacy `sub_tenant_type` (1–5) into:
  - values 1–4 → `co_applicant.relationship_to_main` slug (`spouse|registered_partner|life_partner|roommate`)
  - value 5 → `household_info.children_count > 0` + `children[]` rows
- [ ] Drop `form.token` and the token attach on submit.
- [ ] Inline client-side "no dogs" warning on `pets_description` (regex `/\b(Hund|Hunde|Dog)\b/i`).
- [ ] Submit POSTs JSON to `/api/form/register` — Statamic forwards from there.
- [ ] "Danke" screen / `Feedback.vue`: confirm it does **not** show a reference number.

### 2.3 Layout
- [ ] Inject `window.APORTA_LOOKUPS_URL` in the layout that mounts the form:
  ```blade
  <script>window.APORTA_LOOKUPS_URL = @json(config('aporta.lookups_url'));</script>
  ```

---

## 3. Testing (per `.docs/Statamic-Integration.md` §8)

- [ ] Fresh submission against dev backend → 201, application visible in back office
- [ ] Replay same payload via tinker → 200, no duplicate
- [ ] Backend offline: visitor sees "danke", job retries with backoff, succeeds when backend returns
- [ ] `/lookups` offline + cold cache: form refuses to render, shows error message
- [ ] `/lookups` offline + warm browser cache: form still renders
- [ ] Invalid payload (bad slug) → 422 → lands in `failed_jobs` → engineering alert fires
- [ ] Page-level password gate still blocks unauthenticated access
- [ ] CORS on `/lookups` works from the Statamic origin

---

## 4. Follow-ups (separate PRs after this ships)

- [ ] **Framework upgrade**: Laravel 10 → 11 or 12, Statamic 5 → latest, PHP bump. Reason: L10 active support ended Aug 2025 (security-only). Schedule once integration is stable in prod.
- [ ] **Applicant confirmation email**: `Statamic-Integration.md` §6.3 suggests Statamic sends a "Vielen Dank für Ihre Anmeldung" mail on submit. Out of scope for this PR; needs a Mailable + trigger from the controller.
- [ ] **`failed_jobs` pruning** scheduled task: `php artisan queue:prune-failed --hours=720` weekly (or similar).
- [ ] **Existing-tenants flow replacement**: if/when the new backend defines a contract for existing tenants, build a parallel forwarder. For now the form is gone.
- [ ] **Page-level password gate**: revisit whether to move it onto Statamic or simplify. Per integration doc it's a Statamic concern; we're keeping the current implementation for now.

---

## 5. Out of scope for this PR (explicitly)

- Statamic page-level password gate (untouched)
- Backend-side CORS / API key issuance (handled by backend team)
- Existing-tenants migration (form removed, no replacement)
- Framework upgrade (see §4)
- Applicant confirmation email (see §4)
