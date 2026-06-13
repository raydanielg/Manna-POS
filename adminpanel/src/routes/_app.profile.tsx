import { createFileRoute } from "@tanstack/react-router";
import { CheckCircle2, Shield, Building, MapPin, Calendar } from "lucide-react";
import { PageHeader } from "@/components/app-shell";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { useAuth } from "@/lib/auth-context";

export const Route = createFileRoute("/_app/profile")({
  head: () => ({ meta: [{ title: "Profile — MannaPOS" }] }),
  component: ProfilePage,
});

function ProfilePage() {
  const { user } = useAuth();

  return (
    <div>
      <PageHeader title="Profile" description="Your personal account and business information." />

      <div className="grid gap-6 lg:grid-cols-3">
        <div className="rounded-xl border border-border/50 bg-card p-6 lg:col-span-2 shadow-sm">
          <div className="flex items-center gap-4">
            <Avatar className="h-16 w-16">
              <AvatarFallback className="bg-primary/15 text-primary text-lg font-semibold">
                {user?.name?.charAt(0).toUpperCase() || "A"}
              </AvatarFallback>
            </Avatar>
            <div>
              <h3 className="text-lg font-semibold">{user?.name || "Admin"}</h3>
              <p className="text-sm text-muted-foreground">Member since {user?.created_at ? new Date(user.created_at).toLocaleDateString() : "2024"}</p>
              <div className="mt-1 flex gap-2">
                <Badge variant="outline" className="border-primary/30 bg-primary/10 text-primary capitalize">
                  <Shield className="h-3 w-3 mr-1" />
                  {user?.role || "user"}
                </Badge>
              </div>
            </div>
          </div>

          <div className="mt-6 grid gap-4 sm:grid-cols-2">
            <div className="space-y-1.5"><Label>Full name</Label><Input defaultValue={user?.name || ""} /></div>
            <div className="space-y-1.5"><Label>Email</Label><Input defaultValue={user?.email || ""} /></div>
            <div className="space-y-1.5"><Label>Business name</Label><Input defaultValue={user?.business_name || ""} /></div>
            <div className="space-y-1.5"><Label>City</Label><Input defaultValue={user?.business_city || ""} /></div>
            <div className="space-y-1.5"><Label>Country</Label><Input defaultValue={user?.business_country || ""} /></div>
            <div className="space-y-1.5"><Label>Currency</Label><Input defaultValue={user?.currency || "TZS"} /></div>
          </div>
          <div className="mt-5 flex justify-end gap-2">
            <Button variant="outline" size="sm">Cancel</Button>
            <Button size="sm" className="shadow-sm">Save profile</Button>
          </div>
        </div>

        <div className="rounded-xl border border-border/50 bg-card p-6 shadow-sm">
          <h3 className="text-sm font-semibold">Account Status</h3>
          <div className="mt-4 space-y-3">
            <div className="flex items-center justify-between rounded-lg border border-border/50 bg-background p-3">
              <div>
                <div className="text-sm font-medium">Account Type</div>
                <div className="text-xs text-muted-foreground">Admin Account</div>
              </div>
              <Badge variant="outline" className="border-primary/30 bg-primary/10 text-primary gap-1">
                <CheckCircle2 className="h-3 w-3" /> Active
              </Badge>
            </div>
            <div className="flex items-center justify-between rounded-lg border border-border/50 bg-background p-3">
              <div>
                <div className="text-sm font-medium">Email</div>
                <div className="text-xs text-muted-foreground">{user?.email || ""}</div>
              </div>
              <Badge variant="outline" className="border-primary/30 bg-primary/10 text-primary gap-1">
                <CheckCircle2 className="h-3 w-3" /> Verified
              </Badge>
            </div>
            <div className="flex items-center justify-between rounded-lg border border-border/50 bg-background p-3">
              <div>
                <div className="text-sm font-medium">Business</div>
                <div className="text-xs text-muted-foreground flex items-center gap-1">
                  <Building className="h-3 w-3" />
                  {user?.business_name || "Not set"}
                </div>
              </div>
              <Badge variant="outline" className="border-primary/30 bg-primary/10 text-primary gap-1">
                <CheckCircle2 className="h-3 w-3" /> Verified
              </Badge>
            </div>
          </div>

          <div className="mt-6 rounded-lg border border-primary/20 bg-primary/5 p-3">
            <div className="flex items-center gap-2 text-sm font-medium text-primary">
              <span className="h-2 w-2 rounded-full bg-primary animate-pulse" />
              All systems operational
            </div>
            <p className="mt-1 text-xs text-muted-foreground">Last checked just now</p>
          </div>
        </div>
      </div>
    </div>
  );
}