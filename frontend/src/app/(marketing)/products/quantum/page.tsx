import type { Metadata } from "next";
import { CmsPage, cmsPageMetadata } from "@/components/cms/cms-page";

export async function generateMetadata(): Promise<Metadata> {
  return cmsPageMetadata({ path: "products/quantum" });
}

export default function ProductQuantumPage() {
  return <CmsPage path="products/quantum" />;
}
