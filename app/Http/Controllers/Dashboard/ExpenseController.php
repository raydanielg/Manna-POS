?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class ExpenseController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = Expense::with("category");
            if ($req->search) $q->where("reference","like","%{$req->search}%")->orWhere("notes","like","%{$req->search}%");
            if ($req->category_id) $q->where("expense_category_id",$req->category_id);
            return response()->json($q->latest()->get());
        }
        $categories = ExpenseCategory::all();
        return view("dashboard.expenses.list-expenses", compact("categories"));
    }
    public function store(Request $req) {
        $data = $req->validate(["expense_category_id"=>"nullable|exists:expense_categories,id","expense_date"=>"required|date","amount"=>"required|numeric|min:0","payment_method"=>"in:cash,card,mobile_money,cheque","notes"=>"nullable|string"]);
        $data["reference"] = "EXP-".strtoupper(Str::random(6));
        $data["created_by"] = auth()->id();
        $e = Expense::create($data);
        return response()->json(["success"=>true,"expense"=>$e->load("category")], 201);
    }
    public function show(Expense $expense) { return response()->json($expense); }
    public function update(Request $req, Expense $expense) {
        $data = $req->validate(["expense_category_id"=>"nullable|exists:expense_categories,id","expense_date"=>"required|date","amount"=>"required|numeric|min:0","payment_method"=>"in:cash,card,mobile_money,cheque","notes"=>"nullable|string"]);
        $expense->update($data);
        return response()->json(["success"=>true,"expense"=>$expense->load("category")]);
    }
    public function destroy(Expense $expense) { $expense->delete(); return response()->json(["success"=>true]); }
}
