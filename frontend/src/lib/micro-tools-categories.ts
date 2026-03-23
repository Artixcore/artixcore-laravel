export const MICRO_TOOL_CATEGORY_LABELS: Record<string, string> = {
  web: "Web tools",
  "domain-dns": "Domain & DNS",
  "security-trust": "Security & trust",
  media: "Media",
  "seo-content": "SEO & content",
  developer: "Developer",
  marketing: "Marketing",
};

export const MICRO_TOOL_CATEGORY_ORDER = [
  "web",
  "domain-dns",
  "security-trust",
  "media",
  "seo-content",
  "developer",
  "marketing",
] as const;

export type MicroToolCategorySlug = (typeof MICRO_TOOL_CATEGORY_ORDER)[number];

export function categoryLabel(slug: string): string {
  return MICRO_TOOL_CATEGORY_LABELS[slug] ?? slug;
}
