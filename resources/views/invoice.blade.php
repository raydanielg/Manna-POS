@php
$company = $company ?? [
    'name' => auth()->user()->business_name ?? 'Manna Company LTD',
    'address' => auth()->user()->business_address ?? 'Mwanza, Tanzania',
    'phone' => auth()->user()->phone ?? '0740000000',
    'email' => auth()->user()->email ?? 'info@manna.co.tz',
    'tin' => auth()->user()->tax_number ?? '',
    'logo' => asset('logo.png'),
];
$customer = $customer ?? [
    'name' => 'Walk-in Customer',
    'phone' => '',
    'email' => '',
    'address' => '',
];
$items = $items ?? [];
$subtotal = $subtotal ?? 0;
$discount = $discount ?? 0;
$tax = $tax ?? 0;
$taxRate = $taxRate ?? 18;
$total = $total ?? 0;
$paid = $paid ?? 0;
$balance = $total - $paid;
$reference = $reference ?? 'INV-001';
$saleDate = $saleDate ?? now()->format('d/m/Y');
$status = $status ?? 'PAID';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Invoice {{ $reference }}</title>
<style>
@page { size: A4; margin: 12mm; }
* { box-sizing: border-box; }
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f1f4fb;
    color: #1e293b;
    margin: 0;
    padding: 20px;
}
.invoice {
    width: 210mm;
    min-height: 277mm;
    background: #fff;
    margin: 0 auto;
    padding: 25mm 20mm;
    box-shadow: 0 4px 24px rgba(0,0,0,.08);
    border-radius: 8px;
}

