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

export type PortalUser = {
  id: number;
  name: string;
  email: string;
  user_kind: string;
  phone: string | null;
  bio: string | null;
  designation: string | null;
};

export type PortalLoginResponse = {
  data: {
    token: string;
    token_type: string;
    user: PortalUser;
  };
};

export type PortalMeResponse = {
  data: {
    user: PortalUser;
    avatar_url: string;
    roles: string[];
    permissions: string[];
  };
};

export type PortalProfilePhoto = {
  id: number;
  url: string;
  thumb_url: string;
  name: string;
  file_name: string;
  mime_type: string | null;
};

export type PortalProfileResponse = {
  data: {
    user: PortalUser;
    avatar_url: string;
    avatar_thumb_url: string;
    photos: PortalProfilePhoto[];
  };
};

export type PortalApiErrorBody = {
  message?: string;
  errors?: Record<string, string[]>;
};

export class PortalApiError extends Error {
  status: number;
  body: PortalApiErrorBody | null;

  constructor(message: string, status: number, body: PortalApiErrorBody | null) {
    super(message);
    this.name = "PortalApiError";
    this.status = status;
    this.body = body;
  }
}

function formatValidationMessage(body: PortalApiErrorBody | null): string {
  if (!body?.errors) {
    return body?.message ?? "Request failed";
  }
  const first = Object.values(body.errors).flat()[0];
  return first ?? body.message ?? "Validation failed";
}

async function parsePortalResponse<T>(
  res: Response
): Promise<T> {
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
    const err = data as PortalApiErrorBody | null;
    throw new PortalApiError(
      formatValidationMessage(err),
      res.status,
      err
    );
  }
  return data as T;
}

function authHeaders(token: string, extra?: HeadersInit): HeadersInit {
  return {
    Accept: "application/json",
    Authorization: `Bearer ${token}`,
    ...extra,
  };
}

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
  return parsePortalResponse<PortalLoginResponse>(res);
}

export async function portalMe(token: string): Promise<PortalMeResponse> {
  const base = getApiBase();
  const res = await fetch(`${base}/portal/me`, {
    headers: authHeaders(token),
  });
  return parsePortalResponse<PortalMeResponse>(res);
}

export async function portalLogout(token: string): Promise<void> {
  const base = getApiBase();
  await fetch(`${base}/auth/logout`, {
    method: "POST",
    headers: authHeaders(token),
  });
}

export async function portalProfileGet(
  token: string
): Promise<PortalProfileResponse> {
  const base = getApiBase();
  const res = await fetch(`${base}/portal/profile`, {
    headers: authHeaders(token),
  });
  return parsePortalResponse<PortalProfileResponse>(res);
}

export async function portalProfileUpdate(
  token: string,
  payload: {
    name: string;
    email: string;
    phone: string;
    bio: string;
    designation: string;
  }
): Promise<PortalProfileResponse> {
  const base = getApiBase();
  const res = await fetch(`${base}/portal/profile`, {
    method: "PATCH",
    headers: {
      ...authHeaders(token),
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
  });
  return parsePortalResponse<PortalProfileResponse>(res);
}

export async function portalPasswordUpdate(
  token: string,
  payload: {
    current_password: string;
    password: string;
    password_confirmation: string;
  }
): Promise<{ data: { message: string } }> {
  const base = getApiBase();
  const res = await fetch(`${base}/portal/profile/password`, {
    method: "PUT",
    headers: {
      ...authHeaders(token),
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
  });
  return parsePortalResponse<{ data: { message: string } }>(res);
}

export async function portalAvatarUpload(
  token: string,
  file: File
): Promise<PortalProfileResponse> {
  const base = getApiBase();
  const form = new FormData();
  form.append("avatar", file);
  const res = await fetch(`${base}/portal/profile/avatar`, {
    method: "POST",
    headers: authHeaders(token),
    body: form,
  });
  return parsePortalResponse<PortalProfileResponse>(res);
}

export async function portalAvatarRemove(
  token: string
): Promise<PortalProfileResponse> {
  const base = getApiBase();
  const res = await fetch(`${base}/portal/profile/avatar`, {
    method: "DELETE",
    headers: authHeaders(token),
  });
  return parsePortalResponse<PortalProfileResponse>(res);
}

export async function portalPhotosList(
  token: string
): Promise<{ data: { photos: PortalProfilePhoto[] } }> {
  const base = getApiBase();
  const res = await fetch(`${base}/portal/profile/photos`, {
    headers: authHeaders(token),
  });
  return parsePortalResponse<{ data: { photos: PortalProfilePhoto[] } }>(res);
}

export async function portalPhotoUpload(
  token: string,
  file: File
): Promise<{ data: { photo: PortalProfilePhoto } }> {
  const base = getApiBase();
  const form = new FormData();
  form.append("photo", file);
  const res = await fetch(`${base}/portal/profile/photos`, {
    method: "POST",
    headers: authHeaders(token),
    body: form,
  });
  return parsePortalResponse<{ data: { photo: PortalProfilePhoto } }>(res);
}

export async function portalPhotoDelete(
  token: string,
  photoId: number
): Promise<{ data: { message: string } }> {
  const base = getApiBase();
  const res = await fetch(`${base}/portal/profile/photos/${photoId}`, {
    method: "DELETE",
    headers: authHeaders(token),
  });
  return parsePortalResponse<{ data: { message: string } }>(res);
}
