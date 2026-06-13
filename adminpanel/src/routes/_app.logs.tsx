import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";

export const Route = createFileRoute("/_app/logs")({
  head: () => ({ meta: [{ title: "Logs — SalamaPay" }] }),
  component: LogsPage,
});

const logs = [
  { time: "14:21:08", method: "POST", path: "/v1/payments", status: 200, dur: "142ms" },
  { time: "14:20:42", method: "GET", path: "/v1/transactions/TX-10292", status: 200, dur: "31ms" },
  { time: "14:19:11", method: "POST", path: "/v1/payment_links", status: 201, dur: "211ms" },
  { time: "14:18:54", method: "POST", path: "/v1/payments", status: 402, dur: "98ms" },
  { time: "14:18:01", method: "GET", path: "/v1/balance", status: 200, dur: "22ms" },
  { time: "14:17:33", method: "POST", path: "/v1/payouts", status: 200, dur: "318ms" },
  { time: "14:16:09", method: "POST", path: "/v1/webhooks/test", status: 500, dur: "1421ms" },
];

function statusColor(s: number) {
  if (s >= 500) return "text-destructive";
  if (s >= 400) return "text-warning";
  return "text-primary";
}

function LogsPage() {
  return (
    <div>
      <PageHeader title="API Logs" description="Real-time request history for your account." />
      <div className="rounded-xl border border-border bg-card font-mono text-xs">
        <div className="grid grid-cols-12 border-b border-border px-5 py-2.5 text-[11px] uppercase tracking-wider text-muted-foreground">
          <div className="col-span-2">Time</div>
          <div className="col-span-1">Method</div>
          <div className="col-span-6">Path</div>
          <div className="col-span-2">Status</div>
          <div className="col-span-1">Latency</div>
        </div>
        {logs.map((l, i) => (
          <div key={i} className="grid grid-cols-12 border-b border-border px-5 py-2.5 last:border-0 hover:bg-accent/30">
            <div className="col-span-2 text-muted-foreground">{l.time}</div>
            <div className="col-span-1 font-semibold">{l.method}</div>
            <div className="col-span-6 truncate">{l.path}</div>
            <div className={`col-span-2 font-semibold ${statusColor(l.status)}`}>{l.status}</div>
            <div className="col-span-1 text-muted-foreground">{l.dur}</div>
          </div>
        ))}
      </div>
    </div>
  );
}