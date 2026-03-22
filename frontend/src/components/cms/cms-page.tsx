import type { Metadata } from "next";
import { BlockRenderer } from "@/components/blocks/block-renderer";
import { getPage } from "@/lib/cms-api";

type Props = { path: string };

export async function cmsPageMetadata({ path }: Props): Promise<Metadata> {
  try {
    const page = await getPage(path);
    return {
      title: page.meta_title ?? page.title,
      description: page.meta_description ?? undefined,
    };
  } catch {
    return { title: "Artixcore" };
  }
}

export async function CmsPage({ path }: Props) {
  try {
    const page = await getPage(path);

    return (
      <>
        {path !== "home" ? (
          <header className="border-b border-border/60 bg-muted-bg/20">
            <div className="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
              <h1 className="text-3xl font-semibold tracking-tight sm:text-4xl">
                {page.title}
              </h1>
              {page.meta_description ? (
                <p className="mt-3 max-w-2xl text-muted">{page.meta_description}</p>
              ) : null}
            </div>
          </header>
        ) : null}
        <BlockRenderer blocks={page.blocks} />
      </>
    );
  } catch {
    return (
      <div className="mx-auto max-w-6xl px-4 py-16 text-center text-sm text-muted">
        This page could not be loaded from the API. Ensure Laravel is running and
        the database is seeded.
      </div>
    );
  }
}
