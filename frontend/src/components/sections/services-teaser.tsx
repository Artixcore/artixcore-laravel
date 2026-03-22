import {
  Blocks,
  Cpu,
  Globe,
  Layers,
  Smartphone,
} from "lucide-react";
import Link from "next/link";
import { Card, CardDescription, CardTitle } from "@/components/ui/card";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";
import { LinkButton } from "@/components/ui/link-button";

const items = [
  {
    title: "Software development",
    desc: "Custom systems, APIs, and integrations engineered for reliability.",
    href: "/services/software",
    icon: Layers,
  },
  {
    title: "SaaS platforms",
    desc: "Multi-tenant products with billing, auth, and observability baked in.",
    href: "/services/saas",
    icon: Globe,
  },
  {
    title: "Blockchain solutions",
    desc: "Smart contracts, wallets, and secure on-chain workflows.",
    href: "/services/blockchain",
    icon: Blocks,
  },
  {
    title: "Quantum computing",
    desc: "Research tooling, simulators, and hybrid classical–quantum pipelines.",
    href: "/services/quantum",
    icon: Cpu,
  },
  {
    title: "Mobile & web",
    desc: "Fast, accessible experiences across iOS, Android, and the browser.",
    href: "/services/mobile-web",
    icon: Smartphone,
  },
] as const;

export function ServicesTeaserSection() {
  return (
    <Section className="border-b border-border">
      <Container>
        <div className="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
          <div>
            <h2 className="text-3xl font-semibold tracking-tight md:text-4xl">
              What we build
            </h2>
            <p className="mt-2 max-w-xl text-muted">
              One partner for ambitious product teams — from zero to scale.
            </p>
          </div>
          <LinkButton href="/services" variant="outline">
            All services
          </LinkButton>
        </div>
        <ul className="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {items.map(({ title, desc, href, icon: Icon }) => (
            <li key={href}>
              <Link href={href} className="group block h-full">
                <Card className="h-full transition-[border-color,box-shadow] hover:border-accent/40 hover:shadow-[var(--shadow-glow)]">
                  <div className="flex items-start gap-4">
                    <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-[var(--radius-md)] bg-muted-bg text-accent">
                      <Icon className="h-5 w-5" aria-hidden />
                    </span>
                    <div>
                      <CardTitle className="group-hover:text-accent transition-colors">
                        {title}
                      </CardTitle>
                      <CardDescription>{desc}</CardDescription>
                    </div>
                  </div>
                </Card>
              </Link>
            </li>
          ))}
        </ul>
      </Container>
    </Section>
  );
}
