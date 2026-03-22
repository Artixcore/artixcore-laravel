import type { Metadata } from "next";
import { DashboardPreviewShell } from "@/components/dashboard/dashboard-preview-shell";

export const metadata: Metadata = {
  title: "Dashboard preview",
  description: "Static SaaS-style console preview.",
};

export default function DashboardPreviewPage() {
  return <DashboardPreviewShell />;
}
