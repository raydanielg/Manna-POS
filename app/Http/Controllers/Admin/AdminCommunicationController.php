<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\SmsTemplate;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AdminCommunicationController extends Controller
{
    // Email Templates
    public function emailTemplates()
    {
        return view('admin.communication.email-templates');
    }

    public function emailTemplatesList(Request $req)
    {
        $q = EmailTemplate::query();
        if ($req->search) $q->where('name','like',"%{$req->search}%")->orWhere('code','like',"%{$req->search}%");
        if ($req->category) $q->where('category', $req->category);
        return response()->json($q->orderBy('name')->get());
    }

    public function emailTemplatesStore(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:191|unique:email_templates,name',
            'subject' => 'required|string|max:191',
            'code' => 'required|string|max:100|unique:email_templates,code',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'category' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $data['is_active'] ?? true;
        return response()->json(['success'=>true,'template'=>EmailTemplate::create($data)], 201);
    }

    public function emailTemplatesShow(EmailTemplate $template)
    {
        return response()->json($template);
    }

    public function emailTemplatesUpdate(Request $req, EmailTemplate $template)
    {
        $data = $req->validate([
            'name' => "required|string|max:191|unique:email_templates,name,{$template->id}",
            'subject' => 'required|string|max:191',
            'code' => "required|string|max:100|unique:email_templates,code,{$template->id}",
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'category' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);
        $template->update($data);
        return response()->json(['success'=>true,'template'=>$template]);
    }

    public function emailTemplatesDestroy(EmailTemplate $template)
    {
        $template->delete();
        return response()->json(['success'=>true]);
    }

    // SMS Templates
    public function smsTemplates()
    {
        return view('admin.communication.sms-templates');
    }

    public function smsTemplatesList(Request $req)
    {
        $q = SmsTemplate::query();
        if ($req->search) $q->where('name','like',"%{$req->search}%")->orWhere('code','like',"%{$req->search}%");
        return response()->json($q->orderBy('name')->get());
    }

    public function smsTemplatesStore(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:191|unique:sms_templates,name',
            'code' => 'required|string|max:100|unique:sms_templates,code',
            'message' => 'required|string',
            'variables' => 'nullable|array',
            'category' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $data['is_active'] ?? true;
        return response()->json(['success'=>true,'template'=>SmsTemplate::create($data)], 201);
    }

    public function smsTemplatesUpdate(Request $req, SmsTemplate $template)
    {
        $data = $req->validate([
            'name' => "required|string|max:191|unique:sms_templates,name,{$template->id}",
            'code' => "required|string|max:100|unique:sms_templates,code,{$template->id}",
            'message' => 'required|string',
            'variables' => 'nullable|array',
            'category' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);
        $template->update($data);
        return response()->json(['success'=>true,'template'=>$template]);
    }

    public function smsTemplatesDestroy(SmsTemplate $template)
    {
        $template->delete();
        return response()->json(['success'=>true]);
    }

    // Announcements
    public function announcements()
    {
        return view('admin.communication.announcements');
    }

    public function announcementsList(Request $req)
    {
        $q = Announcement::with('creator:id,name');
        if ($req->status) $q->where('status', $req->status);
        return response()->json($q->latest()->get());
    }

    public function announcementsStore(Request $req)
    {
        $data = $req->validate([
            'title' => 'required|string|max:191',
            'content' => 'required|string',
            'type' => 'nullable|string|max:20',
            'status' => 'nullable|string|max:20',
            'scheduled_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
        ]);
        $data['created_by'] = auth()->id();
        $data['type'] = $data['type'] ?? 'info';
        $data['status'] = $data['status'] ?? 'draft';
        return response()->json(['success'=>true,'announcement'=>Announcement::create($data)], 201);
    }

    public function announcementsUpdate(Request $req, Announcement $announcement)
    {
        $data = $req->validate([
            'title' => 'required|string|max:191',
            'content' => 'required|string',
            'type' => 'nullable|string|max:20',
            'status' => 'nullable|string|max:20',
            'scheduled_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
        ]);
        $announcement->update($data);
        return response()->json(['success'=>true,'announcement'=>$announcement]);
    }

    public function announcementsDestroy(Announcement $announcement)
    {
        $announcement->delete();
        return response()->json(['success'=>true]);
    }
}
