"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import * as React from "react";
import { Menu, X } from "lucide-react";
import type { NavItemDTO } from "@/lib/cms-types";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { ThemeToggle } from "./theme-toggle";

function isActive(pathname: string, href: string | null): boolean {
  if (!href || href === "#") {
    return false;
  }
  if (href === "/") {
    return pathname === "/";
  }
  return pathname === href || pathname.startsWith(`${href}/`);
}

export function MobileNav({ items }: { items: NavItemDTO[] }) {
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
          className="fixed inset-x-0 top-14 z-50 max-h-[min(80vh,calc(100vh-3.5rem))] overflow-y-auto border-b border-border bg-background/95 px-4 py-4 shadow-lg backdrop-blur-md"
          role="dialog"
          aria-modal="true"
        >
          <nav className="flex flex-col gap-1" aria-label="Mobile">
            {items.map((item) =>
              item.children.length > 0 ? (
                <div key={item.id} className="flex flex-col gap-1 py-2">
                  <span className="text-xs font-semibold uppercase tracking-wider text-muted">
                    {item.label}
                  </span>
                  {item.children.map((c) => (
                    <Link
                      key={c.id}
                      href={c.href ?? "#"}
                      className={cn(
                        "rounded-[var(--radius-md)] px-3 py-2 text-sm hover:bg-muted-bg",
                        isActive(pathname, c.href) && "bg-muted-bg text-foreground"
                      )}
                    >
                      {c.label}
                    </Link>
                  ))}
                </div>
              ) : (
                <Link
                  key={item.id}
                  href={item.href ?? "/"}
                  className={cn(
                    "rounded-[var(--radius-md)] px-3 py-2 text-sm font-medium hover:bg-muted-bg",
                    isActive(pathname, item.href) && "bg-muted-bg"
                  )}
                >
                  {item.label}
                </Link>
              )
            )}
            <Link
              href="/dashboard-preview"
              className={cn(
                "rounded-[var(--radius-md)] px-3 py-2 text-sm font-medium hover:bg-muted-bg",
                pathname.startsWith("/dashboard-preview") && "bg-muted-bg"
              )}
            >
              Dashboard preview
            </Link>
          </nav>
        </div>
      ) : null}
    </div>
  );
}
