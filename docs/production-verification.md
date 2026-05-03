# Production verification (Artixcore)

Run after each deploy to DigitalOcean App Platform (or equivalent).

## Post-deploy commands (one-shot, web component console or job)

```bash
php artisan migrate --force
php artisan db:seed --class=ProductionContactInfoSeeder --force
php artisan storage:link --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Never on production: `php artisan migrate:fresh`, `php artisan db:wipe`, `php artisan migrate:rollback` (unless you understand data loss).

## DigitalOcean environment variables

Set in the App Platform UI (or bind from secrets):

| Key | Example / notes |
|-----|-----------------|
| `APP_KEY` | `base64:...` (runtime secret) |
| `APP_URL` | `https://artixcore.com` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `CONTACT_EMAIL` | `hello@artixcore.com` (optional; seeder default) |
| `MAIL_FROM_ADDRESS` | `hello@artixcore.com` |
| `LEADS_NOTIFICATION_EMAIL` | `hello@artixcore.com` |
| `MEDIA_DISK` | `public` (dev) or `spaces` (durable uploads on DO) |
| `AWS_ACCESS_KEY_ID` | Spaces access key (if `MEDIA_DISK=spaces`) |
| `AWS_SECRET_ACCESS_KEY` | Spaces secret |
| `AWS_DEFAULT_REGION` | e.g. `nyc3` |
| `AWS_BUCKET` | Space name |
| `AWS_ENDPOINT` | e.g. `https://nyc3.digitaloceanspaces.com` |
| `AWS_URL` | CDN or origin URL for public files (e.g. `https://your-space.nyc3.digitaloceanspaces.com` or custom CDN) |
| `AWS_USE_PATH_STYLE_ENDPOINT` | `false` (typical for Spaces) |
| `ASSET_URL` | Optional; leave empty unless using a separate asset CDN |
| `DB_URL` | From managed MySQL bindable (see `.do/app.yaml`) |
| `TRUSTED_PROXIES` | `*` (behind DO load balancer) |

## Contact email checks

- [ ] Footer on `https://artixcore.com/` shows `hello@artixcore.com` (mailto).
- [ ] `/contact` and lead/get-started pages show the same if `site_settings.contact_email` is set.
- [ ] `GET /api/v1/site` JSON includes `"contact_email":"hello@artixcore.com"`.
- [ ] View source on home: JSON-LD `Organization` includes `email` and `contactPoint` when contact email is configured.

## Image / media checks

- [ ] In the running web container, `public/storage` is a symlink to `storage/app/public` (`ls -la public/storage`).
- [ ] `GET /storage/...` for a known public file returns `200` (e.g. branded logo under `media/...` if seeded).
- [ ] No `http://localhost` or plain `http://` in generated image/OG URLs (use HTTPS and correct host).
- [ ] After enabling DigitalOcean Spaces: upload via admin, note URL, redeploy, image still loads (proves durable storage).
- [ ] Browser console: no mixed-content warnings for images.

## Build / deploy

- [ ] `composer install --no-dev` succeeds in CI / image build.
- [ ] `npm ci && npm run build` produces `public/build/manifest.json` in the image.
- [ ] `php artisan migrate --force` exits 0.
- [ ] Health check `/up` passes.

## Root causes (reference)

- **Wrong footer email:** historical `site_settings.contact_email` seeded as `hello@artixcore.test`; fixed idempotently by `ProductionContactInfoSeeder`.
- **Images 404:** `storage:link` ran only in the PRE_DEPLOY migrate container; web/queue containers lacked `public/storage`. Dockerfile CMD and `.do/app.yaml` `run_command` now run `storage:link --force` before `serve` / `queue:work`.
- **Uploads lost on redeploy:** App Platform has ephemeral disk; use `MEDIA_DISK=spaces` and Spaces credentials for persistent media.
