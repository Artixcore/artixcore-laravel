"use client";

import { useEffect, useState } from "react";
import { getStoredPortalToken } from "@/lib/portal-api";
import {
  fetchToolsSession,
  type ToolsSession,
} from "@/lib/tools-api";
import { MicroToolsAdSlot } from "@/components/micro-tools/micro-tools-ad-slot";

type Props = {
  children: React.ReactNode;
};

export function MicroToolsSessionShell({ children }: Props) {
  const [session, setSession] = useState<ToolsSession | null>(null);

  useEffect(() => {
    const token = getStoredPortalToken();
    void fetchToolsSession(token).then(setSession);
  }, []);

  const adFree = session?.ad_free === true;

  return (
    <div className="space-y-8">
      {!adFree ? (
        <MicroToolsAdSlot className="w-full" label="Sponsored" />
      ) : null}
      {children}
      {!adFree ? (
        <MicroToolsAdSlot className="w-full" label="Sponsored" />
      ) : null}
      {session?.authenticated ? (
        <p className="text-center text-sm text-emerald-700 dark:text-emerald-400">
          Signed in with Artixcore ID — ad-free tools experience.
        </p>
      ) : (
        <p className="text-center text-sm text-zinc-600 dark:text-zinc-400">
          <a
            href="/portal"
            className="font-medium text-zinc-900 underline underline-offset-2 dark:text-zinc-100"
          >
            Sign in to the portal
          </a>{" "}
          for an ad-free experience, saved history, and favorites.
        </p>
      )}
    </div>
  );
}
