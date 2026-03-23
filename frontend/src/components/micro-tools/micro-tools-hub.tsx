"use client";

import { useMemo, useState } from "react";
import Link from "next/link";
import type { MicroToolDTO } from "@/lib/tools-api";
import {
  MICRO_TOOL_CATEGORY_ORDER,
  categoryLabel,
} from "@/lib/micro-tools-categories";
import { Input } from "@/components/ui/input";

export function MicroToolsHub({ tools }: { tools: MicroToolDTO[] }) {
  const [q, setQ] = useState("");

  const filtered = useMemo(() => {
    const needle = q.trim().toLowerCase();
    if (!needle) return tools;
    return tools.filter(
      (t) =>
        t.title.toLowerCase().includes(needle) ||
        t.description.toLowerCase().includes(needle) ||
        t.slug.includes(needle)
    );
  }, [tools, q]);

  const popular = filtered.filter((t) => t.is_popular).slice(0, 8);
  const newest = filtered.filter((t) => t.is_new).slice(0, 8);

  const byCategory = useMemo(() => {
    const m = new Map<string, MicroToolDTO[]>();
    for (const c of MICRO_TOOL_CATEGORY_ORDER) {
      m.set(c, []);
    }
    for (const t of filtered) {
      const list = m.get(t.category) ?? [];
      list.push(t);
      m.set(t.category, list);
    }
    return m;
  }, [filtered]);

  return (
    <div className="space-y-10">
      <div className="max-w-xl">
        <label htmlFor="tool-search" className="sr-only">
          Search tools
        </label>
        <Input
          id="tool-search"
          type="search"
          placeholder="Search by name or description…"
          value={q}
          onChange={(e) => setQ(e.target.value)}
          className="w-full"
        />
      </div>

      {popular.length > 0 ? (
        <section>
          <h2 className="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
            Popular
          </h2>
          <ul className="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {popular.map((t) => (
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
        </section>
      ) : null}

      {newest.length > 0 ? (
        <section>
          <h2 className="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
            New
          </h2>
          <ul className="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {newest.map((t) => (
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
        </section>
      ) : null}

      <section>
        <h2 className="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
          All categories
        </h2>
        <div className="mt-6 space-y-10">
          {MICRO_TOOL_CATEGORY_ORDER.map((cat) => {
            const list = byCategory.get(cat) ?? [];
            if (list.length === 0) return null;
            return (
              <div key={cat}>
                <h3 className="text-base font-medium text-zinc-800 dark:text-zinc-200">
                  <Link
                    href={`/micro-tools/${cat}`}
                    className="hover:underline"
                  >
                    {categoryLabel(cat)}
                  </Link>
                </h3>
                <ul className="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                  {list.map((t) => (
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
              </div>
            );
          })}
        </div>
      </section>

      {filtered.length === 0 ? (
        <p className="text-sm text-muted">No tools match your search.</p>
      ) : null}
    </div>
  );
}
