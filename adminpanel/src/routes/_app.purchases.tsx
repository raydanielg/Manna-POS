import { createFileRoute } from "@tanstack/react-router";
import { useState, useEffect } from "react";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Search, ArrowDownToLine, DollarSign, Calendar, Eye } from "lucide-react";
import { apiClient } from "@/lib/api/client";
import { StatusBadge } from "@/components/status-badge";

export const Route = createFileRoute("/_app/purchases")({
  head: () => ({ meta: [{ title: "Purchases — MannaPOS" }] }),
  component: PurchasesPage,
});

interface Purchase {
  id: number;
  reference: string;
  supplier?: { name: string };
  total: number;
  status: string;
  purchase_date: string;
  created_at: string;
}

function PurchasesPage() {
  const [purchases, setPurchases] = useState<Purchase[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");

  useEffect(() => {
    loadPurchases();
  }, []);

  const loadPurchases = async () => {
    try {
      setIsLoading(true);
      const data = await apiClient.get<Purchase[]>("/purchases?limit=100");
      setPurchases(data);
    } catch (error) {
      console.error("Failed to load purchases:", error);
    } finally {
      setIsLoading(false);
    }
  };

  const filteredPurchases = purchases.filter(
    (purchase) =>
      purchase.reference.toLowerCase().includes(searchQuery.toLowerCase()) ||
      (purchase.supplier?.name && purchase.supplier.name.toLowerCase().includes(searchQuery.toLowerCase()))
  );

  const formatTZS = (value: number) => `TZS ${value.toLocaleString()}`;

  return (
    <div className="space-y-6">
      <PageHeader
        title="Purchases Management"
        description="View and manage all purchase orders"
        actions={
          <Button className="shadow-lg shadow-primary/20">
            <ArrowDownToLine className="mr-2 h-4 w-4" />
            New Purchase
          </Button>
        }
      />

      <Card className="shadow-sm">
        <CardHeader>
          <div className="flex items-center justify-between">
            <div>
              <CardTitle>All Purchases</CardTitle>
              <CardDescription>{purchases.length} total purchases</CardDescription>
            </div>
            <div className="relative">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <Input
                placeholder="Search purchases..."
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
                    <th className="text-left py-3 px-4 text-xs font-medium text-muted-foreground uppercase tracking-wider">Supplier</th>
                    <th className="text-left py-3 px-4 text-xs font-medium text-muted-foreground uppercase tracking-wider">Amount</th>
                    <th className="text-left py-3 px-4 text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                    <th className="text-left py-3 px-4 text-xs font-medium text-muted-foreground uppercase tracking-wider">Date</th>
                    <th className="text-right py-3 px-4 text-xs font-medium text-muted-foreground uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredPurchases.map((purchase) => (
                    <tr key={purchase.id} className="border-b border-border/30 hover:bg-accent/30 transition-colors">
                      <td className="py-4 px-4">
                        <div className="text-sm font-mono text-primary font-medium">{purchase.reference}</div>
                      </td>
                      <td className="py-4 px-4">
                        <div className="text-sm text-foreground">{purchase.supplier?.name || "—"}</div>
                      </td>
                      <td className="py-4 px-4">
                        <div className="text-sm font-semibold text-foreground flex items-center gap-1">
                          <DollarSign className="h-3 w-3 text-muted-foreground" />
                          {formatTZS(parseFloat(purchase.total.toString()))}
                        </div>
                      </td>
                      <td className="py-4 px-4">
                        <StatusBadge status={purchase.status === "completed" ? "success" : purchase.status === "pending" ? "pending" : "success"} />
                      </td>
                      <td className="py-4 px-4">
                        <div className="text-sm text-muted-foreground flex items-center gap-1">
                          <Calendar className="h-3 w-3" />
                          {new Date(purchase.purchase_date).toLocaleDateString()}
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
              {filteredPurchases.length === 0 && (
                <div className="text-center py-12 text-muted-foreground">
                  No purchases found
                </div>
              )}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
