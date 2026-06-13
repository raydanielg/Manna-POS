import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Switch } from "@/components/ui/switch";
import { Badge } from "@/components/ui/badge";
import { Lock, Shield, UserCheck, UserX, AlertTriangle, CheckCircle2 } from "lucide-react";

export const Route = createFileRoute("/_app/access-control")({
  head: () => ({ meta: [{ title: "Access Control — MannaPOS" }] }),
  component: AccessControlPage,
});

function AccessControlPage() {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Access Control"
        description="Manage user permissions and access levels"
      />

      <div className="grid gap-6 md:grid-cols-3">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Active Users</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">1,234</div>
            <div className="text-xs text-muted-foreground mt-1">Currently active</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Admin Users</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">12</div>
            <div className="text-xs text-muted-foreground mt-1">With admin access</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Restricted Access</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">5</div>
            <div className="text-xs text-muted-foreground mt-1">Limited permissions</div>
          </CardContent>
        </Card>
      </div>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Role-Based Access Control</CardTitle>
          <CardDescription>Define permissions for different user roles</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {[
              { role: "Super Admin", users: 2, permissions: "Full access to all features" },
              { role: "Admin", users: 10, permissions: "Manage users, products, sales" },
              { role: "Manager", users: 25, permissions: "View reports, manage inventory" },
              { role: "Staff", users: 150, permissions: "Process sales, view products" },
              { role: "Viewer", users: 47, permissions: "View only access" },
            ].map((item, index) => (
              <div key={index} className="flex items-center justify-between p-4 rounded-lg bg-background border">
                <div className="flex items-center gap-3">
                  <Shield className="h-5 w-5 text-primary" />
                  <div>
                    <div className="font-medium">{item.role}</div>
                    <div className="text-xs text-muted-foreground">{item.permissions}</div>
                  </div>
                </div>
                <div className="flex items-center gap-3">
                  <Badge variant="outline">{item.users} users</Badge>
                  <Button size="sm" variant="outline">Edit</Button>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Access Policies</CardTitle>
          <CardDescription>Configure system-wide access rules</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <Lock className="h-5 w-5 text-blue-500" />
                <div>
                  <div className="font-medium">Require Admin Approval</div>
                  <div className="text-xs text-muted-foreground">New users need admin approval</div>
                </div>
              </div>
              <Switch defaultChecked />
            </div>
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <UserCheck className="h-5 w-5 text-green-500" />
                <div>
                  <div className="font-medium">Auto-Activate Users</div>
                  <div className="text-xs text-muted-foreground">New users are active immediately</div>
                </div>
              </div>
              <Switch />
            </div>
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <AlertTriangle className="h-5 w-5 text-orange-500" />
                <div>
                  <div className="font-medium">IP Whitelist</div>
                  <div className="text-xs text-muted-foreground">Restrict access by IP address</div>
                </div>
              </div>
              <Switch />
            </div>
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <UserX className="h-5 w-5 text-red-500" />
                <div>
                  <div className="font-medium">Session Timeout</div>
                  <div className="text-xs text-muted-foreground">Auto-logout after inactivity</div>
                </div>
              </div>
              <Switch defaultChecked />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
