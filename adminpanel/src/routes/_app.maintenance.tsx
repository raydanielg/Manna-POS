import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Switch } from "@/components/ui/switch";
import { Badge } from "@/components/ui/badge";
import { Wrench, AlertTriangle, CheckCircle2, Clock, Globe, Smartphone } from "lucide-react";
import { useState } from "react";

export const Route = createFileRoute("/_app/maintenance")({
  head: () => ({ meta: [{ title: "Maintenance Mode — MannaPOS" }] }),
  component: MaintenancePage,
});

function MaintenancePage() {
  const [maintenanceMode, setMaintenanceMode] = useState(false);
  const [mobileAppDisabled, setMobileAppDisabled] = useState(false);
  const [webAppDisabled, setWebAppDisabled] = useState(false);

  return (
    <div className="space-y-6">
      <PageHeader
        title="Maintenance Mode"
        description="Control system availability and maintenance windows"
      />

      <div className="grid gap-6 md:grid-cols-2">
        <Card className="shadow-sm">
          <CardHeader>
            <div className="flex items-center justify-between">
              <div>
                <CardTitle>System Maintenance</CardTitle>
                <CardDescription>Toggle maintenance mode for the entire system</CardDescription>
              </div>
              <Switch
                checked={maintenanceMode}
                onCheckedChange={setMaintenanceMode}
                className="scale-125"
              />
            </div>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="flex items-center gap-3 p-3 rounded-lg bg-background border">
                {maintenanceMode ? (
                  <AlertTriangle className="h-5 w-5 text-orange-500" />
                ) : (
                  <CheckCircle2 className="h-5 w-5 text-green-500" />
                )}
                <div>
                  <div className="font-medium text-sm">
                    {maintenanceMode ? "System in Maintenance" : "System Operational"}
                  </div>
                  <div className="text-xs text-muted-foreground">
                    {maintenanceMode
                      ? "Users will see maintenance message"
                      : "All services are running normally"}
                  </div>
                </div>
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium">Maintenance Message</label>
                <textarea
                  className="w-full h-20 px-3 py-2 rounded-md border border-input bg-background text-sm"
                  placeholder="Enter custom maintenance message..."
                  defaultValue="System is currently under maintenance. Please check back later."
                />
              </div>
              <Button size="sm" className="w-full">
                Save Configuration
              </Button>
            </div>
          </CardContent>
        </Card>

        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle>Platform Control</CardTitle>
            <CardDescription>Disable specific platforms independently</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="flex items-center justify-between p-3 rounded-lg bg-background border">
                <div className="flex items-center gap-3">
                  <Smartphone className="h-5 w-5 text-primary" />
                  <div>
                    <div className="font-medium text-sm">Mobile App</div>
                    <div className="text-xs text-muted-foreground">Disable mobile app access</div>
                  </div>
                </div>
                <Switch
                  checked={mobileAppDisabled}
                  onCheckedChange={setMobileAppDisabled}
                />
              </div>
              <div className="flex items-center justify-between p-3 rounded-lg bg-background border">
                <div className="flex items-center gap-3">
                  <Globe className="h-5 w-5 text-blue-500" />
                  <div>
                    <div className="font-medium text-sm">Web App</div>
                    <div className="text-xs text-muted-foreground">Disable web app access</div>
                  </div>
                </div>
                <Switch
                  checked={webAppDisabled}
                  onCheckedChange={setWebAppDisabled}
                />
              </div>
              <Button size="sm" className="w-full" variant="outline">
                Apply Changes
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Scheduled Maintenance</CardTitle>
          <CardDescription>Plan future maintenance windows</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <Clock className="h-5 w-5 text-muted-foreground" />
                <div>
                  <div className="font-medium">Scheduled Maintenance</div>
                  <div className="text-sm text-muted-foreground">
                    No scheduled maintenance windows
                  </div>
                </div>
              </div>
              <Button size="sm">Schedule</Button>
            </div>
            <div className="grid grid-cols-3 gap-4">
              <div className="p-4 rounded-lg bg-background border">
                <div className="text-2xl font-bold">0</div>
                <div className="text-xs text-muted-foreground">Scheduled</div>
              </div>
              <div className="p-4 rounded-lg bg-background border">
                <div className="text-2xl font-bold">0</div>
                <div className="text-xs text-muted-foreground">Completed</div>
              </div>
              <div className="p-4 rounded-lg bg-background border">
                <div className="text-2xl font-bold">99.9%</div>
                <div className="text-xs text-muted-foreground">Uptime</div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
