import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Switch } from "@/components/ui/switch";
import { useAuth } from "@/lib/auth-context";

export const Route = createFileRoute("/_app/settings")({
  head: () => ({ meta: [{ title: "Settings — MannaPOS" }] }),
  component: SettingsPage,
});

function SettingsPage() {
  const { user } = useAuth();

  return (
    <div>
      <PageHeader title="General Settings" description="Manage business details and preferences." />
      <div className="grid gap-6 lg:grid-cols-3">
        <div className="rounded-xl border border-border/50 bg-card p-6 lg:col-span-2 space-y-4 shadow-sm">
          <h3 className="text-sm font-semibold">Business Information</h3>
          <div className="grid gap-4 sm:grid-cols-2">
            <div className="space-y-1.5"><Label>Business name</Label><Input defaultValue={user?.business_name || "MannaPOS"} /></div>
            <div className="space-y-1.5"><Label>Support email</Label><Input defaultValue="support@manna.pos" /></div>
            <div className="space-y-1.5"><Label>Country</Label><Input defaultValue={user?.business_country || "Tanzania"} /></div>
            <div className="space-y-1.5"><Label>Currency</Label><Input defaultValue={user?.currency || "TZS"} /></div>
            <div className="space-y-1.5"><Label>City</Label><Input defaultValue={user?.business_city || "Dar es Salaam"} /></div>
            <div className="space-y-1.5"><Label>Timezone</Label><Input defaultValue="Africa/Dar_es_Salaam" /></div>
          </div>
          <div className="flex justify-end"><Button size="sm" className="shadow-sm">Save changes</Button></div>
        </div>

        <div className="rounded-xl border border-border/50 bg-card p-6 space-y-4 shadow-sm">
          <h3 className="text-sm font-semibold">Preferences</h3>
          {[
            { label: "Email notifications", desc: "Get notified about new sales" },
            { label: "Daily summary", desc: "Receive a daily activity report" },
            { label: "Low stock alerts", desc: "Alert when products are low" },
            { label: "Two-factor auth", desc: "Require 2FA on dashboard logins" },
          ].map((p, i) => (
            <div key={p.label} className="flex items-start justify-between gap-3">
              <div>
                <div className="text-sm font-medium">{p.label}</div>
                <div className="text-xs text-muted-foreground">{p.desc}</div>
              </div>
              <Switch defaultChecked={i !== 1} />
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}