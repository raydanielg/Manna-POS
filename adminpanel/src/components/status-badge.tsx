import { cn } from "@/lib/utils";

type Status = "success" | "pending" | "failed" | "refunded" | "active" | "paused" | "completed";

const styles: Record<Status, string> = {
  success: "bg-primary/10 text-primary border-primary/20",
  completed: "bg-primary/10 text-primary border-primary/20",
  active: "bg-primary/10 text-primary border-primary/20",
  pending: "bg-warning/10 text-warning border-warning/20",
  paused: "bg-muted text-muted-foreground border-border",
  failed: "bg-destructive/10 text-destructive border-destructive/30",
  refunded: "bg-muted text-muted-foreground border-border",
};

export function StatusBadge({ status }: { status: Status }) {
  return (
    <span
      className={cn(
        "inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[11px] font-medium capitalize",
        styles[status],
      )}
    >
      <span className={cn("h-1.5 w-1.5 rounded-full", {
        "bg-primary": status === "success" || status === "active",
        "bg-warning": status === "pending",
        "bg-destructive": status === "failed",
        "bg-muted-foreground": status === "refunded" || status === "paused",
      })} />
      {status}
    </span>
  );
}