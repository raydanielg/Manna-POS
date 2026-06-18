<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
class ExpenseCategoryController extends Controller {
    public function index(Request $req) {
        $q = ExpenseCategory::withCount("expenses")->forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where("name","like","%{$req->search}%");
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","description"=>"nullable|string"]);
        $data["created_by"] = $this->currentBusinessId();
        return response()->json(["success"=>true,"category"=>ExpenseCategory::create($data)], 201);
    }
    public function show(ExpenseCategory $expenseCategory) { $this->ensureOwns($expenseCategory); return response()->json($expenseCategory); }
    public function update(Request $req, ExpenseCategory $expenseCategory) {
        $this->ensureOwns($expenseCategory);
        $expenseCategory->update($req->validate(["name"=>"required|string|max:191","description"=>"nullable|string"]));
        return response()->json(["success"=>true,"category"=>$expenseCategory]);
    }
    public function destroy(ExpenseCategory $expenseCategory) { $this->ensureOwns($expenseCategory); $expenseCategory->delete(); return response()->json(["success"=>true]); }
}
