import { createFileRoute } from "@tanstack/react-router";
import { Plus } from "lucide-react";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { StatusBadge } from "@/components/status-badge";

export const Route = createFileRoute("/_app/webhooks")({
  head: () => ({ meta: [{ title: "Webhooks — SalamaPay" }] }),
  component: WebhooksPage,
});

const hooks = [
  { url: "https://api.merchant.co.tz/webhooks/salamapay", events: ["payment.success", "payout.completed"], status: "active" as const },
  { url: "https://example-shop.com/sp/webhook", events: ["payment.success", "payment.failed", "refund.created"], status: "active" as const },
  { url: "https://staging.merchant.co.tz/webhook", events: ["*"], status: "paused" as const },
];

function WebhooksPage() {
  return (
    <div>
      <PageHeader
        title="Webhooks"
        description="Receive real-time notifications for events."
        actions={<Button size="sm"><Plus className="h-3.5 w-3.5" /> Add endpoint</Button>}
      />

      <div className="grid gap-6 lg:grid-cols-3">
        <div className="rounded-xl border border-border bg-card p-6 lg:col-span-2">
          <h3 className="text-sm font-semibold">Endpoints</h3>
          <div className="mt-4 space-y-3">
            {hooks.map((h) => (
              <div key={h.url} className="rounded-lg border border-border bg-background p-4">
                <div className="flex flex-wrap items-start justify-between gap-2">
                  <code className="break-all text-xs">{h.url}</code>
                  <StatusBadge status={h.status} />
                </div>
                <div className="mt-2 flex flex-wrap gap-1.5">
                  {h.events.map((e) => (
                    <span key={e} className="rounded-full border border-border bg-muted px-2 py-0.5 text-[10px] text-muted-foreground">{e}</span>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>

        <div className="rounded-xl border border-border bg-card p-6">
          <h3 className="text-sm font-semibold">Add endpoint</h3>
          <div className="mt-4 space-y-3">
            <div className="space-y-1.5">
              <Label>Endpoint URL</Label>
              <Input placeholder="https://api.example.com/webhook" />
            </div>
            <div className="space-y-1.5">
              <Label>Description</Label>
              <Input placeholder="Production payments handler" />
            </div>
            <Button className="w-full">Save endpoint</Button>
          </div>
        </div>
      </div>
    </div>
  );
}