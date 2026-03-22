import { SiteFooter } from "@/components/layout/site-footer";
import { SiteHeader } from "@/components/layout/site-header";
import {
  fallbackNavigationItems,
  flattenFooterLinks,
  getFooterNavigation,
  getPrimaryNavigation,
} from "@/lib/cms-api";
import type { NavItemDTO } from "@/lib/cms-types";

const homeItem: NavItemDTO = {
  id: -1,
  label: "Home",
  href: "/",
  feature: null,
  children: [],
};

export default async function MarketingLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  let headerItems: NavItemDTO[] = [homeItem, ...fallbackNavigationItems()];
  try {
    const nav = await getPrimaryNavigation();
    headerItems = [homeItem, ...nav.items];
  } catch {
    // API unreachable — static fallback already set
  }

  let footerLinks = flattenFooterLinks(fallbackNavigationItems());
  try {
    const foot = await getFooterNavigation();
    if (foot.items.length > 0) {
      footerLinks = flattenFooterLinks(foot.items);
    }
  } catch {
    // keep fallback
  }

  return (
    <>
      <SiteHeader navItems={headerItems} />
      <main className="flex-1">{children}</main>
      <SiteFooter exploreLinks={footerLinks} />
    </>
  );
}
