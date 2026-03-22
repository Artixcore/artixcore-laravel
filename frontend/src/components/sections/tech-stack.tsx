import { Container } from "@/components/ui/container";
import { Section } from "@/components/ui/section";

const stack = [
  "TypeScript",
  "Next.js",
  "Laravel",
  "PostgreSQL",
  "Redis",
  "Kubernetes",
  "AWS",
  "Solidity",
  "Rust",
  "Python",
] as const;

export function TechStackSection() {
  return (
    <Section className="bg-muted-bg/40">
      <Container>
        <p className="text-center text-sm font-medium uppercase tracking-wider text-muted">
          Tech we ship with
        </p>
        <ul className="mt-8 flex flex-wrap items-center justify-center gap-3 md:gap-4">
          {stack.map((name) => (
            <li
              key={name}
              className="rounded-full border border-border bg-card px-4 py-2 text-sm font-medium text-foreground shadow-sm"
            >
              {name}
            </li>
          ))}
        </ul>
      </Container>
    </Section>
  );
}
