import type { Metadata } from "next";
import Link from "next/link";
import { CmsPage, cmsPageMetadata } from "@/components/cms/cms-page";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

export async function generateMetadata(): Promise<Metadata> {
  return cmsPageMetadata({ path: "company" });
}

export default function CompanyPage() {
  return (
    <>
      <CmsPage path="company" />
      <Section className="border-t border-border/40">
        <Container>
          <div className="grid gap-6 sm:grid-cols-3">
            <Link
              href="/about"
              className="rounded-[var(--radius-lg)] border border-border/80 p-6 transition-colors hover:border-border hover:bg-card"
            >
              <h2 className="text-lg font-semibold">About</h2>
              <p className="mt-2 text-sm text-muted">
                Mission, values, and how we work.
              </p>
            </Link>
            <Link
              href="/team"
              className="rounded-[var(--radius-lg)] border border-border/80 p-6 transition-colors hover:border-border hover:bg-card"
            >
              <h2 className="text-lg font-semibold">Team</h2>
              <p className="mt-2 text-sm text-muted">People behind Artixcore.</p>
            </Link>
            <Link
              href="/contact"
              className="rounded-[var(--radius-lg)] border border-border/80 p-6 transition-colors hover:border-border hover:bg-card"
            >
              <h2 className="text-lg font-semibold">Contact</h2>
              <p className="mt-2 text-sm text-muted">Start a conversation.</p>
            </Link>
          </div>
        </Container>
      </Section>
    </>
  );
}
