export type NavFeature = {
  title?: string;
  description?: string;
  href?: string;
} | null;

export type NavItemDTO = {
  id: number;
  label: string;
  href: string | null;
  feature: NavFeature;
  children: NavItemDTO[];
};

export type NavigationResponse = {
  menu: string;
  items: NavItemDTO[];
};

export type PageBlockDTO = {
  id: number;
  type: string;
  sort_order: number;
  data: Record<string, unknown>;
};

export type PageDTO = {
  path: string;
  href: string;
  title: string;
  meta_title: string | null;
  meta_description: string | null;
  meta: Record<string, unknown> | null;
  blocks: PageBlockDTO[];
};

export type SiteDTO = {
  site_name: string | null;
  default_meta_title: string | null;
  default_meta_description: string | null;
  contact_email: string | null;
  social_links: unknown[];
  design_tokens: Record<string, unknown>;
  theme_default: "system" | "light" | "dark" | string;
  logo: { url: string; alt: string | null } | null;
  favicon_url: string | null;
  og_default: { url: string; alt: string | null } | null;
};
