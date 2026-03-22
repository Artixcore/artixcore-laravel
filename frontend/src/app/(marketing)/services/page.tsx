import type { Metadata } from "next";
import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";
import { services } from "@/lib/services";
import { site } from "@/lib/constants";

export const metadata: Metadata = {
  title: "Services",
  description: `${site.name} services — software, SaaS, blockchain, quantum, and mobile & web.`,
};

export default function ServicesPage() {
  const list = Object.values(services);
  return (
    <Section>
      <Container>
        <h1 className="text-4xl font-semibold tracking-tight md:text-5xl">
          Services
        </h1>
        <p className="mt-4 max-w-2xl text-lg text-muted">
          End-to-end delivery across your stack. Pick a lane to see how we
          engage.
        </p>
        <ul className="mt-12 space-y-4">
          {list.map((s) => (
            <li key={s.slug}>
              <Link
                href={`/services/${s.slug}`}
                className="group flex items-start justify-between gap-4 rounded-[var(--radius-lg)] border border-border bg-card p-6 transition-[border-color,box-shadow] hover:border-accent/40 hover:shadow-[var(--shadow-glow)]"
              >
                <div>
                  <h2 className="text-xl font-semibold tracking-tight group-hover:text-accent transition-colors">
                    {s.title}
                  </h2>
                  <p className="mt-2 text-muted">{s.subtitle}</p>
                </div>
                <ArrowRight
                  className="mt-1 h-5 w-5 shrink-0 text-muted group-hover:text-accent transition-colors"
                  aria-hidden
                />
              </Link>
            </li>
          ))}
        </ul>
      </Container>
    </Section>
  );
}
