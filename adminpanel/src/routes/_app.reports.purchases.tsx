import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { ArrowDownToLine, DollarSign, TrendingUp, Calendar } from "lucide-react";
import { apiClient } from "@/lib/api/client";
import { useEffect, useState } from "react";

export const Route = createFileRoute("/_app/reports/purchases")({
  head: () => ({ meta: [{ title: "Purchase Report — MannaPOS" }] }),
  component: PurchaseReportPage,
});

function PurchaseReportPage() {
  const [isLoading, setIsLoading] = useState(true);
  const [reportData, setReportData] = useState<any>(null);

  useEffect(() => {
    loadReport();
  }, []);

  const loadReport = async () => {
    try {
      setIsLoading(true);
      const data = await apiClient.get("/reports/purchases");
      setReportData(data);
    } catch (error) {
      console.error("Failed to load report:", error);
    } finally {
      setIsLoading(false);
    }
  };

  const formatTZS = (value: number) => `TZS ${value.toLocaleString()}`;

  return (
    <div className="space-y-6">
      <PageHeader
        title="Purchase Report"
        description="View detailed purchase analytics and spending metrics"
      />

      {isLoading ? (
        <div className="flex items-center justify-center h-96">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <Card className="shadow-sm">
            <CardHeader className="pb-3">
              <CardTitle className="text-sm font-medium text-muted-foreground">Total Purchases</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-center gap-2">
                <ArrowDownToLine className="h-4 w-4 text-primary" />
                <div className="text-2xl font-bold">{reportData?.total_purchases || 0}</div>
              </div>
            </CardContent>
          </Card>
          <Card className="shadow-sm">
            <CardHeader className="pb-3">
              <CardTitle className="text-sm font-medium text-muted-foreground">Total Spending</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-center gap-2">
                <DollarSign className="h-4 w-4 text-green-500" />
                <div className="text-2xl font-bold">{reportData ? formatTZS(reportData.total_spending || 0) : "TZS 0"}</div>
              </div>
            </CardContent>
          </Card>
          <Card className="shadow-sm">
            <CardHeader className="pb-3">
              <CardTitle className="text-sm font-medium text-muted-foreground">Average Purchase</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-center gap-2">
                <TrendingUp className="h-4 w-4 text-blue-500" />
                <div className="text-2xl font-bold">{reportData ? formatTZS(reportData.average_purchase || 0) : "TZS 0"}</div>
              </div>
            </CardContent>
          </Card>
          <Card className="shadow-sm">
            <CardHeader className="pb-3">
              <CardTitle className="text-sm font-medium text-muted-foreground">This Month</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-center gap-2">
                <Calendar className="h-4 w-4 text-purple-500" />
                <div className="text-2xl font-bold">{reportData?.this_month || 0}</div>
              </div>
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  );
}
