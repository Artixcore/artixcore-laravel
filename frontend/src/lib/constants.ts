const defaultLogoUrl =
  "https://storaeall.s3.us-east-1.amazonaws.com/artixcore-logo.PNG";

export const site = {
  name: "Artixcore",
  tagline: "Software that scales with ambition",
  description:
    "We build software, SaaS platforms, blockchain systems, quantum-ready tooling, and mobile & web applications for teams who care about craft.",
  /** Navbar, footer, OG image, and favicon (unless overridden). */
  logoUrl: process.env.NEXT_PUBLIC_SITE_LOGO_URL ?? defaultLogoUrl,
  /**
   * If the PNG is icon-only, set true so `Logo` shows `name` next to the image.
   * Default false assumes the asset already includes the wordmark.
   */
  logoIsIconOnly: false,
};

export const nav = [
  { href: "/", label: "Home" },
  { href: "/about", label: "About" },
  {
    href: "/services",
    label: "Services",
    children: [
      { href: "/services/software", label: "Software development" },
      { href: "/services/saas", label: "SaaS platforms" },
      { href: "/services/blockchain", label: "Blockchain" },
      { href: "/services/quantum", label: "Quantum computing" },
      { href: "/services/mobile-web", label: "Mobile & web" },
    ],
  },
  { href: "/products", label: "Products" },
  { href: "/contact", label: "Contact" },
  { href: "/dashboard-preview", label: "Dashboard preview" },
] as const;

export const serviceSlugs = [
  "software",
  "saas",
  "blockchain",
  "quantum",
  "mobile-web",
] as const;

export type ServiceSlug = (typeof serviceSlugs)[number];
