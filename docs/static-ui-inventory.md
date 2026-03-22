# Static UI pack — layout and component inventory

Reference: Mizzle / Bootstrap 5.3 theme under `public/artixcore UI/`. Visual skin: `assets/css/artixcore-skin.css` (imported from `style.css`).

## Asset trees

- **Canonical (identical):** `public/artixcore UI/assets/` and `public/assets/` — same `style.css` (verified by MD5). Both load `artixcore-skin.css` via `@import`.
- **RTL docs bundle:** `public/artixcore UI/rtl/docs/assets/` — same theme lineage; includes `artixcore-skin.css` for consistent overrides.

## Layout primitives

| Pattern | Typical classes / markup |
| --- | --- |
| Page shell | `<body>`, `main` |
| Sticky header | `header.header-sticky`, `header-absolute`, `navbar.navbar-expand-xl`, `container` |
| Primary nav | `navbar-nav`, `navbar-nav-scroll`, `dropdown-hover`, mega `dropdown-menu.dropdown-menu-size-lg` |
| Sections | Rows/cols `row g-4`, `py-5` / `py-lg-8`, `bg-light`, `bg-dark`, `position-relative` |
| Footer | `footer.bg-dark`, `data-bs-theme="dark"`, `pt-6 pt-lg-8` |

## Repeated components (reference templates)

| Component | Where it appears |
| --- | --- |
| Hero | Full-width banner, heading + CTA buttons, optional `swiper` / background image |
| Feature / icon rows | `card`, `icon-lg`, grid of value props |
| CTA bands | `bg-primary`, `btn-light`, centered copy |
| Pricing | `table`, `card` with tier lists |
| Logo / client strip | `row` of `img` partners |
| Mega dropdown | Multi-column `dropdown-menu` with demo links |
| Forms | `form-control`, `form-floating`, contact blocks |
| Account / dashboard | Sidebar + main column layouts in `account-*.html` |

## Typography

- **Body:** Inter (`--bs-body-font-family`).
- **Headings:** Instrument Sans (compiled rule on `h1`–`h6`).
- **Skin adjustments:** Letter-spacing and line-height tuned in `artixcore-skin.css` only (no HTML edits).

## Spacing rhythm

- Base scale aligns to **4px** via Bootstrap spacing utilities; skin adds slightly larger radii/shadows and XL readability where safe.
- **Structural clarity:** Consistent vertical rhythm between section headings and body (IBM-style organization only — no vendor chrome).

## Reference pages for QA

| Page type | File |
| --- | --- |
| Home (default) | `index-2.html` |
| Home (creative) | `index-creative-agency.html` |
| About | `about-v2.html` |
| Services | `services-v1.html` |
| Contact | `contact-v2.html` |
| Account | `account-detail.html` |

## Verification checklist (manual)

Open files locally in a browser (file:// or static server):

| Check | Files |
| --- | --- |
| Skin loads (no 404 for `artixcore-skin.css`) | DevTools Network, any page under `artixcore UI/` |
| Light theme typography / color | `index-2.html`, `index-creative-agency.html` |
| Dark toggle + sticky header glass | `index-2.html` (has theme script) |
| Forms + focus rings | `contact-v2.html` |
| Account tables | `account-detail.html` |
| RTL bundle | `rtl/docs/index.html` or any `rtl/docs/*.html` using `assets/css/style.css` |

Confirm `prefers-reduced-motion` in OS settings removes long transitions.
