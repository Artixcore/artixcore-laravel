import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import {
  MICRO_TOOL_CATEGORY_ORDER,
  categoryLabel,
} from "@/lib/micro-tools-categories";
import { fetchMicroToolBySlug } from "@/lib/tools-api";
import { MicroToolDetail } from "@/components/micro-tools/micro-tool-detail";
import { MicroToolsSessionShell } from "@/components/micro-tools/micro-tools-session-shell";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

type Props = { params: Promise<{ category: string; tool: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { category, tool: slug } = await params;
  const t = await fetchMicroToolBySlug(slug).catch(() => null);
  if (!t || t.category !== category) {
    return { title: "Tool" };
  }
  return {
    title: `${t.title} | Micro Tools`,
    description: t.description,
  };
}

export default async function MicroToolPage({ params }: Props) {
  const { category, tool: slug } = await params;
  if (!MICRO_TOOL_CATEGORY_ORDER.includes(category as never)) {
    notFound();
  }

  const tool = await fetchMicroToolBySlug(slug).catch(() => null);
  if (!tool || tool.category !== category) {
    notFound();
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
          <Link
            href={`/micro-tools/${category}`}
            className="hover:underline"
          >
            {categoryLabel(category)}
          </Link>
          <span className="mx-2">/</span>
          <span className="text-foreground">{tool.title}</span>
        </nav>
        <div className="mt-8">
          <MicroToolsSessionShell>
            <MicroToolDetail tool={tool} />
          </MicroToolsSessionShell>
        </div>
      </Container>
    </Section>
  );
}
