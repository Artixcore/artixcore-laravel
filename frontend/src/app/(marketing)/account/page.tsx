import type { Metadata } from "next";
import { LinkButton } from "@/components/ui/link-button";
import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

export const metadata: Metadata = {
  title: "Account",
  robots: { index: false, follow: false },
};

/** Placeholder for future Sanctum-authenticated account area */
export default function AccountPlaceholderPage() {
  return (
    <Section>
      <Container className="max-w-lg">
        <h1 className="text-3xl font-semibold tracking-tight">Account</h1>
        <p className="mt-4 text-muted">
          Authentication is not wired yet. When Sanctum SPA login is enabled,
          this route will host the signed-in experience (profile, API tokens,
          billing).
        </p>
        <p className="mt-4 text-sm text-muted">
          Backend: enable session +{" "}
          <code className="rounded bg-muted-bg px-1 py-0.5 text-xs">
            EnsureFrontendRequestsAreStateful
          </code>{" "}
          on API routes that require cookie auth; call{" "}
          <code className="rounded bg-muted-bg px-1 py-0.5 text-xs">
            /sanctum/csrf-cookie
          </code>{" "}
          before mutating requests from the SPA.
        </p>
        <div className="mt-8 flex flex-wrap gap-3">
          <LinkButton href="/">Home</LinkButton>
          <LinkButton href="/contact" variant="outline">
            Contact
          </LinkButton>
        </div>
      </Container>
    </Section>
  );
}
