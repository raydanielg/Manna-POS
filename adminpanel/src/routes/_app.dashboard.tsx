import { createFileRoute } from "@tanstack/react-router";
import { ShoppingCart, Package, Users, TrendingUp, DollarSign, ArrowUpRight, Wallet } from "lucide-react";
import {
  ResponsiveContainer,
  AreaChart,
  Area,
  XAxis,
  YAxis,
  Tooltip,
  CartesianGrid,
  BarChart,
  Bar,
} from "recharts";
import { PageHeader } from "@/components/app-shell";
import { StatusBadge } from "@/components/status-badge";
import { Button } from "@/components/ui/button";
import { useAuth } from "@/lib/auth-context";
import { apiClient } from "@/lib/api/client";
import { useEffect, useState } from "react";

export const Route = createFileRoute("/_app/dashboard")({
  head: () => ({ meta: [{ title: "Dashboard — MannaPOS" }] }),
  component: DashboardPage,
});

interface DashboardStats {
  total_sales: number;
  total_orders: number;
  total_products: number;
  total_customers: number;
  sales_growth: number;
  orders_growth: number;
}

interface Sale {
  id: number;
  reference: string;
  customer: { name: string; email: string } | null;
  total: number;
  status: string;
  sale_date: string;
  created_at: string;
}

interface SalesChartData {
  day: string;
  sales: number;
  orders: number;
}

function formatTZS(value: number) {
  return `TZS ${value.toLocaleString()}`;
}

