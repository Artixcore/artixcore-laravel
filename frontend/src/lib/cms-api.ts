import type {
  NavigationResponse,
  PageDTO,
  NavItemDTO,
} from "@/lib/cms-types";

/** Comma-separated topic slugs; sent as `X-Interest-Topics` for article list ordering (first-party only). */
export const INTEREST_TOPICS_COOKIE = "artixcore_topics";

export function interestTopicsHeader(
  cookieValue: string | undefined
): HeadersInit | undefined {
  if (!cookieValue?.trim()) {
    return undefined;
  }
  return { "X-Interest-Topics": cookieValue };
}

export function getApiBase(): string {
  return (
    process.env.NEXT_PUBLIC_API_URL ?? "http://127.0.0.1:8000/api/v1"
  ).replace(/\/$/, "");
}

const base = getApiBase();

async function getJson<T>(path: string, init?: RequestInit): Promise<T> {
  const url = path.startsWith("http") ? path : `${base}${path.startsWith("/") ? path : `/${path}`}`;
  const res = await fetch(url, {
    ...init,
    headers: {
      Accept: "application/json",
      ...init?.headers,
    },
    next: init?.next ?? { revalidate: 60 },
  });
  if (!res.ok) {
    throw new Error(`CMS request failed: ${res.status} ${url}`);
  }
  return res.json() as Promise<T>;
}

export async function getPrimaryNavigation(): Promise<NavigationResponse> {
  const json = await getJson<{ data: NavigationResponse }>("/navigation?menu=primary");
  return json.data;
}

export async function getFooterNavigation(): Promise<NavigationResponse> {
  const json = await getJson<{ data: NavigationResponse }>("/navigation?menu=footer");
  return json.data;
}

export function flattenFooterLinks(
  items: NavItemDTO[]
): { href: string; label: string }[] {
  return items.map((i) => ({
    href: i.href ?? "/",
    label: i.label,
  }));
}

export async function getPage(path: string): Promise<PageDTO> {
  const encoded = encodeURIComponent(path);
  const json = await getJson<{ data: PageDTO }>(`/pages/${encoded}`);
  return json.data;
}

export type ArticleSummary = {
  id: number;
  slug: string;
  title: string;
  summary: string | null;
  featured: boolean;
  published_at: string | null;
};

export type Paginated<T> = {
  data: T[];
  links?: { first: string | null; last: string | null; prev: string | null; next: string | null };
  meta?: { current_page: number; last_page: number; total: number };
};

export async function getArticles(
  searchParams?: Record<string, string | undefined>,
  headers?: HeadersInit
): Promise<Paginated<ArticleSummary>> {
  const q = new URLSearchParams();
  if (searchParams) {
    Object.entries(searchParams).forEach(([k, v]) => {
      if (v) q.set(k, v);
    });
  }
  const qs = q.toString();
  const path = qs ? `/articles?${qs}` : "/articles";
  return getJson<Paginated<ArticleSummary>>(path, {
    headers,
    cache: "no-store",
  });
}

export async function getArticle(slug: string): Promise<unknown> {
  return getJson(`/articles/${encodeURIComponent(slug)}`, { cache: "no-store" });
}

export async function getResearchPapers(
  searchParams?: Record<string, string | undefined>
): Promise<Paginated<ArticleSummary>> {
  const q = new URLSearchParams();
  if (searchParams) {
    Object.entries(searchParams).forEach(([k, v]) => {
      if (v) q.set(k, v);
    });
  }
  const qs = q.toString();
  const path = qs ? `/research-papers?${qs}` : "/research-papers";
  return getJson(path, { cache: "no-store" });
}

export async function getResearchPaper(slug: string): Promise<unknown> {
  return getJson(`/research-papers/${encodeURIComponent(slug)}`, {
    cache: "no-store",
  });
}

export async function getCaseStudies(
  searchParams?: Record<string, string | undefined>
): Promise<Paginated<ArticleSummary>> {
  const q = new URLSearchParams();
  if (searchParams) {
    Object.entries(searchParams).forEach(([k, v]) => {
      if (v) q.set(k, v);
    });
  }
  const qs = q.toString();
  const path = qs ? `/case-studies?${qs}` : "/case-studies";
  return getJson(path, { cache: "no-store" });
}

export async function getCaseStudy(slug: string): Promise<unknown> {
  return getJson(`/case-studies/${encodeURIComponent(slug)}`, { cache: "no-store" });
}

export async function getProducts(
  searchParams?: Record<string, string | undefined>
): Promise<Paginated<ArticleSummary>> {
  const q = new URLSearchParams();
  if (searchParams) {
    Object.entries(searchParams).forEach(([k, v]) => {
      if (v) q.set(k, v);
    });
  }
  const qs = q.toString();
  const path = qs ? `/products?${qs}` : "/products";
  return getJson(path, { cache: "no-store" });
}

export async function getProduct(slug: string): Promise<unknown> {
  return getJson(`/products/${encodeURIComponent(slug)}`, { cache: "no-store" });
}

export async function getTeamProfiles(): Promise<{ data: unknown[] }> {
  return getJson("/team", { cache: "no-store" });
}

export async function getTrending(
  type: "articles" | "research_papers" | "case_studies" | "products"
): Promise<{ data: ArticleSummary[] }> {
  return getJson(`/trending?type=${type}`, { next: { revalidate: 120 } });
}

/** Offline / build fallback — mirrors legacy static nav shape. */
export function fallbackNavigationItems(): NavItemDTO[] {
  return [
    {
      id: 0,
      label: "Products",
      href: "/products",
      feature: null,
      children: [
        { id: 0, label: "SaaS", href: "/products/saas", feature: null, children: [] },
        {
          id: 0,
          label: "Blockchain",
          href: "/products/blockchain",
          feature: null,
          children: [],
        },
        {
          id: 0,
          label: "Quantum",
          href: "/products/quantum",
          feature: null,
          children: [],
        },
      ],
    },
    {
      id: 0,
      label: "Solutions",
      href: "/solutions",
      feature: null,
      children: [
        {
          id: 0,
          label: "Enterprise SaaS",
          href: "/solutions/enterprise-saas",
          feature: null,
          children: [],
        },
        { id: 0, label: "Web3 & tokens", href: "/solutions/web3", feature: null, children: [] },
        {
          id: 0,
          label: "R&D platforms",
          href: "/solutions/research-platforms",
          feature: null,
          children: [],
        },
      ],
    },
    {
      id: 0,
      label: "Research",
      href: "/research",
      feature: null,
      children: [
        {
          id: 0,
          label: "Papers",
          href: "/research/papers",
          feature: null,
          children: [],
        },
      ],
    },
    {
      id: 0,
      label: "Resources",
      href: "/resources",
      feature: null,
      children: [
        {
          id: 0,
          label: "Articles",
          href: "/resources/articles",
          feature: null,
          children: [],
        },
        {
          id: 0,
          label: "Case studies",
          href: "/resources/case-studies",
          feature: null,
          children: [],
        },
      ],
    },
    {
      id: 0,
      label: "Company",
      href: "/company",
      feature: null,
      children: [
        { id: 0, label: "About", href: "/about", feature: null, children: [] },
        { id: 0, label: "Team", href: "/team", feature: null, children: [] },
        { id: 0, label: "Contact", href: "/contact", feature: null, children: [] },
      ],
    },
  ];
}
