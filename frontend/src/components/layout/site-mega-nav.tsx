"use client";

import * as NavigationMenu from "@radix-ui/react-navigation-menu";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { ChevronDown } from "lucide-react";
import type { NavItemDTO } from "@/lib/cms-types";
import { cn } from "@/lib/utils";

const triggerClass =
  "group inline-flex items-center gap-1 rounded-[var(--radius-md)] px-3 py-2 text-sm font-medium text-muted outline-none transition-colors hover:bg-muted-bg hover:text-foreground data-[state=open]:bg-muted-bg data-[state=open]:text-foreground focus-visible:ring-2 focus-visible:ring-ring";

function isActivePath(pathname: string, href: string | null): boolean {
  if (!href || href === "#") {
    return false;
  }
  if (href === "/") {
    return pathname === "/";
  }
  return pathname === href || pathname.startsWith(`${href}/`);
}

function itemOrChildActive(pathname: string, item: NavItemDTO): boolean {
  if (isActivePath(pathname, item.href)) {
    return true;
  }
  return item.children.some((c) => isActivePath(pathname, c.href));
}

export function SiteMegaNav({ items }: { items: NavItemDTO[] }) {
  const pathname = usePathname();

  return (
    <NavigationMenu.Root className="relative z-30 hidden lg:flex" delayDuration={40}>
      <NavigationMenu.List className="flex items-center gap-0.5">
        {items.map((item) => {
          const hasChildren = item.children.length > 0;
          const active = itemOrChildActive(pathname, item);

          if (hasChildren) {
            return (
              <NavigationMenu.Item key={item.id}>
                <NavigationMenu.Trigger
                  className={cn(triggerClass, active && "text-foreground")}
                >
                  {item.label}
                  <ChevronDown
                    className="h-4 w-4 opacity-60 transition-transform duration-200 group-data-[state=open]:rotate-180"
                    aria-hidden
                  />
                </NavigationMenu.Trigger>
                <NavigationMenu.Content className="absolute left-0 top-full z-50 pt-2 data-[motion=from-end]:animate-in data-[motion=from-start]:animate-in data-[motion=to-end]:animate-out data-[motion=to-start]:animate-out data-[motion=from-end]:fade-in data-[motion=from-start]:fade-in data-[motion=to-end]:fade-out data-[motion=to-start]:fade-out">
                  <div className="w-[min(100vw-2rem,56rem)] rounded-[var(--radius-lg)] border border-border bg-card/95 p-6 shadow-xl backdrop-blur-md">
                    <div className="grid gap-8 md:grid-cols-[1fr,minmax(0,220px)]">
                      <div>
                        <p className="text-xs font-semibold uppercase tracking-wider text-muted">
                          Explore
                        </p>
                        <ul className="mt-4 grid gap-1 sm:grid-cols-2">
                          {item.children.map((c) => (
                            <li key={c.id}>
                              <NavigationMenu.Link asChild>
                                <Link
                                  href={c.href ?? "#"}
                                  className={cn(
                                    "block rounded-[var(--radius-md)] px-3 py-2 text-sm text-muted transition-colors hover:bg-muted-bg hover:text-foreground",
                                    isActivePath(pathname, c.href) &&
                                      "bg-muted-bg text-foreground"
                                  )}
                                >
                                  {c.label}
                                </Link>
                              </NavigationMenu.Link>
                            </li>
                          ))}
                        </ul>
                      </div>
                      {item.feature &&
                      (item.feature.title || item.feature.description) ? (
                        <aside className="rounded-[var(--radius-md)] border border-border/80 bg-muted-bg/40 p-4">
                          {item.feature.title ? (
                            <p className="text-sm font-semibold">{item.feature.title}</p>
                          ) : null}
                          {item.feature.description ? (
                            <p className="mt-2 text-sm text-muted">
                              {item.feature.description}
                            </p>
                          ) : null}
                          {item.feature.href ? (
                            <Link
                              href={item.feature.href}
                              className="mt-4 inline-flex text-sm font-medium text-foreground underline-offset-4 hover:underline"
                            >
                              Learn more
                            </Link>
                          ) : null}
                        </aside>
                      ) : null}
                    </div>
                  </div>
                </NavigationMenu.Content>
              </NavigationMenu.Item>
            );
          }

          return (
            <NavigationMenu.Item key={item.id}>
              <NavigationMenu.Link asChild>
                <Link
                  href={item.href ?? "/"}
                  className={cn(
                    "rounded-[var(--radius-md)] px-3 py-2 text-sm font-medium text-muted transition-colors hover:bg-muted-bg hover:text-foreground",
                    isActivePath(pathname, item.href) && "bg-muted-bg text-foreground"
                  )}
                >
                  {item.label}
                </Link>
              </NavigationMenu.Link>
            </NavigationMenu.Item>
          );
        })}
      </NavigationMenu.List>
    </NavigationMenu.Root>
  );
}
