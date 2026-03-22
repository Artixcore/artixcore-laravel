"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import * as React from "react";
import { Menu, X } from "lucide-react";
import { nav } from "@/lib/constants";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { ThemeToggle } from "./theme-toggle";

export function MobileNav() {
  const [open, setOpen] = React.useState(false);
  const pathname = usePathname();

  React.useEffect(() => {
    setOpen(false);
  }, [pathname]);

  return (
    <div className="flex items-center gap-2 lg:hidden">
      <ThemeToggle />
      <Button
        type="button"
        variant="ghost"
        size="sm"
        className="h-9 w-9 p-0"
        aria-expanded={open}
        aria-controls="mobile-nav"
        onClick={() => setOpen((o) => !o)}
        aria-label={open ? "Close menu" : "Open menu"}
      >
        {open ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
      </Button>
      {open ? (
        <div
          id="mobile-nav"
          className="fixed inset-x-0 top-14 z-50 border-b border-border bg-background/95 px-4 py-4 shadow-lg backdrop-blur-md"
          role="dialog"
          aria-modal="true"
        >
          <nav className="flex flex-col gap-1">
            {nav.map((item) =>
              "children" in item && item.children ? (
                <div key={item.href} className="flex flex-col gap-1 py-2">
                  <span className="text-xs font-semibold uppercase tracking-wider text-muted">
                    {item.label}
                  </span>
                  {item.children.map((c) => (
                    <Link
                      key={c.href}
                      href={c.href}
                      className={cn(
                        "rounded-[var(--radius-md)] px-3 py-2 text-sm hover:bg-muted-bg",
                        pathname === c.href && "bg-muted-bg text-foreground"
                      )}
                    >
                      {c.label}
                    </Link>
                  ))}
                </div>
              ) : (
                <Link
                  key={item.href}
                  href={item.href}
                  className={cn(
                    "rounded-[var(--radius-md)] px-3 py-2 text-sm font-medium hover:bg-muted-bg",
                    pathname === item.href && "bg-muted-bg"
                  )}
                >
                  {item.label}
                </Link>
              )
            )}
          </nav>
        </div>
      ) : null}
    </div>
  );
}
