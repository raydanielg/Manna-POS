@extends('layouts.page')

@section('title', 'System Status - ' . config('app.name', 'MannaPOS'))

@section('content')
<div class="pb-16">
        <div class="max-w-4xl mx-auto px-4 lg:px-12">
            <div class="bg-white rounded-2xl shadow-sm p-8 lg:p-12">
                <div class="flex items-center justify-between mb-8">
                    <h1 class="text-4xl font-bold text-gray-900">System Status</h1>
                    <div class="flex items-center">
                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                        <span class="text-green-600 font-medium">All Systems Operational</span>
                    </div>
                </div>

                <div class="space-y-4 mb-12">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                            <span class="font-medium text-gray-900">Web Application</span>
                        </div>
                        <span class="text-green-600 font-medium">Operational</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                            <span class="font-medium text-gray-900">API Services</span>
                        </div>
                        <span class="text-green-600 font-medium">Operational</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                            <span class="font-medium text-gray-900">Database</span>
                        </div>
                        <span class="text-green-600 font-medium">Operational</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                            <span class="font-medium text-gray-900">Payment Processing</span>
                        </div>
                        <span class="text-green-600 font-medium">Operational</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                            <span class="font-medium text-gray-900">Email Services</span>
                        </div>
                        <span class="text-green-600 font-medium">Operational</span>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-6">Past Incidents</h2>
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900">Scheduled Maintenance</span>
                            <span class="text-gray-500 text-sm">Dec 15, 2023</span>
                        </div>
                        <p class="text-gray-600 text-sm">Planned system upgrade completed successfully. No downtime experienced.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900">API Latency Issues</span>
                            <span class="text-gray-500 text-sm">Nov 20, 2023</span>
                        </div>
                        <p class="text-gray-600 text-sm">Resolved within 30 minutes. Affected API response times for some endpoints.</p>
                    </div>
                </div>

                <div class="mt-12 p-6 bg-gray-50 rounded-lg">
                    <h3 class="font-bold text-gray-900 mb-2">Uptime Statistics</h3>
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">99.9%</div>
                            <div class="text-gray-600 text-sm">Last 30 days</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">99.8%</div>
                            <div class="text-gray-600 text-sm">Last 90 days</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">99.7%</div>
                            <div class="text-gray-600 text-sm">Last 12 months</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
