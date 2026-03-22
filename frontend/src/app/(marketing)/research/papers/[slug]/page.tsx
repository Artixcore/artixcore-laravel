import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { Container } from "@/components/ui/container";
import { Prose } from "@/components/ui/prose";
import { Section } from "@/components/ui/section";
import { getApiBase } from "@/lib/cms-api";
type PaperPayload = {
  data: {
    slug: string;
    title: string;
    summary: string | null;
    body: string | null;
    meta_title: string | null;
    meta_description: string | null;
  };
};

async function loadPaper(slug: string): Promise<PaperPayload["data"] | null> {
  try {
    const res = await fetch(
      `${getApiBase()}/research-papers/${encodeURIComponent(slug)}`,
      { headers: { Accept: "application/json" }, cache: "no-store" }
    );
    if (!res.ok) {
      return null;
    }
    const json = (await res.json()) as PaperPayload;
    return json.data;
  } catch {
    return null;
  }
}

export async function generateMetadata({
  params,
}: {
  params: Promise<{ slug: string }>;
}): Promise<Metadata> {
  const { slug } = await params;
  const paper = await loadPaper(slug);
  if (!paper) {
    return { title: "Paper" };
  }
  return {
    title: paper.meta_title ?? paper.title,
    description: paper.meta_description ?? paper.summary ?? undefined,
  };
}

export default async function ResearchPaperDetailPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const paper = await loadPaper(slug);
  if (!paper) {
    notFound();
  }

  return (
    <Section>
      <Container>
        <Link
          href="/research/papers"
          className="text-sm font-medium text-muted hover:text-foreground"
        >
          ← All papers
        </Link>
        <h1 className="mt-6 text-4xl font-semibold tracking-tight">
          {paper.title}
        </h1>
        {paper.summary ? (
          <p className="mt-4 text-lg text-muted">{paper.summary}</p>
        ) : null}
        {paper.body ? (
          <Prose className="mt-10 whitespace-pre-wrap">{paper.body}</Prose>
        ) : null}
      </Container>
    </Section>
  );
}
