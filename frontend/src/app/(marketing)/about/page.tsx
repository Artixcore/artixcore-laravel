import type { Metadata } from "next";
import { Container } from "@/components/ui/container";
import { Prose } from "@/components/ui/prose";
import { Section } from "@/components/ui/section";
import { site } from "@/lib/constants";

export const metadata: Metadata = {
  title: "About",
  description: `Learn about ${site.name} — mission, values, and how we work with product teams.`,
};

export default function AboutPage() {
  return (
    <Section>
      <Container>
        <h1 className="text-4xl font-semibold tracking-tight md:text-5xl">
          About {site.name}
        </h1>
        <Prose className="mt-10">
          <p>
            We are a software studio focused on durable systems: clear
            architecture, honest timelines, and operators who have seen
            production fire drills and still sleep at night.
          </p>
          <h2>Mission</h2>
          <p>
            Help ambitious teams ship reliable software — from first API to
            global scale — without sacrificing security or developer experience.
          </p>
          <h2>How we work</h2>
          <p>
            Embedded squads, tight feedback loops, and documentation that
            outlasts any single engineer. You get pragmatic defaults, not
            resume-driven complexity.
          </p>
          <h2>Team</h2>
          <p>
            Senior engineers across backend, frontend, infrastructure, and
            security. We pull specialists for blockchain, mobile, and quantum
            workstreams when your roadmap needs depth.
          </p>
        </Prose>
      </Container>
    </Section>
  );
}
