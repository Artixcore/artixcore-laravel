import type { Metadata } from "next";
import Link from "next/link";
import {
  fetchMicroToolsList,
  type MicroToolDTO,
} from "@/lib/tools-api";
import { MicroToolsHub } from "@/components/micro-tools/micro-tools-hub";
import { MicroToolsSessionShell } from "@/components/micro-tools/micro-tools-session-shell";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

export const metadata: Metadata = {
  title: "Micro Tools",
  description:
    "Free web, DNS, SEO, developer, and marketing utilities. Sign in for ad-free access, favorites, and saved reports.",
};

export default async function MicroToolsHubPage() {
  let tools: MicroToolDTO[] = [];
  try {
    tools = await fetchMicroToolsList({ sort: "default" });
  } catch {
    tools = [];
  }

  return (
    <Section>
      <Container>
        <nav className="text-sm text-muted">
          <Link href="/" className="hover:underline">
            Home
          </Link>
          <span className="mx-2">/</span>
          <span className="text-foreground">Micro Tools</span>
        </nav>
        <h1 className="mt-4 text-4xl font-semibold tracking-tight">
          Micro Tools
        </h1>
        <p className="mt-3 max-w-2xl text-muted">
          Practical, lightweight tools for builders, marketers, and teams.
          Use everything publicly; sign in to the portal for an ad-free
          experience, favorites, and saved run history.
        </p>
        <p className="mt-2 text-sm text-muted">
          <Link href="/micro-tools/me" className="font-medium hover:underline">
            Favorites &amp; history
          </Link>{" "}
          (portal account)
        </p>
        <div className="mt-10">
          <MicroToolsSessionShell>
            <MicroToolsHub tools={tools} />
          </MicroToolsSessionShell>
        </div>
      </Container>
    </Section>
  );
}
