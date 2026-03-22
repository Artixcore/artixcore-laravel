import type { Metadata } from "next";
import { ContactForm } from "@/components/contact/contact-form";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";
import { site } from "@/lib/constants";

export const metadata: Metadata = {
  title: "Contact",
  description: `Contact ${site.name} — tell us about your project.`,
};

export default function ContactPage() {
  return (
    <Section>
      <Container>
        <div className="grid gap-12 lg:grid-cols-2 lg:gap-16">
          <div>
            <h1 className="text-4xl font-semibold tracking-tight md:text-5xl">
              Let&apos;s talk
            </h1>
            <p className="mt-4 text-lg text-muted">
              Share a few details — we&apos;ll follow up within two business days
              with next steps.
            </p>
            <p className="mt-6 text-sm text-muted">
              Prefer email? hello@example.com (replace with your production
              address).
            </p>
          </div>
          <div className="rounded-[var(--radius-lg)] border border-border bg-card p-6 md:p-8">
            <ContactForm />
          </div>
        </div>
      </Container>
    </Section>
  );
}
