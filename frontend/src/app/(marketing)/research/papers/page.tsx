import type { Metadata } from "next";
import Link from "next/link";
import { getResearchPapers } from "@/lib/cms-api";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

export const metadata: Metadata = {
  title: "Research papers",
  description: "Publications, notes, and technical memos from Artixcore research.",
};

export default async function ResearchPapersIndexPage() {
  let papers: { slug: string; title: string; summary: string | null }[] = [];
  try {
    const res = await getResearchPapers();
    papers = res.data.map((p) => ({
      slug: p.slug,
      title: p.title,
      summary: p.summary,
    }));
  } catch {
    papers = [];
  }

  return (
    <Section>
      <Container>
        <h1 className="text-4xl font-semibold tracking-tight">Research papers</h1>
        <p className="mt-3 max-w-2xl text-muted">
          Deep dives and draft frameworks from our R&amp;D practice.
        </p>
        <ul className="mt-10 space-y-4">
          {papers.map((p) => (
            <li key={p.slug}>
              <Link
                href={`/research/papers/${p.slug}`}
                className="block rounded-[var(--radius-lg)] border border-border/80 p-5 transition-colors hover:border-border hover:bg-card"
              >
                <h2 className="text-lg font-semibold">{p.title}</h2>
                {p.summary ? (
                  <p className="mt-2 text-sm text-muted">{p.summary}</p>
                ) : null}
              </Link>
            </li>
          ))}
        </ul>
        {papers.length === 0 ? (
          <p className="mt-8 text-sm text-muted">No papers published yet.</p>
        ) : null}
      </Container>
    </Section>
  );
}
