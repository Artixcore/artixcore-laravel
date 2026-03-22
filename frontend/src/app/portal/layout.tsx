import Link from "next/link";

export default function PortalLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <div className="min-h-full bg-background text-foreground">
      <header className="border-b border-border/80 bg-muted-bg/20">
        <div className="mx-auto flex h-14 max-w-4xl items-center justify-between px-4">
          <span className="text-sm font-semibold tracking-tight">
            Artixcore portal
          </span>
          <Link
            href="/"
            className="text-sm text-muted transition-colors hover:text-foreground"
          >
            Marketing site
          </Link>
        </div>
      </header>
      <div className="mx-auto max-w-4xl px-4 py-10">{children}</div>
    </div>
  );
}
