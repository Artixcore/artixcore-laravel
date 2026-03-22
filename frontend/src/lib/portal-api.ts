import { getApiBase } from "@/lib/cms-api";

const TOKEN_KEY = "artixcore_portal_token";

export function getStoredPortalToken(): string | null {
  if (typeof window === "undefined") {
    return null;
  }
  return localStorage.getItem(TOKEN_KEY);
}

export function setStoredPortalToken(token: string | null): void {
  if (typeof window === "undefined") {
    return;
  }
  if (token) {
    localStorage.setItem(TOKEN_KEY, token);
  } else {
    localStorage.removeItem(TOKEN_KEY);
  }
}

export type PortalLoginResponse = {
  data: {
    token: string;
    token_type: string;
    user: { id: number; name: string; email: string; user_kind: string };
  };
};

export type PortalMeResponse = {
  data: {
    user: { id: number; name: string; email: string; user_kind: string };
    roles: string[];
    permissions: string[];
  };
};

export async function portalLogin(
  email: string,
  password: string
): Promise<PortalLoginResponse> {
  const base = getApiBase();
  const res = await fetch(`${base}/auth/login`, {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ email, password }),
  });
  const json = (await res.json()) as PortalLoginResponse & {
    message?: string;
  };
  if (!res.ok) {
    throw new Error(json.message ?? `Login failed (${res.status})`);
  }
  return json as PortalLoginResponse;
}

export async function portalMe(token: string): Promise<PortalMeResponse> {
  const base = getApiBase();
  const res = await fetch(`${base}/portal/me`, {
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
  const json = (await res.json()) as PortalMeResponse;
  if (!res.ok) {
    throw new Error(`Portal /me failed (${res.status})`);
  }
  return json;
}

export async function portalLogout(token: string): Promise<void> {
  const base = getApiBase();
  await fetch(`${base}/auth/logout`, {
    method: "POST",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
}
