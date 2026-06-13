import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Smartphone, ShoppingCart, Package, Users, BarChart3, Settings, QrCode, Bell, Search, Plus, TrendingUp, Wallet, Clock, CheckCircle2 } from "lucide-react";

export const Route = createFileRoute("/_app/mobile-app")({
  head: () => ({ meta: [{ title: "Mobile App — MannaPOS" }] }),
  component: MobileAppPage,
});

function MobileAppPage() {
  const features = [
    { icon: ShoppingCart, title: "Quick Sale", description: "Process sales quickly", color: "text-green-500" },
    { icon: Package, title: "Products", description: "Manage inventory", color: "text-blue-500" },
    { icon: Users, title: "Customers", description: "Customer management", color: "text-purple-500" },
    { icon: BarChart3, title: "Reports", description: "View analytics", color: "text-orange-500" },
    { icon: Wallet, title: "Expenses", description: "Track expenses", color: "text-red-500" },
    { icon: QrCode, title: "Scanner", description: "QR code scanning", color: "text-cyan-500" },
    { icon: Bell, title: "Notifications", description: "Real-time alerts", color: "text-pink-500" },
    { icon: Settings, title: "Settings", description: "App configuration", color: "text-gray-500" },
  ];

  const recentActivity = [
    { action: "Sale completed", amount: "TZS 45,000", time: "2 min ago", status: "success" },
    { action: "Product added", amount: "New Item", time: "15 min ago", status: "info" },
    { action: "Customer created", amount: "John Doe", time: "1 hour ago", status: "success" },
    { action: "Expense logged", amount: "TZS 5,000", time: "2 hours ago", status: "warning" },
  ];

  return (
    <div className="space-y-6">
      <PageHeader
        title="Mobile App Interface"
        description="Mobile-friendly interface for field operations"
        actions={
          <Button className="shadow-lg shadow-primary/20">
            <Smartphone className="h-4 w-4 mr-2" />
            Launch App
          </Button>
        }
      />

      <div className="grid gap-6 md:grid-cols-4">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Today's Sales</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">TZS 450,000</div>
            <div className="text-xs text-muted-foreground mt-1 flex items-center gap-1">
              <TrendingUp className="h-3 w-3 text-green-500" />
              +12% from yesterday
            </div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Transactions</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">23</div>
            <div className="text-xs text-muted-foreground mt-1">Completed today</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Products</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">1,248</div>
            <div className="text-xs text-muted-foreground mt-1">In inventory</div>
          </CardContent>
        </Card>
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle className="text-sm">Customers</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">892</div>
            <div className="text-xs text-muted-foreground mt-1">Total customers</div>
          </CardContent>
        </Card>
      </div>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Quick Actions</CardTitle>
          <CardDescription>Frequently used mobile features</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <Button variant="outline" className="h-24 flex flex-col items-center justify-center gap-2 shadow-sm">
              <ShoppingCart className="h-8 w-8 text-green-500" />
              <span className="text-sm font-medium">New Sale</span>
            </Button>
            <Button variant="outline" className="h-24 flex flex-col items-center justify-center gap-2 shadow-sm">
              <Package className="h-8 w-8 text-blue-500" />
              <span className="text-sm font-medium">Add Product</span>
            </Button>
            <Button variant="outline" className="h-24 flex flex-col items-center justify-center gap-2 shadow-sm">
              <Users className="h-8 w-8 text-purple-500" />
              <span className="text-sm font-medium">Add Customer</span>
            </Button>
            <Button variant="outline" className="h-24 flex flex-col items-center justify-center gap-2 shadow-sm">
              <QrCode className="h-8 w-8 text-cyan-500" />
              <span className="text-sm font-medium">Scan QR</span>
            </Button>
          </div>
        </CardContent>
      </Card>

      <div className="grid gap-6 lg:grid-cols-2">
        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle>Mobile Features</CardTitle>
            <CardDescription>Available features on mobile app</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-2 gap-3">
              {features.map((feature, index) => (
                <div key={index} className="flex items-center gap-3 p-3 rounded-lg bg-background border hover:bg-accent/30 transition-colors">
                  <feature.icon className={`h-5 w-5 ${feature.color}`} />
                  <div>
                    <div className="text-sm font-medium">{feature.title}</div>
                    <div className="text-xs text-muted-foreground">{feature.description}</div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card className="shadow-sm">
          <CardHeader>
            <CardTitle>Recent Activity</CardTitle>
            <CardDescription>Latest mobile transactions</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {recentActivity.map((activity, index) => (
                <div key={index} className="flex items-center justify-between p-3 rounded-lg bg-background border">
                  <div className="flex items-center gap-3">
                    <CheckCircle2 className="h-4 w-4 text-green-500" />
                    <div>
                      <div className="text-sm font-medium">{activity.action}</div>
                      <div className="text-xs text-muted-foreground">{activity.amount}</div>
                    </div>
                  </div>
                  <div className="flex items-center gap-2">
                    <Clock className="h-3 w-3 text-muted-foreground" />
                    <div className="text-xs text-muted-foreground">{activity.time}</div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Mobile App Settings</CardTitle>
          <CardDescription>Configure mobile app behavior</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid gap-4 md:grid-cols-3">
            <div className="p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-2 mb-2">
                <Bell className="h-4 w-4 text-primary" />
                <div className="text-sm font-medium">Push Notifications</div>
              </div>
              <div className="text-xs text-muted-foreground">Real-time alerts enabled</div>
              <Badge className="mt-2 bg-green-500/10 text-green-500 border-green-500/20">Active</Badge>
            </div>
            <div className="p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-2 mb-2">
                <Search className="h-4 w-4 text-blue-500" />
                <div className="text-sm font-medium">Offline Mode</div>
              </div>
              <div className="text-xs text-muted-foreground">Work without internet</div>
              <Badge className="mt-2 bg-green-500/10 text-green-500 border-green-500/20">Enabled</Badge>
            </div>
            <div className="p-4 rounded-lg bg-background border">
              <div className="flex items-center gap-2 mb-2">
                <Plus className="h-4 w-4 text-purple-500" />
                <div className="text-sm font-medium">Quick Actions</div>
              </div>
              <div className="text-xs text-muted-foreground">Fast access to features</div>
              <Badge className="mt-2 bg-green-500/10 text-green-500 border-green-500/20">Configured</Badge>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
