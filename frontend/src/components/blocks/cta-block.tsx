import type { CtaBlockData } from "@/lib/blocks/schemas";
import { LinkButton } from "@/components/ui/link-button";
import { Section } from "@/components/ui/section";

export function CtaBlock({ data }: { data: CtaBlockData }) {
  return (
    <Section className="border-b border-border/40">
      <div className="mx-auto max-w-3xl rounded-[var(--radius-xl)] border border-border bg-muted-bg/30 px-8 py-12 text-center">
        <h2 className="text-2xl font-semibold tracking-tight">{data.title}</h2>
        {data.body ? (
          <p className="mt-3 text-muted">{data.body}</p>
        ) : null}
        {data.href && data.buttonLabel ? (
          <div className="mt-8 flex justify-center">
            <LinkButton href={data.href} size="lg">
              {data.buttonLabel}
            </LinkButton>
          </div>
        ) : null}
      </div>
    </Section>
  );
}
