import { CmsPage, cmsPageMetadata } from "@/components/cms/cms-page";

type Props = {
  params: Promise<{ slug: string[] }>;
};

export async function generateMetadata({ params }: Props) {
  const { slug } = await params;
  const path = slug.join("/");
  return cmsPageMetadata({ path });
}

export default async function MarketingCmsCatchAllPage({ params }: Props) {
  const { slug } = await params;
  const path = slug.join("/");
  return <CmsPage path={path} />;
}
