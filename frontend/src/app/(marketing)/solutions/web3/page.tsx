import type { Metadata } from "next";
import { CmsPage, cmsPageMetadata } from "@/components/cms/cms-page";

export async function generateMetadata(): Promise<Metadata> {
  return cmsPageMetadata({ path: "solutions/web3" });
}

export default function SolutionWeb3Page() {
  return <CmsPage path="solutions/web3" />;
}
