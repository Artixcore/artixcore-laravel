import * as React from "react";
import { cn } from "@/lib/utils";

export type ButtonProps = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: "primary" | "secondary" | "ghost" | "outline";
  size?: "sm" | "md" | "lg";
};

export const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant = "primary", size = "md", ...props }, ref) => (
    <button
      ref={ref}
      className={cn(
        "inline-flex items-center justify-center gap-2 font-medium transition-[color,background,border,box-shadow] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:pointer-events-none disabled:opacity-50",
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
  )
);
Button.displayName = "Button";
