@extends('layouts.dashboard')
@section('page_title', 'My Feedback')

@section('content')
<div class="dash-content animate__animated animate__fadeInUp">

    <div class="dash-section" id="my-feedback-section">
        <div class="dash-section-header">
            <div class="dash-section-title flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                My Feedback & Complaints
            </div>
            <a href="{{ route('dashboard.feedback.create') }}" class="btn btn-primary" style="padding:0.35rem 0.9rem;font-size:0.8rem;">+ New Message</a>
        </div>
        <div class="dash-section-content">
            @if($feedbacks->isEmpty())
                <div class="empty-state">
                    <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <div class="empty-title">No messages yet</div>
                    <div class="empty-desc">You have not submitted any feedback or complaints. Click "New Message" to get started.</div>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($feedbacks as $fb)
                    <a href="{{ route('dashboard.feedback.show', $fb) }}" class="flex items-start gap-4 p-4 rounded-xl border border-slate-100 bg-white hover:shadow-md hover:border-blue-100 transition group no-underline">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white
                            @if($fb->type=='complaint') bg-red-500
                            @elseif($fb->type=='bug_report') bg-orange-500
                            @elseif($fb->type=='feature_request') bg-violet-500
                            @elseif($fb->type=='feedback') bg-emerald-500
                            @else bg-slate-400 @endif">
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
                        <div class="hidden sm:block text-xs text-slate-300 group-hover:text-blue-400 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
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
