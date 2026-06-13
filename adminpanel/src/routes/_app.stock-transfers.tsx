import { createFileRoute } from "@tanstack/react-router";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { ArrowRightLeft, Package, Calendar, Building } from "lucide-react";

export const Route = createFileRoute("/_app/stock-transfers")({
  head: () => ({ meta: [{ title: "Stock Transfers — MannaPOS" }] }),
  component: StockTransfersPage,
});

function StockTransfersPage() {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Stock Transfers"
        description="Manage stock transfers between locations"
        actions={
          <Button className="shadow-lg shadow-primary/20">
            <ArrowRightLeft className="mr-2 h-4 w-4" />
            New Transfer
          </Button>
        }
      />

      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle>Stock Transfers</CardTitle>
          <CardDescription>View and manage all stock transfer records</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="text-center py-12 text-muted-foreground">
            <ArrowRightLeft className="h-12 w-12 mx-auto mb-4 text-muted-foreground/50" />
            <p className="text-lg font-medium">No stock transfers yet</p>
            <p className="text-sm mt-2">Create your first stock transfer to get started</p>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
