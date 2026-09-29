# Public registration mode

The application form (`/api/form/register`) is normally behind a password
gate: visitors enter a password, receive a token, and the submit is only
accepted with that token in the `X-Form-Token` header.

For short open-registration windows the gate can be switched off with a
single `.env` flag.

## Switching

Open the form to everyone:

```
FORM_PUBLIC=true
php artisan config:clear   # or config:cache if config is cached
```

Back to password-only:

```
FORM_PUBLIC=false
php artisan config:clear
```

The frontend bundle must be built once with this feature included
(`npm run build`); after that, no rebuild or deploy is needed to toggle — the flag is read at runtime by
both the backend and the page. If static caching is ever enabled, also clear
the static cache after toggling.

## What the flag does

| Where | `FORM_PUBLIC=false` (default) | `FORM_PUBLIC=true` |
|---|---|---|
| Form page | Password screen first | Form shown directly |
| `POST /api/form/register` | Requires valid `X-Form-Token` (401 otherwise) | No token needed; validation still applies |
| `POST /api/form/register/authenticate` | Unchanged | Unchanged (unused) |

Code touchpoints:

- `config/form.php` — `public` setting, reads `FORM_PUBLIC`
- `app/Http/Requests/SubmitApplicationRequest.php` — `authorize()` returns
  `true` in public mode
- `resources/views/partials/components/elements.antlers.html` — exposes
  `window.APORTA_FORM_PUBLIC`
- `resources/js/form/Register.vue` — starts with `isAuthenticated` set from
  that flag

## Notes

- `FORM_PUBLIC` must exist in the production `.env` (defaults to `false` if
  missing).
- While public, the submit endpoint has no rate limiting. Consider adding
  `throttle:` middleware to the route in `routes/api.php` for that period.
