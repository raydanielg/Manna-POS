import { createFileRoute } from "@tanstack/react-router";
import { Smartphone, Key, Globe, Shield, Lock, AlertTriangle, CheckCircle2, Clock, Ban, Eye } from "lucide-react";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Switch } from "@/components/ui/switch";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

export const Route = createFileRoute("/_app/security")({
  head: () => ({ meta: [{ title: "Security — MannaPOS" }] }),
  component: SecurityPage,
});

const sessions = [
  { device: "MacBook Pro · Chrome", location: "Dar es Salaam, TZ", last: "Active now", current: true },
  { device: "iPhone 15 · Safari", location: "Dar es Salaam, TZ", last: "3 hours ago", current: false },
  { device: "Windows · Firefox", location: "Nairobi, KE", last: "2 days ago", current: false },
];

function SecurityPage() {
  return (
    <div className="space-y-6">
      <PageHeader title="Security Settings" description="Protect your account with advanced security features" />

      <div className="grid gap-6 md:grid-cols-3">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Security Score</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold text-green-500">85%</div>
            <div className="text-xs text-muted-foreground mt-1">Strong security</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Failed Attempts</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold">3</div>
            <div className="text-xs text-muted-foreground mt-1">Last 24 hours</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Blocked IPs</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold">12</div>
            <div className="text-xs text-muted-foreground mt-1">Permanently blocked</div>
          </CardContent>
        </Card>
      </div>

      <div className="grid gap-6 lg:grid-cols-2">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle>Password Security</CardTitle>
            <CardDescription>Manage password policies and requirements</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="flex items-center justify-between p-3 rounded-lg bg-background border">
                <div className="flex items-center gap-2">
                  <Key className="h-4 w-4 text-primary" />
                  <div>
                    <div className="text-sm font-medium">Change Password</div>
                    <div className="text-xs text-muted-foreground">Update your password</div>
                  </div>
                </div>
                <Button size="sm" variant="outline">Change</Button>
              </div>
              <div className="flex items-center justify-between p-3 rounded-lg bg-background border">
                <div className="flex items-center gap-2">
                  <Lock className="h-4 w-4 text-blue-500" />
                  <div>
                    <div className="text-sm font-medium">Password Expiry</div>
                    <div className="text-xs text-muted-foreground">Require password change every 90 days</div>
                  </div>
                </div>
                <Switch />
              </div>
              <div className="flex items-center justify-between p-3 rounded-lg bg-background border">
                <div className="flex items-center gap-2">
                  <Shield className="h-4 w-4 text-green-500" />
                  <div>
                    <div className="text-sm font-medium">Password Strength</div>
                    <div className="text-xs text-muted-foreground">Require strong passwords</div>
                  </div>
                </div>
                <Switch defaultChecked />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle>Two-Factor Authentication</CardTitle>
            <CardDescription>Add an extra layer of security</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="flex items-center justify-between p-3 rounded-lg bg-background border">
                <div className="flex items-center gap-2">
                  <Smartphone className="h-4 w-4 text-purple-500" />
                  <div>
                    <div className="text-sm font-medium">Authenticator App</div>
                    <div className="text-xs text-muted-foreground">Use Google Authenticator or Authy</div>
                  </div>
                </div>
                <Switch defaultChecked />
              </div>
              <div className="flex items-center justify-between p-3 rounded-lg bg-background border">
                <div className="flex items-center gap-2">
                  <Globe className="h-4 w-4 text-blue-500" />
                  <div>
                    <div className="text-sm font-medium">SMS Verification</div>
                    <div className="text-xs text-muted-foreground">Send codes via SMS</div>
                  </div>
                </div>
                <Switch />
              </div>
              <div className="flex items-center justify-between p-3 rounded-lg bg-background border">
                <div className="flex items-center gap-2">
                  <Eye className="h-4 w-4 text-green-500" />
                  <div>
                    <div className="text-sm font-medium">Biometric Login</div>
                    <div className="text-xs text-muted-foreground">Fingerprint or Face ID</div>
                  </div>
                </div>
                <Switch />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Active Sessions</CardTitle>
          <CardDescription>Manage and monitor active login sessions</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {sessions.map((s, i) => (
              <div key={i} className="flex items-center justify-between p-4 rounded-lg bg-background border">
                <div className="flex items-center gap-3">
                  <Globe className="h-5 w-5 text-muted-foreground" />
                  <div>
                    <div className="font-medium text-sm">{s.device}</div>
                    <div className="text-xs text-muted-foreground">{s.location} · {s.last}</div>
                  </div>
                </div>
                <div className="flex items-center gap-2">
                  {s.current ? (
                    <Badge className="bg-green-500/10 text-green-500 border-green-500/20">Current</Badge>
                  ) : (
                    <Button variant="ghost" size="sm" className="h-8 text-xs text-destructive hover:text-destructive">
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
          <CardTitle>Security Alerts</CardTitle>
          <CardDescription>Recent security events and notifications</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            <div className="flex items-center gap-3 p-3 rounded-lg bg-background border">
              <AlertTriangle className="h-4 w-4 text-orange-500" />
              <div className="flex-1">
                <div className="text-sm font-medium">Failed login attempt</div>
                <div className="text-xs text-muted-foreground">Unknown IP from Nairobi, Kenya</div>
              </div>
              <div className="text-xs text-muted-foreground">2 hours ago</div>
            </div>
            <div className="flex items-center gap-3 p-3 rounded-lg bg-background border">
              <CheckCircle2 className="h-4 w-4 text-green-500" />
              <div className="flex-1">
                <div className="text-sm font-medium">Password changed</div>
                <div className="text-xs text-muted-foreground">By admin@manna.pos</div>
              </div>
              <div className="text-xs text-muted-foreground">1 day ago</div>
            </div>
            <div className="flex items-center gap-3 p-3 rounded-lg bg-background border">
              <Clock className="h-4 w-4 text-blue-500" />
              <div className="flex-1">
                <div className="text-sm font-medium">Session expired</div>
                <div className="text-xs text-muted-foreground">iPhone 15 · Safari</div>
              </div>
              <div className="text-xs text-muted-foreground">3 days ago</div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}