function DashboardPage() {
  const { user } = useAuth();
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [salesData, setSalesData] = useState<SalesChartData[]>([]);
  const [recentSales, setRecentSales] = useState<Sale[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    loadDashboardData();
  }, []);

  const loadDashboardData = async () => {
    try {
      setIsLoading(true);
      
      // Fetch stats
      const statsData = await apiClient.get<DashboardStats>('/dashboard/stats');
      setStats(statsData);

      // Fetch recent sales
      const sales = await apiClient.get<Sale[]>('/sales?limit=5&sort=created_at,desc');
      setRecentSales(sales);

      // Generate chart data from sales (last 7 days)
      const chartData = generateChartData(sales);
      setSalesData(chartData);
    } catch (error) {
      console.error('Failed to load dashboard data:', error);
    } finally {
      setIsLoading(false);
    }
  };

  const generateChartData = (sales: Sale[]): SalesChartData[] => {
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const today = new Date();
    const last7Days = Array.from({ length: 7 }, (_, i) => {
      const date = new Date(today);
      date.setDate(date.getDate() - (6 - i));
      return date;
    });

    return last7Days.map(date => {
      const dayName = days[date.getDay()];
      const daySales = sales.filter(s => {
        const saleDate = new Date(s.created_at);
        return saleDate.toDateString() === date.toDateString();
      });

      return {
        day: dayName,
        sales: daySales.reduce((sum, s) => sum + parseFloat(s.total.toString()), 0),
        orders: daySales.length,
      };
    });
  };

  const statsCards = stats ? [
    { label: "Total Sales", value: formatTZS(stats.total_sales), delta: `+${stats.sales_growth}%`, icon: DollarSign },
    { label: "Total Orders", value: stats.total_orders.toString(), delta: `+${stats.orders_growth}%`, icon: ShoppingCart },
    { label: "Total Products", value: stats.total_products.toString(), delta: "Active", icon: Package },
    { label: "Total Customers", value: stats.total_customers.toString(), delta: "Registered", icon: Users },
  ] : [];

  const recentSalesFormatted = recentSales.map(sale => ({
    id: sale.reference,
    customer: sale.customer?.name || 'Walk-in Customer',
    email: sale.customer?.email || '',
    amount: parseFloat(sale.total.toString()),
    status: sale.status === 'completed' ? 'success' as const : sale.status === 'pending' ? 'pending' as const : 'success' as const,
    date: new Date(sale.created_at).toLocaleString(),
  }));

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="text-center">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-4"></div>
          <p className="text-sm text-muted-foreground">Loading dashboard data...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title={`Welcome back, ${user?.name || "Admin"}`}
        description="Here's what's happening with your business today."
        actions={
          <>
            <Button variant="outline" size="sm" className="shadow-sm">Export Report</Button>
            <Button size="sm" className="shadow-lg shadow-primary/20">New Sale</Button>
          </>
        }
      />

      {/* Quick Actions */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
        <Button variant="outline" className="h-auto py-3 flex flex-col items-center gap-2 shadow-sm">
          <ShoppingCart className="h-5 w-5" />
          <span className="text-xs font-medium">Quick Sale</span>
        </Button>
        <Button variant="outline" className="h-auto py-3 flex flex-col items-center gap-2 shadow-sm">
          <Package className="h-5 w-5" />
          <span className="text-xs font-medium">Add Product</span>
        </Button>
        <Button variant="outline" className="h-auto py-3 flex flex-col items-center gap-2 shadow-sm">
          <Users className="h-5 w-5" />
          <span className="text-xs font-medium">Add Customer</span>
        </Button>
        <Button variant="outline" className="h-auto py-3 flex flex-col items-center gap-2 shadow-sm">
          <Wallet className="h-5 w-5" />
          <span className="text-xs font-medium">Add Expense</span>
        </Button>
      </div>

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {statsCards.map((s) => (
          <div key={s.label} className="rounded-xl border border-border/50 bg-card p-5 shadow-sm hover:shadow-md transition-shadow">
            <div className="flex items-start justify-between">
              <span className="text-xs font-medium text-muted-foreground">{s.label}</span>
              <div className="p-2 rounded-lg bg-primary/10">
                <s.icon className="h-4 w-4 text-primary" />
              </div>
            </div>
            <div className="mt-3 text-2xl font-bold tracking-tight">{s.value}</div>
            <div className="mt-1 text-xs font-medium text-primary">{s.delta} vs last week</div>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div className="rounded-xl border border-border/50 bg-card p-6 shadow-sm lg:col-span-2">
          <div className="mb-6 flex items-center justify-between">
            <div>
              <h3 className="text-base font-semibold">Sales Revenue</h3>
              <p className="text-xs text-muted-foreground">Last 7 days</p>
            </div>
            <div className="text-right">
              <div className="text-xl font-bold">{formatTZS(salesData.reduce((sum, d) => sum + d.sales, 0))}</div>
              <div className="text-xs font-medium text-primary">Live Data</div>
            </div>
          </div>
          <div className="h-72">
            <ResponsiveContainer width="100%" height="100%">
              <AreaChart data={salesData} margin={{ top: 8, right: 8, left: 0, bottom: 0 }}>
                <defs>
                  <linearGradient id="sales" x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0%" stopColor="oklch(0.74 0.18 148)" stopOpacity={0.5} />
                    <stop offset="100%" stopColor="oklch(0.74 0.18 148)" stopOpacity={0} />
                  </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="3 3" stroke="oklch(0.3 0.02 258)" />
                <XAxis dataKey="day" tick={{ fill: "oklch(0.68 0.018 258)", fontSize: 12 }} axisLine={false} tickLine={false} />
                <YAxis tick={{ fill: "oklch(0.68 0.018 258)", fontSize: 12 }} axisLine={false} tickLine={false} tickFormatter={(v) => `${v / 1_000_000}M`} />
                <Tooltip
                  formatter={(v: number) => formatTZS(v)}
                />
                <Area type="monotone" dataKey="sales" stroke="oklch(0.74 0.18 148)" strokeWidth={2} fill="url(#sales)" />
              </AreaChart>
            </ResponsiveContainer>
          </div>
        </div>

        <div className="rounded-xl border border-border/50 bg-card p-6 shadow-sm">
          <div className="mb-6">
            <h3 className="text-base font-semibold">Orders Volume</h3>
            <p className="text-xs text-muted-foreground">Last 7 days</p>
          </div>
          <div className="h-72">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={salesData} margin={{ top: 8, right: 8, left: 0, bottom: 0 }}>
                <CartesianGrid strokeDasharray="3 3" stroke="oklch(0.3 0.02 258)" />
                <XAxis dataKey="day" tick={{ fill: "oklch(0.68 0.018 258)", fontSize: 12 }} axisLine={false} tickLine={false} />
                <YAxis tick={{ fill: "oklch(0.68 0.018 258)", fontSize: 12 }} axisLine={false} tickLine={false} />
                <Tooltip />
                <Bar dataKey="orders" fill="oklch(0.74 0.18 148)" radius={[4, 4, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </div>
      </div>

      <div className="rounded-xl border border-border/50 bg-card shadow-sm">
        <div className="flex items-center justify-between border-b border-border/50 px-6 py-4">
          <div>
            <h3 className="text-base font-semibold">Recent Sales</h3>
            <p className="text-xs text-muted-foreground">Latest transactions from your database</p>
          </div>
          <Button variant="ghost" size="sm">View all</Button>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="text-left text-xs text-muted-foreground bg-muted/30">
                <th className="px-6 py-3 font-medium">Invoice ID</th>
                <th className="px-6 py-3 font-medium">Customer</th>
                <th className="px-6 py-3 font-medium">Amount</th>
                <th className="px-6 py-3 font-medium">Status</th>
                <th className="px-6 py-3 font-medium">Date</th>
              </tr>
            </thead>
            <tbody>
              {recentSalesFormatted.length > 0 ? recentSalesFormatted.map((sale) => (
                <tr key={sale.id} className="border-t border-border/50 hover:bg-accent/30 transition-colors">
                  <td className="px-6 py-4 font-mono text-xs text-muted-foreground">{sale.id}</td>
                  <td className="px-6 py-4">
                    <div className="font-medium text-foreground">{sale.customer}</div>
                    <div className="text-xs text-muted-foreground">{sale.email}</div>
                  </td>
                  <td className="px-6 py-4 font-semibold">{formatTZS(sale.amount)}</td>
                  <td className="px-6 py-4"><StatusBadge status={sale.status} /></td>
                  <td className="px-6 py-4 text-muted-foreground">{sale.date}</td>
                </tr>
              )) : (
                <tr>
                  <td colSpan={5} className="px-6 py-8 text-center text-muted-foreground">
                    No sales data available
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}