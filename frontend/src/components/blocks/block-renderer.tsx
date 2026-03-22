import type { PageBlockDTO } from "@/lib/cms-types";
import {
  ctaBlockDataSchema,
  featureGridBlockDataSchema,
  heroBlockDataSchema,
} from "@/lib/blocks/schemas";
import { CtaBlock } from "@/components/blocks/cta-block";
import { FeatureGridBlock } from "@/components/blocks/feature-grid-block";
import { HeroBlock } from "@/components/blocks/hero-block";

function UnknownBlock({ type }: { type: string }) {
  return (
    <div className="mx-auto max-w-6xl px-4 py-8 text-sm text-muted">
      Unknown block type: <code>{type}</code>
    </div>
  );
}

export function BlockRenderer({ blocks }: { blocks: PageBlockDTO[] }) {
  const sorted = [...blocks].sort((a, b) => a.sort_order - b.sort_order);

  return (
    <>
      {sorted.map((block) => {
        switch (block.type) {
          case "hero": {
            const parsed = heroBlockDataSchema.safeParse(block.data);
            if (!parsed.success) {
              return <UnknownBlock key={block.id} type={block.type} />;
            }
            return <HeroBlock key={block.id} data={parsed.data} />;
          }
          case "feature_grid": {
            const parsed = featureGridBlockDataSchema.safeParse(block.data);
            if (!parsed.success) {
              return <UnknownBlock key={block.id} type={block.type} />;
            }
            return <FeatureGridBlock key={block.id} data={parsed.data} />;
          }
          case "cta": {
            const parsed = ctaBlockDataSchema.safeParse(block.data);
            if (!parsed.success) {
              return <UnknownBlock key={block.id} type={block.type} />;
            }
            return <CtaBlock key={block.id} data={parsed.data} />;
          }
          default:
            return <UnknownBlock key={block.id} type={block.type} />;
        }
      })}
    </>
  );
}
