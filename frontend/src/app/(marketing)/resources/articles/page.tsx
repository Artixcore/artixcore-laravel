import type { Metadata } from "next";
import { cookies } from "next/headers";
import Link from "next/link";
import {
  getArticles,
  interestTopicsHeader,
  INTEREST_TOPICS_COOKIE,
} from "@/lib/cms-api";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

export const metadata: Metadata = {
  title: "Articles & insights",
  description: "Articles on SaaS, blockchain, quantum engineering, and research-led delivery.",
};

export default async function ArticlesIndexPage() {
  const cookieStore = await cookies();
  const headers = interestTopicsHeader(
    cookieStore.get(INTEREST_TOPICS_COOKIE)?.value
  );

  let articles: { slug: string; title: string; summary: string | null }[] = [];
  try {
    const res = await getArticles(undefined, headers);
    articles = res.data.map((a) => ({
      slug: a.slug,
      title: a.title,
      summary: a.summary,
    }));
  } catch {
    articles = [];
  }

  return (
    <Section>
      <Container>
        <h1 className="text-4xl font-semibold tracking-tight">Articles &amp; insights</h1>
        <p className="mt-3 max-w-2xl text-muted">
          Long-form notes from our engineering and research practice.
        </p>
        <ul className="mt-10 space-y-4">
          {articles.map((a) => (
            <li key={a.slug}>
              <Link
                href={`/resources/articles/${a.slug}`}
                className="block rounded-[var(--radius-lg)] border border-border/80 p-5 transition-colors hover:border-border hover:bg-card"
              >
                <h2 className="text-lg font-semibold">{a.title}</h2>
                {a.summary ? (
                  <p className="mt-2 text-sm text-muted">{a.summary}</p>
                ) : null}
              </Link>
            </li>
          ))}
        </ul>
        {articles.length === 0 ? (
          <p className="mt-8 text-sm text-muted">No articles yet.</p>
        ) : null}
      </Container>
    </Section>
  );
}
