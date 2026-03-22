import Link from "next/link";
import { getTrending } from "@/lib/cms-api";
import { Section } from "@/components/ui/section";

export async function TrendingArticlesSection() {
  let items: { slug: string; title: string; summary: string | null }[] = [];
  try {
    const res = await getTrending("articles");
    items = res.data.map((a) => ({
      slug: a.slug,
      title: a.title,
      summary: a.summary,
    }));
  } catch {
    items = [];
  }

  if (items.length === 0) {
    return null;
  }

  return (
    <Section className="border-b border-border/40">
      <div className="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <h2 className="text-2xl font-semibold tracking-tight">Trending insights</h2>
        <Link
          href="/resources/articles"
          className="text-sm font-medium text-muted hover:text-foreground"
        >
          View all articles
        </Link>
      </div>
      <ul className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {items.slice(0, 6).map((a) => (
          <li key={a.slug}>
            <Link
              href={`/resources/articles/${a.slug}`}
              className="block rounded-[var(--radius-lg)] border border-border/80 p-5 transition-colors hover:border-border hover:bg-card"
            >
              <h3 className="font-medium">{a.title}</h3>
              {a.summary ? (
                <p className="mt-2 line-clamp-2 text-sm text-muted">{a.summary}</p>
              ) : null}
            </Link>
          </li>
        ))}
      </ul>
    </Section>
  );
}
