import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { Container } from "@/components/ui/container";
import { LinkButton } from "@/components/ui/link-button";
import { Section } from "@/components/ui/section";
import { serviceSlugs } from "@/lib/constants";
import { getService } from "@/lib/services";

type Props = { params: Promise<{ slug: string }> };

export async function generateStaticParams() {
  return serviceSlugs.map((slug) => ({ slug }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const s = getService(slug);
  if (!s) return { title: "Not found" };
  return {
    title: s.title,
    description: s.subtitle,
  };
}

export default async function ServiceDetailPage({ params }: Props) {
  const { slug } = await params;
  const s = getService(slug);
  if (!s) notFound();

  return (
    <Section>
      <Container>
        <p className="text-sm text-muted">
          <Link href="/services" className="hover:text-foreground">
            Services
          </Link>
          <span className="mx-2 text-border">/</span>
          <span className="text-foreground">{s.title}</span>
        </p>
        <h1 className="mt-4 text-4xl font-semibold tracking-tight md:text-5xl">
          {s.title}
        </h1>
        <p className="mt-4 max-w-2xl text-lg text-muted">{s.subtitle}</p>
        <div className="mt-10 max-w-2xl space-y-4 text-muted">
          {s.body.map((p, i) => (
            <p key={i}>{p}</p>
          ))}
        </div>
        <h2 className="mt-12 text-lg font-semibold text-foreground">
          Highlights
        </h2>
        <ul className="mt-4 list-inside list-disc space-y-2 text-muted">
          {s.highlights.map((h) => (
            <li key={h}>{h}</li>
          ))}
        </ul>
        <div className="mt-12">
          <LinkButton href="/contact">Discuss this engagement</LinkButton>
        </div>
      </Container>
    </Section>
  );
}
