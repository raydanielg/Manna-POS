import { createFileRoute } from "@tanstack/react-router";
import { useState, useEffect } from "react";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Search, ShoppingCart, DollarSign, Calendar, Eye } from "lucide-react";
import { apiClient } from "@/lib/api/client";
import { StatusBadge } from "@/components/status-badge";

export const Route = createFileRoute("/_app/sales")({
  head: () => ({ meta: [{ title: "Sales — MannaPOS" }] }),
  component: SalesPage,
});

interface Sale {
  id: number;
  reference: string;
  customer?: { name: string };
  total: number;
  status: string;
  payment_method: string;
  sale_date: string;
  created_at: string;
}

function SalesPage() {
  const [sales, setSales] = useState<Sale[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");

  useEffect(() => {
    loadSales();
  }, []);

  const loadSales = async () => {
    try {
      setIsLoading(true);
      const data = await apiClient.get<Sale[]>("/sales?limit=100");
      setSales(data);
    } catch (error) {
      console.error("Failed to load sales:", error);
    } finally {
      setIsLoading(false);
    }
  };

  const filteredSales = sales.filter(
    (sale) =>
      sale.reference.toLowerCase().includes(searchQuery.toLowerCase()) ||
      (sale.customer?.name && sale.customer.name.toLowerCase().includes(searchQuery.toLowerCase()))
  );

  const formatTZS = (value: number) => `TZS ${value.toLocaleString()}`;

  return (
    <div className="space-y-6">
      <PageHeader
        title="Sales Management"
        description="View and manage all sales transactions"
        actions={
          <Button className="shadow-lg shadow-primary/20">
            <ShoppingCart className="mr-2 h-4 w-4" />
            New Sale
          </Button>
        }
      />

      <Card className="shadow-sm">
        <CardHeader>
          <div className="flex items-center justify-between">
            <div>
              <CardTitle>All Sales</CardTitle>
              <CardDescription>{sales.length} total sales</CardDescription>
            </div>
            <div className="relative">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <Input
                placeholder="Search sales..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="pl-9 w-64"
              />
            </div>
          </div>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="flex items-center justify-center h-64">
              <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b border-border/50">
                    <th className="text-left py-3 px-4 text-xs font-medium text-muted-foreground uppercase tracking-wider">Reference</th>
                    <th className="text-left py-3 px-4 text-xs font-medium text-muted-foreground uppercase tracking-wider">Customer</th>
                    <th className="text-left py-3 px-4 text-xs font-medium text-muted-foreground uppercase tracking-wider">Amount</th>
                    <th className="text-left py-3 px-4 text-xs font-medium text-muted-foreground uppercase tracking-wider">Payment</th>
                    <th className="text-left py-3 px-4 text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                    <th className="text-left py-3 px-4 text-xs font-medium text-muted-foreground uppercase tracking-wider">Date</th>
                    <th className="text-right py-3 px-4 text-xs font-medium text-muted-foreground uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredSales.map((sale) => (
                    <tr key={sale.id} className="border-b border-border/30 hover:bg-accent/30 transition-colors">
                      <td className="py-4 px-4">
                        <div className="text-sm font-mono text-primary font-medium">{sale.reference}</div>
                      </td>
                      <td className="py-4 px-4">
                        <div className="text-sm text-foreground">{sale.customer?.name || "Walk-in Customer"}</div>
                      </td>
                      <td className="py-4 px-4">
                        <div className="text-sm font-semibold text-foreground flex items-center gap-1">
                          <DollarSign className="h-3 w-3 text-muted-foreground" />
                          {formatTZS(parseFloat(sale.total.toString()))}
                        </div>
                      </td>
                      <td className="py-4 px-4">
                        <Badge variant="outline" className="capitalize">{sale.payment_method}</Badge>
                      </td>
                      <td className="py-4 px-4">
                        <StatusBadge status={sale.status === "completed" ? "success" : sale.status === "pending" ? "pending" : "success"} />
                      </td>
                      <td className="py-4 px-4">
                        <div className="text-sm text-muted-foreground flex items-center gap-1">
                          <Calendar className="h-3 w-3" />
                          {new Date(sale.sale_date).toLocaleDateString()}
                        </div>
                      </td>
                      <td className="py-4 px-4 text-right">
                        <Button variant="ghost" size="sm" className="h-8 w-8 p-0">
                          <Eye className="h-4 w-4" />
                        </Button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
              {filteredSales.length === 0 && (
                <div className="text-center py-12 text-muted-foreground">
                  No sales found
                </div>
              )}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
