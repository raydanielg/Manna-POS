import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Search, FileText, User, Shield, AlertTriangle, CheckCircle2, Clock, Filter, Download } from "lucide-react";

export const Route = createFileRoute("/_app/audit-logs")({
  head: () => ({ meta: [{ title: "Audit Logs — MannaPOS" }] }),
  component: AuditLogsPage,
});

function AuditLogsPage() {
  const logs = [
    { id: 1, action: "User Login", user: "admin@manna.pos", ip: "192.168.1.100", status: "success", time: "2 min ago" },
    { id: 2, action: "Product Created", user: "john@manna.pos", ip: "192.168.1.101", status: "success", time: "15 min ago" },
    { id: 3, action: "Failed Login", user: "unknown", ip: "192.168.1.200", status: "failed", time: "1 hour ago" },
    { id: 4, action: "Settings Updated", user: "admin@manna.pos", ip: "192.168.1.100", status: "success", time: "2 hours ago" },
    { id: 5, action: "Sale Completed", user: "jane@manna.pos", ip: "192.168.1.102", status: "success", time: "3 hours ago" },
    { id: 6, action: "User Deleted", user: "admin@manna.pos", ip: "192.168.1.100", status: "success", time: "5 hours ago" },
    { id: 7, action: "Password Changed", user: "admin@manna.pos", ip: "192.168.1.100", status: "success", time: "1 day ago" },
    { id: 8, action: "API Key Generated", user: "admin@manna.pos", ip: "192.168.1.100", status: "success", time: "2 days ago" },
  ];

  return (
    <div className="space-y-6">
      <PageHeader
        title="Audit Logs"
        description="Track all system activities and security events"
        actions={
          <Button variant="outline" className="shadow-sm">
            <Download className="h-4 w-4 mr-2" />
            Export Logs
          </Button>
        }
      />

      <div className="grid gap-6 md:grid-cols-4">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Total Events</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">12,345</div>
            <div className="text-xs text-muted-foreground mt-1">All time</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Today</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">234</div>
            <div className="text-xs text-muted-foreground mt-1">Events today</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Security Events</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-orange-500">12</div>
            <div className="text-xs text-muted-foreground mt-1">Need attention</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Failed Logins</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-red-500">5</div>
            <div className="text-xs text-muted-foreground mt-1">Last 24 hours</div>
          </CardContent>
        </Card>
      </div>

      <Card className="shadow-sm">
        <CardHeader>
          <div className="flex items-center justify-between">
            <div>
              <CardTitle>Activity Log</CardTitle>
              <CardDescription>Recent system activities and events</CardDescription>
            </div>
            <div className="flex items-center gap-2">
              <div className="relative">
                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                  placeholder="Search logs..."
                  className="pl-9 w-64"
                />
              </div>
              <Button variant="outline" size="sm">
                <Filter className="h-4 w-4 mr-2" />
                Filter
              </Button>
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {logs.map((log) => (
              <div key={log.id} className="flex items-center gap-4 p-4 rounded-lg bg-background border hover:bg-accent/30 transition-colors">
                <div className="flex-shrink-0">
                  {log.status === "success" ? (
                    <CheckCircle2 className="h-5 w-5 text-green-500" />
                  ) : (
                    <AlertTriangle className="h-5 w-5 text-red-500" />
                  )}
                </div>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center gap-2">
                    <div className="font-medium text-sm">{log.action}</div>
                    <Badge variant={log.status === "success" ? "default" : "destructive"} className="text-xs">
                      {log.status}
                    </Badge>
                  </div>
                  <div className="flex items-center gap-4 mt-1 text-xs text-muted-foreground">
                    <div className="flex items-center gap-1">
                      <User className="h-3 w-3" />
                      {log.user}
                    </div>
                    <div className="flex items-center gap-1">
                      <Shield className="h-3 w-3" />
                      {log.ip}
                    </div>
                  </div>
                </div>
                <div className="flex items-center gap-1 text-xs text-muted-foreground">
                  <Clock className="h-3 w-3" />
                  {log.time}
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
