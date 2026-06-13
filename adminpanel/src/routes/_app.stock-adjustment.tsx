import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Package, AlertTriangle } from "lucide-react";

export const Route = createFileRoute("/_app/stock-adjustment")({
  head: () => ({ meta: [{ title: "Stock Adjustment — MannaPOS" }] }),
  component: StockAdjustmentPage,
});

function StockAdjustmentPage() {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Stock Adjustment"
        description="Adjust stock levels for inventory management"
        actions={
          <Button className="shadow-lg shadow-primary/20">
            <Package className="mr-2 h-4 w-4" />
            New Adjustment
          </Button>
        }
      />

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Stock Adjustments</CardTitle>
          <CardDescription>View and manage all stock adjustment records</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="text-center py-12 text-muted-foreground">
            <AlertTriangle className="h-12 w-12 mx-auto mb-4 text-muted-foreground/50" />
            <p className="text-lg font-medium">No stock adjustments yet</p>
            <p className="text-sm mt-2">Create your first stock adjustment to get started</p>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
