import dynamic from "next/dynamic";
import type { Metadata } from "next";
import { HeroSection } from "@/components/sections/hero";
import { ServicesTeaserSection } from "@/components/sections/services-teaser";
import { site } from "@/lib/constants";

const TechStackSection = dynamic(
  () =>
    import("@/components/sections/tech-stack").then((m) => m.TechStackSection),
  { loading: () => <div className="min-h-[120px]" aria-hidden /> }
);

const SocialProofSection = dynamic(
  () =>
    import("@/components/sections/social-proof").then(
      (m) => m.SocialProofSection
    ),
  { loading: () => <div className="min-h-[160px]" aria-hidden /> }
);

const CtaSection = dynamic(
  () => import("@/components/sections/cta-section").then((m) => m.CtaSection),
  { loading: () => <div className="min-h-[200px]" aria-hidden /> }
);

export const metadata: Metadata = {
  title: "Home",
  description: site.description,
};

export default function HomePage() {
  return (
    <>
      <HeroSection />
      <ServicesTeaserSection />
      <TechStackSection />
      <SocialProofSection />
      <CtaSection />
    </>
  );
}
