import Image from "next/image";
import Link from "next/link";
import { cn } from "@/lib/utils";
import { site } from "@/lib/constants";

type LogoSize = "sm" | "md";

const sizeClasses: Record<LogoSize, string> = {
  sm: "h-8 max-h-8 max-w-[9rem] sm:max-w-[10rem]",
  md: "h-10 max-h-10 max-w-[11rem] sm:h-11 sm:max-h-11 sm:max-w-[12rem]",
};

export function Logo({
  className,
  size = "sm",
  showWordmark = site.logoIsIconOnly,
  logoUrl = site.logoUrl,
  siteName = site.name,
}: {
  className?: string;
  size?: LogoSize;
  /** Overrides `site.logoIsIconOnly` when the image is icon-only vs full wordmark. */
  showWordmark?: boolean;
  /** CMS/API logo URL; falls back to `site.logoUrl` from env/constants. */
  logoUrl?: string;
  /** Display name next to icon-only logos; defaults to marketing constants. */
  siteName?: string;
}) {
  return (
    <Link
      href="/"
      className={cn(
        "inline-flex items-center gap-2.5 font-semibold tracking-tight text-foreground",
        className
      )}
    >
      <Image
        src={logoUrl}
        alt={showWordmark ? "" : `${siteName} home`}
        width={320}
        height={320}
        className={cn(
          "w-auto shrink-0 object-contain object-left",
          sizeClasses[size]
        )}
        priority
        sizes="(max-width: 640px) 9rem, 10rem"
      />
      {showWordmark ? (
        <span className="leading-none">{siteName}</span>
      ) : null}
    </Link>
  );
}
