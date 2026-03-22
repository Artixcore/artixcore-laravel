import { Badge } from "@/components/ui/badge";
import { Container } from "@/components/ui/container";
import { LinkButton } from "@/components/ui/link-button";
import { site } from "@/lib/constants";

export function HeroSection() {
  return (
    <section className="relative overflow-hidden border-b border-border py-20 md:py-28">
      <div
        className="pointer-events-none absolute -right-40 -top-40 h-80 w-80 rounded-full blur-3xl motion-safe:animate-pulse"
        style={{ background: "var(--accent-glow)" }}
        aria-hidden
      />
      <div
        className="pointer-events-none absolute -bottom-32 -left-32 h-72 w-72 rounded-full blur-3xl opacity-60"
        style={{
          background:
            "color-mix(in srgb, var(--accent-secondary) 30%, transparent)",
        }}
        aria-hidden
      />
      <Container className="relative">
        <div className="max-w-3xl">
          <Badge className="mb-6 border-accent/30 bg-accent/10 text-accent">
            Software · SaaS · Blockchain · Quantum
          </Badge>
          <h1 className="text-4xl font-semibold tracking-tight text-foreground sm:text-5xl md:text-6xl md:leading-[1.08]">
            {site.tagline}
          </h1>
          <p className="mt-6 max-w-xl text-lg text-muted md:text-xl">
            {site.description}
          </p>
          <div className="mt-10 flex flex-wrap gap-3">
            <LinkButton href="/contact" size="lg">
              Start a project
            </LinkButton>
            <LinkButton href="/services" variant="outline" size="lg">
              View services
            </LinkButton>
          </div>
        </div>
      </Container>
    </section>
  );
}
