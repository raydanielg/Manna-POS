import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { StatusBadge } from "@/components/status-badge";
import { formatTZS } from "@/lib/mock-data";

export const Route = createFileRoute("/_app/payouts")({
  head: () => ({ meta: [{ title: "Payouts — SalamaPay" }] }),
  component: PayoutsPage,
});

const payouts = [
  { id: "PO-4019", destination: "CRDB Bank ••• 8842", amount: 12500000, status: "success" as const, date: "2026-06-11" },
  { id: "PO-4018", destination: "NMB Bank ••• 1024", amount: 4200000, status: "pending" as const, date: "2026-06-10" },
  { id: "PO-4017", destination: "M-Pesa ••• 254", amount: 850000, status: "success" as const, date: "2026-06-09" },
  { id: "PO-4016", destination: "CRDB Bank ••• 8842", amount: 6700000, status: "success" as const, date: "2026-06-07" },
];

function PayoutsPage() {
  return (
    <div>
      <PageHeader
        title="Payouts"
        description="Funds sent from your SalamaPay balance to bank or mobile money."
        actions={<Button size="sm">Initiate payout</Button>}
      />

      <div className="grid gap-4 sm:grid-cols-3">
        <div className="rounded-xl border border-border bg-card p-5">
          <div className="text-xs text-muted-foreground">Available balance</div>
          <div className="mt-2 text-2xl font-semibold">TZS 24,300,000</div>
          <Button size="sm" className="mt-3">Withdraw</Button>
        </div>
        <div className="rounded-xl border border-border bg-card p-5">
          <div className="text-xs text-muted-foreground">Pending payouts</div>
          <div className="mt-2 text-2xl font-semibold">TZS 4,200,000</div>
        </div>
        <div className="rounded-xl border border-border bg-card p-5">
          <div className="text-xs text-muted-foreground">Last 30 days</div>
          <div className="mt-2 text-2xl font-semibold">TZS 64,150,000</div>
        </div>
      </div>

      <div className="mt-6 rounded-xl border border-border bg-card">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="text-left text-xs text-muted-foreground">
                <th className="px-5 py-3 font-medium">Payout ID</th>
                <th className="px-5 py-3 font-medium">Destination</th>
                <th className="px-5 py-3 font-medium">Amount</th>
                <th className="px-5 py-3 font-medium">Status</th>
                <th className="px-5 py-3 font-medium">Date</th>
              </tr>
            </thead>
            <tbody>
              {payouts.map((p) => (
                <tr key={p.id} className="border-t border-border hover:bg-accent/30">
                  <td className="px-5 py-3 font-mono text-xs text-muted-foreground">{p.id}</td>
                  <td className="px-5 py-3">{p.destination}</td>
                  <td className="px-5 py-3 font-medium">{formatTZS(p.amount)}</td>
                  <td className="px-5 py-3"><StatusBadge status={p.status} /></td>
                  <td className="px-5 py-3 text-muted-foreground">{p.date}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}