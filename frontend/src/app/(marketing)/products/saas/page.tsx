import type { Metadata } from "next";
import { CmsPage, cmsPageMetadata } from "@/components/cms/cms-page";

export async function generateMetadata(): Promise<Metadata> {
  return cmsPageMetadata({ path: "products/saas" });
}

export default function ProductSaaSPage() {
  return <CmsPage path="products/saas" />;
}
