import { createFileRoute } from "@tanstack/react-router";
import { Mail, MessageSquare, Send } from "lucide-react";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { StatusBadge } from "@/components/status-badge";

export const Route = createFileRoute("/_app/marketing")({
  head: () => ({ meta: [{ title: "Marketing — SalamaPay" }] }),
  component: MarketingPage,
});

const campaigns = [
  { name: "June Promo – Premium", channel: "Email", audience: 12480, status: "active" as const, sent: "2026-06-08" },
  { name: "Workshop reminder", channel: "SMS", audience: 4210, status: "active" as const, sent: "2026-06-05" },
  { name: "Q2 Loyalty offer", channel: "Email", audience: 8920, status: "paused" as const, sent: "2026-05-28" },
];

function MarketingPage() {
  return (
    <div>
      <PageHeader
        title="Marketing"
        description="Run email and SMS campaigns to your audience."
      />

      <div className="grid gap-6 lg:grid-cols-3">
        <div className="rounded-xl border border-border bg-card p-6 lg:col-span-2">
          <h3 className="text-sm font-semibold">Create campaign</h3>
          <div className="mt-4 space-y-4">
            <div className="flex gap-2">
              <button className="flex flex-1 items-center gap-2 rounded-md border border-primary/40 bg-primary/10 px-3 py-2 text-sm text-primary">
                <Mail className="h-4 w-4" /> Email
              </button>
              <button className="flex flex-1 items-center gap-2 rounded-md border border-border bg-background px-3 py-2 text-sm text-muted-foreground hover:text-foreground">
                <MessageSquare className="h-4 w-4" /> SMS
              </button>
            </div>
            <div className="space-y-1.5">
              <Label>Campaign name</Label>
              <Input placeholder="June promo – Premium" />
            </div>
            <div className="space-y-1.5">
              <Label>Audience</Label>
              <Input placeholder="All customers (12,480)" />
            </div>
            <div className="space-y-1.5">
              <Label>Subject</Label>
              <Input placeholder="Save 20% this June" />
            </div>
            <div className="space-y-1.5">
              <Label>Message</Label>
              <Textarea placeholder="Write your message…" rows={5} />
            </div>
            <div className="flex justify-end gap-2">
              <Button variant="outline" size="sm">Save draft</Button>
              <Button size="sm"><Send className="h-3.5 w-3.5" /> Send campaign</Button>
            </div>
          </div>
        </div>

        <div className="rounded-xl border border-border bg-card p-6">
          <h3 className="text-sm font-semibold">Recent campaigns</h3>
          <div className="mt-4 space-y-3">
            {campaigns.map((c) => (
              <div key={c.name} className="rounded-lg border border-border bg-background p-3">
                <div className="flex items-start justify-between gap-2">
                  <div>
                    <div className="text-sm font-medium">{c.name}</div>
                    <div className="mt-0.5 text-xs text-muted-foreground">
                      {c.channel} · {c.audience.toLocaleString()} recipients
                    </div>
                  </div>
                  <StatusBadge status={c.status} />
                </div>
                <div className="mt-2 text-[11px] text-muted-foreground">Sent {c.sent}</div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}