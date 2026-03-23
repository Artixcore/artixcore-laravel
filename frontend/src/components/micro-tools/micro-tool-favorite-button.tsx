"use client";

import { useEffect, useState } from "react";
import { Star } from "lucide-react";
import { getStoredPortalToken } from "@/lib/portal-api";
import {
  addToolFavorite,
  fetchToolFavorites,
  removeToolFavorite,
} from "@/lib/tools-api";
import { Button } from "@/components/ui/button";

export function MicroToolFavoriteButton({ toolId }: { toolId: number }) {
  const [on, setOn] = useState(false);
  const [ready, setReady] = useState(false);

  useEffect(() => {
    const token = getStoredPortalToken();
    if (!token) {
      setReady(true);
      return;
    }
    void fetchToolFavorites(token).then((list) => {
      setOn(list.some((t) => t.id === toolId));
      setReady(true);
    });
  }, [toolId]);

  async function toggle() {
    const token = getStoredPortalToken();
    if (!token) return;
    if (on) {
      await removeToolFavorite(token, toolId);
      setOn(false);
    } else {
      await addToolFavorite(token, toolId);
      setOn(true);
    }
  }

  const token = typeof window !== "undefined" ? getStoredPortalToken() : null;

  if (!ready || !token) {
    return null;
  }

  return (
    <Button
      type="button"
      variant="ghost"
      size="sm"
      className="gap-1.5 text-zinc-600 dark:text-zinc-400"
      onClick={() => void toggle()}
      aria-pressed={on}
      aria-label={on ? "Remove from favorites" : "Add to favorites"}
    >
      <Star
        className="h-4 w-4"
        fill={on ? "currentColor" : "none"}
        strokeWidth={1.75}
      />
      {on ? "Favorited" : "Favorite"}
    </Button>
  );
}
