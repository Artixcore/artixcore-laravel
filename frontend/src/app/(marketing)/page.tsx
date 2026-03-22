import type { Metadata } from "next";
import { BlockRenderer } from "@/components/blocks/block-renderer";
import { cmsPageMetadata } from "@/components/cms/cms-page";
import { TrendingArticlesSection } from "@/components/sections/trending-articles";
import { getPage } from "@/lib/cms-api";
import type { PageBlockDTO } from "@/lib/cms-types";
import { site } from "@/lib/constants";

export async function generateMetadata(): Promise<Metadata> {
  try {
    return await cmsPageMetadata({ path: "home" });
  } catch {
    return {
      title: "Home",
      description: site.description,
    };
  }
}

export default async function HomePage() {
  let blocks: PageBlockDTO[] = [];
  try {
    const page = await getPage("home");
    blocks = page.blocks;
  } catch {
    blocks = [];
  }

  return (
    <>
      {blocks.length > 0 ? (
        <BlockRenderer blocks={blocks} />
      ) : (
        <div className="mx-auto max-w-6xl px-4 py-20 text-center text-muted">
          CMS unavailable — start Laravel and run{" "}
          <code className="rounded bg-muted-bg px-1">php artisan migrate --seed</code>.
        </div>
      )}
      <TrendingArticlesSection />
    </>
  );
}
