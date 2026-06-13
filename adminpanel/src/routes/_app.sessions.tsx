import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Eye, Ban, Globe, Smartphone, Laptop, Clock, AlertTriangle, CheckCircle2 } from "lucide-react";

export const Route = createFileRoute("/_app/sessions")({
  head: () => ({ meta: [{ title: "Session Management — MannaPOS" }] }),
  component: SessionsPage,
});

function SessionsPage() {
  const sessions = [
    { id: 1, device: "MacBook Pro · Chrome", location: "Dar es Salaam, TZ", ip: "192.168.1.100", last: "Active now", current: true },
    { id: 2, device: "iPhone 15 · Safari", location: "Dar es Salaam, TZ", ip: "192.168.1.101", last: "3 hours ago", current: false },
    { id: 3, device: "Windows · Firefox", location: "Nairobi, KE", ip: "192.168.1.200", last: "2 days ago", current: false },
    { id: 4, device: "Android · Chrome", location: "Mombasa, KE", ip: "192.168.1.150", last: "1 week ago", current: false },
  ];

  return (
    <div className="space-y-6">
      <PageHeader
        title="Session Management"
        description="Manage active user sessions across all devices"
        actions={
          <Button variant="outline" className="shadow-sm">
            <Ban className="h-4 w-4 mr-2" />
            Revoke All
          </Button>
        }
      />

      <div className="grid gap-6 md:grid-cols-3">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Active Sessions</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">4</div>
            <div className="text-xs text-muted-foreground mt-1">Currently active</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Total Sessions</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">23</div>
            <div className="text-xs text-muted-foreground mt-1">All time</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Security Score</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-green-500">95%</div>
            <div className="text-xs text-muted-foreground mt-1">Excellent</div>
          </CardContent>
        </Card>
      </div>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Active Sessions</CardTitle>
          <CardDescription>View and manage all active login sessions</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {sessions.map((session) => (
              <div key={session.id} className="flex items-center justify-between p-4 rounded-lg bg-background border hover:bg-accent/30 transition-colors">
                <div className="flex items-center gap-4">
                  <div className="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    {session.device.includes("iPhone") || session.device.includes("Android") ? (
                      <Smartphone className="h-5 w-5 text-primary" />
                    ) : (
                      <Laptop className="h-5 w-5 text-primary" />
                    )}
                  </div>
                  <div>
                    <div className="font-medium text-sm">{session.device}</div>
                    <div className="flex items-center gap-3 mt-1 text-xs text-muted-foreground">
                      <div className="flex items-center gap-1">
                        <Globe className="h-3 w-3" />
                        {session.location}
                      </div>
                      <div className="flex items-center gap-1">
                        <Clock className="h-3 w-3" />
                        {session.last}
                      </div>
                    </div>
                  </div>
                </div>
                <div className="flex items-center gap-3">
                  {session.current ? (
                    <Badge className="bg-green-500/10 text-green-500 border-green-500/20">Current</Badge>
                  ) : (
                    <Button variant="ghost" size="sm" className="h-8 text-destructive hover:text-destructive">
                      <Ban className="h-3 w-3 mr-1" />
                      Revoke
                    </Button>
                  )}
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Session Security</CardTitle>
          <CardDescription>Configure session security settings</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            <div className="flex items-center gap-3 p-3 rounded-lg bg-background border">
              <CheckCircle2 className="h-4 w-4 text-green-500" />
              <div className="flex-1">
                <div className="text-sm font-medium">Auto-logout on inactivity</div>
                <div className="text-xs text-muted-foreground">Sessions expire after 30 minutes of inactivity</div>
              </div>
              <Badge className="bg-green-500/10 text-green-500 border-green-500/20">Enabled</Badge>
            </div>
            <div className="flex items-center gap-3 p-3 rounded-lg bg-background border">
              <AlertTriangle className="h-4 w-4 text-orange-500" />
              <div className="flex-1">
                <div className="text-sm font-medium">Concurrent session limit</div>
                <div className="text-xs text-muted-foreground">Maximum 5 active sessions per user</div>
              </div>
              <Badge className="bg-orange-500/10 text-orange-500 border-orange-500/20">Warning</Badge>
            </div>
            <div className="flex items-center gap-3 p-3 rounded-lg bg-background border">
              <Eye className="h-4 w-4 text-blue-500" />
              <div className="flex-1">
                <div className="text-sm font-medium">Session monitoring</div>
                <div className="text-xs text-muted-foreground">Track all session activities</div>
              </div>
              <Badge className="bg-green-500/10 text-green-500 border-green-500/20">Active</Badge>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
