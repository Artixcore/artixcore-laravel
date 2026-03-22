import { z } from "zod";

const ctaSchema = z.object({
  label: z.string(),
  href: z.string(),
});

export const heroBlockDataSchema = z.object({
  eyebrow: z.string().optional(),
  title: z.string(),
  subtitle: z.string().optional(),
  primaryCta: ctaSchema.optional(),
  secondaryCta: ctaSchema.optional(),
});

export const featureGridItemSchema = z.object({
  title: z.string(),
  description: z.string().optional(),
  href: z.string().optional(),
});

export const featureGridBlockDataSchema = z.object({
  heading: z.string().optional(),
  items: z.array(featureGridItemSchema),
});

export const ctaBlockDataSchema = z.object({
  title: z.string(),
  body: z.string().optional(),
  buttonLabel: z.string().optional(),
  href: z.string().optional(),
});

export type HeroBlockData = z.infer<typeof heroBlockDataSchema>;
export type FeatureGridBlockData = z.infer<typeof featureGridBlockDataSchema>;
export type CtaBlockData = z.infer<typeof ctaBlockDataSchema>;
