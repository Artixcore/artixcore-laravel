"use client";

import { useState } from "react";
import { getStoredPortalToken } from "@/lib/portal-api";
import {
  runMicroToolServer,
  saveToolReport,
  type MicroToolDTO,
} from "@/lib/tools-api";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";

type Field = {
  name: string;
  type?: string;
  label?: string;
  placeholder?: string;
  required?: boolean;
  default?: string | number;
};

export function ServerToolRunner({ tool }: { tool: MicroToolDTO }) {
  const fields = (tool.input_schema?.fields ?? []) as Field[];
  const [values, setValues] = useState<Record<string, string>>(() => {
    const init: Record<string, string> = {};
    for (const f of fields) {
      if (f.default !== undefined && f.default !== null) {
        init[f.name] = String(f.default);
      } else {
        init[f.name] = "";
      }
    }
    return init;
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [result, setResult] = useState<unknown>(null);
  const [runId, setRunId] = useState<number | null>(null);
  const [reportTitle, setReportTitle] = useState("");
  const [reportMsg, setReportMsg] = useState<string | null>(null);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);
    setResult(null);
    setRunId(null);
    setReportMsg(null);
    setLoading(true);
    try {
      const body: Record<string, unknown> = {};
      for (const f of fields) {
        const raw = values[f.name]?.trim() ?? "";
        if (f.type === "number" && raw !== "") {
          body[f.name] = Number(raw);
        } else {
          body[f.name] = raw;
        }
      }
      const token = getStoredPortalToken();
      const { data, meta } = await runMicroToolServer(tool.slug, body, token);
      setResult(data);
      setRunId(meta.run_id);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Request failed");
    } finally {
      setLoading(false);
    }
  }

  async function onSaveReport() {
    const token = getStoredPortalToken();
    if (!token || !runId || !reportTitle.trim()) return;
    const id = await saveToolReport(token, runId, reportTitle.trim());
    setReportMsg(id ? "Report saved." : "Could not save report.");
  }

  return (
    <div className="space-y-6">
      <form onSubmit={onSubmit} className="space-y-4">
        {fields.map((f) => (
          <div key={f.name} className="space-y-2">
            <Label htmlFor={f.name}>
              {f.label ?? f.name}
              {f.required ? " *" : ""}
            </Label>
            {f.type === "textarea" ? (
              <Textarea
                id={f.name}
                required={!!f.required}
                placeholder={f.placeholder}
                value={values[f.name] ?? ""}
                onChange={(e) =>
                  setValues((v) => ({ ...v, [f.name]: e.target.value }))
                }
                rows={5}
              />
            ) : (
              <Input
                id={f.name}
                type={f.type === "number" ? "number" : "text"}
                required={!!f.required}
                placeholder={f.placeholder}
                value={values[f.name] ?? ""}
                onChange={(e) =>
                  setValues((v) => ({ ...v, [f.name]: e.target.value }))
                }
              />
            )}
          </div>
        ))}
        <Button type="submit" disabled={loading}>
          {loading ? "Running…" : "Run tool"}
        </Button>
      </form>
      {error ? (
        <p className="text-sm text-red-600 dark:text-red-400" role="alert">
          {error}
        </p>
      ) : null}
      {result !== null ? (
        <div className="space-y-3">
          <h3 className="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
            Result
          </h3>
          <pre className="max-h-[480px] overflow-auto rounded-lg border border-zinc-200 bg-zinc-50 p-4 text-xs dark:border-zinc-800 dark:bg-zinc-950">
            {JSON.stringify(result, null, 2)}
          </pre>
          {runId !== null ? (
            <div className="flex flex-col gap-2 sm:flex-row sm:items-end">
              <div className="flex-1 space-y-2">
                <Label htmlFor="report-title">Save as report</Label>
                <Input
                  id="report-title"
                  value={reportTitle}
                  onChange={(e) => setReportTitle(e.target.value)}
                  placeholder="Short title"
                />
              </div>
              <Button type="button" variant="outline" onClick={onSaveReport}>
                Save report
              </Button>
            </div>
          ) : null}
          {reportMsg ? (
            <p className="text-sm text-zinc-600 dark:text-zinc-400">
              {reportMsg}
            </p>
          ) : null}
        </div>
      ) : null}
    </div>
  );
}
