import type { ServiceSlug } from "./constants";

export type ServiceDetail = {
  slug: ServiceSlug;
  title: string;
  subtitle: string;
  body: string[];
  highlights: string[];
};

export const services: Record<ServiceSlug, ServiceDetail> = {
  software: {
    slug: "software",
    title: "Software development",
    subtitle:
      "Bespoke applications, internal tools, and integrations that stay maintainable for years.",
    body: [
      "We design systems around your domain: clear boundaries, testable modules, and observability from day one.",
      "Whether you are modernizing a monolith or greenfielding a service mesh, we align delivery with your risk profile and compliance needs.",
    ],
    highlights: [
      "Domain-driven APIs & eventing",
      "CI/CD, IaC, and staged rollouts",
      "Performance budgets and SLOs",
    ],
  },
  saas: {
    slug: "saas",
    title: "SaaS platforms",
    subtitle:
      "Multi-tenant products with secure isolation, metering, and growth-ready billing.",
    body: [
      "From signup to enterprise SSO, we implement auth flows, org models, and admin consoles that scale with your customer base.",
      "We help you instrument funnels, ship feature flags, and iterate without breaking trust.",
    ],
    highlights: [
      "Tenant isolation & RBAC",
      "Usage-based billing hooks",
      "Product analytics pipelines",
    ],
  },
  blockchain: {
    slug: "blockchain",
    title: "Blockchain solutions",
    subtitle:
      "Wallets, smart contracts, and backends that treat security and UX as equals.",
    body: [
      "We build on-chain logic where it matters and keep everything else off-chain for speed and cost.",
      "Auditable flows, key management guidance, and clear operator runbooks come standard.",
    ],
    highlights: [
      "Solidity / EVM ecosystems",
      "Indexing & indexer hygiene",
      "Wallet connect & custody patterns",
    ],
  },
  quantum: {
    slug: "quantum",
    title: "Quantum computing",
    subtitle:
      "Prototypes, simulators, and research software that bridge classical HPC and quantum hardware.",
    body: [
      "We partner on algorithm evaluation, circuit tooling, and hybrid workflows so teams can explore NISQ-era value responsibly.",
      "Interfaces, notebooks, and batch runners are tailored to your researchers and engineers.",
    ],
    highlights: [
      "SDK integration & benchmarking",
      "Experiment tracking",
      "Cloud quantum provider adapters",
    ],
  },
  "mobile-web": {
    slug: "mobile-web",
    title: "Mobile & web applications",
    subtitle:
      "Polished interfaces with accessibility, performance, and design systems that your team can extend.",
    body: [
      "We ship responsive web apps and coordinate with native teams when you need store-ready mobile experiences.",
      "Shared tokens, component libraries, and end-to-end tests keep velocity high after launch.",
    ],
    highlights: [
      "Next.js / React Native patterns",
      "A11y & Core Web Vitals",
      "Offline-first where it counts",
    ],
  },
};

export function getService(slug: string): ServiceDetail | undefined {
  return services[slug as ServiceSlug];
}
