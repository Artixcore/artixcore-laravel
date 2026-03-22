import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { Container } from "@/components/ui/container";
import { Prose } from "@/components/ui/prose";
import { Section } from "@/components/ui/section";
import { getApiBase } from "@/lib/cms-api";

type StudyPayload = {
  data: {
    slug: string;
    title: string;
    client_name: string | null;
    summary: string | null;
    body: string | null;
    meta_title: string | null;
    meta_description: string | null;
  };
};

async function loadStudy(slug: string): Promise<StudyPayload["data"] | null> {
  try {
    const res = await fetch(
      `${getApiBase()}/case-studies/${encodeURIComponent(slug)}`,
      { headers: { Accept: "application/json" }, cache: "no-store" }
    );
    if (!res.ok) {
      return null;
    }
    const json = (await res.json()) as StudyPayload;
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
  const study = await loadStudy(slug);
  if (!study) {
    return { title: "Case study" };
  }
  return {
    title: study.meta_title ?? study.title,
    description: study.meta_description ?? study.summary ?? undefined,
  };
}

export default async function CaseStudyDetailPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const study = await loadStudy(slug);
  if (!study) {
    notFound();
  }

  return (
    <Section>
      <Container>
        <Link
          href="/resources/case-studies"
          className="text-sm font-medium text-muted hover:text-foreground"
        >
          ← All case studies
        </Link>
        <h1 className="mt-6 text-4xl font-semibold tracking-tight">
          {study.title}
        </h1>
        {study.client_name ? (
          <p className="mt-2 text-sm font-medium text-muted">
            {study.client_name}
          </p>
        ) : null}
        {study.summary ? (
          <p className="mt-4 text-lg text-muted">{study.summary}</p>
        ) : null}
        {study.body ? (
          <Prose className="mt-10 whitespace-pre-wrap">{study.body}</Prose>
        ) : null}
      </Container>
    </Section>
  );
}
