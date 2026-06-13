export type TxStatus = "success" | "pending" | "failed" | "refunded";

export const transactions: {
  id: string;
  customer: string;
  email: string;
  amount: number;
  method: "M-Pesa" | "Tigo Pesa" | "Airtel Money" | "Card" | "Bank";
  status: TxStatus;
  date: string;
}[] = [
  { id: "TX-10293", customer: "Amani Mushi", email: "amani@gmail.com", amount: 248500, method: "M-Pesa", status: "success", date: "2026-06-12 14:21" },
  { id: "TX-10292", customer: "Grace Mwakatobe", email: "grace.m@outlook.com", amount: 76000, method: "Tigo Pesa", status: "success", date: "2026-06-12 13:58" },
  { id: "TX-10291", customer: "Innocent Lyimo", email: "i.lyimo@yahoo.com", amount: 1200000, method: "Card", status: "pending", date: "2026-06-12 13:31" },
  { id: "TX-10290", customer: "Neema Kazoba", email: "neema@salamapay.com", amount: 45000, method: "Airtel Money", status: "success", date: "2026-06-12 12:09" },
  { id: "TX-10289", customer: "John Mahenge", email: "john.m@gmail.com", amount: 320000, method: "Bank", status: "failed", date: "2026-06-12 11:42" },
  { id: "TX-10288", customer: "Zainab Hassan", email: "zainab@gmail.com", amount: 89500, method: "M-Pesa", status: "success", date: "2026-06-12 10:14" },
  { id: "TX-10287", customer: "Peter Sanga", email: "p.sanga@gmail.com", amount: 150000, method: "Card", status: "refunded", date: "2026-06-11 18:55" },
  { id: "TX-10286", customer: "Lucy Mwakyusa", email: "lucy@outlook.com", amount: 52000, method: "Tigo Pesa", status: "success", date: "2026-06-11 17:21" },
];

export const revenueSeries = [
  { day: "Mon", revenue: 1240000, volume: 184 },
  { day: "Tue", revenue: 1580000, volume: 221 },
  { day: "Wed", revenue: 1320000, volume: 198 },
  { day: "Thu", revenue: 1890000, volume: 256 },
  { day: "Fri", revenue: 2240000, volume: 312 },
  { day: "Sat", revenue: 1740000, volume: 245 },
  { day: "Sun", revenue: 2110000, volume: 289 },
];

export const methodDistribution = [
  { name: "M-Pesa", value: 42 },
  { name: "Tigo Pesa", value: 21 },
  { name: "Airtel Money", value: 14 },
  { name: "Card", value: 17 },
  { name: "Bank", value: 6 },
];

export const paymentLinks = [
  { id: "12345", title: "Premium Subscription", amount: 49000, currency: "TZS", url: "salamapay.com/pay/12345", clicks: 312, status: "active" as const },
  { id: "12346", title: "Workshop Ticket", amount: 25000, currency: "TZS", url: "salamapay.com/pay/12346", clicks: 89, status: "active" as const },
  { id: "12347", title: "Donation – Mtoto Hope", amount: 0, currency: "TZS", url: "salamapay.com/pay/12347", clicks: 1042, status: "active" as const },
  { id: "12348", title: "Invoice #2026-014", amount: 1800000, currency: "TZS", url: "salamapay.com/pay/12348", clicks: 4, status: "paused" as const },
];

export function formatTZS(n: number) {
  return new Intl.NumberFormat("en-TZ", { style: "currency", currency: "TZS", maximumFractionDigits: 0 }).format(n);
}