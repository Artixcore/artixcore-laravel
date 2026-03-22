import Link from "next/link";
import { Container } from "@/components/ui/container";
import { LinkButton } from "@/components/ui/link-button";
import { Logo } from "@/components/layout/logo";

export default function NotFound() {
  return (
    <div className="flex min-h-[60vh] flex-col items-center justify-center border-b border-border py-24">
      <Container className="text-center">
        <div className="mb-8 flex justify-center">
          <Logo className="justify-center" />
        </div>
        <p className="text-sm font-medium text-muted">404</p>
        <h1 className="mt-2 text-3xl font-semibold tracking-tight">
          Page not found
        </h1>
        <p className="mt-4 text-muted">
          The page you requested does not exist or was moved.
        </p>
        <div className="mt-8 flex justify-center gap-3">
          <LinkButton href="/">Back home</LinkButton>
          <Link
            href="/contact"
            className="inline-flex h-10 items-center rounded-[var(--radius-md)] border border-border px-4 text-sm font-medium hover:bg-muted-bg"
          >
            Contact
          </Link>
        </div>
      </Container>
    </div>
  );
}
