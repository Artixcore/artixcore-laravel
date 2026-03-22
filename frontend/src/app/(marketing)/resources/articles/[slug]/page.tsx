import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { Container } from "@/components/ui/container";
import { Prose } from "@/components/ui/prose";
import { Section } from "@/components/ui/section";
import { getApiBase } from "@/lib/cms-api";

type ArticlePayload = {
  data: {
    slug: string;
    title: string;
    summary: string | null;
    body: string | null;
    meta_title: string | null;
    meta_description: string | null;
  };
};

async function loadArticle(slug: string): Promise<ArticlePayload["data"] | null> {
  try {
    const res = await fetch(
      `${getApiBase()}/articles/${encodeURIComponent(slug)}`,
      { headers: { Accept: "application/json" }, cache: "no-store" }
    );
    if (!res.ok) {
      return null;
    }
    const json = (await res.json()) as ArticlePayload;
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
  const article = await loadArticle(slug);
  if (!article) {
    return { title: "Article" };
  }
  return {
    title: article.meta_title ?? article.title,
    description: article.meta_description ?? article.summary ?? undefined,
  };
}

export default async function ArticleDetailPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const article = await loadArticle(slug);
  if (!article) {
    notFound();
  }

  return (
    <Section>
      <Container>
        <Link
          href="/resources/articles"
          className="text-sm font-medium text-muted hover:text-foreground"
        >
          ← All articles
        </Link>
        <h1 className="mt-6 text-4xl font-semibold tracking-tight">
          {article.title}
        </h1>
        {article.summary ? (
          <p className="mt-4 text-lg text-muted">{article.summary}</p>
        ) : null}
        {article.body ? (
          <Prose className="mt-10 whitespace-pre-wrap">{article.body}</Prose>
        ) : null}
      </Container>
    </Section>
  );
}
