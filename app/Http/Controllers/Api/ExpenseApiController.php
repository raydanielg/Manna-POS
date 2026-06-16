<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseApiController extends Controller {
    use UserIdTrait;
    public function index(Request $req) {
        $q = Expense::with('category')->where('created_by', $this->userId());
        if ($req->from) $q->whereDate('expense_date','>=',$req->from);
        if ($req->to) $q->whereDate('expense_date','<=',$req->to);
        if ($req->category_id) $q->where('expense_category_id',$req->category_id);
        return response()->json($q->orderByDesc('created_at')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'category_id' => 'nullable|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'reference' => 'nullable|string|max:191',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        $data['created_by'] = $this->userId();
        if (isset($data['category_id'])) $data['expense_category_id'] = $data['category_id'];
        $expense = Expense::create($data);
        return response()->json($expense->load('category'), 201);
    }
    public function update(Request $req, $id) {
        $e = Expense::where('created_by', $this->userId())->findOrFail($id);
        $data = $req->validate([
            'category_id' => 'nullable|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'reference' => 'nullable|string|max:191',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        if (isset($data['category_id'])) $data['expense_category_id'] = $data['category_id'];
        $e->update($data);
        return response()->json($e->fresh('category'));
    }
    public function destroy($id) {
        Expense::where('created_by', $this->userId())->where('id',$id)->delete();
        return response()->json(['message'=>'Expense deleted']);
    }
}
