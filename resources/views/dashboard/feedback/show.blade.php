@extends('layouts.dashboard')
@section('page_title', 'Feedback: ' . $feedback->subject)

@section('content')
<div class="dash-content animate__animated animate__fadeInUp">

    {{-- Header card --}}
    <div class="dash-section mb-4">
        <div class="dash-section-content">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wide
                            @if($feedback->type=='complaint') bg-red-50 text-red-600
                            @elseif($feedback->type=='bug_report') bg-orange-50 text-orange-600
                            @elseif($feedback->type=='feature_request') bg-violet-50 text-violet-600
                            @elseif($feedback->type=='feedback') bg-emerald-50 text-emerald-600
                            @else bg-slate-50 text-slate-600 @endif">
                            {{ ucfirst(str_replace('_',' ',$feedback->type)) }}
                        </span>
                        @if($feedback->status == 'open')
                            <span class="badge badge-pending">Open</span>
                        @elseif($feedback->status == 'in_progress')
                            <span class="badge badge-info">In Progress</span>
                        @elseif($feedback->status == 'resolved')
                            <span class="badge badge-success">Resolved</span>
                        @else
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">Closed</span>
                        @endif
                    </div>
                    <h1 class="text-lg font-bold text-slate-900">{{ $feedback->subject }}</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Submitted {{ $feedback->created_at->format('M d, Y g:i A') }}</p>
                </div>
                <a href="{{ route('dashboard.feedback.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Chat thread --}}
    <div class="dash-section">
        <div class="dash-section-header">
            <div class="dash-section-title flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Conversation
            </div>
        </div>
        <div class="dash-section-content">
            <div class="space-y-5 max-h-[500px] overflow-y-auto pr-1" id="chat-scroll">

                {{-- Original message --}}
                <div class="flex gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-violet-500 flex items-center justify-center flex-shrink-0 text-xs font-bold text-white">
                        {{ strtoupper(substr($feedback->name,0,1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-baseline gap-2 mb-1">
                            <span class="text-sm font-semibold text-slate-800">{{ $feedback->name }}</span>
                            <span class="text-[0.65rem] text-slate-400">{{ $feedback->created_at->format('g:i A, M d') }}</span>
                        </div>
                        <div class="bg-slate-50 rounded-xl rounded-tl-none px-4 py-3 text-sm text-slate-700 leading-relaxed">
                            {{ $feedback->message }}
                        </div>
                    </div>
                </div>

                {{-- Replies --}}
                @foreach($feedback->replies as $reply)
                <div class="flex gap-3 @if($reply->sender_type == 'admin') flex-row-reverse @endif">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold text-white
                        @if($reply->sender_type == 'admin') bg-emerald-500 @else bg-gradient-to-br from-blue-500 to-violet-500 @endif">
                        @if($reply->sender_type == 'admin')
                            A
                        @else
                            {{ strtoupper(substr($reply->user->name ?? $feedback->name,0,1)) }}
                        @endif
                    </div>
                    <div class="flex-1 @if($reply->sender_type == 'admin') text-right @endif">
                        <div class="flex items-baseline gap-2 mb-1 @if($reply->sender_type == 'admin') justify-end @endif">
                            @if($reply->sender_type == 'admin')
                                <span class="text-[0.65rem] text-slate-400">{{ $reply->created_at->format('g:i A, M d') }}</span>
                                <span class="text-sm font-semibold text-slate-800">Support Team</span>
                            @else
                                <span class="text-sm font-semibold text-slate-800">{{ $reply->user->name ?? $feedback->name }}</span>
                                <span class="text-[0.65rem] text-slate-400">{{ $reply->created_at->format('g:i A, M d') }}</span>
                            @endif
                        </div>
                        <div class="@if($reply->sender_type == 'admin') bg-emerald-50 text-emerald-900 rounded-tr-none @else bg-slate-50 text-slate-700 rounded-tl-none @endif rounded-xl px-4 py-3 text-sm leading-relaxed inline-block text-left max-w-full">
                            {{ $reply->message }}
                        </div>
                    </div>
                </div>
                @endforeach

            </div>

            {{-- Reply form --}}
            @if(!in_array($feedback->status, ['resolved','closed']))
            <form action="{{ route('dashboard.feedback.reply', $feedback) }}" method="POST" class="mt-5 pt-4 border-t border-slate-100">
                @csrf
                <div class="flex gap-3">
                    <div class="flex-1">
                        <textarea name="message" rows="2" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition resize-none"
                            placeholder="Type your reply..."></textarea>
                    </div>
                    <div class="flex flex-col justify-end">
                        <button type="submit" class="btn btn-primary h-10 px-4 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Send
                        </button>
                    </div>
                </div>
            </form>
            @else
                <div class="mt-5 pt-4 border-t border-slate-100 text-center">
                    <span class="inline-flex items-center gap-1.5 text-sm text-slate-400 bg-slate-50 px-4 py-2 rounded-full">
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
</script>
@endsection
