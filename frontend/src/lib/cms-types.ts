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
