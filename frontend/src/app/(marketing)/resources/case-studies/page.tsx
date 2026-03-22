import type { Metadata } from "next";
import Link from "next/link";
import { getCaseStudies } from "@/lib/cms-api";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

export const metadata: Metadata = {
  title: "Case studies",
  description: "Selected delivery stories from Artixcore client work.",
};

export default async function CaseStudiesIndexPage() {
  let studies: { slug: string; title: string; summary: string | null }[] = [];
  try {
    const res = await getCaseStudies();
    studies = res.data.map((s) => ({
      slug: s.slug,
      title: s.title,
      summary: s.summary,
    }));
  } catch {
    studies = [];
  }

  return (
    <Section>
      <Container>
        <h1 className="text-4xl font-semibold tracking-tight">Case studies</h1>
        <p className="mt-3 max-w-2xl text-muted">
          Outcomes, constraints, and how we partnered with product teams.
        </p>
        <ul className="mt-10 space-y-4">
          {studies.map((s) => (
            <li key={s.slug}>
              <Link
                href={`/resources/case-studies/${s.slug}`}
                className="block rounded-[var(--radius-lg)] border border-border/80 p-5 transition-colors hover:border-border hover:bg-card"
              >
                <h2 className="text-lg font-semibold">{s.title}</h2>
                {s.summary ? (
                  <p className="mt-2 text-sm text-muted">{s.summary}</p>
                ) : null}
              </Link>
            </li>
          ))}
        </ul>
        {studies.length === 0 ? (
          <p className="mt-8 text-sm text-muted">No case studies yet.</p>
        ) : null}
      </Container>
    </Section>
  );
}
