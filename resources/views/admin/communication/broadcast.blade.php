@extends('admin.layouts.app')
@section('page_title', 'Send Broadcast')
@section('content')
<div class="page-card" style="max-width:800px;">
    <div class="card-header">
        <div class="card-title">Send Broadcast Message</div>
    </div>
    <div style="padding:1.25rem;">
        <form id="broadcastForm">
            <div class="form-row">
                <div class="form-group">
                    <label>Channel *</label>
                    <select class="form-control" id="channel" required>
                        <option value="email">Email</option>
                        <option value="sms">SMS</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Target Audience *</label>
                    <select class="form-control" id="target" required>
                        <option value="all">All Users</option>
                        <option value="users">Users Only</option>
                        <option value="admins">Admins Only</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Subject *</label>
                <input type="text" class="form-control" id="subject" required placeholder="Broadcast subject">
            </div>
            <div class="form-group">
                <label>Message *</label>
                <textarea class="form-control" id="message" rows="6" required placeholder="Write your broadcast message..." oninput="updatePreview()"></textarea>
            </div>
            <div class="form-group">
                <label>Schedule (optional)</label>
                <input type="datetime-local" class="form-control" id="scheduled_at">
            </div>
        </form>

        <div class="page-card" style="margin-top:1rem;background:#fafbff;">
            <div class="card-header">
                <div class="card-title">Preview</div>
            </div>
            <div style="padding:1rem;font-size:0.85rem;color:#374151;line-height:1.6;min-height:60px;white-space:pre-wrap;" id="previewContent">Your message will appear here...</div>
        </div>

        <div style="display:flex;gap:0.75rem;margin-top:1.25rem;justify-content:flex-end;">
            <button class="btn btn-secondary" onclick="resetForm()">Reset</button>
            <button class="btn btn-primary" onclick="confirmBroadcast()">Send Broadcast</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/communication/broadcast';

function updatePreview() {
    const msg = document.getElementById('message').value.trim();
    document.getElementById('previewContent').textContent = msg || 'Your message will appear here...';
}

function resetForm() {
    document.getElementById('broadcastForm').reset();
    updatePreview();
}

function confirmBroadcast() {
    const channel = document.getElementById('channel').value;
    const target = document.getElementById('target').value;
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();
    const scheduled_at = document.getElementById('scheduled_at').value;

    if (!subject || !message) { Swal.fire({ icon: 'error', title: 'Required', text: 'Subject and message are required' }); return; }

    let confirmText = `Send "${subject}" via ${channel} to ${target}`;
    if (scheduled_at) confirmText += ` (scheduled: ${new Date(scheduled_at).toLocaleString()})`;

    Swal.fire({
        title: 'Send Broadcast?',
        text: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#e03057',
        confirmButtonText: 'Send'
    }).then(async (r) => {
        if (!r.isConfirmed) return;
        try {
            await apiFetch(API, { method: 'POST', body: { channel, target, subject, message, scheduled_at } });
            Swal.fire({ icon: 'success', title: 'Broadcast sent!', timer: 2000, showConfirmButton: false });
            resetForm();
        } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
    });
}

updatePreview();
</script>
@endsection
