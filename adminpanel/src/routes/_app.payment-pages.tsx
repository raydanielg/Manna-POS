import { createFileRoute } from "@tanstack/react-router";
import { useState } from "react";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Switch } from "@/components/ui/switch";
import { formatTZS } from "@/lib/mock-data";

export const Route = createFileRoute("/_app/payment-pages")({
  head: () => ({ meta: [{ title: "Payment Pages — SalamaPay" }] }),
  component: PaymentPagesPage,
});

const methods = ["M-Pesa", "Tigo Pesa", "Airtel Money", "Card", "Bank"] as const;

function PaymentPagesPage() {
  const [title, setTitle] = useState("Premium Subscription");
  const [amount, setAmount] = useState(49000);
  const [enabled, setEnabled] = useState<Record<string, boolean>>({
    "M-Pesa": true, "Tigo Pesa": true, "Airtel Money": true, "Card": true, "Bank": false,
  });

  return (
    <div>
      <PageHeader
        title="Payment Pages"
        description="Design a hosted checkout page in minutes."
        actions={<Button size="sm">Publish page</Button>}
      />

      <div className="grid gap-6 lg:grid-cols-2">
        <div className="rounded-xl border border-border bg-card p-6">
          <h3 className="text-sm font-semibold">Builder</h3>
          <div className="mt-4 space-y-4">
            <div className="space-y-1.5">
              <Label>Title</Label>
              <Input value={title} onChange={(e) => setTitle(e.target.value)} />
            </div>
            <div className="space-y-1.5">
              <Label>Amount (TZS)</Label>
              <Input type="number" value={amount} onChange={(e) => setAmount(Number(e.target.value))} />
            </div>
            <div>
              <Label>Payment methods</Label>
              <div className="mt-2 space-y-2">
                {methods.map((m) => (
                  <div key={m} className="flex items-center justify-between rounded-md border border-border bg-background px-3 py-2">
                    <span className="text-sm">{m}</span>
                    <Switch
                      checked={enabled[m]}
                      onCheckedChange={(v) => setEnabled((s) => ({ ...s, [m]: v }))}
                    />
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>

        <div className="rounded-xl border border-border bg-card p-6">
          <h3 className="text-sm font-semibold">Preview</h3>
          <div className="mt-4 rounded-lg border border-border bg-background p-6">
            <div className="text-center">
              <div className="mx-auto h-10 w-10 rounded-md bg-primary text-primary-foreground font-bold flex items-center justify-center">S</div>
              <h4 className="mt-3 text-base font-semibold">{title}</h4>
              <div className="mt-1 text-2xl font-semibold">{formatTZS(amount)}</div>
            </div>
            <div className="mt-5 space-y-2">
              {methods.filter((m) => enabled[m]).map((m) => (
                <button key={m} className="w-full rounded-md border border-border bg-card px-4 py-2.5 text-sm text-left hover:border-primary/40 hover:bg-accent/40">
                  Pay with {m}
                </button>
              ))}
            </div>
            <Button className="mt-4 w-full">Continue</Button>
            <p className="mt-3 text-center text-[10px] text-muted-foreground">Secured by SalamaPay</p>
          </div>
        </div>
      </div>
    </div>
  );
}