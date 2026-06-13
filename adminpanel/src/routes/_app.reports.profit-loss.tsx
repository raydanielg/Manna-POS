import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { TrendingUp, TrendingDown, DollarSign, Wallet } from "lucide-react";
import { apiClient } from "@/lib/api/client";
import { useEffect, useState } from "react";

export const Route = createFileRoute("/_app/reports/profit-loss")({
  head: () => ({ meta: [{ title: "Profit/Loss Report — MannaPOS" }] }),
  component: ProfitLossPage,
});

function ProfitLossPage() {
  const [isLoading, setIsLoading] = useState(true);
  const [reportData, setReportData] = useState<any>(null);

  useEffect(() => {
    loadReport();
  }, []);

  const loadReport = async () => {
    try {
      setIsLoading(true);
      const data = await apiClient.get("/reports/profit-loss");
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
        title="Profit/Loss Report"
        description="View your business profitability and financial performance"
      />

      {isLoading ? (
        <div className="flex items-center justify-center h-96">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <Card className="shadow-sm">
            <CardHeader className="pb-3">
              <CardTitle className="text-sm font-medium text-muted-foreground">Total Revenue</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-center gap-2">
                <DollarSign className="h-4 w-4 text-green-500" />
                <div className="text-2xl font-bold">{reportData ? formatTZS(reportData.total_revenue || 0) : "TZS 0"}</div>
              </div>
            </CardContent>
          </Card>
          <Card className="shadow-sm">
            <CardHeader className="pb-3">
              <CardTitle className="text-sm font-medium text-muted-foreground">Total Expenses</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-center gap-2">
                <Wallet className="h-4 w-4 text-red-500" />
                <div className="text-2xl font-bold">{reportData ? formatTZS(reportData.total_expenses || 0) : "TZS 0"}</div>
              </div>
            </CardContent>
          </Card>
          <Card className="shadow-sm">
            <CardHeader className="pb-3">
              <CardTitle className="text-sm font-medium text-muted-foreground">Net Profit</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-center gap-2">
                <TrendingUp className="h-4 w-4 text-primary" />
                <div className="text-2xl font-bold">{reportData ? formatTZS(reportData.net_profit || 0) : "TZS 0"}</div>
              </div>
            </CardContent>
          </Card>
          <Card className="shadow-sm">
            <CardHeader className="pb-3">
              <CardTitle className="text-sm font-medium text-muted-foreground">Profit Margin</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-center gap-2">
                <TrendingDown className="h-4 w-4 text-blue-500" />
                <div className="text-2xl font-bold">{reportData ? `${reportData.profit_margin || 0}%` : "0%"}</div>
              </div>
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  );
}
