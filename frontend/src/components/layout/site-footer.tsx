import Link from "next/link";
import { nav, site } from "@/lib/constants";
import { Container } from "@/components/ui/container";
import { Logo } from "./logo";

function footerExploreLinks(): { href: string; label: string }[] {
  const out: { href: string; label: string }[] = [];
  for (const item of nav) {
    if ("children" in item && item.children) {
      out.push({ href: item.href, label: item.label });
      for (const c of item.children) {
        out.push({ href: c.href, label: c.label });
      }
    } else {
      out.push({ href: item.href, label: item.label });
    }
  }
  return out;
}

export function SiteFooter() {
  const flatLinks = footerExploreLinks();

  return (
    <footer className="border-t border-border bg-muted-bg/30 py-16">
      <Container>
        <div className="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
          <div className="space-y-4">
            <Logo />
            <p className="max-w-xs text-sm text-muted">{site.description}</p>
          </div>
          <div>
            <p className="text-sm font-semibold text-foreground">Explore</p>
            <ul className="mt-4 space-y-2 text-sm text-muted">
              {flatLinks.map((l) => (
                <li key={`${l.href}-${l.label}`}>
                  <Link
                    href={l.href}
                    className="hover:text-foreground transition-colors"
                  >
                    {l.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
          <div>
            <p className="text-sm font-semibold text-foreground">Services</p>
            <ul className="mt-4 space-y-2 text-sm text-muted">
              <li>
                <Link href="/services/software" className="hover:text-foreground">
                  Software development
                </Link>
              </li>
              <li>
                <Link href="/services/saas" className="hover:text-foreground">
                  SaaS platforms
                </Link>
              </li>
              <li>
                <Link href="/services/blockchain" className="hover:text-foreground">
                  Blockchain
                </Link>
              </li>
              <li>
                <Link href="/services/quantum" className="hover:text-foreground">
                  Quantum
                </Link>
              </li>
            </ul>
          </div>
          <div>
            <p className="text-sm font-semibold text-foreground">Contact</p>
            <p className="mt-4 text-sm text-muted">
              Ready to build?{" "}
              <Link href="/contact" className="text-accent hover:underline">
                Get in touch
              </Link>
            </p>
          </div>
        </div>
        <p className="mt-12 border-t border-border pt-8 text-center text-xs text-muted">
          © {new Date().getFullYear()} {site.name}. All rights reserved.
        </p>
      </Container>
    </footer>
  );
}
