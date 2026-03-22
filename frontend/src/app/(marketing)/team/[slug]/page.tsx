import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { Container } from "@/components/ui/container";
import { Prose } from "@/components/ui/prose";
import { Section } from "@/components/ui/section";
import { getApiBase } from "@/lib/cms-api";

type ProfilePayload = {
  data: {
    slug: string;
    name: string;
    role: string | null;
    bio: string | null;
  };
};

async function loadProfile(slug: string): Promise<ProfilePayload["data"] | null> {
  try {
    const res = await fetch(`${getApiBase()}/team/${encodeURIComponent(slug)}`, {
      headers: { Accept: "application/json" },
      cache: "no-store",
    });
    if (!res.ok) {
      return null;
    }
    const json = (await res.json()) as ProfilePayload;
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
  const profile = await loadProfile(slug);
  if (!profile) {
    return { title: "Team" };
  }
  return {
    title: profile.name,
    description: profile.role ?? undefined,
  };
}

export default async function TeamMemberPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const profile = await loadProfile(slug);
  if (!profile) {
    notFound();
  }

  return (
    <Section>
      <Container>
        <Link
          href="/team"
          className="text-sm font-medium text-muted hover:text-foreground"
        >
          ← Team
        </Link>
        <h1 className="mt-6 text-4xl font-semibold tracking-tight">
          {profile.name}
        </h1>
        {profile.role ? (
          <p className="mt-2 text-lg text-muted">{profile.role}</p>
        ) : null}
        {profile.bio ? (
          <Prose className="mt-8 whitespace-pre-wrap">{profile.bio}</Prose>
        ) : null}
      </Container>
    </Section>
  );
}
