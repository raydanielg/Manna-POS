import { createFileRoute } from "@tanstack/react-router";
import {
  ResponsiveContainer,
  LineChart,
  Line,
  XAxis,
  YAxis,
  Tooltip,
  CartesianGrid,
  PieChart,
  Pie,
  Cell,
  Legend,
} from "recharts";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { revenueSeries, methodDistribution, formatTZS } from "@/lib/mock-data";

export const Route = createFileRoute("/_app/analytics")({
  head: () => ({ meta: [{ title: "Analytics — SalamaPay" }] }),
  component: AnalyticsPage,
});

const COLORS = ["oklch(0.74 0.18 148)", "oklch(0.68 0.15 230)", "oklch(0.78 0.15 75)", "oklch(0.65 0.22 300)", "oklch(0.7 0.2 20)"];

function AnalyticsPage() {
  return (
    <div>
      <PageHeader
        title="Analytics"
        description="Deep insights across revenue, methods and success rate."
        actions={
          <div className="inline-flex rounded-md border border-border bg-card p-1 text-xs">
            {["Daily", "Weekly", "Monthly"].map((p, i) => (
              <button key={p} className={i === 1 ? "rounded px-3 py-1 bg-accent text-foreground" : "px-3 py-1 text-muted-foreground hover:text-foreground"}>
                {p}
              </button>
            ))}
          </div>
        }
      />

      <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div className="rounded-xl border border-border bg-card p-5 lg:col-span-2">
          <h3 className="text-sm font-semibold">Revenue trend</h3>
          <p className="text-xs text-muted-foreground">Across the last 7 days</p>
          <div className="mt-4 h-72">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={revenueSeries}>
                <CartesianGrid strokeDasharray="3 3" stroke="oklch(0.3 0.02 258)" />
                <XAxis dataKey="day" tick={{ fill: "oklch(0.68 0.018 258)", fontSize: 12 }} axisLine={false} tickLine={false} />
                <YAxis tick={{ fill: "oklch(0.68 0.018 258)", fontSize: 12 }} axisLine={false} tickLine={false} tickFormatter={(v) => `${v / 1_000_000}M`} />
                <Tooltip
                  contentStyle={{ background: "oklch(0.21 0.022 258)", border: "1px solid oklch(0.3 0.02 258)", borderRadius: 8, fontSize: 12 }}
                  formatter={(v: number) => formatTZS(v)}
                />
                <Line type="monotone" dataKey="revenue" stroke="oklch(0.74 0.18 148)" strokeWidth={2.5} dot={{ r: 3 }} />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </div>

        <div className="rounded-xl border border-border bg-card p-5">
          <h3 className="text-sm font-semibold">Payment methods</h3>
          <p className="text-xs text-muted-foreground">Distribution this month</p>
          <div className="mt-4 h-72">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie data={methodDistribution} dataKey="value" nameKey="name" innerRadius={55} outerRadius={90} paddingAngle={2}>
                  {methodDistribution.map((_, i) => (
                    <Cell key={i} fill={COLORS[i % COLORS.length]} stroke="transparent" />
                  ))}
                </Pie>
                <Legend wrapperStyle={{ fontSize: 12, color: "oklch(0.85 0.01 258)" }} />
                <Tooltip contentStyle={{ background: "oklch(0.21 0.022 258)", border: "1px solid oklch(0.3 0.02 258)", borderRadius: 8, fontSize: 12 }} />
              </PieChart>
            </ResponsiveContainer>
          </div>
        </div>
      </div>

      <div className="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
        {[
          { label: "Success rate", value: "97.8%", color: "text-primary" },
          { label: "Failed rate", value: "1.4%", color: "text-destructive" },
          { label: "Avg. ticket", value: "TZS 312,450", color: "text-foreground" },
        ].map((s) => (
          <div key={s.label} className="rounded-xl border border-border bg-card p-5">
            <div className="text-xs text-muted-foreground">{s.label}</div>
            <div className={`mt-2 text-2xl font-semibold ${s.color}`}>{s.value}</div>
          </div>
        ))}
      </div>
    </div>
  );
}