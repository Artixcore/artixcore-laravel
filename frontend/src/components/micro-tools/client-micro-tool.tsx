"use client";

import { useMemo, useState } from "react";
import type { MicroToolDTO } from "@/lib/tools-api";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";

function slugify(title: string): string {
  return title
    .toLowerCase()
    .normalize("NFKD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "")
    .slice(0, 120);
}

function readabilitySimple(text: string): {
  sentences: number;
  words: number;
  avgWordsPerSentence: number;
} {
  const clean = text.trim();
  if (!clean) {
    return { sentences: 0, words: 0, avgWordsPerSentence: 0 };
  }
  const sentences = Math.max(
    1,
    (clean.match(/[.!?]+/g) ?? []).length || 1
  );
  const words = (clean.match(/\S+/g) ?? []).length;
  return {
    sentences,
    words,
    avgWordsPerSentence: Math.round((words / sentences) * 10) / 10,
  };
}

export function ClientMicroTool({ tool }: { tool: MicroToolDTO }) {
  switch (tool.slug) {
    case "json-formatter":
      return <JsonFormatterTool />;
    case "base64":
      return <Base64Tool />;
    case "uuid-generator":
      return <UuidTool />;
    case "slug-generator":
      return <SlugTool />;
    case "readability":
      return <ReadabilityTool />;
    case "timestamp-converter":
      return <TimestampTool />;
    case "jwt-decode":
      return <JwtDecodeTool />;
    case "utm-builder":
      return <UtmBuilderTool />;
    case "password-strength":
      return <PasswordStrengthTool />;
    default:
      return (
        <p className="text-sm text-zinc-600 dark:text-zinc-400">
          This tool runs in the browser; a dedicated UI is coming soon for{" "}
          <strong>{tool.slug}</strong>.
        </p>
      );
  }
}

function JsonFormatterTool() {
  const [raw, setRaw] = useState("");
  const [out, setOut] = useState<string | null>(null);
  const [err, setErr] = useState<string | null>(null);

  function format() {
    setErr(null);
    setOut(null);
    try {
      const parsed = JSON.parse(raw) as unknown;
      setOut(JSON.stringify(parsed, null, 2));
    } catch {
      setErr("Invalid JSON.");
    }
  }

  return (
    <div className="space-y-4">
      <div className="space-y-2">
        <Label htmlFor="json-in">JSON</Label>
        <Textarea
          id="json-in"
          rows={10}
          value={raw}
          onChange={(e) => setRaw(e.target.value)}
          placeholder='{"hello": "world"}'
        />
      </div>
      <Button type="button" onClick={format}>
        Format & validate
      </Button>
      {err ? <p className="text-sm text-red-600">{err}</p> : null}
      {out ? (
        <pre className="max-h-96 overflow-auto rounded-lg border border-zinc-200 bg-zinc-50 p-4 text-xs dark:border-zinc-800 dark:bg-zinc-950">
          {out}
        </pre>
      ) : null}
    </div>
  );
}

function Base64Tool() {
  const [text, setText] = useState("");
  const [mode, setMode] = useState<"encode" | "decode">("encode");
  const [out, setOut] = useState("");
  const [err, setErr] = useState<string | null>(null);

  function run() {
    setErr(null);
    setOut("");
    try {
      if (mode === "encode") {
        setOut(btoa(unescape(encodeURIComponent(text))));
      } else {
        setOut(decodeURIComponent(escape(atob(text))));
      }
    } catch {
      setErr("Could not complete operation.");
    }
  }

  return (
    <div className="space-y-4">
      <div className="space-y-2">
        <Label>Mode</Label>
        <div className="flex gap-4">
          <label className="flex items-center gap-2 text-sm">
            <input
              type="radio"
              name="b64m"
              checked={mode === "encode"}
              onChange={() => setMode("encode")}
            />
            Encode
          </label>
          <label className="flex items-center gap-2 text-sm">
            <input
              type="radio"
              name="b64m"
              checked={mode === "decode"}
              onChange={() => setMode("decode")}
            />
            Decode
          </label>
        </div>
      </div>
      <div className="space-y-2">
        <Label htmlFor="b64-t">Text</Label>
        <Textarea
          id="b64-t"
          rows={6}
          value={text}
          onChange={(e) => setText(e.target.value)}
        />
      </div>
      <Button type="button" onClick={run}>
        Run
      </Button>
      {err ? <p className="text-sm text-red-600">{err}</p> : null}
      {out ? (
        <pre className="rounded-lg border border-zinc-200 bg-zinc-50 p-4 text-xs dark:border-zinc-800 dark:bg-zinc-950">
          {out}
        </pre>
      ) : null}
    </div>
  );
}

function UuidTool() {
  const [count, setCount] = useState(5);
  const [nonce, setNonce] = useState(0);
  const lines = useMemo(() => {
    const n = Math.min(50, Math.max(1, count));
    const out: string[] = [];
    for (let i = 0; i < n; i++) {
      out.push(crypto.randomUUID());
    }
    return out;
  }, [count, nonce]);

  return (
    <div className="space-y-4">
      <div className="space-y-2">
        <Label htmlFor="uuid-n">Count (max 50)</Label>
        <Input
          id="uuid-n"
          type="number"
          min={1}
          max={50}
          value={count}
          onChange={(e) => setCount(Number(e.target.value))}
        />
      </div>
      <pre className="max-h-80 overflow-auto rounded-lg border border-zinc-200 bg-zinc-50 p-4 text-xs dark:border-zinc-800 dark:bg-zinc-950">
        {lines.join("\n")}
      </pre>
      <Button
        type="button"
        variant="outline"
        onClick={() => setNonce((x) => x + 1)}
      >
        Regenerate
      </Button>
    </div>
  );
}

function SlugTool() {
  const [title, setTitle] = useState("");
  const slug = useMemo(() => slugify(title), [title]);
  return (
    <div className="space-y-4">
      <div className="space-y-2">
        <Label htmlFor="slug-t">Title</Label>
        <Input
          id="slug-t"
          value={title}
          onChange={(e) => setTitle(e.target.value)}
          placeholder="My Great Article Title"
        />
      </div>
      <div className="space-y-2">
        <Label>Slug</Label>
        <code className="block rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-950">
          {slug || "—"}
        </code>
      </div>
    </div>
  );
}

function ReadabilityTool() {
  const [text, setText] = useState("");
  const stats = useMemo(() => readabilitySimple(text), [text]);
  return (
    <div className="space-y-4">
      <div className="space-y-2">
        <Label htmlFor="read-t">Text</Label>
        <Textarea
          id="read-t"
          rows={10}
          value={text}
          onChange={(e) => setText(e.target.value)}
        />
      </div>
      <ul className="text-sm text-zinc-700 dark:text-zinc-300">
        <li>Words: {stats.words}</li>
        <li>Sentence segments (approx.): {stats.sentences}</li>
        <li>Avg words / sentence: {stats.avgWordsPerSentence}</li>
      </ul>
    </div>
  );
}

function TimestampTool() {
  const [input, setInput] = useState("");
  const parsed = useMemo(() => {
    const t = input.trim();
    if (!t) return null;
    if (/^\d{10}$/.test(t)) {
      const d = new Date(Number(t) * 1000);
      return { kind: "unix_s" as const, date: d };
    }
    if (/^\d{13}$/.test(t)) {
      const d = new Date(Number(t));
      return { kind: "unix_ms" as const, date: d };
    }
    const d = new Date(t);
    if (!Number.isNaN(d.getTime())) {
      return { kind: "date" as const, date: d };
    }
    return null;
  }, [input]);

  return (
    <div className="space-y-4">
      <div className="space-y-2">
        <Label htmlFor="ts-in">Unix time or date string</Label>
        <Input
          id="ts-in"
          value={input}
          onChange={(e) => setInput(e.target.value)}
          placeholder="1710000000 or 2024-01-01T00:00:00Z"
        />
      </div>
      {parsed ? (
        <ul className="text-sm text-zinc-700 dark:text-zinc-300">
          <li>ISO: {parsed.date.toISOString()}</li>
          <li>Local: {parsed.date.toString()}</li>
          <li>Unix (seconds): {Math.floor(parsed.date.getTime() / 1000)}</li>
        </ul>
      ) : input.trim() ? (
        <p className="text-sm text-amber-700">Could not parse.</p>
      ) : null}
    </div>
  );
}

function JwtDecodeTool() {
  const [token, setToken] = useState("");
  const decoded = useMemo(() => {
    const parts = token.trim().split(".");
    if (parts.length < 2) return null;
    try {
      const decode = (b: string) => {
        const pad = b.length % 4 === 0 ? "" : "=".repeat(4 - (b.length % 4));
        const json = atob(b.replace(/-/g, "+").replace(/_/g, "/") + pad);
        return JSON.parse(json) as unknown;
      };
      return { header: decode(parts[0]), payload: decode(parts[1]) };
    } catch {
      return null;
    }
  }, [token]);

  return (
    <div className="space-y-4">
      <div className="space-y-2">
        <Label htmlFor="jwt">JWT</Label>
        <Textarea
          id="jwt"
          rows={4}
          value={token}
          onChange={(e) => setToken(e.target.value)}
        />
      </div>
      {decoded ? (
        <pre className="max-h-96 overflow-auto rounded-lg border border-zinc-200 bg-zinc-50 p-4 text-xs dark:border-zinc-800 dark:bg-zinc-950">
          {JSON.stringify(decoded, null, 2)}
        </pre>
      ) : null}
      <p className="text-xs text-zinc-500">
        Signature is not verified — for debugging only.
      </p>
    </div>
  );
}

function UtmBuilderTool() {
  const [base, setBase] = useState("https://example.com/page");
  const [source, setSource] = useState("");
  const [medium, setMedium] = useState("");
  const [campaign, setCampaign] = useState("");
  const built = useMemo(() => {
    try {
      const u = new URL(base);
      if (source) u.searchParams.set("utm_source", source);
      if (medium) u.searchParams.set("utm_medium", medium);
      if (campaign) u.searchParams.set("utm_campaign", campaign);
      return u.toString();
    } catch {
      return "";
    }
  }, [base, source, medium, campaign]);

  return (
    <div className="space-y-4">
      <div className="space-y-2">
        <Label htmlFor="utm-base">Base URL</Label>
        <Input
          id="utm-base"
          value={base}
          onChange={(e) => setBase(e.target.value)}
        />
      </div>
      <div className="grid gap-4 sm:grid-cols-3">
        <div className="space-y-2">
          <Label htmlFor="utm-s">utm_source</Label>
          <Input
            id="utm-s"
            value={source}
            onChange={(e) => setSource(e.target.value)}
          />
        </div>
        <div className="space-y-2">
          <Label htmlFor="utm-m">utm_medium</Label>
          <Input
            id="utm-m"
            value={medium}
            onChange={(e) => setMedium(e.target.value)}
          />
        </div>
        <div className="space-y-2">
          <Label htmlFor="utm-c">utm_campaign</Label>
          <Input
            id="utm-c"
            value={campaign}
            onChange={(e) => setCampaign(e.target.value)}
          />
        </div>
      </div>
      <div className="space-y-2">
        <Label>Result</Label>
        <code className="block break-all rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-950">
          {built || "—"}
        </code>
      </div>
    </div>
  );
}

function PasswordStrengthTool() {
  const [pw, setPw] = useState("");
  const score = useMemo(() => {
    let s = 0;
    if (pw.length >= 8) s++;
    if (pw.length >= 12) s++;
    if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) s++;
    if (/\d/.test(pw)) s++;
    if (/[^a-zA-Z0-9]/.test(pw)) s++;
    return Math.min(5, s);
  }, [pw]);
  const label = ["Very weak", "Weak", "Fair", "Good", "Strong", "Stronger"][score];

  return (
    <div className="space-y-4">
      <div className="space-y-2">
        <Label htmlFor="pw">Password</Label>
        <Input
          id="pw"
          type="password"
          autoComplete="new-password"
          value={pw}
          onChange={(e) => setPw(e.target.value)}
        />
      </div>
      <p className="text-sm text-zinc-700 dark:text-zinc-300">
        Strength (local heuristic): <strong>{label}</strong> ({score}/5)
      </p>
      <p className="text-xs text-zinc-500">
        Nothing is sent to our servers — analysis runs only in your browser.
      </p>
    </div>
  );
}
