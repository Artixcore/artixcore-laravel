import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

export function SocialProofSection() {
  return (
    <Section className="border-b border-border">
      <Container>
        <div className="grid gap-8 md:grid-cols-3">
          {[
            { k: "99.95%", l: "Target uptime for core APIs" },
            { k: "<200ms", l: "Typical p95 API latency at the edge" },
            { k: "24/7", l: "On-call playbooks for production systems" },
          ].map((row) => (
            <div
              key={row.l}
              className="rounded-[var(--radius-lg)] border border-border bg-card p-6 text-center"
            >
              <p className="text-3xl font-semibold tracking-tight text-accent md:text-4xl">
                {row.k}
              </p>
              <p className="mt-2 text-sm text-muted">{row.l}</p>
            </div>
          ))}
        </div>
      </Container>
    </Section>
  );
}
