/**
 * Canonical CMS block type ids — keep aligned with `App\Support\Content\PageBlockType` (PHP)
 * and `/api/v1/meta/block-types`.
 */
export const PAGE_BLOCK_TYPES = [
  "hero",
  "feature_grid",
  "product_showcase",
  "research_highlight",
  "article_rail",
  "cta",
  "rich_text",
] as const;

export type PageBlockTypeId = (typeof PAGE_BLOCK_TYPES)[number];
