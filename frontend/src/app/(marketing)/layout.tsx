import { SiteFooter } from "@/components/layout/site-footer";
import { SiteHeader } from "@/components/layout/site-header";
import {
  fallbackNavigationItems,
  flattenFooterLinks,
  getFooterNavigation,
  getPrimaryNavigation,
  getSite,
} from "@/lib/cms-api";
import { designTokensToCss } from "@/lib/design-tokens";
import type { NavItemDTO } from "@/lib/cms-types";
import { site as siteConstants } from "@/lib/constants";

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
  let logoUrl: string | undefined;
  let siteName: string | undefined;
  let tokensCss = "";

  try {
    const cmsSite = await getSite();
    logoUrl = cmsSite.logo?.url ?? undefined;
    siteName = cmsSite.site_name ?? siteConstants.name;
    tokensCss = designTokensToCss(cmsSite.design_tokens);
  } catch {
    siteName = siteConstants.name;
  }

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
      {tokensCss ? (
        <style
          id="cms-design-tokens"
          dangerouslySetInnerHTML={{ __html: tokensCss }}
        />
      ) : null}
      <SiteHeader
        navItems={headerItems}
        logoUrl={logoUrl}
        siteName={siteName}
      />
      <main className="flex-1">{children}</main>
      <SiteFooter exploreLinks={footerLinks} />
    </>
  );
}
