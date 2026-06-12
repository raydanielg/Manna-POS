?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;
class NotificationTemplateController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) return response()->json(NotificationTemplate::all());
        return view("dashboard.notification-templates.index");
    }
    public function store(Request $req) {
        $data = $req->validate(["type"=>"required|string|max:100","subject"=>"required|string|max:191","body"=>"required|string","is_active"=>"boolean"]);
        return response()->json(["success"=>true,"template"=>NotificationTemplate::create($data)], 201);
    }
    public function show(NotificationTemplate $notificationTemplate) { return response()->json($notificationTemplate); }
    public function update(Request $req, NotificationTemplate $notificationTemplate) {
        $notificationTemplate->update($req->validate(["type"=>"required|string|max:100","subject"=>"required|string|max:191","body"=>"required|string","is_active"=>"boolean"]));
        return response()->json(["success"=>true,"template"=>$notificationTemplate]);
    }
    public function destroy(NotificationTemplate $notificationTemplate) { $notificationTemplate->delete(); return response()->json(["success"=>true]); }
}
