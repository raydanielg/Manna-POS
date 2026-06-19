@extends('layouts.dashboard')
@section('page_title', 'Support & Feedback')

@section('content')
<div class="dash-content animate__animated animate__fadeInUp">

    {{-- Stats --}}
    <div class="kpi-grid mb-5" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));" data-aos="fade-up">
        <div class="kpi-card">
            <div class="kpi-icon" style="background: #eff6ff;">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $stats['total'] }}</div>
                <div class="kpi-label">Total Messages</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background: #fef9c3;">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $stats['open'] }}</div>
                <div class="kpi-label">Open / In Progress</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background: #dcfce7;">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $stats['resolved'] }}</div>
                <div class="kpi-label">Resolved / Closed</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background: #fee2e2;">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <div class="kpi-val">{{ $stats['high'] }}</div>
                <div class="kpi-label">High Priority Open</div>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="dash-section mb-5" data-aos="fade-up" data-aos-delay="100">
        <div class="dash-section-content">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 outline-none bg-white">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status')=='open' ? 'selected' : '' }}>Open</option>
                    <option value="resolved" {{ request('status')=='resolved' ? 'selected' : '' }}>Resolved / Closed</option>
                </select>
                <select name="type" onchange="this.form.submit()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 outline-none bg-white">
                    <option value="">All Types</option>
                    @foreach(['feedback'=>'Feedback','complaint'=>'Complaint','feature_request'=>'Feature Request','bug_report'=>'Bug Report','general'=>'General'] as $val=>$label)
                    <option value="{{ $val }}" {{ request('type')==$val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if(request('status') || request('type'))
                    <a href="{{ route('dashboard.feedback.admin.index') }}" class="text-sm text-red-500 font-semibold hover:underline">Clear Filters</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-card" data-aos="fade-up" data-aos-delay="200">
        <div class="table-head flex items-center justify-between">
            <div class="table-title flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                All Messages
            </div>
            <span class="text-xs text-slate-400 font-medium">{{ $feedbacks->total() }} total</span>
        </div>
        <div class="tbl-responsive">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Subject</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width:100px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $fb)
                    <tr data-aos="fade-up" data-aos-delay="{{ 250 + $loop->iteration * 60 }}">
                        <td>
                            <div class="text-sm font-semibold text-slate-800">{{ $fb->name }}</div>
                            <div class="text-xs text-slate-400">{{ $fb->email }}</div>
                        </td>
                        <td class="font-medium text-slate-700 max-w-[240px] truncate">{{ $fb->subject }}</td>
                        <td>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                @if($fb->type=='complaint') bg-red-50 text-red-600
                                @elseif($fb->type=='bug_report') bg-orange-50 text-orange-600
                                @elseif($fb->type=='feature_request') bg-violet-50 text-violet-600
                                @elseif($fb->type=='feedback') bg-emerald-50 text-emerald-600
                                @else bg-slate-50 text-slate-600 @endif">
                                {{ ucfirst(str_replace('_',' ',$fb->type)) }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('dashboard.feedback.admin.priority', $fb) }}" method="POST" class="inline priority-form">
                                @csrf @method('PATCH')
                                <select name="priority" onchange="submitInline(this, 'Priority updated')" class="rounded-md border border-slate-200 px-2 py-1 text-[0.7rem] font-bold uppercase outline-none focus:border-blue-500 bg-white cursor-pointer">
                                    <option value="low" {{ $fb->priority=='low' ? 'selected' : '' }} class="text-green-600">Low</option>
                                    <option value="medium" {{ $fb->priority=='medium' ? 'selected' : '' }} class="text-yellow-600">Medium</option>
                                    <option value="high" {{ $fb->priority=='high' ? 'selected' : '' }} class="text-red-600">High</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('dashboard.feedback.admin.status', $fb) }}" method="POST" class="inline status-form">
                                @csrf @method('PATCH')
                                <select name="status" onchange="submitInline(this, 'Status updated')" class="rounded-md border border-slate-200 px-2 py-1 text-[0.7rem] font-bold outline-none focus:border-blue-500 bg-white cursor-pointer">
                                    <option value="open" {{ $fb->status=='open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $fb->status=='in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ $fb->status=='resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $fb->status=='closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-xs text-slate-400">{{ $fb->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('dashboard.feedback.admin.show', $fb) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-2.5 py-1.5 rounded-lg transition">
                                Open
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7">
                        <div class="empty-state" data-aos="zoom-in">
                            <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            <div class="empty-title">No messages found</div>
                            <div class="empty-desc">Adjust filters or wait for customers to submit feedback.</div>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($feedbacks->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $feedbacks->links() }}</div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
async function submitInline(select, msg) {
    const form = select.closest('form');
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
