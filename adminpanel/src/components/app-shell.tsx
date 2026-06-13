import { useState, type ReactNode } from "react";
import { Link, useRouterState, useNavigate } from "@tanstack/react-router";
import {
  LayoutDashboard,
  Users,
  Package,
  ShoppingCart,
  ArrowDownToLine,
  ArrowUpToLine,
  Truck,
  Wallet,
  FileText,
  Settings,
  User,
  Search,
  Bell,
  ChevronDown,
  LogOut,
  Menu,
  X,
  Calculator,
  Calendar,
  CreditCard,
  Shield,
  Activity,
  Database,
  Lock,
  Smartphone,
  Wrench,
  AlertTriangle,
  BarChart3,
  Key,
  Eye,
  Fingerprint,
} from "lucide-react";
import { cn } from "@/lib/utils";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { useAuth } from "@/lib/auth-context";

type NavItem = { title: string; to: string; icon: typeof LayoutDashboard };
type NavSection = { label: string; items: NavItem[] };

const navSections: NavSection[] = [
  {
    label: "Main",
    items: [
      { title: "Dashboard", to: "/dashboard", icon: LayoutDashboard },
    ],
  },
  {
    label: "Management",
    items: [
      { title: "Users", to: "/users", icon: Users },
      { title: "Contacts", to: "/contacts", icon: Users },
      { title: "Products", to: "/products", icon: Package },
      { title: "Purchases", to: "/purchases", icon: ArrowDownToLine },
      { title: "Sales", to: "/sales", icon: ShoppingCart },
      { title: "Stock Transfers", to: "/stock-transfers", icon: Truck },
      { title: "Stock Adjustment", to: "/stock-adjustment", icon: Package },
      { title: "Expenses", to: "/expenses", icon: Wallet },
    ],
  },
  {
    label: "System",
    items: [
      { title: "Maintenance Mode", to: "/maintenance", icon: Wrench },
      { title: "Mobile App Control", to: "/mobile-control", icon: Smartphone },
      { title: "Mobile App", to: "/mobile-app", icon: Smartphone },
      { title: "System Health", to: "/system-health", icon: Activity },
      { title: "Audit Logs", to: "/audit-logs", icon: FileText },
      { title: "Database Backup", to: "/database-backup", icon: Database },
    ],
  },
  {
    label: "Security",
    items: [
      { title: "Security Settings", to: "/security", icon: Shield },
      { title: "Access Control", to: "/access-control", icon: Lock },
      { title: "API Keys", to: "/api-keys", icon: Key },
      { title: "Session Management", to: "/sessions", icon: Eye },
      { title: "Two-Factor Auth", to: "/2fa", icon: Fingerprint },
    ],
  },
  {
    label: "Reports",
    items: [
      { title: "Profit/Loss", to: "/reports/profit-loss", icon: FileText },
      { title: "Inventory", to: "/reports/inventory", icon: Package },
      { title: "Sales Report", to: "/reports/sales", icon: ShoppingCart },
      { title: "Purchase Report", to: "/reports/purchases", icon: ArrowDownToLine },
      { title: "Analytics", to: "/reports/analytics", icon: BarChart3 },
    ],
  },
  {
    label: "Settings",
    items: [
      { title: "General", to: "/settings", icon: Settings },
      { title: "Profile", to: "/profile", icon: User },
      { title: "Pricing Plans", to: "/pricing-plans", icon: CreditCard },
    ],
  },
];

function SidebarContent({ collapsed }: { collapsed: boolean }) {
  const pathname = useRouterState({ select: (s) => s.location.pathname });
  return (
    <nav className="flex h-full flex-col gap-6 px-3 py-5">
      <Link to="/dashboard" className="flex items-center gap-2 px-2">
        <div className="flex h-8 w-8 items-center justify-center rounded-md bg-primary text-primary-foreground font-bold">
          M
        </div>
        {!collapsed && (
          <div className="leading-tight">
            <div className="text-sm font-semibold text-sidebar-foreground">MannaPOS</div>
            <div className="text-[10px] uppercase tracking-wider text-muted-foreground">
              Admin Panel
            </div>
          </div>
        )}
      </Link>

      <div className="flex-1 space-y-5 overflow-y-auto">
        {navSections.map((section) => (
          <div key={section.label}>
            {!collapsed && (
              <div className="px-2 pb-2 text-[10px] font-semibold uppercase tracking-[0.12em] text-muted-foreground">
                {section.label}
              </div>
            )}
            <ul className="space-y-0.5">
              {section.items.map((item) => {
                const active = pathname === item.to;
                const Icon = item.icon;
                return (
                  <li key={item.to}>
                    <Link
                      to={item.to}
                      className={cn(
                        "group flex items-center gap-3 rounded-md px-2.5 py-2 text-sm transition-colors",
                        active
                          ? "bg-sidebar-accent text-sidebar-accent-foreground"
                          : "text-sidebar-foreground/80 hover:bg-sidebar-accent/60 hover:text-sidebar-foreground",
                      )}
                    >
                      <Icon
                        className={cn(
                          "h-4 w-4 shrink-0",
                          active ? "text-primary" : "text-muted-foreground group-hover:text-foreground",
                        )}
                      />
                      {!collapsed && <span className="truncate">{item.title}</span>}
                    </Link>
                  </li>
                );
              })}
            </ul>
          </div>
        ))}
      </div>

      {!collapsed && (
        <div className="rounded-lg border border-sidebar-border bg-sidebar-accent/40 p-3 text-xs">
          <div className="flex items-center gap-2 font-medium text-sidebar-foreground">
            <span className="h-2 w-2 rounded-full bg-primary animate-pulse" />
            All systems operational
          </div>
          <p className="mt-1 text-muted-foreground">API latency normal · 99.99% uptime</p>
        </div>
      )}
    </nav>
  );
}

