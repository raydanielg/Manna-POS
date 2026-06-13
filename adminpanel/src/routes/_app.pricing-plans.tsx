import { createFileRoute } from "@tanstack/react-router";
import { useState, useEffect } from "react";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Switch } from "@/components/ui/switch";
import { Badge } from "@/components/ui/badge";
import { Search, Plus, Edit, Trash2, CreditCard, Check, Star, Zap } from "lucide-react";
import { apiClient } from "@/lib/api/client";

export const Route = createFileRoute("/_app/pricing-plans")({
  head: () => ({ meta: [{ title: "Pricing Plans — MannaPOS" }] }),
  component: PricingPlansPage,
});

interface PricingPlan {
  id: number;
  name: string;
  price: number;
  currency: string;
  billing_cycle: string;
  features: string[];
  is_active: boolean;
  is_popular: boolean;
  max_users: number;
  max_products: number;
  created_at: string;
}

function PricingPlansPage() {
  const [plans, setPlans] = useState<PricingPlan[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [editingPlan, setEditingPlan] = useState<PricingPlan | null>(null);
  const [formData, setFormData] = useState({
    name: "",
    price: "",
    currency: "TZS",
    billing_cycle: "monthly",
    features: "",
    is_active: true,
    is_popular: false,
    max_users: "",
    max_products: "",
  });

  useEffect(() => {
    loadPlans();
  }, []);

  const loadPlans = async () => {
    try {
      setIsLoading(true);
      // Mock data for now - replace with actual API call
      const mockPlans: PricingPlan[] = [
        {
          id: 1,
          name: "Starter",
          price: 50000,
          currency: "TZS",
          billing_cycle: "monthly",
          features: ["Up to 100 products", "1 user", "Basic reports", "Email support"],
          is_active: true,
          is_popular: false,
          max_users: 1,
          max_products: 100,
          created_at: new Date().toISOString(),
        },
        {
          id: 2,
          name: "Professional",
          price: 150000,
          currency: "TZS",
          billing_cycle: "monthly",
          features: ["Up to 1000 products", "5 users", "Advanced reports", "Priority support", "API access"],
          is_active: true,
          is_popular: true,
          max_users: 5,
          max_products: 1000,
          created_at: new Date().toISOString(),
        },
        {
          id: 3,
          name: "Enterprise",
          price: 500000,
          currency: "TZS",
          billing_cycle: "monthly",
          features: ["Unlimited products", "Unlimited users", "Custom reports", "24/7 support", "Custom integrations", "White-label"],
          is_active: true,
          is_popular: false,
          max_users: 999,
          max_products: 999999,
          created_at: new Date().toISOString(),
        },
      ];
      setPlans(mockPlans);
    } catch (error) {
      console.error("Failed to load pricing plans:", error);
    } finally {
      setIsLoading(false);
    }
  };

  const handleAddPlan = () => {
    setEditingPlan(null);
    setFormData({
      name: "",
      price: "",
      currency: "TZS",
      billing_cycle: "monthly",
      features: "",
      is_active: true,
      is_popular: false,
      max_users: "",
      max_products: "",
    });
    setIsDialogOpen(true);
  };

  const handleEditPlan = (plan: PricingPlan) => {
    setEditingPlan(plan);
    setFormData({
      name: plan.name,
      price: plan.price.toString(),
      currency: plan.currency,
      billing_cycle: plan.billing_cycle,
      features: plan.features.join(", "),
      is_active: plan.is_active,
      is_popular: plan.is_popular,
      max_users: plan.max_users.toString(),
      max_products: plan.max_products.toString(),
    });
    setIsDialogOpen(true);
  };

  const handleDeletePlan = async (planId: number) => {
    if (!confirm("Are you sure you want to delete this pricing plan?")) return;
    try {
      setPlans(plans.filter(p => p.id !== planId));
    } catch (error) {
      console.error("Failed to delete plan:", error);
      alert("Failed to delete plan");
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const features = formData.features.split(",").map(f => f.trim()).filter(f => f);
      const newPlan: PricingPlan = {
        id: editingPlan?.id || Date.now(),
        name: formData.name,
        price: parseFloat(formData.price),
        currency: formData.currency,
        billing_cycle: formData.billing_cycle,
        features,
        is_active: formData.is_active,
        is_popular: formData.is_popular,
        max_users: parseInt(formData.max_users),
        max_products: parseInt(formData.max_products),
        created_at: new Date().toISOString(),
      };

      if (editingPlan) {
        setPlans(plans.map(p => p.id === editingPlan.id ? newPlan : p));
      } else {
        setPlans([...plans, newPlan]);
      }
      setIsDialogOpen(false);
    } catch (error) {
      console.error("Failed to save plan:", error);
      alert("Failed to save plan");
    }
  };

  const filteredPlans = plans.filter(
    (plan) =>
      plan.name.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const formatPrice = (price: number, currency: string) => `${currency} ${price.toLocaleString()}`;

  return (
    <div className="space-y-6">
      <PageHeader
        title="Pricing Plans"
        description="Manage subscription plans for your SaaS offering"
        actions={
          <Button onClick={handleAddPlan} className="shadow-lg shadow-primary/20">
            <Plus className="mr-2 h-4 w-4" />
            Add Plan
          </Button>
        }
      />

      <Card className="shadow-sm">
        <CardHeader>
          <div className="flex items-center justify-between">
            <div>
              <CardTitle>All Plans</CardTitle>
              <CardDescription>{plans.length} total plans</CardDescription>
            </div>
            <div className="relative">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <Input
                placeholder="Search plans..."
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
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {filteredPlans.map((plan) => (
                <div
                  key={plan.id}
                  className={`relative rounded-xl border ${
                    plan.is_popular ? "border-primary/50 shadow-lg shadow-primary/10" : "border-border/50"
                  } bg-card p-6 hover:shadow-md transition-shadow`}
                >
                  {plan.is_popular && (
                    <div className="absolute -top-3 left-1/2 -translate-x-1/2">
                      <Badge className="bg-primary text-primary-foreground gap-1">
                        <Star className="h-3 w-3" />
                        Popular
                      </Badge>
                    </div>
                  )}
                  <div className="flex items-center justify-between mb-4">
                    <div className="flex items-center gap-2">
                      <div className="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
                        <CreditCard className="h-5 w-5 text-primary" />
                      </div>
                      <div>
                        <h3 className="font-semibold text-foreground">{plan.name}</h3>
                        <p className="text-xs text-muted-foreground capitalize">{plan.billing_cycle}</p>
                      </div>
                    </div>
                    <div className="flex items-center gap-1">
                      <Switch
                        checked={plan.is_active}
                        onCheckedChange={() => {
                          setPlans(plans.map(p => p.id === plan.id ? { ...p, is_active: !p.is_active } : p));
                        }}
                      />
                    </div>
                  </div>
                  <div className="mb-4">
                    <div className="text-3xl font-bold text-foreground">
                      {formatPrice(plan.price, plan.currency)}
                    </div>
                    <p className="text-xs text-muted-foreground">per {plan.billing_cycle}</p>
                  </div>
                  <div className="space-y-2 mb-4">
                    <div className="flex items-center gap-2 text-sm">
                      <Zap className="h-4 w-4 text-primary" />
                      <span className="text-muted-foreground">{plan.max_users} users</span>
                    </div>
                    <div className="flex items-center gap-2 text-sm">
                      <Check className="h-4 w-4 text-green-500" />
                      <span className="text-muted-foreground">{plan.max_products} products</span>
                    </div>
                  </div>
                  <div className="space-y-2 mb-6">
                    {plan.features.slice(0, 3).map((feature, index) => (
                      <div key={index} className="flex items-center gap-2 text-sm">
                        <Check className="h-4 w-4 text-green-500" />
                        <span className="text-muted-foreground">{feature}</span>
                      </div>
                    ))}
                    {plan.features.length > 3 && (
                      <div className="text-xs text-muted-foreground">
                        +{plan.features.length - 3} more features
                      </div>
                    )}
                  </div>
                  <div className="flex gap-2">
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => handleEditPlan(plan)}
                      className="flex-1"
                    >
                      <Edit className="h-4 w-4 mr-1" />
                      Edit
                    </Button>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => handleDeletePlan(plan.id)}
                      className="text-destructive hover:text-destructive hover:bg-destructive/10"
                    >
                      <Trash2 className="h-4 w-4" />
                    </Button>
                  </div>
                </div>
              ))}
            </div>
          )}
          {filteredPlans.length === 0 && (
            <div className="text-center py-12 text-muted-foreground">
              No pricing plans found
            </div>
          )}
        </CardContent>
      </Card>

      <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>{editingPlan ? "Edit Pricing Plan" : "Add New Pricing Plan"}</DialogTitle>
            <DialogDescription>
              {editingPlan ? "Update pricing plan details" : "Create a new subscription plan"}
            </DialogDescription>
          </DialogHeader>
          <form onSubmit={handleSubmit}>
            <div className="grid gap-4 py-4">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="name">Plan Name</Label>
                  <Input
                    id="name"
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="price">Price</Label>
                  <Input
                    id="price"
                    type="number"
                    step="0.01"
                    value={formData.price}
                    onChange={(e) => setFormData({ ...formData, price: e.target.value })}
                    required
                  />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="currency">Currency</Label>
                  <Input
                    id="currency"
                    value={formData.currency}
                    onChange={(e) => setFormData({ ...formData, currency: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="billing_cycle">Billing Cycle</Label>
                  <select
                    id="billing_cycle"
                    value={formData.billing_cycle}
                    onChange={(e) => setFormData({ ...formData, billing_cycle: e.target.value })}
                    className="w-full h-10 px-3 rounded-md border border-input bg-background text-sm"
                  >
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="yearly">Yearly</option>
                  </select>
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="max_users">Max Users</Label>
                  <Input
                    id="max_users"
                    type="number"
                    value={formData.max_users}
                    onChange={(e) => setFormData({ ...formData, max_users: e.target.value })}
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="max_products">Max Products</Label>
                  <Input
                    id="max_products"
                    type="number"
                    value={formData.max_products}
                    onChange={(e) => setFormData({ ...formData, max_products: e.target.value })}
                    required
                  />
                </div>
              </div>
              <div className="space-y-2">
                <Label htmlFor="features">Features (comma-separated)</Label>
                <Input
                  id="features"
                  value={formData.features}
                  onChange={(e) => setFormData({ ...formData, features: e.target.value })}
                  placeholder="Feature 1, Feature 2, Feature 3"
                />
              </div>
              <div className="flex items-center gap-4">
                <div className="flex items-center gap-2">
                  <Switch
                    id="is_active"
                    checked={formData.is_active}
                    onCheckedChange={(checked) => setFormData({ ...formData, is_active: checked })}
                  />
                  <Label htmlFor="is_active">Active</Label>
                </div>
                <div className="flex items-center gap-2">
                  <Switch
                    id="is_popular"
                    checked={formData.is_popular}
                    onCheckedChange={(checked) => setFormData({ ...formData, is_popular: checked })}
                  />
                  <Label htmlFor="is_popular">Popular</Label>
                </div>
              </div>
            </div>
            <DialogFooter>
              <Button type="button" variant="outline" onClick={() => setIsDialogOpen(false)}>
                Cancel
              </Button>
              <Button type="submit">{editingPlan ? "Update Plan" : "Create Plan"}</Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  );
}
