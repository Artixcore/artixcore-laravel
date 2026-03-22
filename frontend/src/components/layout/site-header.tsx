"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import * as React from "react";
import { ChevronDown } from "lucide-react";
import { nav } from "@/lib/constants";
import { cn } from "@/lib/utils";
import { Logo } from "./logo";
import { MobileNav } from "./mobile-nav";
import { ThemeToggle } from "./theme-toggle";
import { LinkButton } from "@/components/ui/link-button";

export function SiteHeader() {
  const pathname = usePathname();

  return (
    <header className="sticky top-0 z-40 border-b border-border/80 bg-background/80 backdrop-blur-md">
      <div className="mx-auto flex h-14 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <Logo />
        <nav
          className="hidden items-center gap-1 lg:flex"
          aria-label="Main"
        >
          {nav.map((item) =>
            "children" in item && item.children ? (
              <div key={item.href} className="relative group">
                <button
                  type="button"
                  className={cn(
                    "inline-flex items-center gap-1 rounded-[var(--radius-md)] px-3 py-2 text-sm font-medium text-muted transition-colors hover:bg-muted-bg hover:text-foreground",
                    item.children.some((c) => pathname.startsWith(c.href)) &&
                      "text-foreground"
                  )}
                  aria-expanded="false"
                  aria-haspopup="true"
                >
                  {item.label}
                  <ChevronDown className="h-4 w-4 opacity-60" />
                </button>
                <div className="invisible absolute left-0 top-full z-50 pt-2 opacity-0 transition-[opacity,visibility] group-hover:visible group-hover:opacity-100">
                  <div className="min-w-[220px] rounded-[var(--radius-lg)] border border-border bg-card p-2 shadow-lg">
                    {item.children.map((c) => (
                      <Link
                        key={c.href}
                        href={c.href}
                        className={cn(
                          "block rounded-[var(--radius-md)] px-3 py-2 text-sm text-muted hover:bg-muted-bg hover:text-foreground",
                          pathname === c.href && "bg-muted-bg text-foreground"
                        )}
                      >
                        {c.label}
                      </Link>
                    ))}
                  </div>
                </div>
              </div>
            ) : (
              <Link
                key={item.href}
                href={item.href}
                className={cn(
                  "rounded-[var(--radius-md)] px-3 py-2 text-sm font-medium text-muted transition-colors hover:bg-muted-bg hover:text-foreground",
                  pathname === item.href && "bg-muted-bg text-foreground"
                )}
              >
                {item.label}
              </Link>
            )
          )}
        </nav>
        <div className="flex items-center gap-2">
          <div className="hidden lg:block">
            <ThemeToggle />
          </div>
          <LinkButton
            href="/contact"
            size="sm"
            className="hidden sm:inline-flex"
          >
            Book a call
          </LinkButton>
          <MobileNav />
        </div>
      </div>
    </header>
  );
}
