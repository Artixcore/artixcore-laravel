import type { Metadata } from "next";
import Link from "next/link";
import { getTeamProfiles } from "@/lib/cms-api";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

export const metadata: Metadata = {
  title: "Team",
  description: "Meet the Artixcore team.",
};

type Profile = {
  slug: string;
  name: string;
  role: string | null;
};

export default async function TeamPage() {
  let profiles: Profile[] = [];
  try {
    const res = await getTeamProfiles();
    profiles = (res.data as Profile[]).map((p) => ({
      slug: p.slug,
      name: p.name,
      role: p.role,
    }));
  } catch {
    profiles = [];
  }

  return (
    <Section>
      <Container>
        <h1 className="text-4xl font-semibold tracking-tight">Team</h1>
        <p className="mt-3 max-w-2xl text-muted">
          Operators, researchers, and builders shipping serious software.
        </p>
        <ul className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {profiles.map((p) => (
            <li key={p.slug}>
              <Link
                href={`/team/${p.slug}`}
                className="block rounded-[var(--radius-lg)] border border-border/80 p-6 transition-colors hover:border-border hover:bg-card"
              >
                <h2 className="text-lg font-semibold">{p.name}</h2>
                {p.role ? (
                  <p className="mt-1 text-sm text-muted">{p.role}</p>
                ) : null}
              </Link>
            </li>
          ))}
        </ul>
        {profiles.length === 0 ? (
          <p className="mt-8 text-sm text-muted">Team profiles coming soon.</p>
        ) : null}
      </Container>
    </Section>
  );
}
