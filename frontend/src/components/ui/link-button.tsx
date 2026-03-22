import Link from "next/link";
import { cn } from "@/lib/utils";
import type { ButtonProps } from "./button";

type LinkButtonProps = Omit<ButtonProps, "type"> &
  React.ComponentProps<typeof Link> & { href: string };

export function LinkButton({
  className,
  variant = "primary",
  size = "md",
  href,
  ...props
}: LinkButtonProps) {
  return (
    <Link
      href={href}
      className={cn(
        "inline-flex items-center justify-center gap-2 font-medium transition-[color,background,border,box-shadow] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background",
        {
          primary:
            "rounded-[var(--radius-md)] bg-accent text-white shadow-sm hover:brightness-110 active:brightness-95",
          secondary:
            "rounded-[var(--radius-md)] bg-muted-bg text-foreground hover:bg-border/60",
          ghost:
            "rounded-[var(--radius-md)] text-foreground hover:bg-muted-bg",
          outline:
            "rounded-[var(--radius-md)] border border-border bg-transparent hover:bg-muted-bg",
        }[variant],
        {
          sm: "h-9 px-3 text-sm",
          md: "h-10 px-4 text-sm",
          lg: "h-11 px-6 text-base",
        }[size],
        className
      )}
      {...props}
    />
  );
}
