<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExpenseApiController extends Controller {
    public function index(Request $req) {
        $q = Expense::with('category:id,name');
        if ($req->search) $q->where('reference','like',"%{$req->search}%")->orWhere('notes','like',"%{$req->search}%");
        if ($req->from) $q->whereDate('expense_date','>=',$req->from);
        if ($req->to) $q->whereDate('expense_date','<=',$req->to);
        return response()->json($q->latest()->take(100)->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['expense_category_id'=>'nullable|exists:expense_categories,id','expense_date'=>'required|date','amount'=>'required|numeric|min:0','payment_method'=>'nullable|string|max:50','notes'=>'nullable|string']);
        $data['reference'] = 'EXP-'.strtoupper(Str::random(6));
        $data['created_by'] = auth()->id();
        return response()->json(Expense::create($data)->load('category:id,name'),201);
    }
    public function show(Expense $expense) {
        return response()->json($expense->load('category:id,name'));
    }
    public function update(Request $req, Expense $expense) {
        $data = $req->validate(['expense_category_id'=>'nullable|exists:expense_categories,id','expense_date'=>'required|date','amount'=>'required|numeric|min:0','payment_method'=>'nullable|string|max:50','notes'=>'nullable|string']);
        $expense->update($data);
        return response()->json($expense->load('category:id,name'));
    }
    public function destroy(Expense $expense) {
        $expense->delete();
        return response()->json(['message'=>'Expense deleted']);
    }
}