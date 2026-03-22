# Artixcore enterprise module backlog

Epic-level follow-ups mapped to repository areas. Items below extend what is now implemented (RBAC, site/media API, CMS meta, portal auth, AI stubs).

## Platform / RBAC

- Add Filament UI for role–permission assignment beyond the User form (dedicated Roles resource or Shield-style matrix).
- Enforce `portal.access` on `GET /api/v1/portal/me` using the same permission resolution as login (not `Gate::before` shortcuts).
- Audit logging (`created_by` / `updated_by`) on high-risk writes across CMS entities.

## Site, media, design system

- Wire root `metadata` icons/OG to `/api/v1/site` when favicon/OG assets exist (avoid static-only `layout.tsx` metadata).
- Image focal point and responsive variants on `media_assets`.
- Validate `design_tokens` keys against an allowlist before emitting CSS.

## CMS and routing

- Gradually replace static marketing routes with CMS paths or explicit redirects; document which hubs stay collection-driven vs free-form pages.
- Add preview tokens for draft pages in the `(preview)` group.
- Optional: codegen or CI check that `PageBlockType` enum matches `frontend/src/lib/blocks/registry.ts` and `/api/v1/meta/block-types`.

## Portal

- HttpOnly cookie + Sanctum SPA flow for first-party domains; avoid long-lived tokens in `localStorage` in production.
- Portal modules: profile update, support tickets, entitlements (tables + policies + UI).
- Server-driven nav for portal from `permissions`/`roles` returned by `/portal/me`.

## Agentic / AI

- Replace `ProcessAiRunJob` stub with real step runner (LLM calls, tools, retries, idempotency on `correlation_id`).
- Filament relation manager for `ai_workflow_steps`; human-in-the-loop transitions on `ai_approvals`.
- Metrics dashboard widgets (run volume, failure rate, latency).

## Quality

- API contract tests for auth error shapes; Dusk or Playwright smoke for `/admin` and `/portal`.
- Load and cache headers for public JSON (`ETag`, `Cache-Control`) where safe.
