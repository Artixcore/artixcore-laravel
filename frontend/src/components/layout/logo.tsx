import Link from "next/link";
import { cn } from "@/lib/utils";
import { site } from "@/lib/constants";

export function Logo({ className }: { className?: string }) {
  return (
    <Link
      href="/"
      className={cn(
        "inline-flex items-center gap-2 font-semibold tracking-tight text-foreground",
        className
      )}
    >
      <span
        className="flex h-8 w-8 items-center justify-center rounded-[var(--radius-md)] bg-accent text-sm font-bold text-white"
        aria-hidden
      >
        A
      </span>
      <span>{site.name}</span>
    </Link>
  );
}
