import Link from "next/link";

export default function PreviewLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <div className="flex min-h-full flex-col">
      <div className="flex h-11 items-center justify-between border-b border-border bg-background/90 px-4 text-sm backdrop-blur">
        <Link href="/" className="text-muted hover:text-foreground transition-colors">
          ← Back to site
        </Link>
        <span className="text-xs text-muted">Dashboard preview</span>
      </div>
      <div className="flex-1">{children}</div>
    </div>
  );
}
