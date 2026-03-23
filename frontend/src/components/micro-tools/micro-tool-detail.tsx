"use client";

import type { MicroToolDTO } from "@/lib/tools-api";
import { ClientMicroTool } from "@/components/micro-tools/client-micro-tool";
import { MicroToolFavoriteButton } from "@/components/micro-tools/micro-tool-favorite-button";
import { ServerToolRunner } from "@/components/micro-tools/server-tool-runner";
import { Badge } from "@/components/ui/badge";

export function MicroToolDetail({ tool }: { tool: MicroToolDTO }) {
  return (
    <div className="space-y-8">
      <div className="flex flex-wrap items-start justify-between gap-4">
        <div>
          <div className="flex flex-wrap items-center gap-2">
            {tool.is_new ? (
              <Badge className="bg-violet-600 text-white">New</Badge>
            ) : null}
            {tool.is_popular ? <Badge>Popular</Badge> : null}
            <span className="text-xs uppercase tracking-wide text-zinc-500">
              {tool.execution_mode === "server" ? "Server" : "Browser"}
            </span>
          </div>
          <h1 className="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-50">
            {tool.title}
          </h1>
          <p className="mt-3 max-w-2xl text-zinc-600 dark:text-zinc-400">
            {tool.description}
          </p>
        </div>
        <MicroToolFavoriteButton toolId={tool.id} />
      </div>
      <div className="rounded-[var(--radius-lg)] border border-border/80 bg-card p-6 shadow-sm">
        {tool.execution_mode === "server" ? (
          <ServerToolRunner tool={tool} />
        ) : (
          <ClientMicroTool tool={tool} />
        )}
      </div>
    </div>
  );
}
