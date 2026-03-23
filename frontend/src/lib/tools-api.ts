import { getApiBase } from "@/lib/cms-api";

const base = () => getApiBase();

export type MicroToolDTO = {
  id: number;
  slug: string;
  category: string;
  title: string;
  description: string;
  icon_key: string | null;
  execution_mode: "client" | "server";
  input_schema: {
    fields?: Array<Record<string, unknown>>;
  } | null;
  is_premium: boolean;
  is_popular: boolean;
  is_new: boolean;
  released_at: string | null;
  featured_score: number;
};

export type MicroToolsListResponse = { data: MicroToolDTO[] };

export async function fetchMicroToolsList(init?: {
  category?: string;
  q?: string;
  sort?: "default" | "popular" | "new";
  cache?: RequestCache;
}): Promise<MicroToolDTO[]> {
  const p = new URLSearchParams();
  if (init?.category) p.set("category", init.category);
  if (init?.q) p.set("q", init.q);
  if (init?.sort) p.set("sort", init.sort);
  const qs = p.toString();
  const url = `${base()}/tools${qs ? `?${qs}` : ""}`;
  const res = await fetch(url, {
    headers: { Accept: "application/json" },
    cache: init?.cache ?? "no-store",
  });
  if (!res.ok) {
    throw new Error(`Tools list failed: ${res.status}`);
  }
  const json = (await res.json()) as MicroToolsListResponse;
  return json.data;
}

export async function fetchMicroToolBySlug(
  slug: string
): Promise<MicroToolDTO | null> {
  const res = await fetch(`${base()}/tools/${encodeURIComponent(slug)}`, {
    headers: { Accept: "application/json" },
    cache: "no-store",
  });
  if (res.status === 404) return null;
  if (!res.ok) {
    throw new Error(`Tool fetch failed: ${res.status}`);
  }
  const json = (await res.json()) as { data: MicroToolDTO };
  return json.data;
}

export type ToolsSession = {
  authenticated: boolean;
  ad_free: boolean;
  aid: string | null;
};

export async function fetchToolsSession(
  bearerToken?: string | null
): Promise<ToolsSession> {
  const headers: HeadersInit = { Accept: "application/json" };
  if (bearerToken) {
    (headers as Record<string, string>).Authorization = `Bearer ${bearerToken}`;
  }
  const res = await fetch(`${base()}/tools/session`, {
    headers,
    cache: "no-store",
  });
  if (!res.ok) {
    return { authenticated: false, ad_free: false, aid: null };
  }
  const json = (await res.json()) as { data: ToolsSession };
  return json.data;
}

export type ToolRunMeta = {
  run_id: number | null;
  ad_free: boolean;
  limits_remaining: number | null;
};

export async function runMicroToolServer(
  slug: string,
  body: Record<string, unknown>,
  bearerToken?: string | null
): Promise<{ data: unknown; meta: ToolRunMeta }> {
  const headers: HeadersInit = {
    Accept: "application/json",
    "Content-Type": "application/json",
  };
  if (bearerToken) {
    (headers as Record<string, string>).Authorization = `Bearer ${bearerToken}`;
  }
  const res = await fetch(
    `${base()}/tools/${encodeURIComponent(slug)}/run`,
    {
      method: "POST",
      headers,
      body: JSON.stringify(body),
    }
  );
  const json = (await res.json()) as {
    data?: unknown;
    meta?: ToolRunMeta;
    message?: string;
  };
  if (!res.ok) {
    throw new Error(json.message ?? `Tool run failed (${res.status})`);
  }
  return {
    data: json.data as unknown,
    meta: json.meta ?? {
      run_id: null,
      ad_free: false,
      limits_remaining: null,
    },
  };
}

export async function fetchToolFavorites(
  bearerToken: string
): Promise<MicroToolDTO[]> {
  const res = await fetch(`${base()}/tools/me/favorites`, {
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${bearerToken}`,
    },
    cache: "no-store",
  });
  if (!res.ok) return [];
  const json = (await res.json()) as MicroToolsListResponse;
  return json.data;
}

export async function addToolFavorite(
  bearerToken: string,
  toolId: number
): Promise<void> {
  await fetch(`${base()}/tools/me/favorites/${toolId}`, {
    method: "POST",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${bearerToken}`,
    },
  });
}

export async function removeToolFavorite(
  bearerToken: string,
  toolId: number
): Promise<void> {
  await fetch(`${base()}/tools/me/favorites/${toolId}`, {
    method: "DELETE",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${bearerToken}`,
    },
  });
}

export type ToolHistoryItem = {
  id: number;
  status: string;
  created_at: string | null;
  tool: { slug: string; title: string; category: string } | null;
  input_summary: Record<string, unknown> | null;
  result: unknown;
};

export async function fetchToolHistory(
  bearerToken: string
): Promise<ToolHistoryItem[]> {
  const res = await fetch(`${base()}/tools/me/history`, {
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${bearerToken}`,
    },
    cache: "no-store",
  });
  if (!res.ok) return [];
  const json = (await res.json()) as { data: ToolHistoryItem[] };
  return json.data;
}

export async function saveToolReport(
  bearerToken: string,
  microToolRunId: number,
  title: string
): Promise<number | null> {
  const res = await fetch(`${base()}/tools/me/reports`, {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      Authorization: `Bearer ${bearerToken}`,
    },
    body: JSON.stringify({
      micro_tool_run_id: microToolRunId,
      title,
    }),
  });
  if (!res.ok) return null;
  const json = (await res.json()) as { data: { id: number } };
  return json.data?.id ?? null;
}