export function AppShell({ children }: { children: ReactNode }) {
  const [collapsed, setCollapsed] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate({ to: "/login" });
  };

  return (
    <div className="flex min-h-screen w-full bg-background text-foreground">
      {/* Desktop sidebar */}
      <aside
        className={cn(
          "hidden md:flex shrink-0 border-r border-sidebar-border bg-sidebar transition-[width] duration-200",
          collapsed ? "w-[68px]" : "w-[240px]",
        )}
      >
        <SidebarContent collapsed={collapsed} />
      </aside>

      {/* Mobile drawer */}
      {mobileOpen && (
        <div className="fixed inset-0 z-50 md:hidden">
          <div
            className="absolute inset-0 bg-black/60"
            onClick={() => setMobileOpen(false)}
          />
          <aside className="absolute left-0 top-0 h-full w-[260px] bg-sidebar border-r border-sidebar-border">
            <button
              className="absolute right-3 top-3 text-muted-foreground hover:text-foreground"
              onClick={() => setMobileOpen(false)}
              aria-label="Close menu"
            >
              <X className="h-5 w-5" />
            </button>
            <SidebarContent collapsed={false} />
          </aside>
        </div>
      )}

      <div className="flex min-w-0 flex-1 flex-col">
        <header className="sticky top-0 z-30 flex h-14 items-center gap-3 border-b border-border bg-background/80 px-4 backdrop-blur md:px-6">
          <button
            className="md:hidden text-muted-foreground hover:text-foreground"
            onClick={() => setMobileOpen(true)}
            aria-label="Open menu"
          >
            <Menu className="h-5 w-5" />
          </button>
          <button
            className="hidden md:inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground hover:bg-accent hover:text-foreground"
            onClick={() => setCollapsed((v) => !v)}
            aria-label="Toggle sidebar"
          >
            <Menu className="h-4 w-4" />
          </button>

          <div className="relative hidden flex-1 max-w-md md:block">
            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <input
              type="search"
              placeholder="Search products, customers, sales…"
              className="h-9 w-full rounded-md border border-border bg-card pl-9 pr-3 text-sm text-foreground placeholder:text-muted-foreground/70 focus:border-primary/60 focus:outline-none focus:ring-1 focus:ring-primary/40"
            />
          </div>

          <div className="ml-auto flex items-center gap-2">
            <button
              className="relative inline-flex h-9 w-9 items-center justify-center rounded-md text-muted-foreground hover:bg-accent hover:text-foreground"
              aria-label="Notifications"
            >
              <Bell className="h-4 w-4" />
              <span className="absolute right-2 top-2 h-1.5 w-1.5 rounded-full bg-primary" />
            </button>

            <DropdownMenu>
              <DropdownMenuTrigger className="flex items-center gap-2 rounded-md px-2 py-1.5 hover:bg-accent">
                <Avatar className="h-7 w-7">
                  <AvatarFallback className="bg-primary/15 text-primary text-xs font-semibold">
                    {user?.name?.charAt(0).toUpperCase() || "A"}
                  </AvatarFallback>
                </Avatar>
                <div className="hidden text-left leading-tight sm:block">
                  <div className="text-xs font-medium text-foreground">{user?.name || "Admin"}</div>
                  <div className="text-[10px] text-muted-foreground">{user?.email || ""}</div>
                </div>
                <ChevronDown className="hidden h-3.5 w-3.5 text-muted-foreground sm:block" />
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" className="w-56">
                <DropdownMenuLabel>
                  <div className="text-sm font-medium">{user?.name || "Admin"}</div>
                  <div className="text-xs font-normal text-muted-foreground">
                    {user?.email || ""}
                  </div>
                </DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem asChild>
                  <Link to="/profile">
                    <User className="h-4 w-4" /> Profile
                  </Link>
                </DropdownMenuItem>
                <DropdownMenuItem asChild>
                  <Link to="/settings">
                    <Settings className="h-4 w-4" /> Settings
                  </Link>
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem
                  className="text-destructive focus:text-destructive"
                  onClick={handleLogout}
                >
                  <LogOut className="h-4 w-4" /> Logout
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </header>

        <main className="flex-1 px-4 py-6 md:px-8 md:py-8">{children}</main>
      </div>
    </div>
  );
}

export function PageHeader({
  title,
  description,
  actions,
}: {
  title: string;
  description?: string;
  actions?: ReactNode;
}) {
  return (
    <div className="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight text-foreground">{title}</h1>
        {description && (
          <p className="mt-1 text-sm text-muted-foreground">{description}</p>
        )}
      </div>
      {actions && <div className="flex flex-wrap items-center gap-2">{actions}</div>}
    </div>
  );
}