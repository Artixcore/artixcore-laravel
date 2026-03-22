"use client";

import { useCallback, useEffect, useState } from "react";
import Link from "next/link";
import {
  getStoredPortalToken,
  PortalApiError,
  portalLogin,
  portalLogout,
  portalMe,
  setStoredPortalToken,
  type PortalMeResponse,
} from "@/lib/portal-api";

export default function PortalHomePage() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [token, setToken] = useState<string | null>(null);
  const [me, setMe] = useState<PortalMeResponse["data"] | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const loadMe = useCallback(async (t: string) => {
    setLoading(true);
    setError(null);
    try {
      const res = await portalMe(t);
      setMe(res.data);
    } catch (e) {
      setMe(null);
      setError(e instanceof Error ? e.message : "Session expired");
      setStoredPortalToken(null);
      setToken(null);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    const t = getStoredPortalToken();
    if (t) {
      setToken(t);
      void loadMe(t);
    }
  }, [loadMe]);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try {
      const res = await portalLogin(email, password);
      setStoredPortalToken(res.data.token);
      setToken(res.data.token);
      await loadMe(res.data.token);
    } catch (err) {
      setError(
        err instanceof PortalApiError
          ? err.message
          : err instanceof Error
            ? err.message
            : "Login failed"
      );
    } finally {
      setLoading(false);
    }
  }

  async function onLogout() {
    if (!token) {
      return;
    }
    setLoading(true);
    try {
      await portalLogout(token);
    } catch {
      /* ignore */
    } finally {
      setStoredPortalToken(null);
      setToken(null);
      setMe(null);
      setLoading(false);
    }
  }

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight">Portal</h1>
        <p className="mt-2 text-sm text-muted">
          External users: sign in with a portal-enabled account (API token,
          Sanctum).
        </p>
      </div>

      {error ? (
        <p className="rounded-md border border-border bg-muted-bg/30 px-3 py-2 text-sm text-muted">
          {error}
        </p>
      ) : null}

      {me ? (
        <div className="space-y-4 rounded-lg border border-border/80 bg-muted-bg/10 p-6">
          <div className="flex flex-wrap items-center justify-between gap-3">
            <div>
              <p className="text-lg font-medium">{me.user.name}</p>
              <p className="text-sm text-muted">{me.user.email}</p>
              <p className="mt-1 text-xs text-muted">
                Kind: {me.user.user_kind}
              </p>
            </div>
            <div className="flex flex-wrap items-center gap-2">
              <Link
                href="/portal/profile"
                className="rounded-md border border-border px-3 py-1.5 text-sm font-medium hover:bg-muted-bg"
              >
                Profile settings
              </Link>
              <button
                type="button"
                onClick={() => void onLogout()}
                disabled={loading}
                className="rounded-md border border-border px-3 py-1.5 text-sm font-medium hover:bg-muted-bg"
              >
                Sign out
              </button>
            </div>
          </div>
          <div className="grid gap-4 sm:grid-cols-2">
            <div>
              <p className="text-xs font-medium uppercase tracking-wide text-muted">
                Roles
              </p>
              <ul className="mt-1 list-inside list-disc text-sm">
                {me.roles.map((r) => (
                  <li key={r}>{r}</li>
                ))}
              </ul>
            </div>
            <div>
              <p className="text-xs font-medium uppercase tracking-wide text-muted">
                Permissions ({me.permissions.length})
              </p>
              <p className="mt-1 text-xs text-muted">
                Use these for client-side menu gating; enforce on the API for
                every action.
              </p>
            </div>
          </div>
        </div>
      ) : (
        <form onSubmit={onSubmit} className="max-w-md space-y-4">
          <div>
            <label className="block text-sm font-medium" htmlFor="email">
              Email
            </label>
            <input
              id="email"
              type="email"
              autoComplete="email"
              required
              value={email}
              onChange={(ev) => setEmail(ev.target.value)}
              className="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm"
            />
          </div>
          <div>
            <label className="block text-sm font-medium" htmlFor="password">
              Password
            </label>
            <input
              id="password"
              type="password"
              autoComplete="current-password"
              required
              value={password}
              onChange={(ev) => setPassword(ev.target.value)}
              className="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm"
            />
          </div>
          <button
            type="submit"
            disabled={loading}
            className="rounded-md bg-foreground px-4 py-2 text-sm font-medium text-background hover:opacity-90 disabled:opacity-50"
          >
            {loading ? "Working…" : "Sign in"}
          </button>
        </form>
      )}
    </div>
  );
}
