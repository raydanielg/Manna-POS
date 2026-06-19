@extends('layouts.dashboard')
@section('page_title', 'Submit Feedback')

@section('content')
<div class="dash-content animate__animated animate__fadeInUp">

    <div class="dash-section" id="feedback-form-section">
        <div class="dash-section-header">
            <div class="dash-section-title flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                Submit Feedback / Complaint
            </div>
        </div>
        <div class="dash-section-content">
            <form action="{{ route('dashboard.feedback.store') }}" method="POST" class="max-w-2xl space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Your Name</label>
                        <input type="text" name="name" value="{{ old('name', Auth::user()->name ?? '') }}" required
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition"
                            placeholder="John Doe">
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', Auth::user()->email ?? '') }}" required
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition"
                            placeholder="john@example.com">
                        @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required
                        class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition"
                        placeholder="Brief title of your message">
                    @error('subject')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Type</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['feedback'=>'Feedback','complaint'=>'Complaint','feature_request'=>'Feature Request','bug_report'=>'Bug Report','general'=>'General'] as $val=>$label)
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="{{ $val }}" {{ old('type')==$val ? 'checked' : '' }} class="peer sr-only" @if($loop->first && !old('type')) checked @endif>
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-semibold border border-slate-200 text-slate-500 bg-white peer-checked:bg-blue-50 peer-checked:border-blue-400 peer-checked:text-blue-700 transition">
                                {{ $label }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    @error('type')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Message</label>
                    <textarea name="message" rows="6" required
                        class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition resize-none"
                        placeholder="Describe your feedback, complaint, or suggestion in detail...">{{ old('message') }}</textarea>
                    @error('message')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn btn-primary">Submit Message</button>
                    <a href="{{ route('dashboard.feedback.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
