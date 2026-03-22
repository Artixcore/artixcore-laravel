import type { HeroBlockData } from "@/lib/blocks/schemas";
import { LinkButton } from "@/components/ui/link-button";

export function HeroBlock({ data }: { data: HeroBlockData }) {
  return (
    <section className="relative overflow-hidden border-b border-border/60">
      <div className="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
        {data.eyebrow ? (
          <p className="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-muted">
            {data.eyebrow}
          </p>
        ) : null}
        <h1 className="max-w-3xl text-balance text-4xl font-semibold tracking-tight sm:text-5xl">
          {data.title}
        </h1>
        {data.subtitle ? (
          <p className="mt-5 max-w-2xl text-pretty text-lg text-muted">{data.subtitle}</p>
        ) : null}
        <div className="mt-10 flex flex-wrap gap-3">
          {data.primaryCta ? (
            <LinkButton href={data.primaryCta.href} size="lg">
              {data.primaryCta.label}
            </LinkButton>
          ) : null}
          {data.secondaryCta ? (
            <LinkButton href={data.secondaryCta.href} variant="outline" size="lg">
              {data.secondaryCta.label}
            </LinkButton>
          ) : null}
        </div>
      </div>
    </section>
  );
}
