import Link from "next/link";
import type { FeatureGridBlockData } from "@/lib/blocks/schemas";
import { Section } from "@/components/ui/section";
import { cn } from "@/lib/utils";

export function FeatureGridBlock({ data }: { data: FeatureGridBlockData }) {
  return (
    <Section className="border-b border-border/40">
      {data.heading ? (
        <h2 className="text-2xl font-semibold tracking-tight">{data.heading}</h2>
      ) : null}
      <ul
        className={cn(
          "mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3",
          data.heading ? "" : "mt-0"
        )}
      >
        {data.items.map((item) => (
          <li key={item.title}>
            {item.href ? (
              <Link
                href={item.href}
                className="block h-full rounded-[var(--radius-lg)] border border-border/80 bg-card/40 p-6 transition-colors hover:border-border hover:bg-card"
              >
                <CardInner item={item} />
              </Link>
            ) : (
              <div className="h-full rounded-[var(--radius-lg)] border border-border/80 bg-card/40 p-6">
                <CardInner item={item} />
              </div>
            )}
          </li>
        ))}
      </ul>
    </Section>
  );
}

function CardInner({
  item,
}: {
  item: { title: string; description?: string };
}) {
  return (
    <>
      <h3 className="text-lg font-semibold">{item.title}</h3>
      {item.description ? (
        <p className="mt-2 text-sm text-muted">{item.description}</p>
      ) : null}
    </>
  );
}
