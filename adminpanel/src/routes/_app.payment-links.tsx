import { createFileRoute } from "@tanstack/react-router";
import { Copy, Share2, Trash2, Plus } from "lucide-react";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { StatusBadge } from "@/components/status-badge";
import { paymentLinks, formatTZS } from "@/lib/mock-data";

export const Route = createFileRoute("/_app/payment-links")({
  head: () => ({ meta: [{ title: "Payment Links — SalamaPay" }] }),
  component: PaymentLinksPage,
});

function PaymentLinksPage() {
  return (
    <div>
      <PageHeader
        title="Payment Links"
        description="Share a link, get paid. No code required."
        actions={<Button size="sm"><Plus className="h-3.5 w-3.5" /> Create payment link</Button>}
      />

      <div className="grid gap-4">
        {paymentLinks.map((link) => (
          <div key={link.id} className="rounded-xl border border-border bg-card p-5">
            <div className="flex flex-wrap items-start justify-between gap-4">
              <div className="min-w-0">
                <div className="flex items-center gap-2">
                  <h3 className="font-medium">{link.title}</h3>
                  <StatusBadge status={link.status} />
                </div>
                <div className="mt-1 text-xs text-muted-foreground">
                  {link.amount > 0 ? formatTZS(link.amount) : "Customer chooses"} · {link.clicks} clicks
                </div>
                <div className="mt-3 flex items-center gap-2">
                  <code className="rounded-md border border-border bg-muted px-2.5 py-1 text-xs text-foreground">
                    {link.url}
                  </code>
                  <Button variant="ghost" size="icon" aria-label="Copy"><Copy className="h-3.5 w-3.5" /></Button>
                  <Button variant="ghost" size="icon" aria-label="Share"><Share2 className="h-3.5 w-3.5" /></Button>
                  <Button variant="ghost" size="icon" aria-label="Delete" className="text-destructive hover:text-destructive"><Trash2 className="h-3.5 w-3.5" /></Button>
                </div>
              </div>
              <Button variant="outline" size="sm">View details</Button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}