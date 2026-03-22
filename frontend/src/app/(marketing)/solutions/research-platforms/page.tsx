import type { Metadata } from "next";
import { CmsPage, cmsPageMetadata } from "@/components/cms/cms-page";

export async function generateMetadata(): Promise<Metadata> {
  return cmsPageMetadata({ path: "solutions/research-platforms" });
}

export default function SolutionResearchPlatformsPage() {
  return <CmsPage path="solutions/research-platforms" />;
}
