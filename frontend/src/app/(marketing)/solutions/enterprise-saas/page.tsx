import type { Metadata } from "next";
import { CmsPage, cmsPageMetadata } from "@/components/cms/cms-page";

export async function generateMetadata(): Promise<Metadata> {
  return cmsPageMetadata({ path: "solutions/enterprise-saas" });
}

export default function SolutionEnterpriseSaasPage() {
  return <CmsPage path="solutions/enterprise-saas" />;
}
