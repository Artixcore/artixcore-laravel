const base =
  typeof window !== "undefined"
    ? process.env.NEXT_PUBLIC_API_URL ?? "http://127.0.0.1:8000/api/v1"
    : process.env.NEXT_PUBLIC_API_URL ?? "http://127.0.0.1:8000/api/v1";

export type ApiErrorBody = {
  message?: string;
  errors?: Record<string, string[]>;
};

export class ApiError extends Error {
  status: number;
  body: ApiErrorBody | null;

  constructor(message: string, status: number, body: ApiErrorBody | null) {
    super(message);
    this.name = "ApiError";
    this.status = status;
    this.body = body;
  }
}

export async function apiFetch<T>(
  path: string,
  init?: RequestInit & { json?: unknown }
): Promise<T> {
  const url = path.startsWith("http") ? path : `${base.replace(/\/$/, "")}${path.startsWith("/") ? path : `/${path}`}`;
  const headers = new Headers(init?.headers);

  let body: BodyInit | undefined = init?.body as BodyInit | undefined;
  if (init?.json !== undefined) {
    headers.set("Content-Type", "application/json");
    headers.set("Accept", "application/json");
    body = JSON.stringify(init.json);
  }

  const res = await fetch(url, {
    ...init,
    headers,
    body,
    credentials: "include",
  });

  const text = await res.text();
  let data: unknown = null;
  if (text) {
    try {
      data = JSON.parse(text) as unknown;
    } catch {
      data = { message: text };
    }
  }

  if (!res.ok) {
    const err = data as ApiErrorBody | null;
    const message =
      err?.message ?? `Request failed (${res.status})`;
    throw new ApiError(message, res.status, err);
  }

  return data as T;
}

/** Strip `/api/v1` from `NEXT_PUBLIC_API_URL` (or `getApiBase()`) → Laravel app origin. */
export function laravelOriginFromApiV1Base(apiBaseWithVersion: string): string {
  return apiBaseWithVersion.replace(/\/$/, "").replace(/\/api\/v1$/, "");
}

function readXsrfTokenFromCookie(): string | undefined {
  if (typeof document === "undefined") {
    return undefined;
  }
  const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/);
  if (!match?.[1]) {
    return undefined;
  }
  try {
    return decodeURIComponent(match[1]);
  } catch {
    return match[1];
  }
}

/**
 * Laravel Sanctum stateful API: prime CSRF cookie, then return headers for mutating requests from the browser.
 * Use with `credentials: "include"` on `fetch` when calling the API from the Next.js app (cross-origin to Laravel).
 */
export async function sanctumStatefulHeaders(
  apiBaseParam?: string
): Promise<Record<string, string>> {
  if (typeof window === "undefined") {
    return {};
  }
  const apiRoot = (apiBaseParam ?? base).replace(/\/$/, "");
  const origin = laravelOriginFromApiV1Base(apiRoot);
  await fetch(`${origin}/sanctum/csrf-cookie`, {
    method: "GET",
    credentials: "include",
  });
  const xsrf = readXsrfTokenFromCookie();
  if (!xsrf) {
    return {};
  }
  return { "X-XSRF-TOKEN": xsrf };
}

/** Laravel Sanctum: fetch CSRF cookie before cookie-based POST from SPA */
export async function ensureSanctumCsrf(apiBaseParam?: string): Promise<void> {
  const apiRoot = (apiBaseParam ?? base).replace(/\/$/, "");
  const origin = laravelOriginFromApiV1Base(apiRoot);
  await fetch(`${origin}/sanctum/csrf-cookie`, {
    method: "GET",
    credentials: "include",
  });
}

export type ContactPayload = {
  name: string;
  email: string;
  company?: string;
  message: string;
};

export type ContactResponse = {
  message: string;
};

export async function submitContact(payload: ContactPayload) {
  return apiFetch<ContactResponse>("/contact", {
    method: "POST",
    json: payload,
    credentials: "omit",
  });
}
