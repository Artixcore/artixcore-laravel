import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import {
  MICRO_TOOL_CATEGORY_ORDER,
  categoryLabel,
} from "@/lib/micro-tools-categories";
import {
  fetchMicroToolsList,
  type MicroToolDTO,
} from "@/lib/tools-api";
import { MicroToolsSessionShell } from "@/components/micro-tools/micro-tools-session-shell";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

type Props = { params: Promise<{ category: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { category } = await params;
  if (!MICRO_TOOL_CATEGORY_ORDER.includes(category as never)) {
    return { title: "Tools" };
  }
  return {
    title: `${categoryLabel(category)} | Micro Tools`,
    description: `Browse ${categoryLabel(category)} in the Artixcore Micro Tools hub.`,
  };
}

export default async function MicroToolsCategoryPage({ params }: Props) {
  const { category } = await params;
  if (!MICRO_TOOL_CATEGORY_ORDER.includes(category as never)) {
    notFound();
  }

  let tools: MicroToolDTO[] = [];
  try {
    tools = await fetchMicroToolsList({ category, sort: "default" });
  } catch {
    tools = [];
  }

  return (
    <Section>
      <Container>
        <nav className="text-sm text-muted">
          <Link href="/" className="hover:underline">
            Home
          </Link>
          <span className="mx-2">/</span>
          <Link href="/micro-tools" className="hover:underline">
            Micro Tools
          </Link>
          <span className="mx-2">/</span>
          <span className="text-foreground">{categoryLabel(category)}</span>
        </nav>
        <h1 className="mt-4 text-3xl font-semibold tracking-tight">
          {categoryLabel(category)}
        </h1>
        <p className="mt-2 max-w-2xl text-muted">
          Tools in this category run in the browser or on our API, depending on
          the card.
        </p>
        <div className="mt-8">
          <MicroToolsSessionShell>
            <ul className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
              {tools.map((t) => (
                <li key={t.slug}>
                  <Link
                    href={`/micro-tools/${t.category}/${t.slug}`}
                    className="block rounded-[var(--radius-lg)] border border-border/80 p-4 transition-colors hover:border-border hover:bg-card"
                  >
                    <span className="text-sm font-medium">{t.title}</span>
                    <p className="mt-1 line-clamp-2 text-xs text-muted">
                      {t.description}
                    </p>
                  </Link>
                </li>
              ))}
            </ul>
            {tools.length === 0 ? (
              <p className="text-sm text-muted">No tools in this category.</p>
            ) : null}
          </MicroToolsSessionShell>
        </div>
      </Container>
    </Section>
  );
}
