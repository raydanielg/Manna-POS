<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryApiController extends Controller {
    public function index() {
        return response()->json(ExpenseCategory::orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['name'=>'required|string|max:191|unique:expense_categories,name','description'=>'nullable|string']);
        return response()->json(ExpenseCategory::create($data), 201);
    }
    public function show(ExpenseCategory $expenseCategory) {
        return response()->json($expenseCategory);
    }
    public function update(Request $req, ExpenseCategory $expenseCategory) {
        $data = $req->validate(['name'=>'required|string|max:191|unique:expense_categories,name,'.$expenseCategory->id,'description'=>'nullable|string']);
        $expenseCategory->update($data);
        return response()->json($expenseCategory);
    }
    public function destroy(ExpenseCategory $expenseCategory) {
        $expenseCategory->delete();
        return response()->json(['message'=>'Expense category deleted']);
    }
}
