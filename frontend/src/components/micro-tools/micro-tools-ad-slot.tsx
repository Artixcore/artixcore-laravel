"use client";

import { cn } from "@/lib/utils";

type Props = {
  className?: string;
  label?: string;
};

/** Placeholder for programmatic ads for guests; hidden when `ad_free` from the API. */
export function MicroToolsAdSlot({
  className,
  label = "Advertisement",
}: Props) {
  const slot = process.env.NEXT_PUBLIC_MICRO_TOOLS_AD_SLOT?.trim();
  return (
    <aside
      className={cn(
        "flex min-h-[90px] items-center justify-center rounded-lg border border-dashed border-zinc-300 bg-zinc-50 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900/40 dark:text-zinc-400",
        className
      )}
      data-ad-slot={slot || undefined}
      aria-label={label}
    >
      <span>{label}</span>
      {slot ? (
        <span className="mt-1 block text-xs opacity-70">Slot: {slot}</span>
      ) : null}
    </aside>
  );
}
