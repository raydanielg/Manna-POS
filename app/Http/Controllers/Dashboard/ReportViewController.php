<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportViewController extends Controller {
    public function purchaseReport() {
        return view('dashboard.reports.purchase-sale');
    }
    public function suppliersReport() {
        return view('dashboard.reports.suppliers');
    }
    public function supplierPriceComparison() {
        return view('dashboard.reports.supplier-price-comparison');
    }
    public function expiryReport() {
        return view('dashboard.reports.expiry');
    }
    public function productTrendsReport() {
        return view('dashboard.reports.product-trends');
    }
}
