@extends('layouts.dashboard')
@section('page_title', 'My Feedback')

@section('content')
<div class="dash-content animate__animated animate__fadeInUp">

    {{-- Header with animated stats --}}
    <div class="kpi-grid mb-5" style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));" data-aos="fade-up">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#eff6ff;">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
            </div>
            <div>
                <div class="kpi-val" id="stat-total">{{ $feedbacks->total() }}</div>
                <div class="kpi-label">Total Messages</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#fef9c3;">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="kpi-val" id="stat-open">{{ $feedbacks->whereIn('status',['open','in_progress'])->count() }}</div>
                <div class="kpi-label">Open / Pending</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#dcfce7;">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="kpi-val" id="stat-resolved">{{ $feedbacks->whereIn('status',['resolved','closed'])->count() }}</div>
                <div class="kpi-label">Resolved</div>
            </div>
        </div>
    </div>

    <div class="dash-section" id="my-feedback-section" data-aos="fade-up" data-aos-delay="100">
        <div class="dash-section-header">
            <div class="dash-section-title flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                My Feedback & Complaints
            </div>
            <button onclick="openFeedbackModal()" class="btn btn-primary flex items-center gap-1.5" style="padding:0.4rem 1rem;font-size:0.8rem;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Message
            </button>
        </div>
        <div class="dash-section-content">
            @if($feedbacks->isEmpty())
                <div class="empty-state" data-aos="zoom-in" data-aos-delay="200">
                    <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <div class="empty-title">No messages yet</div>
                    <div class="empty-desc">You have not submitted any feedback or complaints. Click "New Message" to get started.</div>
                    <button onclick="openFeedbackModal()" class="btn btn-primary mt-3" style="padding:0.4rem 1.2rem;font-size:0.8rem;">Submit First Message</button>
                </div>
            @else
                <div class="space-y-3" id="feedback-list">
                    @foreach($feedbacks as $fb)
                    <a href="{{ route('dashboard.feedback.show', $fb) }}" class="flex items-start gap-4 p-4 rounded-xl border border-slate-100 bg-white hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all duration-300 group no-underline" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 80 }}">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-md
                            @if($fb->type=='complaint') bg-gradient-to-br from-red-500 to-red-600
                            @elseif($fb->type=='bug_report') bg-gradient-to-br from-orange-400 to-orange-500
                            @elseif($fb->type=='feature_request') bg-gradient-to-br from-violet-500 to-violet-600
                            @elseif($fb->type=='feedback') bg-gradient-to-br from-emerald-400 to-emerald-500
                            @else bg-gradient-to-br from-slate-400 to-slate-500 @endif">
                            {{ strtoupper(substr($fb->type,0,1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-sm font-semibold text-slate-800 truncate">{{ $fb->subject }}</span>
                                @if($fb->status == 'open')
                                    <span class="badge badge-pending">Open</span>
                                @elseif($fb->status == 'in_progress')
                                    <span class="badge badge-info">In Progress</span>
                                @elseif($fb->status == 'resolved')
                                    <span class="badge badge-success">Resolved</span>
                                @else
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">Closed</span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-400 mb-1">{{ ucfirst(str_replace('_',' ',$fb->type)) }} &middot; {{ $fb->created_at->format('M d, Y g:i A') }}</div>
                            <p class="text-sm text-slate-500 line-clamp-2">{{ $fb->message }}</p>
                        </div>
                        <div class="hidden sm:flex flex-col items-end gap-1">
                            <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 group-hover:bg-blue-50 group-hover:text-blue-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </div>
                            @if($fb->replies->where('sender_type','admin')->count() > 0)
                                <span class="text-[0.6rem] font-bold px-1.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600">{{ $fb->replies->where('sender_type','admin')->count() }} reply</span>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="mt-4">{{ $feedbacks->links() }}</div>
            @endif
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
function openFeedbackModal() {
    Swal.fire({
        title: '<span style="font-size:1.1rem;font-weight:700;color:#0f172a;">Submit Feedback / Complaint</span>',
        html: `
            <form id="swal-feedback-form" class="text-left space-y-3">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Name</label>
                        <input type="text" name="name" value="{{ Auth::user()->name ?? '' }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                        <input type="email" name="email" value="{{ Auth::user()->email ?? '' }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Subject</label>
                    <input type="text" name="subject" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Type</label>
                    <select name="type" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition bg-white">
                        <option value="feedback">Feedback</option>
                        <option value="complaint">Complaint</option>
                        <option value="feature_request">Feature Request</option>
                        <option value="bug_report">Bug Report</option>
                        <option value="general">General</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Message</label>
                    <textarea name="message" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition resize-none" required></textarea>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Submit',
        confirmButtonColor: '#2563eb',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#94a3b8',
        width: 520,
        customClass: { popup: 'rounded-2xl', confirmButton: 'px-5 py-2 text-sm font-semibold rounded-lg' },
        preConfirm: () => {
            const form = document.getElementById('swal-feedback-form');
            const data = new FormData(form);
            const obj = {};
            data.forEach((v,k)=>obj[k]=v);
            if (!obj.subject || !obj.message || obj.message.length < 10) {
                Swal.showValidationMessage('Please fill subject and message (min 10 chars)');
                return false;
            }
            return obj;
        }
    }).then(async (result) => {
        if (!result.isConfirmed) return;
        Swal.fire({ title: 'Sending...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        try {
            const res = await fetch('{{ route('dashboard.feedback.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(result.value)
            });
            const json = await res.json();
            if (json.success) {
                Swal.fire({ icon: 'success', title: 'Sent!', text: json.message, timer: 2000, showConfirmButton: false });
                setTimeout(() => window.location.reload(), 1200);
            } else {
                throw new Error(json.message || 'Failed');
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: e.message || 'Something went wrong. Please try again.' });
        }
    });
}

// Flash success toast
@if(session('success'))
    Swal.fire({ icon: 'success', title: '{{ session('success') }}', timer: 2500, showConfirmButton: false, toast: true, position: 'top-end' });
@endif
</script>
@endsection
