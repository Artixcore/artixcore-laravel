"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import type { NavItemDTO } from "@/lib/cms-types";
import { cn } from "@/lib/utils";
import { Logo } from "./logo";
import { MobileNav } from "./mobile-nav";
import { SiteMegaNav } from "./site-mega-nav";
import { ThemeToggle } from "./theme-toggle";
import { LinkButton } from "@/components/ui/link-button";

export function SiteHeader({
  navItems,
  logoUrl,
  siteName,
}: {
  navItems: NavItemDTO[];
  logoUrl?: string;
  siteName?: string;
}) {
  const pathname = usePathname();

  return (
    <header className="sticky top-0 z-40 border-b border-border/80 bg-background/80 backdrop-blur-md">
      <div className="mx-auto flex h-14 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <Logo logoUrl={logoUrl} siteName={siteName} />
        <SiteMegaNav items={navItems} />
        <div className="flex items-center gap-2">
          <div className="hidden lg:block">
            <ThemeToggle />
          </div>
          <Link
            href="/dashboard-preview"
            className={cn(
              "hidden rounded-[var(--radius-md)] px-3 py-2 text-sm font-medium text-muted transition-colors hover:bg-muted-bg hover:text-foreground xl:inline-flex",
              pathname.startsWith("/dashboard-preview") && "bg-muted-bg text-foreground"
            )}
          >
            Dashboard preview
          </Link>
          <LinkButton
            href="/contact"
            size="sm"
            className="hidden sm:inline-flex"
          >
            Book a call
          </LinkButton>
          <MobileNav items={navItems} />
        </div>
      </div>
    </header>
  );
}
