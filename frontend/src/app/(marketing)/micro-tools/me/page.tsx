"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { getStoredPortalToken } from "@/lib/portal-api";
import {
  fetchToolFavorites,
  fetchToolHistory,
  type MicroToolDTO,
  type ToolHistoryItem,
} from "@/lib/tools-api";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

export default function MicroToolsMePage() {
  const [token, setToken] = useState<string | null>(null);
  const [favorites, setFavorites] = useState<MicroToolDTO[]>([]);
  const [history, setHistory] = useState<ToolHistoryItem[]>([]);

  useEffect(() => {
    const t = getStoredPortalToken();
    setToken(t);
    if (!t) return;
    void fetchToolFavorites(t).then(setFavorites);
    void fetchToolHistory(t).then(setHistory);
  }, []);

  return (
    <Section>
      <Container>
        <nav className="text-sm text-muted">
          <Link href="/micro-tools" className="hover:underline">
            Micro Tools
          </Link>
          <span className="mx-2">/</span>
          <span className="text-foreground">Favorites &amp; history</span>
        </nav>
        <h1 className="mt-4 text-3xl font-semibold tracking-tight">
          Favorites &amp; history
        </h1>
        {!token ? (
          <p className="mt-4 text-muted">
            <Link href="/portal" className="font-medium hover:underline">
              Sign in to the portal
            </Link>{" "}
            to see favorites and recent server tool runs.
          </p>
        ) : (
          <div className="mt-10 grid gap-12 lg:grid-cols-2">
            <section>
              <h2 className="text-lg font-semibold">Favorites</h2>
              <ul className="mt-4 space-y-2">
                {favorites.map((t) => (
                  <li key={t.id}>
                    <Link
                      href={`/micro-tools/${t.category}/${t.slug}`}
                      className="text-sm font-medium hover:underline"
                    >
                      {t.title}
                    </Link>
                  </li>
                ))}
              </ul>
              {favorites.length === 0 ? (
                <p className="mt-2 text-sm text-muted">No favorites yet.</p>
              ) : null}
            </section>
            <section>
              <h2 className="text-lg font-semibold">Recent runs</h2>
              <ul className="mt-4 space-y-3">
                {history.map((h) => (
                  <li
                    key={h.id}
                    className="rounded-lg border border-border/80 p-3 text-sm"
                  >
                    <div className="font-medium">
                      {h.tool?.title ?? "Tool"}{" "}
                      <span className="text-xs font-normal text-muted">
                        {h.status}
                      </span>
                    </div>
                    {h.tool ? (
                      <Link
                        href={`/micro-tools/${h.tool.category}/${h.tool.slug}`}
                        className="mt-1 inline-block text-xs text-muted hover:underline"
                      >
                        Open tool
                      </Link>
                    ) : null}
                  </li>
                ))}
              </ul>
              {history.length === 0 ? (
                <p className="mt-2 text-sm text-muted">
                  Run a server-side tool while signed in to build history.
                </p>
              ) : null}
            </section>
          </div>
        )}
      </Container>
    </Section>
  );
}
