import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Switch } from "@/components/ui/switch";
import { Badge } from "@/components/ui/badge";
import { Smartphone, Wifi, WifiOff, RefreshCw, Download, Upload, AlertCircle, CheckCircle2, Users, Activity } from "lucide-react";
import { useState } from "react";

export const Route = createFileRoute("/_app/mobile-control")({
  head: () => ({ meta: [{ title: "Mobile App Control — MannaPOS" }] }),
  component: MobileControlPage,
});

function MobileControlPage() {
  const [appEnabled, setAppEnabled] = useState(true);
  const [forceUpdate, setForceUpdate] = useState(false);
  const [offlineMode, setOfflineMode] = useState(false);

  return (
    <div className="space-y-6">
      <PageHeader
        title="Mobile App Control"
        description="Manage mobile app access and synchronization"
      />

      <div className="grid gap-6 md:grid-cols-3">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Active Users</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-2">
              <Users className="h-5 w-5 text-primary" />
              <div className="text-2xl font-bold">1,234</div>
            </div>
            <div className="text-xs text-muted-foreground mt-1">Currently online</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Sync Status</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-2">
              <Activity className="h-5 w-5 text-green-500" />
              <div className="text-2xl font-bold">98%</div>
            </div>
            <div className="text-xs text-muted-foreground mt-1">Sync rate</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">App Version</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-2">
              <Smartphone className="h-5 w-5 text-blue-500" />
              <div className="text-2xl font-bold">2.4.1</div>
            </div>
            <div className="text-xs text-muted-foreground mt-1">Latest version</div>
          </CardContent>
        </Card>
      </div>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>App Access Control</CardTitle>
          <CardDescription>Enable or disable mobile app access</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                {appEnabled ? (
                  <Wifi className="h-6 w-6 text-green-500" />
                ) : (
                  <WifiOff className="h-6 w-6 text-red-500" />
                )}
                <div>
                  <div className="font-medium">Mobile App Access</div>
                  <div className="text-sm text-muted-foreground">
                    {appEnabled ? "Mobile app is accessible" : "Mobile app is disabled"}
                  </div>
                </div>
              </div>
              <Switch
                checked={appEnabled}
                onCheckedChange={setAppEnabled}
                className="scale-125"
              />
            </div>
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <RefreshCw className="h-6 w-6 text-blue-500" />
                <div>
                  <div className="font-medium">Force Update</div>
                  <div className="text-sm text-muted-foreground">
                    Force all users to update to latest version
                  </div>
                </div>
              </div>
              <Switch
                checked={forceUpdate}
                onCheckedChange={setForceUpdate}
              />
            </div>
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <Download className="h-6 w-6 text-purple-500" />
                <div>
                  <div className="font-medium">Offline Mode</div>
                  <div className="text-sm text-muted-foreground">
                    Allow users to work offline
                  </div>
                </div>
              </div>
              <Switch
                checked={offlineMode}
                onCheckedChange={setOfflineMode}
              />
            </div>
            <Button className="w-full">Save Changes</Button>
          </div>
        </CardContent>
      </Card>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Sync Management</CardTitle>
          <CardDescription>Control data synchronization settings</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="p-4 rounded-lg bg-background border">
                <div className="flex items-center gap-2 mb-2">
                  <Upload className="h-4 w-4 text-green-500" />
                  <div className="font-medium text-sm">Uploads</div>
                </div>
                <div className="text-2xl font-bold">12,345</div>
                <div className="text-xs text-muted-foreground">Pending uploads</div>
              </div>
              <div className="p-4 rounded-lg bg-background border">
                <div className="flex items-center gap-2 mb-2">
                  <Download className="h-4 w-4 text-blue-500" />
                  <div className="font-medium text-sm">Downloads</div>
                </div>
                <div className="text-2xl font-bold">8,901</div>
                <div className="text-xs text-muted-foreground">Pending downloads</div>
              </div>
            </div>
            <div className="flex gap-2">
              <Button variant="outline" className="flex-1">
                <RefreshCw className="h-4 w-4 mr-2" />
                Force Sync All
              </Button>
              <Button variant="outline" className="flex-1">
                <AlertCircle className="h-4 w-4 mr-2" />
                Clear Queue
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Device Management</CardTitle>
          <CardDescription>View and manage connected devices</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {[
              { device: "iPhone 14 Pro", user: "John Doe", status: "online", lastSync: "2 min ago" },
              { device: "Samsung Galaxy S23", user: "Jane Smith", status: "online", lastSync: "5 min ago" },
              { device: "iPhone 13", user: "Bob Johnson", status: "offline", lastSync: "1 hour ago" },
            ].map((item, index) => (
              <div key={index} className="flex items-center justify-between p-3 rounded-lg bg-background border">
                <div className="flex items-center gap-3">
                  <Smartphone className="h-5 w-5 text-muted-foreground" />
                  <div>
                    <div className="font-medium text-sm">{item.device}</div>
                    <div className="text-xs text-muted-foreground">{item.user}</div>
                  </div>
                </div>
                <div className="flex items-center gap-3">
                  <Badge variant={item.status === "online" ? "default" : "secondary"} className="text-xs">
                    {item.status}
                  </Badge>
                  <div className="text-xs text-muted-foreground">{item.lastSync}</div>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
