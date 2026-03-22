import type { Metadata } from "next";
import { Container } from "@/components/ui/container";
import { Card, CardDescription, CardTitle } from "@/components/ui/card";
import { LinkButton } from "@/components/ui/link-button";
import { Section } from "@/components/ui/section";
import { site } from "@/lib/constants";

const products = [
  {
    name: "Artixcore Flow",
    desc: "Workflow automation with approvals, webhooks, and audit trails for regulated teams.",
  },
  {
    name: "Artixcore Ledger Hub",
    desc: "Connect on-chain events to your data warehouse with sane schemas and replay tools.",
  },
  {
    name: "Artixcore Pulse",
    desc: "SaaS metrics in one place — MRR, churn, and infra spend without spreadsheet gymnastics.",
  },
] as const;

export const metadata: Metadata = {
  title: "Products & platforms",
  description: `Platforms and productized capabilities from ${site.name}.`,
};

export default function ProductsPage() {
  return (
    <Section>
      <Container>
        <h1 className="text-4xl font-semibold tracking-tight md:text-5xl">
          Products & platforms
        </h1>
        <p className="mt-4 max-w-2xl text-lg text-muted">
          Opinionated foundations we reuse across engagements — available as
          managed modules or starting points for your roadmap.
        </p>
        <ul className="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {products.map((p) => (
            <li key={p.name}>
              <Card className="h-full">
                <CardTitle>{p.name}</CardTitle>
                <CardDescription className="mt-2">{p.desc}</CardDescription>
                <div className="mt-6">
                  <LinkButton href="/contact" variant="outline" size="sm">
                    Request access
                  </LinkButton>
                </div>
              </Card>
            </li>
          ))}
        </ul>
      </Container>
    </Section>
  );
}
