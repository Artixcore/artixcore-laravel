import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";
import { LinkButton } from "@/components/ui/link-button";

export function CtaSection() {
  return (
    <Section>
      <Container>
        <div className="relative overflow-hidden rounded-[var(--radius-xl)] border border-border bg-card px-8 py-14 text-center shadow-[var(--shadow-glow)] md:px-16">
          <h2 className="text-3xl font-semibold tracking-tight md:text-4xl">
            Let&apos;s ship your next release
          </h2>
          <p className="mx-auto mt-4 max-w-lg text-muted">
            Tell us about your roadmap — we&apos;ll respond with a clear plan,
            timeline, and team fit.
          </p>
          <div className="mt-8 flex flex-wrap justify-center gap-3">
            <LinkButton href="/contact" size="lg">
              Contact us
            </LinkButton>
            <LinkButton href="/products" variant="outline" size="lg">
              See platforms
            </LinkButton>
          </div>
        </div>
      </Container>
    </Section>
  );
}
