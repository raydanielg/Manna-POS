<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
class ExpenseCategoryController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = ExpenseCategory::withCount("expenses");
            if ($req->search) $q->where("name","like","%{$req->search}%");
            return response()->json($q->latest()->get());
        }
        return view("dashboard.expenses.expense-categories");
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","description"=>"nullable|string"]);
        return response()->json(["success"=>true,"category"=>ExpenseCategory::create($data)], 201);
    }
    public function show(ExpenseCategory $expenseCategory) { return response()->json($expenseCategory); }
    public function update(Request $req, ExpenseCategory $expenseCategory) {
        $expenseCategory->update($req->validate(["name"=>"required|string|max:191","description"=>"nullable|string"]));
        return response()->json(["success"=>true,"category"=>$expenseCategory]);
    }
    public function destroy(ExpenseCategory $expenseCategory) { $expenseCategory->delete(); return response()->json(["success"=>true]); }
}
