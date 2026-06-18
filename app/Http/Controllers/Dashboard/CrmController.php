<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrmController extends Controller {
    public function activities() {
        return view('dashboard.crm.activities');
    }
    public function dashboard() {
        return view('dashboard.crm.dashboard');
    }
}
