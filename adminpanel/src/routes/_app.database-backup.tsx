import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Database, Download, Upload, Clock, CheckCircle2, AlertTriangle, HardDrive, RefreshCw, Calendar } from "lucide-react";

export const Route = createFileRoute("/_app/database-backup")({
  head: () => ({ meta: [{ title: "Database Backup — MannaPOS" }] }),
  component: DatabaseBackupPage,
});

function DatabaseBackupPage() {
  const backups = [
    { id: 1, name: "daily-backup-2024-06-12", size: "245 MB", date: "2024-06-12", status: "completed" },
    { id: 2, name: "daily-backup-2024-06-11", size: "244 MB", date: "2024-06-11", status: "completed" },
    { id: 3, name: "daily-backup-2024-06-10", size: "243 MB", date: "2024-06-10", status: "completed" },
    { id: 4, name: "weekly-backup-2024-06-09", size: "1.2 GB", date: "2024-06-09", status: "completed" },
  ];

  return (
    <div className="space-y-6">
      <PageHeader
        title="Database Backup"
        description="Manage database backups and restore points"
        actions={
          <Button className="shadow-lg shadow-primary/20">
            <RefreshCw className="h-4 w-4 mr-2" />
            Create Backup
          </Button>
        }
      />

      <div className="grid gap-6 md:grid-cols-4">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Total Backups</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">24</div>
            <div className="text-xs text-muted-foreground mt-1">All time</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Storage Used</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">5.8 GB</div>
            <div className="text-xs text-muted-foreground mt-1">Of 10 GB quota</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Last Backup</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">2h ago</div>
            <div className="text-xs text-muted-foreground mt-1">Successfully completed</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Auto-Backup</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-green-500">Active</div>
            <div className="text-xs text-muted-foreground mt-1">Daily at 2:00 AM</div>
          </CardContent>
        </Card>
      </div>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Backup Schedule</CardTitle>
          <CardDescription>Configure automated backup settings</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid gap-4 md:grid-cols-3">
            <div className="p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-2 mb-2">
                <Calendar className="h-4 w-4 text-primary" />
                <div className="text-sm font-medium">Daily Backup</div>
              </div>
              <div className="text-xs text-muted-foreground">Every day at 2:00 AM</div>
              <Badge className="mt-2 bg-green-500/10 text-green-500 border-green-500/20">Active</Badge>
            </div>
            <div className="p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-2 mb-2">
                <Calendar className="h-4 w-4 text-blue-500" />
                <div className="text-sm font-medium">Weekly Backup</div>
              </div>
              <div className="text-xs text-muted-foreground">Every Sunday at 3:00 AM</div>
              <Badge className="mt-2 bg-green-500/10 text-green-500 border-green-500/20">Active</Badge>
            </div>
            <div className="p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-2 mb-2">
                <Calendar className="h-4 w-4 text-purple-500" />
                <div className="text-sm font-medium">Monthly Backup</div>
              </div>
              <div className="text-xs text-muted-foreground">1st of every month</div>
              <Badge className="mt-2 bg-green-500/10 text-green-500 border-green-500/20">Active</Badge>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Backup History</CardTitle>
          <CardDescription>View and manage your database backups</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {backups.map((backup) => (
              <div key={backup.id} className="flex items-center justify-between p-4 rounded-lg bg-background border hover:bg-accent/30 transition-colors">
                <div className="flex items-center gap-4">
                  <div className="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <Database className="h-5 w-5 text-primary" />
                  </div>
                  <div>
                    <div className="font-medium text-sm">{backup.name}</div>
                    <div className="flex items-center gap-3 mt-1 text-xs text-muted-foreground">
                      <div className="flex items-center gap-1">
                        <HardDrive className="h-3 w-3" />
                        {backup.size}
                      </div>
                      <div className="flex items-center gap-1">
                        <Clock className="h-3 w-3" />
                        {backup.date}
                      </div>
                    </div>
                  </div>
                </div>
                <div className="flex items-center gap-3">
                  <Badge className="bg-green-500/10 text-green-500 border-green-500/20">{backup.status}</Badge>
                  <Button variant="ghost" size="sm" className="h-8">
                    <Download className="h-3 w-3 mr-1" />
                    Download
                  </Button>
                  <Button variant="ghost" size="sm" className="h-8">
                    <Upload className="h-3 w-3 mr-1" />
                    Restore
                  </Button>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Backup Settings</CardTitle>
          <CardDescription>Configure backup preferences</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <CheckCircle2 className="h-5 w-5 text-green-500" />
                <div>
                  <div className="font-medium">Auto-Backup Enabled</div>
                  <div className="text-xs text-muted-foreground">Automatic daily backups</div>
                </div>
              </div>
              <Badge className="bg-green-500/10 text-green-500 border-green-500/20">Enabled</Badge>
            </div>
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <AlertTriangle className="h-5 w-5 text-orange-500" />
                <div>
                  <div className="font-medium">Retention Policy</div>
                  <div className="text-xs text-muted-foreground">Keep backups for 30 days</div>
                </div>
              </div>
              <Badge className="bg-orange-500/10 text-orange-500 border-orange-500/20">30 Days</Badge>
            </div>
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <Database className="h-5 w-5 text-blue-500" />
                <div>
                  <div className="font-medium">Compression</div>
                  <div className="text-xs text-muted-foreground">Compress backup files</div>
                </div>
              </div>
              <Badge className="bg-green-500/10 text-green-500 border-green-500/20">Enabled</Badge>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
