import type { Metadata } from "next";
import { CmsPage, cmsPageMetadata } from "@/components/cms/cms-page";

export async function generateMetadata(): Promise<Metadata> {
  return cmsPageMetadata({ path: "research" });
}

export default function ResearchPage() {
  return <CmsPage path="research" />;
}
