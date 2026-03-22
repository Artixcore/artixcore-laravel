/**
 * Flattens top-level string/number entries into CSS custom properties for :root.
 */
export function designTokensToCss(
  tokens: Record<string, unknown> | null | undefined
): string {
  if (!tokens || typeof tokens !== "object") {
    return "";
  }
  const lines: string[] = [];
  for (const [key, value] of Object.entries(tokens)) {
    if (typeof value === "string" || typeof value === "number") {
      lines.push(`  ${key}: ${String(value)};`);
    }
  }
  return lines.length > 0 ? `:root {\n${lines.join("\n")}\n}` : "";
}
