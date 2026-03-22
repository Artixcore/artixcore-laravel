import * as React from "react";
import { cn } from "@/lib/utils";

/** Simple readable text column — no typography plugin required */
export function Prose({
  className,
  ...props
}: React.HTMLAttributes<HTMLDivElement>) {
  return (
    <div
      className={cn(
        "max-w-2xl space-y-4 text-base leading-relaxed text-muted [&_h2]:text-foreground [&_h2]:text-xl [&_h2]:font-semibold [&_h3]:text-foreground [&_h3]:text-lg [&_h3]:font-medium [&_strong]:text-foreground",
        className
      )}
      {...props}
    />
  );
}
