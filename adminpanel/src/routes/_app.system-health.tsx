import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Activity, Server, Database, Wifi, Cpu, HardDrive, CheckCircle2, AlertTriangle, XCircle, RefreshCw } from "lucide-react";

export const Route = createFileRoute("/_app/system-health")({
  head: () => ({ meta: [{ title: "System Health — MannaPOS" }] }),
  component: SystemHealthPage,
});

function SystemHealthPage() {
  const services = [
    { name: "API Server", status: "operational", uptime: "99.9%", latency: "45ms" },
    { name: "Database", status: "operational", uptime: "99.8%", latency: "12ms" },
    { name: "Cache Server", status: "operational", uptime: "99.9%", latency: "8ms" },
    { name: "File Storage", status: "operational", uptime: "99.7%", latency: "23ms" },
    { name: "Email Service", status: "degraded", uptime: "98.5%", latency: "150ms" },
    { name: "SMS Gateway", status: "operational", uptime: "99.9%", latency: "89ms" },
  ];

  const metrics = [
    { label: "CPU Usage", value: "45%", status: "normal" },
    { label: "Memory Usage", value: "62%", status: "normal" },
    { label: "Disk Usage", value: "78%", status: "warning" },
    { label: "Network I/O", value: "12 MB/s", status: "normal" },
  ];

  return (
    <div className="space-y-6">
      <PageHeader
        title="System Health"
        description="Monitor system performance and service status"
      />

      <div className="grid gap-6 md:grid-cols-4">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Overall Status</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-2">
              <CheckCircle2 className="h-5 w-5 text-green-500" />
              <div className="text-2xl font-bold text-green-500">Operational</div>
            </div>
            <div className="text-xs text-muted-foreground mt-1">All systems running</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Uptime</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">99.9%</div>
            <div className="text-xs text-muted-foreground mt-1">Last 30 days</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Response Time</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">45ms</div>
            <div className="text-xs text-muted-foreground mt-1">Average latency</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Active Users</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">1,234</div>
            <div className="text-xs text-muted-foreground mt-1">Currently online</div>
          </CardContent>
        </Card>
      </div>

      <div className="grid gap-6 lg:grid-cols-2">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle>Service Status</CardTitle>
            <CardDescription>Monitor all system services</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {services.map((service, index) => (
                <div key={index} className="flex items-center justify-between p-3 rounded-lg bg-background border">
                  <div className="flex items-center gap-3">
                    {service.status === "operational" ? (
                      <CheckCircle2 className="h-5 w-5 text-green-500" />
                    ) : service.status === "degraded" ? (
                      <AlertTriangle className="h-5 w-5 text-orange-500" />
                    ) : (
                      <XCircle className="h-5 w-5 text-red-500" />
                    )}
                    <div>
                      <div className="font-medium text-sm">{service.name}</div>
                      <div className="text-xs text-muted-foreground">
                        Uptime: {service.uptime} · Latency: {service.latency}
                      </div>
                    </div>
                  </div>
                  <Badge
                    variant={service.status === "operational" ? "default" : "secondary"}
                    className={service.status === "operational" ? "bg-green-500/10 text-green-500 border-green-500/20" : "bg-orange-500/10 text-orange-500 border-orange-500/20"}
                  >
                    {service.status}
                  </Badge>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle>System Metrics</CardTitle>
            <CardDescription>Real-time system performance</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {metrics.map((metric, index) => (
                <div key={index} className="space-y-2">
                  <div className="flex items-center justify-between">
                    <div className="text-sm font-medium">{metric.label}</div>
                    <div className="text-sm font-bold">{metric.value}</div>
                  </div>
                  <div className="h-2 rounded-full bg-border overflow-hidden">
                    <div
                      className={`h-full transition-all ${
                        metric.status === "normal"
                          ? "bg-green-500"
                          : metric.status === "warning"
                          ? "bg-orange-500"
                          : "bg-red-500"
                      }`}
                      style={{ width: parseInt(metric.value) + "%" }}
                    />
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Resource Usage</CardTitle>
          <CardDescription>Detailed system resource monitoring</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div className="p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-2 mb-2">
                <Cpu className="h-4 w-4 text-blue-500" />
                <div className="text-sm font-medium">CPU</div>
              </div>
              <div className="text-2xl font-bold">45%</div>
              <div className="text-xs text-muted-foreground">4 cores active</div>
            </div>
            <div className="p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-2 mb-2">
                <Server className="h-4 w-4 text-purple-500" />
                <div className="text-sm font-medium">Memory</div>
              </div>
              <div className="text-2xl font-bold">62%</div>
              <div className="text-xs text-muted-foreground">8GB / 16GB used</div>
            </div>
            <div className="p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-2 mb-2">
                <HardDrive className="h-4 w-4 text-green-500" />
                <div className="text-sm font-medium">Storage</div>
              </div>
              <div className="text-2xl font-bold">78%</div>
              <div className="text-xs text-muted-foreground">390GB / 500GB used</div>
            </div>
            <div className="p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-2 mb-2">
                <Wifi className="h-4 w-4 text-orange-500" />
                <div className="text-sm font-medium">Network</div>
              </div>
              <div className="text-2xl font-bold">12 MB/s</div>
              <div className="text-xs text-muted-foreground">Current bandwidth</div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
