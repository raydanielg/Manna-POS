<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;
class NotificationTemplateController extends Controller {
    public function index(Request $req) {
        $q = NotificationTemplate::forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where(function($sq) use($req){ $sq->where("subject","like","%{$req->search}%")->orWhere("type","like","%{$req->search}%"); });
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(["type"=>"required|string|max:100","subject"=>"required|string|max:191","body"=>"required|string","is_active"=>"boolean"]);
        $data["created_by"] = $this->currentBusinessId();
        return response()->json(["success"=>true,"template"=>NotificationTemplate::create($data)], 201);
    }
    public function show(NotificationTemplate $notificationTemplate) { $this->ensureOwns($notificationTemplate); return response()->json($notificationTemplate); }
    public function update(Request $req, NotificationTemplate $notificationTemplate) {
        $this->ensureOwns($notificationTemplate);
        $notificationTemplate->update($req->validate(["type"=>"required|string|max:100","subject"=>"required|string|max:191","body"=>"required|string","is_active"=>"boolean"]));
        return response()->json(["success"=>true,"template"=>$notificationTemplate]);
    }
    public function destroy(NotificationTemplate $notificationTemplate) { $this->ensureOwns($notificationTemplate); $notificationTemplate->delete(); return response()->json(["success"=>true]); }
}
