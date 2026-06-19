<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function show(Request $request, $ref = null)
    {
        // If ref provided, load from database
        if ($ref) {
            $sale = Sale::with(['customer', 'items.product'])
                ->where('reference', $ref)
                ->where('created_by', auth()->user()->currentBusinessId() ?? auth()->id())
                ->first();

            if (!$sale) {
                abort(404, 'Invoice not found');
            }

            $user = auth()->user();
            $company = [
                'name' => $user->business_name ?? 'Manna Company LTD',
                'address' => $user->business_address ?? 'Mwanza, Tanzania',
                'phone' => $user->phone ?? '0740000000',
                'email' => $user->email ?? 'info@manna.co.tz',
                'tin' => $user->tax_number ?? '',
                'logo' => asset('logo.png'),
            ];

            $customer = [
                'name' => $sale->customer?->name ?? 'Walk-in Customer',
                'phone' => $sale->customer?->phone ?? '',
                'email' => $sale->customer?->email ?? '',
                'address' => $sale->customer?->address ?? '',
            ];

            $items = $sale->items->map(fn($item) => [
                'product_name' => $item->product_name ?? ($item->product?->name ?? 'Unknown'),
                'description' => $item->product?->description ?? '',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount ?? 0,
                'total' => $item->total ?? (($item->quantity * $item->unit_price) - ($item->discount ?? 0)),
            ])->toArray();

            $taxRate = floatval($user->tax_percentage ?? 18);
            $subtotal = floatval($sale->subtotal ?? 0);
            $discount = floatval($sale->discount ?? 0);
            $tax = floatval($sale->tax ?? 0);
            $total = floatval($sale->total ?? 0);
            $paid = floatval($sale->paid ?? 0);
            $reference = $sale->reference;
            $saleDate = $sale->sale_date ? $sale->sale_date->format('d/m/Y') : now()->format('d/m/Y');
            $status = $sale->payment_status ?? 'unpaid';
        } else {
            // Demo / default
            $company = [
                'name' => auth()->user()->business_name ?? 'Manna Company LTD',
                'address' => auth()->user()->business_address ?? 'Mwanza, Tanzania',
                'phone' => auth()->user()->phone ?? '0740000000',
                'email' => auth()->user()->email ?? 'info@manna.co.tz',
                'tin' => auth()->user()->tax_number ?? '',
                'logo' => asset('logo.png'),
            ];
            $customer = [
                'name' => 'Walk-in Customer',
                'phone' => '',
                'email' => '',
                'address' => '',
            ];
            $items = [];
            $subtotal = 0;
            $discount = 0;
            $tax = 0;
            $taxRate = floatval(auth()->user()->tax_percentage ?? 18);
            $total = 0;
            $paid = 0;
            $reference = 'INV-001';
            $saleDate = now()->format('d/m/Y');
            $status = 'paid';
        }

        return view('invoice', compact(
            'company', 'customer', 'items', 'subtotal', 'discount', 'tax', 'taxRate',
            'total', 'paid', 'reference', 'saleDate', 'status'
        ));
    }

    public function pdf(Request $request, $ref = null)
    {
        // Check if dompdf is available
        if (!class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            return response()->json(['error' => 'PDF library not installed. Run: composer require barryvdh/laravel-dompdf'], 500);
        }

        $viewData = $this->getInvoiceData($ref);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice', $viewData);
        return $pdf->download('invoice-' . ($ref ?? '001') . '.pdf');
    }

    private function getInvoiceData($ref = null)
    {
        if ($ref) {
            $sale = Sale::with(['customer', 'items.product'])
                ->where('reference', $ref)
                ->where('created_by', auth()->user()->currentBusinessId() ?? auth()->id())
                ->first();

            if (!$sale) {
                abort(404, 'Invoice not found');
            }

            $user = auth()->user();
            return [
                'company' => [
                    'name' => $user->business_name ?? 'Manna Company LTD',
                    'address' => $user->business_address ?? 'Mwanza, Tanzania',
                    'phone' => $user->phone ?? '0740000000',
                    'email' => $user->email ?? 'info@manna.co.tz',
                    'tin' => $user->tax_number ?? '',
                    'logo' => public_path('logo.png'),
                ],
                'customer' => [
                    'name' => $sale->customer?->name ?? 'Walk-in Customer',
                    'phone' => $sale->customer?->phone ?? '',
                    'email' => $sale->customer?->email ?? '',
                    'address' => $sale->customer?->address ?? '',
                ],
                'items' => $sale->items->map(fn($item) => [
                    'product_name' => $item->product_name ?? ($item->product?->name ?? 'Unknown'),
                    'description' => $item->product?->description ?? '',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount ?? 0,
                    'total' => $item->total ?? (($item->quantity * $item->unit_price) - ($item->discount ?? 0)),
                ])->toArray(),
                'subtotal' => floatval($sale->subtotal ?? 0),
                'discount' => floatval($sale->discount ?? 0),
                'tax' => floatval($sale->tax ?? 0),
                'taxRate' => floatval($user->tax_percentage ?? 18),
                'total' => floatval($sale->total ?? 0),
                'paid' => floatval($sale->paid ?? 0),
                'reference' => $sale->reference,
                'saleDate' => $sale->sale_date ? $sale->sale_date->format('d/m/Y') : now()->format('d/m/Y'),
                'status' => $sale->payment_status ?? 'unpaid',
            ];
        }

        return [
            'company' => [
                'name' => 'Manna Company LTD',
                'address' => 'Mwanza, Tanzania',
                'phone' => '0740000000',
                'email' => 'info@manna.co.tz',
                'tin' => '',
                'logo' => public_path('logo.png'),
            ],
            'customer' => ['name'=>'Walk-in Customer','phone'=>'','email'=>'','address'=>''],
            'items' => [],
            'subtotal' => 0, 'discount' => 0, 'tax' => 0, 'taxRate' => 18,
            'total' => 0, 'paid' => 0,
            'reference' => 'INV-001',
            'saleDate' => now()->format('d/m/Y'),
            'status' => 'paid',
        ];
    }
}
