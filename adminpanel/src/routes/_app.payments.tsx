import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { StatusBadge } from "@/components/status-badge";
import { transactions, formatTZS } from "@/lib/mock-data";

export const Route = createFileRoute("/_app/payments")({
  head: () => ({ meta: [{ title: "Payments — SalamaPay" }] }),
  component: PaymentsPage,
});

function PaymentsPage() {
  return (
    <div>
      <PageHeader
        title="Payments"
        description="Incoming payments collected through SalamaPay."
        actions={<Button size="sm">New payment</Button>}
      />

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
        {[
          { label: "Captured", value: "TZS 38.2M" },
          { label: "Pending", value: "TZS 1.2M" },
          { label: "Refunded", value: "TZS 150K" },
        ].map((s) => (
          <div key={s.label} className="rounded-xl border border-border bg-card p-5">
            <div className="text-xs text-muted-foreground">{s.label}</div>
            <div className="mt-2 text-2xl font-semibold">{s.value}</div>
          </div>
        ))}
      </div>

      <div className="mt-6 rounded-xl border border-border bg-card">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="text-left text-xs text-muted-foreground">
                <th className="px-5 py-3 font-medium">ID</th>
                <th className="px-5 py-3 font-medium">Customer</th>
                <th className="px-5 py-3 font-medium">Amount</th>
                <th className="px-5 py-3 font-medium">Method</th>
                <th className="px-5 py-3 font-medium">Status</th>
                <th className="px-5 py-3 font-medium">Date</th>
              </tr>
            </thead>
            <tbody>
              {transactions.filter((t) => t.status !== "refunded").map((tx) => (
                <tr key={tx.id} className="border-t border-border hover:bg-accent/30">
                  <td className="px-5 py-3 font-mono text-xs text-muted-foreground">{tx.id}</td>
                  <td className="px-5 py-3">{tx.customer}</td>
                  <td className="px-5 py-3 font-medium">{formatTZS(tx.amount)}</td>
                  <td className="px-5 py-3 text-muted-foreground">{tx.method}</td>
                  <td className="px-5 py-3"><StatusBadge status={tx.status} /></td>
                  <td className="px-5 py-3 text-muted-foreground">{tx.date}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}