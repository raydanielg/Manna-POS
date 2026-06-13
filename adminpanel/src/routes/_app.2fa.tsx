import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Switch } from "@/components/ui/switch";
import { Badge } from "@/components/ui/badge";
import { Fingerprint, Smartphone, Shield, CheckCircle2, AlertTriangle, QrCode, Key } from "lucide-react";

export const Route = createFileRoute("/_app/2fa")({
  head: () => ({ meta: [{ title: "Two-Factor Auth — MannaPOS" }] }),
  component: TwoFactorPage,
});

function TwoFactorPage() {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Two-Factor Authentication"
        description="Add an extra layer of security to your account"
      />

      <div className="grid gap-6 md:grid-cols-3">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">2FA Status</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-2">
              <CheckCircle2 className="h-5 w-5 text-green-500" />
              <div className="text-2xl font-bold text-green-500">Enabled</div>
            </div>
            <div className="text-xs text-muted-foreground mt-1">Account protected</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Backup Codes</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">8</div>
            <div className="text-xs text-muted-foreground mt-1">Codes remaining</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Auth Methods</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">2</div>
            <div className="text-xs text-muted-foreground mt-1">Methods configured</div>
          </CardContent>
        </Card>
      </div>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Authentication Methods</CardTitle>
          <CardDescription>Configure your preferred 2FA methods</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <Smartphone className="h-5 w-5 text-purple-500" />
                <div>
                  <div className="font-medium">Authenticator App</div>
                  <div className="text-xs text-muted-foreground">Use Google Authenticator or Authy</div>
                </div>
              </div>
              <div className="flex items-center gap-3">
                <Badge className="bg-green-500/10 text-green-500 border-green-500/20">Active</Badge>
                <Switch defaultChecked />
              </div>
            </div>
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <Fingerprint className="h-5 w-5 text-blue-500" />
                <div>
                  <div className="font-medium">Biometric Authentication</div>
                  <div className="text-xs text-muted-foreground">Fingerprint or Face ID</div>
                </div>
              </div>
              <div className="flex items-center gap-3">
                <Badge className="bg-green-500/10 text-green-500 border-green-500/20">Active</Badge>
                <Switch defaultChecked />
              </div>
            </div>
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <QrCode className="h-5 w-5 text-orange-500" />
                <div>
                  <div className="font-medium">QR Code Authentication</div>
                  <div className="text-xs text-muted-foreground">Scan QR code for quick login</div>
                </div>
              </div>
              <div className="flex items-center gap-3">
                <Badge variant="secondary">Inactive</Badge>
                <Switch />
              </div>
            </div>
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <Key className="h-5 w-5 text-green-500" />
                <div>
                  <div className="font-medium">Hardware Token</div>
                  <div className="text-xs text-muted-foreground">Use YubiKey or security key</div>
                </div>
              </div>
              <div className="flex items-center gap-3">
                <Badge variant="secondary">Inactive</Badge>
                <Switch />
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Backup Codes</CardTitle>
          <CardDescription>Generate and manage backup codes for account recovery</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="p-4 rounded-lg bg-background border">
              <div className="flex items-center justify-between mb-3">
                <div className="flex items-center gap-2">
                  <Key className="h-4 w-4 text-primary" />
                  <div className="font-medium text-sm">Your Backup Codes</div>
                </div>
                <Button size="sm" variant="outline">Generate New</Button>
              </div>
              <div className="grid grid-cols-4 gap-2">
                {["ABCD-1234", "EFGH-5678", "IJKL-9012", "MNOP-3456", "QRST-7890", "UVWX-1234", "YZAB-5678", "CDEF-9012"].map((code, index) => (
                  <div key={index} className="p-2 rounded bg-muted/30 text-xs font-mono text-center">
                    {code}
                  </div>
                ))}
              </div>
              <p className="text-xs text-muted-foreground mt-3">
                Store these codes in a safe place. Each code can only be used once.
              </p>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Security Settings</CardTitle>
          <CardDescription>Configure 2FA security preferences</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <Shield className="h-5 w-5 text-green-500" />
                <div>
                  <div className="font-medium">Require 2FA for Admin Access</div>
                  <div className="text-xs text-muted-foreground">Mandatory 2FA for admin panel login</div>
                </div>
              </div>
              <Switch defaultChecked />
            </div>
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <AlertTriangle className="h-5 w-5 text-orange-500" />
                <div>
                  <div className="font-medium">Remember Device</div>
                  <div className="text-xs text-muted-foreground">Skip 2FA for trusted devices</div>
                </div>
              </div>
              <Switch />
            </div>
            <div className="flex items-center justify-between p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-3">
                <CheckCircle2 className="h-5 w-5 text-blue-500" />
                <div>
                  <div className="font-medium">Login Notifications</div>
                  <div className="text-xs text-muted-foreground">Alert on new device login</div>
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
