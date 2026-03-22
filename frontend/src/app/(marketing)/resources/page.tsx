import type { Metadata } from "next";
import Link from "next/link";
import { CmsPage, cmsPageMetadata } from "@/components/cms/cms-page";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

export async function generateMetadata(): Promise<Metadata> {
  return cmsPageMetadata({ path: "resources" });
}

export default function ResourcesPage() {
  return (
    <>
      <CmsPage path="resources" />
      <Section className="border-t border-border/40">
        <Container>
          <div className="grid gap-6 sm:grid-cols-2">
            <Link
              href="/resources/articles"
              className="rounded-[var(--radius-lg)] border border-border/80 p-6 transition-colors hover:border-border hover:bg-card"
            >
              <h2 className="text-lg font-semibold">Articles &amp; insights</h2>
              <p className="mt-2 text-sm text-muted">
                Engineering notes, product thinking, and industry context.
              </p>
            </Link>
            <Link
              href="/resources/case-studies"
              className="rounded-[var(--radius-lg)] border border-border/80 p-6 transition-colors hover:border-border hover:bg-card"
            >
              <h2 className="text-lg font-semibold">Case studies</h2>
              <p className="mt-2 text-sm text-muted">
                How we ship with teams under real constraints.
              </p>
            </Link>
          </div>
        </Container>
      </Section>
    </>
  );
}
