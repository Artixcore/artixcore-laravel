import type { Metadata } from "next";
import { Geist, Geist_Mono } from "next/font/google";
import { ThemeProvider } from "@/components/providers/theme-provider";
import { getSite } from "@/lib/cms-api";
import { site } from "@/lib/constants";
import "./globals.css";

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
  display: "swap",
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
  display: "swap",
});

const metadataBase = new URL(
  process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000"
);

function staticRootMetadata(): Metadata {
  return {
    metadataBase,
    title: {
      default: `${site.name} — ${site.tagline}`,
      template: `%s — ${site.name}`,
    },
    description: site.description,
    icons: {
      icon: [{ url: site.logoUrl, type: "image/png" }],
      apple: [{ url: site.logoUrl }],
    },
    openGraph: {
      type: "website",
      siteName: site.name,
      title: site.name,
      description: site.description,
      images: [{ url: site.logoUrl, alt: `${site.name} logo` }],
    },
    twitter: {
      card: "summary_large_image",
      title: site.name,
      description: site.description,
      images: [site.logoUrl],
    },
    robots: { index: true, follow: true },
  };
}

export async function generateMetadata(): Promise<Metadata> {
  try {
    const cms = await getSite();
    const name = cms.site_name?.trim() || site.name;
    const titleDefault =
      cms.default_meta_title?.trim() || `${name} — ${site.tagline}`;
    const description =
      cms.default_meta_description?.trim() || site.description;
    const iconUrl = cms.favicon_url || cms.logo?.url || site.logoUrl;
    const appleUrl = cms.logo?.url || iconUrl;
    const ogImageUrl = cms.og_default?.url || cms.logo?.url || site.logoUrl;
    const ogAlt =
      cms.og_default?.alt?.trim() ||
      cms.logo?.alt?.trim() ||
      `${name} logo`;
    const ogTitle = cms.default_meta_title?.trim() || name;

    return {
      metadataBase,
      title: {
        default: titleDefault,
        template: `%s — ${name}`,
      },
      description,
      icons: {
        icon: [{ url: iconUrl, type: "image/png" }],
        apple: [{ url: appleUrl, type: "image/png" }],
      },
      openGraph: {
        type: "website",
        siteName: name,
        title: ogTitle,
        description,
        images: [{ url: ogImageUrl, alt: ogAlt }],
      },
      twitter: {
        card: "summary_large_image",
        title: ogTitle,
        description,
        images: [ogImageUrl],
      },
      robots: { index: true, follow: true },
    };
  } catch {
    return staticRootMetadata();
  }
}

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  let defaultTheme = "dark";
  try {
    const cms = await getSite();
    if (
      cms.theme_default === "light" ||
      cms.theme_default === "dark" ||
      cms.theme_default === "system"
    ) {
      defaultTheme = cms.theme_default;
    }
  } catch {
    // keep default
  }

  return (
    <html
      lang="en"
      suppressHydrationWarning
      data-scroll-behavior="smooth"
      className={`${geistSans.variable} ${geistMono.variable} h-full antialiased`}
    >
      <body className="min-h-full flex flex-col bg-background text-foreground">
        <ThemeProvider defaultTheme={defaultTheme}>{children}</ThemeProvider>
      </body>
    </html>
  );
}
