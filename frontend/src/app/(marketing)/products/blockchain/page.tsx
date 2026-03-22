import type { Metadata } from "next";
import { CmsPage, cmsPageMetadata } from "@/components/cms/cms-page";

export async function generateMetadata(): Promise<Metadata> {
  return cmsPageMetadata({ path: "products/blockchain" });
}

export default function ProductBlockchainPage() {
  return <CmsPage path="products/blockchain" />;
}
