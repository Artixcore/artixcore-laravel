"use client";

import * as React from "react";
import { ApiError, submitContact } from "@/lib/api";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";

export function ContactForm() {
  const [status, setStatus] = React.useState<
    "idle" | "loading" | "success" | "error"
  >("idle");
  const [message, setMessage] = React.useState<string | null>(null);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setStatus("loading");
    setMessage(null);
    const form = e.currentTarget;
    const fd = new FormData(form);
    const name = String(fd.get("name") ?? "").trim();
    const email = String(fd.get("email") ?? "").trim();
    const company = String(fd.get("company") ?? "").trim();
    const msg = String(fd.get("message") ?? "").trim();

    try {
      const res = await submitContact({
        name,
        email,
        company: company || undefined,
        message: msg,
      });
      setStatus("success");
      setMessage(res.message);
      form.reset();
    } catch (err) {
      setStatus("error");
      if (err instanceof ApiError && err.body?.errors) {
        const first = Object.values(err.body.errors).flat()[0];
        setMessage(first ?? err.message);
      } else if (err instanceof Error) {
        setMessage(err.message);
      } else {
        setMessage("Something went wrong.");
      }
    }
  }

  return (
    <form onSubmit={onSubmit} className="space-y-6">
      <div className="grid gap-6 sm:grid-cols-2">
        <div className="space-y-2">
          <Label htmlFor="name">Name</Label>
          <Input id="name" name="name" required autoComplete="name" />
        </div>
        <div className="space-y-2">
          <Label htmlFor="email">Email</Label>
          <Input
            id="email"
            name="email"
            type="email"
            required
            autoComplete="email"
          />
        </div>
      </div>
      <div className="space-y-2">
        <Label htmlFor="company">Company (optional)</Label>
        <Input id="company" name="company" autoComplete="organization" />
      </div>
      <div className="space-y-2">
        <Label htmlFor="message">Message</Label>
        <Textarea id="message" name="message" required rows={5} />
      </div>
      {message ? (
        <p
          className={
            status === "success" ? "text-sm text-accent" : "text-sm text-red-500"
          }
          role="status"
        >
          {message}
        </p>
      ) : null}
      <Button type="submit" disabled={status === "loading"}>
        {status === "loading" ? "Sending…" : "Send message"}
      </Button>
    </form>
  );
}
