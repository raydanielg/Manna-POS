import { createFileRoute } from "@tanstack/react-router";
import { Download, Filter } from "lucide-react";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { StatusBadge } from "@/components/status-badge";
import { transactions, formatTZS } from "@/lib/mock-data";

export const Route = createFileRoute("/_app/transactions")({
  head: () => ({ meta: [{ title: "Transactions — SalamaPay" }] }),
  component: TransactionsPage,
});

function TransactionsPage() {
  return (
    <div>
      <PageHeader
        title="Transactions"
        description="All payment activity across your account."
        actions={
          <>
            <Button variant="outline" size="sm"><Filter className="h-3.5 w-3.5" /> Filter</Button>
            <Button variant="outline" size="sm"><Download className="h-3.5 w-3.5" /> Export</Button>
          </>
        }
      />

      <div className="rounded-xl border border-border bg-card">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="text-left text-xs text-muted-foreground">
                <th className="px-5 py-3 font-medium">Transaction ID</th>
                <th className="px-5 py-3 font-medium">Customer</th>
                <th className="px-5 py-3 font-medium">Amount</th>
                <th className="px-5 py-3 font-medium">Method</th>
                <th className="px-5 py-3 font-medium">Status</th>
                <th className="px-5 py-3 font-medium">Date</th>
              </tr>
            </thead>
            <tbody>
              {transactions.map((tx) => (
                <tr key={tx.id} className="border-t border-border hover:bg-accent/30">
                  <td className="px-5 py-3 font-mono text-xs text-muted-foreground">{tx.id}</td>
                  <td className="px-5 py-3">
                    <div className="font-medium">{tx.customer}</div>
                    <div className="text-xs text-muted-foreground">{tx.email}</div>
                  </td>
                  <td className="px-5 py-3 font-medium">{formatTZS(tx.amount)}</td>
                  <td className="px-5 py-3 text-muted-foreground">{tx.method}</td>
                  <td className="px-5 py-3"><StatusBadge status={tx.status} /></td>
                  <td className="px-5 py-3 text-muted-foreground">{tx.date}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        <div className="flex items-center justify-between border-t border-border px-5 py-3 text-xs text-muted-foreground">
          <span>Showing 8 of 12,489 transactions</span>
          <div className="flex gap-2">
            <Button variant="outline" size="sm">Previous</Button>
            <Button variant="outline" size="sm">Next</Button>
          </div>
        </div>
      </div>
    </div>
  );
}