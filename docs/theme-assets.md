# Marketing theme assets

Public-facing Blade templates load static assets from **`/theme`** (filesystem: `public/theme/`), copied from the Mizzle template under `public/artixcore UI/assets/`.

Use Laravel’s `asset()` helper:

```php
asset('theme/css/style.css')
asset('theme/vendor/bootstrap/dist/js/bootstrap.bundle.min.js')
```

Do not reference paths with spaces (e.g. `artixcore UI`) from production views.
