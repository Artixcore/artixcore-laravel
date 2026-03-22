import { Container } from "@/components/ui/container";

export default function MarketingLoading() {
  return (
    <div className="border-b border-border py-24">
      <Container>
        <div className="h-8 w-48 animate-pulse rounded-md bg-muted-bg" />
        <div className="mt-6 h-4 max-w-xl animate-pulse rounded bg-muted-bg" />
        <div className="mt-4 h-4 max-w-lg animate-pulse rounded bg-muted-bg" />
      </Container>
    </div>
  );
}
