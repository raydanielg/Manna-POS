@extends('layouts.page')

@section('title', 'Careers - ' . config('app.name', 'MannaPOS'))

@section('content')
<div class="pb-16">
        <div class="max-w-4xl mx-auto px-4 lg:px-12">
            <div class="bg-white rounded-2xl shadow-sm p-8 lg:p-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-6">Join Our Team</h1>
                <p class="text-gray-600 text-lg mb-8">We're looking for talented individuals to help us build the future of POS in Africa.</p>

                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Why Work at MannaPOS?</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-primary-50 rounded-xl p-6">
                            <h3 class="font-bold text-primary-700 mb-2">Impact</h3>
                            <p class="text-gray-600 text-sm">Build products that help thousands of businesses grow and succeed.</p>
                        </div>
                        <div class="bg-primary-50 rounded-xl p-6">
                            <h3 class="font-bold text-primary-700 mb-2">Growth</h3>
                            <p class="text-gray-600 text-sm">Learn from experienced team members and advance your career.</p>
                        </div>
                        <div class="bg-primary-50 rounded-xl p-6">
                            <h3 class="font-bold text-primary-700 mb-2">Flexibility</h3>
                            <p class="text-gray-600 text-sm">Remote-friendly work environment with flexible hours.</p>
                        </div>
                        <div class="bg-primary-50 rounded-xl p-6">
                            <h3 class="font-bold text-primary-700 mb-2">Benefits</h3>
                            <p class="text-gray-600 text-sm">Competitive salary, health insurance, and stock options.</p>
                        </div>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-6">Open Positions</h2>
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-bold text-gray-900">Senior Software Engineer</h3>
                            <span class="bg-primary-100 text-primary-700 text-xs font-medium px-2 py-1 rounded">Full-time</span>
                        </div>
                        <p class="text-gray-600 mb-4">Dar es Salaam · Remote</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Apply Now →</a>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-bold text-gray-900">Product Designer</h3>
                            <span class="bg-primary-100 text-primary-700 text-xs font-medium px-2 py-1 rounded">Full-time</span>
                        </div>
                        <p class="text-gray-600 mb-4">Dar es Salaam · Remote</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Apply Now →</a>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-bold text-gray-900">Customer Success Manager</h3>
                            <span class="bg-primary-100 text-primary-700 text-xs font-medium px-2 py-1 rounded">Full-time</span>
                        </div>
                        <p class="text-gray-600 mb-4">Dar es Salaam</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Apply Now →</a>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-bold text-gray-900">Marketing Manager</h3>
                            <span class="bg-primary-100 text-primary-700 text-xs font-medium px-2 py-1 rounded">Full-time</span>
                        </div>
                        <p class="text-gray-600 mb-4">Dar es Salaam · Remote</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Apply Now →</a>
                    </div>
                </div>

                <div class="mt-12 p-6 bg-gray-50 rounded-lg">
                    <h3 class="font-bold text-gray-900 mb-2">Don't see a role that fits?</h3>
                    <p class="text-gray-600 mb-4">We're always looking for talented people. Send your resume to careers@mannapos.com.</p>
                    <a href="mailto:careers@mannapos.com" class="text-primary-600 hover:underline font-medium">Send Resume →</a>
                </div>
            </div>
        </div>
</div>
@endsection
