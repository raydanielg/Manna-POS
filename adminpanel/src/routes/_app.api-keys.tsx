import { createFileRoute } from "@tanstack/react-router";
import { Copy, Eye, Plus, Key, AlertTriangle, CheckCircle2, Clock, Shield } from "lucide-react";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { StatusBadge } from "@/components/status-badge";

export const Route = createFileRoute("/_app/api-keys")({
  head: () => ({ meta: [{ title: "API Keys — MannaPOS" }] }),
  component: ApiKeysPage,
});

const keys = [
  { name: "Production Server", token: "sk_live_••••••••••••3f9a", created: "2026-04-12", lastUsed: "2 min ago", status: "active" as const, permissions: "Full access" },
  { name: "Mobile App", token: "pk_live_••••••••••••a812", created: "2026-03-02", lastUsed: "1 hour ago", status: "active" as const, permissions: "Read/Write" },
  { name: "Sandbox Testing", token: "sk_test_••••••••••••7c21", created: "2026-02-01", lastUsed: "Yesterday", status: "active" as const, permissions: "Read only" },
];

function ApiKeysPage() {
  return (
    <div className="space-y-6">
      <PageHeader
        title="API Keys"
        description="Manage credentials used by your servers and clients"
        actions={
          <>
            <div className="inline-flex rounded-md border border-border/50 bg-card p-1 text-xs">
              <button className="rounded px-3 py-1 bg-accent text-foreground">Live</button>
              <button className="px-3 py-1 text-muted-foreground hover:text-foreground">Sandbox</button>
            </div>
            <Button size="sm" className="shadow-sm"><Plus className="h-3.5 w-3.5 mr-2" /> Generate key</Button>
          </>
        }
      />

      <div className="grid gap-6 md:grid-cols-3">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Active Keys</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">3</div>
            <div className="text-xs text-muted-foreground mt-1">Currently active</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">API Calls Today</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">12,345</div>
            <div className="text-xs text-muted-foreground mt-1">Successful requests</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Rate Limit</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">75%</div>
            <div className="text-xs text-muted-foreground mt-1">Of quota used</div>
          </CardContent>
        </Card>
      </div>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>API Keys</CardTitle>
          <CardDescription>Manage your API keys and their permissions</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="rounded-xl border border-border/50 bg-card">
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr className="text-left text-xs text-muted-foreground bg-muted/30">
                    <th className="px-5 py-3 font-medium">Name</th>
                    <th className="px-5 py-3 font-medium">Token</th>
                    <th className="px-5 py-3 font-medium">Permissions</th>
                    <th className="px-5 py-3 font-medium">Created</th>
                    <th className="px-5 py-3 font-medium">Last used</th>
                    <th className="px-5 py-3 font-medium">Status</th>
                    <th className="px-5 py-3 font-medium text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {keys.map((k) => (
                    <tr key={k.token} className="border-t border-border/30 hover:bg-accent/30">
                      <td className="px-5 py-3 font-medium flex items-center gap-2">
                        <Key className="h-4 w-4 text-primary" />
                        {k.name}
                      </td>
                      <td className="px-5 py-3 font-mono text-xs">{k.token}</td>
                      <td className="px-5 py-3">
                        <Badge variant="outline" className="text-xs">{k.permissions}</Badge>
                      </td>
                      <td className="px-5 py-3 text-muted-foreground">{k.created}</td>
                      <td className="px-5 py-3 text-muted-foreground">{k.lastUsed}</td>
                      <td className="px-5 py-3"><StatusBadge status={k.status} /></td>
                      <td className="px-5 py-3">
                        <div className="flex justify-end gap-1">
                          <Button variant="ghost" size="icon" className="h-8 w-8"><Eye className="h-3.5 w-3.5" /></Button>
                          <Button variant="ghost" size="icon" className="h-8 w-8"><Copy className="h-3.5 w-3.5" /></Button>
                          <Button variant="ghost" size="sm" className="h-8 text-destructive hover:text-destructive">Revoke</Button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>API Usage</CardTitle>
          <CardDescription>Monitor API usage and performance</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            <div className="flex items-center gap-3 p-3 rounded-lg bg-background border">
              <CheckCircle2 className="h-4 w-4 text-green-500" />
              <div className="flex-1">
                <div className="text-sm font-medium">API Status</div>
                <div className="text-xs text-muted-foreground">All endpoints operational</div>
              </div>
              <Badge className="bg-green-500/10 text-green-500 border-green-500/20">Healthy</Badge>
            </div>
            <div className="flex items-center gap-3 p-3 rounded-lg bg-background border">
              <Clock className="h-4 w-4 text-blue-500" />
              <div className="flex-1">
                <div className="text-sm font-medium">Average Response Time</div>
                <div className="text-xs text-muted-foreground">45ms across all endpoints</div>
              </div>
              <div className="text-sm font-bold">45ms</div>
            </div>
            <div className="flex items-center gap-3 p-3 rounded-lg bg-background border">
              <Shield className="h-4 w-4 text-purple-500" />
              <div className="flex-1">
                <div className="text-sm font-medium">Security Events</div>
                <div className="text-xs text-muted-foreground">No suspicious activity detected</div>
              </div>
              <Badge className="bg-green-500/10 text-green-500 border-green-500/20">Secure</Badge>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}