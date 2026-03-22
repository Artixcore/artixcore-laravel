"use client";

import {
  BarChart3,
  CreditCard,
  LayoutDashboard,
  Settings,
  Users,
} from "lucide-react";
import * as React from "react";
import { Logo } from "@/components/layout/logo";
import { cn } from "@/lib/utils";

const nav = [
  { label: "Overview", icon: LayoutDashboard },
  { label: "Customers", icon: Users },
  { label: "Revenue", icon: BarChart3 },
  { label: "Billing", icon: CreditCard },
  { label: "Settings", icon: Settings },
] as const;

export function DashboardPreviewShell() {
  const [sidebarOpen, setSidebarOpen] = React.useState(false);

  return (
    <div className="flex min-h-[calc(100vh-0px)] bg-muted-bg/50">
      <aside
        className={cn(
          "fixed inset-y-0 left-0 z-30 w-56 border-r border-border bg-card transition-transform lg:static lg:translate-x-0",
          sidebarOpen ? "translate-x-0" : "-translate-x-full"
        )}
      >
        <div className="flex h-14 items-center border-b border-border px-3">
          <Logo showWordmark={false} className="gap-2" />
          <span className="ml-2 text-sm font-medium text-muted">Console</span>
        </div>
        <nav className="space-y-1 p-3">
          {nav.map(({ label, icon: Icon }) => (
            <button
              key={label}
              type="button"
              className="flex w-full items-center gap-2 rounded-[var(--radius-md)] px-3 py-2 text-left text-sm text-muted hover:bg-muted-bg hover:text-foreground"
            >
              <Icon className="h-4 w-4 shrink-0" />
              {label}
            </button>
          ))}
        </nav>
      </aside>
      {sidebarOpen ? (
        <button
          type="button"
          className="fixed inset-0 z-20 bg-black/40 lg:hidden"
          aria-label="Close menu"
          onClick={() => setSidebarOpen(false)}
        />
      ) : null}
      <div className="flex flex-1 flex-col lg:pl-0">
        <header className="flex h-14 items-center justify-between border-b border-border bg-background/90 px-4 backdrop-blur">
          <button
            type="button"
            className="rounded-[var(--radius-md)] border border-border px-3 py-1.5 text-sm lg:hidden"
            onClick={() => setSidebarOpen((o) => !o)}
          >
            Menu
          </button>
          <span className="hidden text-sm text-muted lg:inline">
            Preview — static UI, not connected to live data
          </span>
          <span className="rounded-full bg-accent/15 px-3 py-1 text-xs font-medium text-accent">
            Demo
          </span>
        </header>
        <main className="flex-1 space-y-6 p-4 md:p-8">
          <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            {[
              { k: "MRR", v: "$128,400", d: "+12% vs last month" },
              { k: "Active orgs", v: "842", d: "+3.1%" },
              { k: "API p95", v: "142ms", d: "within SLO" },
              { k: "Failed jobs", v: "0.02%", d: "last 24h" },
            ].map((m) => (
              <div
                key={m.k}
                className="rounded-[var(--radius-lg)] border border-border bg-card p-4 shadow-sm"
              >
                <p className="text-xs font-medium uppercase tracking-wide text-muted">
                  {m.k}
                </p>
                <p className="mt-2 text-2xl font-semibold">{m.v}</p>
                <p className="mt-1 text-xs text-muted">{m.d}</p>
              </div>
            ))}
          </div>
          <div className="rounded-[var(--radius-lg)] border border-border bg-card p-4 shadow-sm">
            <div className="flex items-center justify-between">
              <h2 className="text-sm font-semibold">Recent activity</h2>
              <button
                type="button"
                className="text-xs text-accent hover:underline"
              >
                Export
              </button>
            </div>
            <div className="mt-4 overflow-x-auto">
              <table className="w-full min-w-[520px] text-left text-sm">
                <thead>
                  <tr className="border-b border-border text-muted">
                    <th className="pb-2 font-medium">Customer</th>
                    <th className="pb-2 font-medium">Plan</th>
                    <th className="pb-2 font-medium">Status</th>
                    <th className="pb-2 font-medium">Updated</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {[
                    ["Northwind Labs", "Enterprise", "Healthy", "2h ago"],
                    ["Blue Ocean AI", "Pro", "Trialing", "5h ago"],
                    ["Helix Health", "Enterprise", "Healthy", "1d ago"],
                    ["Polar Mobile", "Starter", "Past due", "2d ago"],
                  ].map((row) => (
                    <tr key={row[0]} className="text-foreground">
                      <td className="py-3 font-medium">{row[0]}</td>
                      <td className="py-3 text-muted">{row[1]}</td>
                      <td className="py-3">
                        <span className="rounded-full bg-muted-bg px-2 py-0.5 text-xs">
                          {row[2]}
                        </span>
                      </td>
                      <td className="py-3 text-muted">{row[3]}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </main>
      </div>
    </div>
  );
}
