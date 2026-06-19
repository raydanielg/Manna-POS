@extends('layouts.dashboard')
@section('page_title', 'Feedback: ' . $feedback->subject)

@section('content')
<div class="dash-content animate__animated animate__fadeInUp">

    {{-- Top action bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5" data-aos="fade-down">
        <a href="{{ route('dashboard.feedback.admin.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to All Messages
        </a>
        <div class="flex items-center gap-2">
            <form action="{{ route('dashboard.feedback.admin.priority', $feedback) }}" method="POST" class="inline" id="priority-form">
                @csrf @method('PATCH')
                <select name="priority" onchange="submitAdminInline(this, 'priority-form', 'Priority updated')" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold focus:border-blue-500 outline-none bg-white cursor-pointer">
                    <option value="low" {{ $feedback->priority=='low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ $feedback->priority=='medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ $feedback->priority=='high' ? 'selected' : '' }}>High</option>
                </select>
            </form>
            <form action="{{ route('dashboard.feedback.admin.status', $feedback) }}" method="POST" class="inline" id="status-form">
                @csrf @method('PATCH')
                <select name="status" onchange="submitAdminInline(this, 'status-form', 'Status updated')" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold focus:border-blue-500 outline-none bg-white cursor-pointer">
                    <option value="open" {{ $feedback->status=='open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ $feedback->status=='in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ $feedback->status=='resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ $feedback->status=='closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </form>
        </div>
    </div>

    {{-- Ticket info --}}
    <div class="dash-section mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="dash-section-content">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wide
                            @if($feedback->type=='complaint') bg-red-50 text-red-600
                            @elseif($feedback->type=='bug_report') bg-orange-50 text-orange-600
                            @elseif($feedback->type=='feature_request') bg-violet-50 text-violet-600
                            @elseif($feedback->type=='feedback') bg-emerald-50 text-emerald-600
                            @else bg-slate-50 text-slate-600 @endif">
                            {{ ucfirst(str_replace('_',' ',$feedback->type)) }}
                        </span>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase
                            @if($feedback->priority=='high') bg-red-100 text-red-600
                            @elseif($feedback->priority=='medium') bg-yellow-100 text-yellow-600
                            @else bg-green-100 text-green-600 @endif">
                            {{ $feedback->priority }}
                        </span>
                    </div>
                    <h1 class="text-lg font-bold text-slate-900 mb-1">{{ $feedback->subject }}</h1>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $feedback->message }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4 space-y-3 border border-slate-100">
                    <div>
                        <div class="text-[0.65rem] font-bold uppercase tracking-wide text-slate-400 mb-0.5">From</div>
                        <div class="text-sm font-semibold text-slate-800">{{ $feedback->name }}</div>
                        <div class="text-xs text-slate-500">{{ $feedback->email }}</div>
                    </div>
                    <div>
                        <div class="text-[0.65rem] font-bold uppercase tracking-wide text-slate-400 mb-0.5">Submitted</div>
                        <div class="text-sm text-slate-700">{{ $feedback->created_at->format('M d, Y g:i A') }}</div>
                    </div>
                    @if($feedback->responded_at)
                    <div>
                        <div class="text-[0.65rem] font-bold uppercase tracking-wide text-slate-400 mb-0.5">Last Response</div>
                        <div class="text-sm text-slate-700">{{ $feedback->responded_at->format('M d, Y g:i A') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Chat thread --}}
    <div class="dash-section" data-aos="fade-up" data-aos-delay="200">
        <div class="dash-section-header">
            <div class="dash-section-title flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Conversation
            </div>
        </div>
        <div class="dash-section-content">
            <div class="space-y-5 max-h-[520px] overflow-y-auto pr-1" id="chat-scroll">

                {{-- Original message --}}
                <div class="flex gap-3" data-aos="fade-right" data-aos-delay="250">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-violet-500 flex items-center justify-center flex-shrink-0 text-xs font-bold text-white shadow-md">
                        {{ strtoupper(substr($feedback->name,0,1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-baseline gap-2 mb-1">
                            <span class="text-sm font-semibold text-slate-800">{{ $feedback->name }}</span>
                            <span class="text-[0.65rem] text-slate-400">{{ $feedback->created_at->format('g:i A, M d') }}</span>
                        </div>
                        <div class="bg-slate-50 rounded-xl rounded-tl-none px-4 py-3 text-sm text-slate-700 leading-relaxed border border-slate-100">
                            {{ $feedback->message }}
                        </div>
                    </div>
                </div>

                {{-- Replies --}}
                @foreach($feedback->replies as $reply)
                <div class="flex gap-3 @if($reply->sender_type == 'admin') flex-row-reverse @endif" data-aos="@if($reply->sender_type == 'admin') fade-left @else fade-right @endif" data-aos-delay="{{ 300 + $loop->iteration * 100 }}">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold text-white shadow-md
                        @if($reply->sender_type == 'admin') bg-gradient-to-br from-emerald-400 to-emerald-500 @else bg-gradient-to-br from-blue-500 to-violet-500 @endif">
                        @if($reply->sender_type == 'admin')
                            {{ strtoupper(substr($reply->user->name ?? 'A',0,1)) }}
                        @else
                            {{ strtoupper(substr($reply->user->name ?? $feedback->name,0,1)) }}
                        @endif
                    </div>
                    <div class="flex-1 @if($reply->sender_type == 'admin') text-right @endif">
                        <div class="flex items-baseline gap-2 mb-1 @if($reply->sender_type == 'admin') justify-end @endif">
                            @if($reply->sender_type == 'admin')
                                <span class="text-[0.65rem] text-slate-400">{{ $reply->created_at->format('g:i A, M d') }}</span>
                                <span class="text-sm font-semibold text-slate-800">{{ $reply->user->name ?? 'Support' }}</span>
                            @else
                                <span class="text-sm font-semibold text-slate-800">{{ $reply->user->name ?? $feedback->name }}</span>
                                <span class="text-[0.65rem] text-slate-400">{{ $reply->created_at->format('g:i A, M d') }}</span>
                            @endif
                        </div>
                        <div class="@if($reply->sender_type == 'admin') bg-emerald-50 text-emerald-900 rounded-tr-none border-emerald-100 @else bg-slate-50 text-slate-700 rounded-tl-none border-slate-100 @endif rounded-xl px-4 py-3 text-sm leading-relaxed inline-block text-left max-w-full border">
                            {{ $reply->message }}
                        </div>
                    </div>
                </div>
                @endforeach

            </div>

            {{-- Admin reply form --}}
            @if(!in_array($feedback->status, ['resolved','closed']))
            <form action="{{ route('dashboard.feedback.admin.reply', $feedback) }}" method="POST" class="mt-5 pt-4 border-t border-slate-100" id="admin-reply-form">
                @csrf
                <div class="flex gap-3">
                    <div class="flex-1">
                        <textarea name="message" rows="2" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none transition resize-none"
                            placeholder="Type your admin reply..."></textarea>
                    </div>
                    <div class="flex flex-col justify-end">
                        <button type="submit" class="btn btn-success h-10 px-4 flex items-center gap-1.5" style="background:#10b981;border-color:#10b981;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Reply
                        </button>
                    </div>
                </div>
            </form>
            @else
                <div class="mt-5 pt-4 border-t border-slate-100 text-center" data-aos="zoom-in">
                    <span class="inline-flex items-center gap-1.5 text-sm text-slate-400 bg-slate-50 px-4 py-2 rounded-full border border-slate-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        This ticket is {{ $feedback->status }} — replies are disabled.
                    </span>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollEl = document.getElementById('chat-scroll');
    if (scrollEl) scrollEl.scrollTop = scrollEl.scrollHeight;
});

async function submitAdminInline(select, formId, msg) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        if (res.ok) {
            Swal.fire({ icon: 'success', title: msg, timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
        } else throw new Error('Failed');
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Update failed.', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
    }
}

@if(session('success'))
    Swal.fire({ icon: 'success', title: '{{ session('success') }}', timer: 2500, showConfirmButton: false, toast: true, position: 'top-end' });
@endif
</script>
@endsection
