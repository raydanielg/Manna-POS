<div class="nav-section-label">Main</div>
<a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="M10 12h4v4h-4z"/></svg>
    Dashboard
</a>

<div class="nav-section-label">Users & Access</div>
<div class="dropdown {{ request()->routeIs('admin.users*') || request()->routeIs('admin.roles*') || request()->routeIs('admin.staff*') ? 'open' : '' }}" id="dropdown-user-access">
    <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-user-access')">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg>
        User Management
        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
    </div>
    <div class="dropdown-children">
        <a href="{{ route('admin.users') }}" class="child-item">All Users</a>
        <a href="{{ route('admin.roles') }}" class="child-item">Roles & Permissions</a>
        <a href="{{ route('admin.sales-commission-agents') }}" class="child-item">Sales Agents</a>
        <a href="{{ route('admin.login-history') }}" class="child-item">Login History</a>
    </div>
</div>

<div class="dropdown {{ request()->routeIs('admin.staff*') ? 'open' : '' }}" id="dropdown-staff">
    <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-staff')">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h-2a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2z"/><path d="M7 20h-2a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2z"/><path d="M12 4l0 .01"/><path d="M12 8l0 .01"/><path d="M12 12l0 .01"/></svg>
        Staff Management
        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
    </div>
    <div class="dropdown-children">
        <a href="{{ route('admin.staff.index') }}" class="child-item">All Staff</a>
        <a href="{{ route('admin.staff.attendance') }}" class="child-item">Attendance</a>
        <a href="{{ route('admin.staff.schedules') }}" class="child-item">Schedules</a>
    </div>
</div>

<div class="nav-section-label">Business</div>
<div class="dropdown {{ request()->routeIs('admin.business*') ? 'open' : '' }}" id="dropdown-business">
    <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-business')">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7m0 1a1 1 0 0 1 1 -h16a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1z"/><path d="M5 11v7a1 1 0 0 0 1 1h3"/><path d="M19 11v7a1 1 0 0 1 -1 1h-3"/><path d="M9 16h6"/><path d="M12 3l3 4h-6z"/></svg>
        Business Management
        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
    </div>
    <div class="dropdown-children">
        <a href="{{ route('admin.business.index') }}" class="child-item">All Businesses</a>
        <a href="{{ route('admin.business.verifications') }}" class="child-item">Verifications</a>
        <a href="{{ route('admin.business.categories') }}" class="child-item">Categories</a>
    </div>
</div>

<div class="nav-section-label">Subscriptions & Billing</div>
<div class="dropdown {{ request()->routeIs('admin.plans*') || request()->routeIs('admin.subscriptions*') ? 'open' : '' }}" id="dropdown-plans">
    <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-plans')">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5"/><path d="M12 12l8 -4.5"/><path d="M8.2 9.8l7.6 -4.6"/><path d="M12 12v9"/><path d="M12 12l-8 -4.5"/></svg>
        Plan Management
        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
    </div>
    <div class="dropdown-children">
        <a href="{{ route('admin.plans') }}" class="child-item">All Plans</a>
        <a href="{{ route('admin.subscriptions') }}" class="child-item">Subscriptions</a>
    </div>
</div>

<div class="dropdown {{ request()->routeIs('admin.billing.*') || request()->routeIs('admin.payments*') || request()->routeIs('admin.gateways*') ? 'open' : '' }}" id="dropdown-billing">
    <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-billing')">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2"/><path d="M14.8 8a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1"/><path d="M12 6v10"/></svg>
        Billing & Payments
        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
    </div>
    <div class="dropdown-children">
        <a href="{{ route('admin.billing.invoices') }}" class="child-item">Invoices</a>
        <a href="{{ route('admin.billing.payments') }}" class="child-item">Payments</a>
        <a href="{{ route('admin.billing.gateways') }}" class="child-item">Payment Gateways</a>
    </div>
</div>

<div class="nav-section-label">Communication</div>
<div class="dropdown {{ request()->routeIs('admin.communication.*') || request()->routeIs('admin.announcements*') || request()->routeIs('admin.notification*') ? 'open' : '' }}" id="dropdown-comm">
    <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-comm')">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"/><path d="M3 7l9 6l9 -6"/></svg>
        Communication
        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
    </div>
    <div class="dropdown-children">
        <a href="{{ route('admin.notification-templates') }}" class="child-item">Notification Templates</a>
        <a href="{{ route('admin.communication.email-templates') }}" class="child-item">Email Templates</a>
        <a href="{{ route('admin.communication.sms-templates') }}" class="child-item">SMS Templates</a>
        <a href="{{ route('admin.communication.announcements') }}" class="child-item">Announcements</a>
    </div>
</div>

<a href="{{ route('admin.support.tickets') }}" class="nav-item {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M9 14h.01"/><path d="M12 14h.01"/><path d="M15 14h.01"/></svg>
    Support Tickets
</a>

<div class="nav-section-label">System</div>
<div class="dropdown {{ request()->routeIs('admin.settings*') ? 'open' : '' }}" id="dropdown-settings">
    <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-settings')">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/></svg>
        Settings
        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
    </div>
    <div class="dropdown-children">
        <a href="{{ route('admin.settings.general') }}" class="child-item">Business Settings</a>
        <a href="{{ route('admin.settings.business-location') }}" class="child-item">Business Locations</a>
        <a href="{{ route('admin.settings.invoice-settings') }}" class="child-item">Invoice Settings</a>
        <a href="{{ route('admin.settings.barcode-settings') }}" class="child-item">Barcode Settings</a>
        <a href="{{ route('admin.settings.tax-rates') }}" class="child-item">Tax Rates</a>
    </div>
</div>

<div class="dropdown {{ request()->routeIs('admin.system.*') ? 'open' : '' }}" id="dropdown-system">
    <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-system')">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M12 12v-4"/><path d="M12 4l0 .01"/></svg>
        System Tools
        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
    </div>
    <div class="dropdown-children">
        <a href="{{ route('admin.system.config') }}" class="child-item">System Config</a>
        <a href="{{ route('admin.system.activity-logs') }}" class="child-item">Activity Logs</a>
        <a href="{{ route('admin.system.backups') }}" class="child-item">Backups</a>
        <a href="{{ route('admin.system.health') }}" class="child-item">System Health</a>
    </div>
</div>

<a href="{{ route('admin.reports') }}" class="nav-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697"/><path d="M18 14v4h4"/><path d="M18 11v-4a2 2 0 0 0 -2 -2h-2"/><path d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M8 11h4"/><path d="M8 15h3"/></svg>
    Reports
</a>

<div class="nav-section-label">Quick Links</div>
<a href="{{ route('dashboard') }}" class="nav-item">
    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/></svg>
    User Dashboard
</a>