/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 3px solid #2563eb;
    padding-bottom: 18px;
    margin-bottom: 20px;
}
.logo img { width: 90px; height: auto; }
.company h2 { margin: 0 0 6px; font-size: 1.35rem; color: #0f172a; }
.company p { margin: 0; font-size: .78rem; color: #64748b; line-height: 1.6; }
.invoice-info { text-align: right; }
.invoice-info h1 { margin: 0 0 8px; font-size: 1.8rem; color: #2563eb; letter-spacing: .08em; text-transform: uppercase; }
.invoice-info p { margin: 0; font-size: .82rem; color: #64748b; line-height: 1.7; }
.badge-status {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    margin-top: 6px;
}
.badge-paid { background: #dcfce7; color: #166534; }
.badge-partial { background: #fef9c3; color: #854d0e; }
.badge-unpaid { background: #fee2e2; color: #991b1b; }

/* Section boxes */
.section { display: flex; gap: 16px; margin-bottom: 20px; }
.box { flex: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; background: #f8fafc; }
.box h4 { margin: 0 0 8px; font-size: .8rem; text-transform: uppercase; letter-spacing: .06em; color: #475569; }
.box p { margin: 0; font-size: .82rem; color: #334155; line-height: 1.7; }

/* Table */
table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: .82rem; }
th { background: #eff6ff; color: #1e40af; font-weight: 700; text-transform: uppercase; font-size: .72rem; letter-spacing: .04em; padding: 10px 8px; border-bottom: 2px solid #bfdbfe; text-align: left; }
td { padding: 10px 8px; border-bottom: 1px solid #e2e8f0; color: #334155; }
tr:last-child td { border-bottom: none; }
.text-right { text-align: right; }
.text-center { text-align: center; }

/* Summary */
.summary { width: 320px; margin-left: auto; margin-top: 20px; }
.summary-row { display: flex; justify-content: space-between; padding: 7px 4px; font-size: .85rem; color: #475569; }
.summary-row.total { font-size: 1.1rem; font-weight: 800; color: #0f172a; border-top: 2px solid #0f172a; padding-top: 10px; margin-top: 6px; }

/* Footer */
.footer { margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 20px; }
.terms { font-size: .75rem; color: #94a3b8; margin-bottom: 20px; }
.signatures { display: flex; justify-content: space-between; margin-top: 50px; }
.sig-box { width: 42%; text-align: center; }
.sig-label { font-size: .78rem; font-weight: 600; color: #475569; margin-bottom: 6px; }
.sig-line { border-bottom: 1px solid #64748b; height: 40px; margin-bottom: 4px; }
.sig-date { font-size: .72rem; color: #94a3b8; }

/* Actions */
.actions {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    background: #fff;
    border-top: 1px solid #e2e8f0;
    padding: 12px;
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
    box-shadow: 0 -4px 16px rgba(0,0,0,.06);
    z-index: 1000;
}
.btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 18px; border-radius: 8px;
    font-size: .82rem; font-weight: 700; cursor: pointer;
    border: none; text-decoration: none; transition: all .15s;
}
.btn-print { background: #1e293b; color: #fff; }
.btn-print:hover { background: #0f172a; }
.btn-pdf { background: #dc2626; color: #fff; }
.btn-pdf:hover { background: #b91c1c; }
.btn-whatsapp { background: #22c55e; color: #fff; }
.btn-whatsapp:hover { background: #16a34a; }
.btn-email { background: #2563eb; color: #fff; }
.btn-email:hover { background: #1d4ed8; }

@media print {
    body { background: #fff; padding: 0; }
    .invoice { width: 100%; box-shadow: none; border-radius: 0; margin: 0; padding: 15mm 12mm; }
    .actions { display: none !important; }
}
</style>
</head>
<body>

<div class="invoice" id="invoice">

  <!-- Header -->
  <div class="header">
    <div class="company">
      <div class="logo">
        <img src="{{ $company['logo'] }}" alt="Logo" onerror="this.style.display='none'">
      </div>
      <h2>{{ $company['name'] }}</h2>
      <p>
        {{ $company['address'] }}<br>
        Phone: {{ $company['phone'] }}<br>
        Email: {{ $company['email'] }}
        @if($company['tin'])<br>TIN: {{ $company['tin'] }}@endif
      </p>
    </div>
    <div class="invoice-info">
      <h1>INVOICE</h1>
      <p>
        <strong>Invoice No:</strong> {{ $reference }}<br>
        <strong>Date:</strong> {{ $saleDate }}<br>
        <strong>Payment:</strong> {{ $status }}
      </p>
      <span class="badge-status badge-{{ strtolower($status) === 'paid' ? 'paid' : (strtolower($status) === 'partial' ? 'partial' : 'unpaid') }}">
        {{ strtoupper($status) }}
      </span>
    </div>
  </div>

  <!-- Details -->
  <div class="section">
    <div class="box">
      <h4>Bill To (Customer)</h4>
      <p>
        <strong>{{ $customer['name'] }}</strong><br>
        @if($customer['phone'])Phone: {{ $customer['phone'] }}<br>@endif
        @if($customer['email'])Email: {{ $customer['email'] }}<br>@endif
        @if($customer['address'])Address: {{ $customer['address'] }}@endif
      </p>
    </div>
    <div class="box">
      <h4>Company Details</h4>
      <p>
        <strong>{{ $company['name'] }}</strong><br>
        @if($company['tin'])TIN: {{ $company['tin'] }}<br>@endif
        Phone: {{ $company['phone'] }}<br>
        Email: {{ $company['email'] }}<br>
        {{ $company['address'] }}
      </p>
    </div>
  </div>

  <!-- Items Table -->
  <table>
    <thead>
      <tr>
        <th class="text-center">#</th>
        <th>Item</th>
        <th>Description</th>
        <th class="text-center">Qty</th>
        <th class="text-right">Unit Price</th>
        <th class="text-right">Disc</th>
        <th class="text-right">VAT</th>
        <th class="text-right">Total</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $i => $item)
      <tr>
        <td class="text-center">{{ $i + 1 }}</td>
        <td>{{ $item['product_name'] ?? $item['name'] ?? '-' }}</td>
        <td>{{ $item['description'] ?? '-' }}</td>
        <td class="text-center">{{ number_format($item['quantity'] ?? 0, 2) }}</td>
        <td class="text-right">{{ number_format($item['unit_price'] ?? 0, 2) }}</td>
        <td class="text-right">{{ number_format($item['discount'] ?? 0, 2) }}</td>
        <td class="text-right">
          @php
            $itemTotal = ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) - ($item['discount'] ?? 0);
            $itemTax = $taxRate > 0 ? ($itemTotal * $taxRate / 100) : 0;
          @endphp
          {{ number_format($itemTax, 2) }}
        </td>
        <td class="text-right">{{ number_format($item['total'] ?? $itemTotal, 2) }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="8" class="text-center" style="padding:20px;color:#94a3b8;">No items</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <!-- Summary -->
  <div class="summary">
    <div class="summary-row">
      <span>Subtotal</span>
      <span>{{ number_format($subtotal, 2) }}</span>
    </div>
    <div class="summary-row">
      <span>Discount</span>
      <span>{{ number_format($discount, 2) }}</span>
    </div>
    <div class="summary-row">
      <span>VAT ({{ $taxRate }}%)</span>
      <span>{{ number_format($tax, 2) }}</span>
    </div>
    <div class="summary-row total">
      <span>Total</span>
      <span>{{ number_format($total, 2) }}</span>
    </div>
    <div class="summary-row">
      <span>Paid</span>
      <span>{{ number_format($paid, 2) }}</span>
    </div>
    <div class="summary-row" style="color:#dc2626;font-weight:700;">
      <span>Balance Due</span>
      <span>{{ number_format(max(0, $balance), 2) }}</span>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer">
    <div class="terms">
      <strong>Terms & Conditions:</strong><br>
      Goods once sold are not returnable. Payment is due within 30 days. Thank you for your business!
    </div>
    <div class="signatures">
      <div class="sig-box">
        <div class="sig-label">Customer Signature</div>
        <div class="sig-line"></div>
        <div class="sig-date">Date: _______________</div>
      </div>
      <div class="sig-box">
        <div class="sig-label">Authorized Signature</div>
        <div class="sig-line"></div>
        <div class="sig-date">Date: _______________</div>
      </div>
    </div>
  </div>

</div>

<!-- Floating Actions -->
<div class="actions">
  <button class="btn btn-print" onclick="window.print()">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
    Print
  </button>
  <a class="btn btn-pdf" href="{{ route('invoice.pdf', ['ref' => $reference]) }}" target="_blank">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Download PDF
  </a>
  <a class="btn btn-whatsapp" href="https://wa.me/?text={{ urlencode(route('invoice.pdf', ['ref' => $reference]) . ' - Invoice ' . $reference) }}" target="_blank">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    Share WhatsApp
  </a>
  <a class="btn btn-email" href="mailto:?subject=Invoice {{ $reference }}&body=Please find your invoice here: {{ urlencode(route('invoice.pdf', ['ref' => $reference])) }}" target="_blank">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
    Share Email
  </a>
</div>

</body>
</html>
