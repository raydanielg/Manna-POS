<?php
namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\Backup;
use App\Models\Business;
use App\Models\BusinessVerification;
use App\Models\CustomerGroup;
use App\Models\Discount;
use App\Models\EmailTemplate;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\NotificationTemplate;
use App\Models\Payment;
use App\Models\SmsTemplate;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffSchedule;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\SubscriptionPlan;
use App\Models\SupportTicket;
use App\Models\TaxRate;
use App\Models\TicketReply;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Warranty;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminDemoSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Seeding admin demo data...');

        // ── Ensure extra users exist ──
        $extraUsers = [
            ['name' => 'John Mwamba', 'email' => 'john@example.com', 'password' => bcrypt('password'), 'phone' => '+255711111111', 'role' => 'user', 'business_name' => 'Mwamba General Store'],
            ['name' => 'Jane Kileo', 'email' => 'jane@example.com', 'password' => bcrypt('password'), 'phone' => '+255722222222', 'role' => 'user', 'business_name' => 'Kileo Wholesalers'],
            ['name' => 'Peter Masanja', 'email' => 'peter@example.com', 'password' => bcrypt('password'), 'phone' => '+255733333333', 'role' => 'user', 'business_name' => 'Masanja Electronics'],
        ];
        foreach ($extraUsers as $u) {
            User::firstOrCreate(['email' => $u['email']], $u);
        }

        // ── Businesses ──
        DB::transaction(function () {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Business::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        });
        $businesses = [
            ['user_id' => 1, 'business_name' => 'Mwamba General Store', 'business_type' => 'retail', 'business_category_id' => 1, 'business_address' => 'Sinza Mori, Plot 45', 'business_city' => 'Dar es Salaam', 'business_country' => 'Tanzania', 'phone' => '+255711111111', 'email' => 'info@mwambastore.co.tz', 'website' => 'https://mwambastore.co.tz', 'registration_number' => 'REG-2024-001', 'tax_number' => 'TIN-123456789', 'currency' => 'TZS', 'status' => 'active', 'is_verified' => true, 'verified_at' => Carbon::now()->subMonth(), 'verified_by' => 2],
            ['user_id' => 2, 'business_name' => 'Kileo Wholesalers', 'business_type' => 'wholesale', 'business_category_id' => 3, 'business_address' => 'Kariakoo Market', 'business_city' => 'Dar es Salaam', 'business_country' => 'Tanzania', 'phone' => '+255722222222', 'email' => 'info@kileowholesale.co.tz', 'currency' => 'TZS', 'status' => 'active', 'is_verified' => true, 'verified_at' => Carbon::now()->subWeeks(2), 'verified_by' => 2],
            ['user_id' => 3, 'business_name' => 'Masanja Electronics', 'business_type' => 'retail', 'business_category_id' => 8, 'business_address' => 'Mwenge, Block C', 'business_city' => 'Dar es Salaam', 'business_country' => 'Tanzania', 'phone' => '+255733333333', 'email' => 'hello@masanja.co.tz', 'currency' => 'TZS', 'status' => 'pending', 'is_verified' => false],
            ['user_id' => 4, 'business_name' => 'Mama Pita Cafe', 'business_type' => 'restaurant', 'business_category_id' => 2, 'business_address' => 'Posta, Samora Ave', 'business_city' => 'Dar es Salaam', 'business_country' => 'Tanzania', 'phone' => '+255712345678', 'email' => 'cafe@mamapita.co.tz', 'currency' => 'TZS', 'status' => 'active', 'is_verified' => true, 'verified_at' => Carbon::now()->subMonth(), 'verified_by' => 2],
        ];
        foreach ($businesses as $b) {
            Business::create($b);
        }

        // ── Business Verifications ──
        BusinessVerification::truncate();
        BusinessVerification::create(['business_id' => 1, 'document_type' => 'business_license', 'document_path' => 'uploads/docs/license_001.pdf', 'status' => 'approved', 'notes' => 'License verified', 'reviewed_by' => 2, 'reviewed_at' => Carbon::now()->subMonth()]);
        BusinessVerification::create(['business_id' => 2, 'document_type' => 'tax_certificate', 'document_path' => 'uploads/docs/tax_002.pdf', 'status' => 'approved', 'reviewed_by' => 2, 'reviewed_at' => Carbon::now()->subWeeks(2)]);
        BusinessVerification::create(['business_id' => 3, 'document_type' => 'business_license', 'document_path' => 'uploads/docs/license_003.pdf', 'status' => 'pending']);
        BusinessVerification::create(['business_id' => 4, 'document_type' => 'tax_certificate', 'document_path' => 'uploads/docs/tax_004.pdf', 'status' => 'approved', 'reviewed_by' => 2, 'reviewed_at' => Carbon::now()->subMonth()]);

        // ── Staff ──
        Staff::truncate();
        $staff = [
            ['first_name' => 'Salim', 'last_name' => 'Juma', 'email' => 'salim@mwambastore.co.tz', 'phone' => '+255741111111', 'user_id' => 1, 'role' => 'cashier', 'department' => 'Sales', 'position' => 'Senior Cashier', 'salary' => 350000, 'pay_type' => 'monthly', 'hire_date' => '2024-01-15', 'status' => 'active', 'address' => 'Sinza', 'emergency_contact' => 'Mariam Juma', 'emergency_phone' => '+255742222222'],
            ['first_name' => 'Asha', 'last_name' => 'Moshi', 'email' => 'asha@mwambastore.co.tz', 'phone' => '+255743333333', 'user_id' => 1, 'role' => 'cashier', 'department' => 'Sales', 'position' => 'Cashier', 'salary' => 280000, 'pay_type' => 'monthly', 'hire_date' => '2024-03-01', 'status' => 'active', 'address' => 'Mabibo'],
            ['first_name' => 'Juma', 'last_name' => 'Mwinyi', 'email' => 'juma@kileowholesale.co.tz', 'phone' => '+255744444444', 'user_id' => 2, 'role' => 'manager', 'department' => 'Management', 'position' => 'Branch Manager', 'salary' => 600000, 'pay_type' => 'monthly', 'hire_date' => '2023-06-01', 'status' => 'active', 'address' => 'Kariakoo', 'emergency_contact' => 'Mwinyi Family', 'emergency_phone' => '+255745555555'],
            ['first_name' => 'Neema', 'last_name' => 'Sanga', 'email' => 'neema@mamapita.co.tz', 'phone' => '+255746666666', 'user_id' => 4, 'role' => 'waitstaff', 'department' => 'Service', 'position' => 'Head Waitress', 'salary' => 300000, 'pay_type' => 'monthly', 'hire_date' => '2024-02-01', 'status' => 'active', 'address' => 'Posta'],
            ['first_name' => 'Abdul', 'last_name' => 'Rashid', 'email' => 'abdul@masanja.co.tz', 'phone' => '+255747777777', 'user_id' => 3, 'role' => 'technician', 'department' => 'Service', 'position' => 'Repair Technician', 'salary' => 0, 'pay_type' => 'hourly', 'hire_date' => '2024-05-01', 'status' => 'active', 'address' => 'Mwenge'],
        ];
        foreach ($staff as $s) {
            Staff::create($s);
        }

        // ── Staff Attendance ──
        StaffAttendance::truncate();
        StaffAttendance::create(['staff_id' => 1, 'date' => Carbon::today(), 'clock_in' => '08:00', 'clock_out' => '17:00', 'status' => 'present', 'notes' => 'On time']);
        StaffAttendance::create(['staff_id' => 1, 'date' => Carbon::yesterday(), 'clock_in' => '08:15', 'clock_out' => '16:30', 'status' => 'present', 'notes' => '']);
        StaffAttendance::create(['staff_id' => 2, 'date' => Carbon::today(), 'clock_in' => '09:00', 'clock_out' => '17:30', 'status' => 'late', 'notes' => 'Arrived 30 min late']);
        StaffAttendance::create(['staff_id' => 3, 'date' => Carbon::today(), 'clock_in' => '07:45', 'clock_out' => '18:00', 'status' => 'present', 'notes' => 'Overtime']);
        StaffAttendance::create(['staff_id' => 4, 'date' => Carbon::today(), 'clock_in' => '08:30', 'clock_out' => '16:45', 'status' => 'present']);

        // ── Staff Schedules ──
        StaffSchedule::truncate();
        foreach ([1, 2, 3, 4, 5] as $sid) {
            for ($d = 0; $d < 5; $d++) {
                StaffSchedule::create(['staff_id' => $sid, 'day_of_week' => $d, 'start_time' => '08:00', 'end_time' => '17:00', 'is_working_day' => ($d < 5)]);
            }
        }

        // ── Subscription Plans ──
        UserSubscription::truncate();  // must truncate before plans (FK)
        SubscriptionPlan::truncate();
        SubscriptionPlan::create(['name' => 'Starter', 'slug' => 'starter', 'description' => 'Perfect for small shops just getting started with digital POS.', 'price_monthly' => 0, 'price_yearly' => 0, 'currency' => 'TZS', 'max_users' => 1, 'max_products' => 100, 'max_locations' => 1, 'features' => ['Basic Sales', 'Inventory (up to 100 products)', 'Cash Payments', 'Daily Reports'], 'is_active' => true, 'is_featured' => false, 'sort_order' => 1, 'badge_color' => '#94a3b8']);
        SubscriptionPlan::create(['name' => 'Growth', 'slug' => 'growth', 'description' => 'For growing businesses that need inventory management and reports.', 'price_monthly' => 45000, 'price_yearly' => 450000, 'currency' => 'TZS', 'max_users' => 3, 'max_products' => 1000, 'max_locations' => 2, 'features' => ['Everything in Starter', 'Up to 3 Users', 'Inventory (1,000 products)', 'Mobile Money Payments', 'Customer Profiles', 'Sales Analytics'], 'is_active' => true, 'is_featured' => true, 'sort_order' => 2, 'badge_color' => '#2563eb']);
        SubscriptionPlan::create(['name' => 'Business', 'slug' => 'business', 'description' => 'For established businesses with multiple staff and locations.', 'price_monthly' => 95000, 'price_yearly' => 950000, 'currency' => 'TZS', 'max_users' => 10, 'max_products' => 10000, 'max_locations' => 5, 'features' => ['Everything in Growth', 'Up to 10 Users', 'Multi-Location (5 branches)', 'Staff Management', 'Purchase Orders', 'Barcode Scanning', 'Advanced Reports'], 'is_active' => true, 'is_featured' => true, 'sort_order' => 3, 'badge_color' => '#16a34a']);
        SubscriptionPlan::create(['name' => 'Enterprise', 'slug' => 'enterprise', 'description' => 'For large organizations with advanced needs and dedicated support.', 'price_monthly' => 250000, 'price_yearly' => 2500000, 'currency' => 'TZS', 'max_users' => 50, 'max_products' => 999999, 'max_locations' => 50, 'features' => ['Everything in Business', 'Unlimited Products', 'Up to 50 Users', '50 Locations', 'API Access', 'Dedicated Account Manager', 'Priority Support', 'Custom Integrations'], 'is_active' => true, 'is_featured' => false, 'sort_order' => 4, 'badge_color' => '#7c3aed']);

        // ── User Subscriptions ──
        UserSubscription::create(['user_id' => 1, 'subscription_plan_id' => 3, 'billing_cycle' => 'monthly', 'amount_paid' => 95000, 'currency' => 'TZS', 'status' => 'active', 'starts_at' => Carbon::now()->subMonths(2), 'expires_at' => Carbon::now()->addMonth(), 'transaction_ref' => 'TXN-001', 'notes' => 'Primary subscription']);
        UserSubscription::create(['user_id' => 2, 'subscription_plan_id' => 2, 'billing_cycle' => 'yearly', 'amount_paid' => 450000, 'currency' => 'TZS', 'status' => 'active', 'starts_at' => Carbon::now()->subMonths(3), 'expires_at' => Carbon::now()->addMonths(9), 'transaction_ref' => 'TXN-002']);
        UserSubscription::create(['user_id' => 3, 'subscription_plan_id' => 2, 'billing_cycle' => 'monthly', 'amount_paid' => 45000, 'currency' => 'TZS', 'status' => 'trial', 'starts_at' => Carbon::now()->subDays(5), 'expires_at' => Carbon::now()->addDays(25), 'transaction_ref' => null, 'notes' => 'Trial period']);
        UserSubscription::create(['user_id' => 4, 'subscription_plan_id' => 1, 'billing_cycle' => 'monthly', 'amount_paid' => 0, 'currency' => 'TZS', 'status' => 'active', 'starts_at' => Carbon::now()->subMonths(4), 'expires_at' => Carbon::now()->addMonths(2), 'transaction_ref' => null, 'notes' => 'Free plan']);
        UserSubscription::create(['user_id' => 2, 'subscription_plan_id' => 3, 'billing_cycle' => 'monthly', 'amount_paid' => 95000, 'currency' => 'TZS', 'status' => 'expired', 'starts_at' => Carbon::now()->subMonths(6), 'expires_at' => Carbon::now()->subMonths(3), 'transaction_ref' => 'TXN-OLD-001', 'notes' => 'Previous plan']);

        // ── Invoices (admin billing) ──
        Invoice::truncate();
        Invoice::create(['invoice_number' => 'INV-2024-001', 'user_id' => 1, 'subscription_id' => 1, 'billing_cycle' => 'monthly', 'subtotal' => 95000, 'tax' => 17100, 'discount' => 0, 'total' => 112100, 'currency' => 'TZS', 'status' => 'paid', 'due_date' => Carbon::now()->subMonth(), 'paid_at' => Carbon::now()->subMonth(), 'notes' => 'Monthly subscription fee']);
        Invoice::create(['invoice_number' => 'INV-2024-002', 'user_id' => 2, 'subscription_id' => 2, 'billing_cycle' => 'yearly', 'subtotal' => 450000, 'tax' => 81000, 'discount' => 45000, 'total' => 486000, 'currency' => 'TZS', 'status' => 'paid', 'due_date' => Carbon::now()->subMonths(3), 'paid_at' => Carbon::now()->subMonths(3), 'notes' => 'Yearly subscription with 10% discount']);
        Invoice::create(['invoice_number' => 'INV-2024-003', 'user_id' => 1, 'subscription_id' => 1, 'billing_cycle' => 'monthly', 'subtotal' => 95000, 'tax' => 17100, 'discount' => 0, 'total' => 112100, 'currency' => 'TZS', 'status' => 'pending', 'due_date' => Carbon::now()->addDay(), 'paid_at' => null, 'notes' => 'Next billing cycle']);
        Invoice::create(['invoice_number' => 'INV-2024-004', 'user_id' => 3, 'subscription_id' => 3, 'billing_cycle' => 'monthly', 'subtotal' => 45000, 'tax' => 8100, 'discount' => 0, 'total' => 53100, 'currency' => 'TZS', 'status' => 'overdue', 'due_date' => Carbon::now()->subDays(5), 'paid_at' => null, 'notes' => 'Overdue payment']);
        Invoice::create(['invoice_number' => 'INV-2024-005', 'user_id' => 2, 'subscription_id' => 5, 'billing_cycle' => 'monthly', 'subtotal' => 95000, 'tax' => 17100, 'discount' => 0, 'total' => 112100, 'currency' => 'TZS', 'status' => 'paid', 'due_date' => Carbon::now()->subMonths(4), 'paid_at' => Carbon::now()->subMonths(4), 'notes' => 'Previous plan invoice']);

        // ── Payments ──
        Payment::truncate();
        Payment::create(['invoice_id' => 1, 'user_id' => 1, 'amount' => 112100, 'currency' => 'TZS', 'payment_method' => 'mpesa', 'transaction_id' => 'MP-2024-001', 'gateway' => 'mpesa', 'status' => 'completed', 'paid_at' => Carbon::now()->subMonth()]);
        Payment::create(['invoice_id' => 2, 'user_id' => 2, 'amount' => 486000, 'currency' => 'TZS', 'payment_method' => 'tigo_pesa', 'transaction_id' => 'TP-2024-001', 'gateway' => 'tigo_pesa', 'status' => 'completed', 'paid_at' => Carbon::now()->subMonths(3)]);
        Payment::create(['invoice_id' => 5, 'user_id' => 2, 'amount' => 112100, 'currency' => 'TZS', 'payment_method' => 'bank_transfer', 'transaction_id' => 'BT-2024-001', 'gateway' => 'bank_transfer', 'status' => 'completed', 'paid_at' => Carbon::now()->subMonths(4)]);

        // ── Support Tickets ──
        SupportTicket::truncate();
        $tickets = [
            ['ticket_number' => 'TKT-001', 'user_id' => 1, 'subject' => 'Unable to process refund', 'description' => 'I keep getting an error when trying to refund a customer order. The system says "Transaction cannot be refunded after 30 days."', 'priority' => 'high', 'category' => 'technical', 'status' => 'open', 'assigned_to' => 2, 'resolved_at' => null],
            ['ticket_number' => 'TKT-002', 'user_id' => 2, 'subject' => 'How to add multiple users', 'description' => 'I recently upgraded to the Growth plan and want to add 2 more cashiers. How do I create their accounts?', 'priority' => 'medium', 'category' => 'billing', 'status' => 'in_progress', 'assigned_to' => 2, 'resolved_at' => null],
            ['ticket_number' => 'TKT-003', 'user_id' => 4, 'subject' => 'Receipt printer not working', 'description' => 'The receipt printer connected to my POS terminal stopped working after the latest update.', 'priority' => 'high', 'category' => 'technical', 'status' => 'open', 'assigned_to' => null, 'resolved_at' => null],
            ['ticket_number' => 'TKT-004', 'user_id' => 1, 'subject' => 'Request for new feature - bulk price update', 'description' => 'It would be very helpful if we could update prices for multiple products at once from the inventory page.', 'priority' => 'low', 'category' => 'feature', 'status' => 'closed', 'assigned_to' => null, 'resolved_at' => Carbon::now()->subDays(10)],
            ['ticket_number' => 'TKT-005', 'user_id' => 3, 'subject' => 'Invoice payment not reflecting', 'description' => 'I paid my invoice via M-Pesa 3 days ago but it still shows as unpaid in my dashboard.', 'priority' => 'urgent', 'category' => 'billing', 'status' => 'open', 'assigned_to' => 2, 'resolved_at' => null],
        ];
        foreach ($tickets as $t) {
            SupportTicket::create($t);
        }

        // ── Ticket Replies ──
        TicketReply::truncate();
        TicketReply::create(['ticket_id' => 1, 'user_id' => 2, 'message' => 'Thank you for reporting this. Could you please provide the transaction ID for the sale you are trying to refund?', 'attachments' => null]);
        TicketReply::create(['ticket_id' => 1, 'user_id' => 1, 'message' => 'The transaction ID is SALE-004, processed on June 13th.', 'attachments' => null]);
        TicketReply::create(['ticket_id' => 2, 'user_id' => 2, 'message' => 'You can add users in Settings > Users. Click "Add User" and assign them the "Cashier" role.', 'attachments' => null]);
        TicketReply::create(['ticket_id' => 4, 'user_id' => 1, 'message' => 'Great suggestion! This has been added to our product roadmap for Q3 2025.', 'attachments' => null]);

        // ── Activity Logs ──
        ActivityLog::truncate();
        $actions = ['created', 'updated', 'deleted', 'logged_in', 'logged_out', 'exported_report'];
        $resources = ['user', 'business', 'subscription', 'invoice', 'staff', 'product', 'sale'];
        $descriptions = [
            'created' => ['Created a new user account', 'Registered a new business', 'Added a new product to inventory', 'Created a new staff record', 'Generated a new invoice'],
            'updated' => ['Updated user profile', 'Modified business information', 'Changed subscription plan', 'Updated product pricing', 'Edited staff details'],
            'deleted' => ['Removed a user account', 'Deleted a business record', 'Removed a product from inventory', 'Archived an old invoice'],
            'logged_in' => ['Logged into the system', 'Authenticated via email'],
            'logged_out' => ['Logged out of the system', 'Session expired'],
            'exported_report' => ['Exported daily sales report', 'Generated monthly financial summary', 'Downloaded inventory report'],
        ];
        for ($i = 0; $i < 25; $i++) {
            $action = $actions[array_rand($actions)];
            $resource = $resources[array_rand($resources)];
            $desc = $descriptions[$action][array_rand($descriptions[$action])];
            ActivityLog::create([
                'user_id' => rand(1, 4),
                'action' => $action,
                'resource_type' => $resource,
                'resource_id' => rand(1, 20),
                'description' => $desc,
                'old_values' => null,
                'new_values' => null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => Carbon::now()->subHours(rand(0, 336)),
            ]);
        }

        // ── Notification Templates ──
        NotificationTemplate::truncate();
        NotificationTemplate::insert([
            ['type' => 'email', 'subject' => 'Welcome to MannaPOS', 'body' => 'Hi {{name}}, welcome to MannaPOS! Get started by setting up your store.', 'is_active' => true],
            ['type' => 'sms', 'subject' => 'Subscription Expiry Warning', 'body' => 'Your MannaPOS subscription is expiring in 3 days. Renew now to avoid interruption.', 'is_active' => true],
            ['type' => 'in_app', 'subject' => 'Invoice Paid', 'body' => 'Invoice #{{invoice_number}} has been paid successfully.', 'is_active' => true],
            ['type' => 'email', 'subject' => 'Subscription Renewed', 'body' => 'Hi {{name}}, your {{plan}} subscription has been renewed. New expiry: {{expiry_date}}.', 'is_active' => true],
            ['type' => 'sms', 'subject' => 'Low Stock Alert', 'body' => 'Low stock alert: {{product}} only has {{quantity}} units remaining. Reorder now!', 'is_active' => true],
        ]);

        // ── Email Templates ──
        EmailTemplate::truncate();
        EmailTemplate::create(['name' => 'Welcome Email', 'subject' => 'Welcome to MannaPOS!', 'code' => 'welcome', 'body' => '<h1>Welcome {{name}}!</h1><p>Thank you for choosing MannaPOS. We are excited to have you on board.</p><p>To get started, please verify your email address by clicking the link below:</p><p><a href="{{verification_link}}">Verify Email</a></p>', 'variables' => ['name', 'verification_link'], 'category' => 'onboarding', 'is_active' => true]);
        EmailTemplate::create(['name' => 'Password Reset', 'subject' => 'Reset Your Password', 'code' => 'password_reset', 'body' => '<h1>Password Reset Request</h1><p>Click the link below to reset your password:</p><p><a href="{{reset_link}}">Reset Password</a></p><p>This link expires in 60 minutes.</p>', 'variables' => ['reset_link'], 'category' => 'security', 'is_active' => true]);
        EmailTemplate::create(['name' => 'Invoice Notification', 'subject' => 'Your Invoice #{{invoice_number}}', 'code' => 'invoice_notification', 'body' => '<h1>Invoice Ready</h1><p>Dear {{name}},</p><p>Your invoice #{{invoice_number}} for {{amount}} {{currency}} is now available.</p><p>Due Date: {{due_date}}</p>', 'variables' => ['invoice_number', 'name', 'amount', 'currency', 'due_date'], 'category' => 'billing', 'is_active' => true]);
        EmailTemplate::create(['name' => 'Subscription Expiry Warning', 'subject' => 'Your Subscription is Expiring', 'code' => 'subscription_expiry', 'body' => '<h1>Subscription Expiring Soon</h1><p>Dear {{name}},</p><p>Your {{plan}} subscription will expire on {{expiry_date}}.</p><p><a href="{{renewal_link}}">Renew Now</a> to avoid service interruption.</p>', 'variables' => ['name', 'plan', 'expiry_date', 'renewal_link'], 'category' => 'billing', 'is_active' => true]);
        EmailTemplate::create(['name' => 'Support Ticket Confirmation', 'subject' => 'Ticket #{{ticket_number}} Received', 'code' => 'ticket_confirmation', 'body' => '<h1>Support Ticket Received</h1><p>Dear {{name}},</p><p>Your support ticket #{{ticket_number}} has been received. We will get back to you within 24 hours.</p>', 'variables' => ['ticket_number', 'name'], 'category' => 'support', 'is_active' => true]);

        // ── SMS Templates ──
        SmsTemplate::truncate();
        SmsTemplate::create(['name' => 'Welcome SMS', 'code' => 'welcome_sms', 'message' => 'Welcome {{name}} to MannaPOS! Your account is ready. Start selling today!', 'variables' => ['name'], 'category' => 'onboarding', 'is_active' => true]);
        SmsTemplate::create(['name' => 'Payment Confirmation', 'code' => 'payment_confirm', 'message' => 'Your payment of {{amount}} {{currency}} for invoice #{{invoice}} has been received. Thank you!', 'variables' => ['amount', 'currency', 'invoice'], 'category' => 'billing', 'is_active' => true]);
        SmsTemplate::create(['name' => 'Low Stock Alert', 'code' => 'low_stock', 'message' => 'ALERT: {{product}} is low on stock ({{qty}} remaining). Please restock soon.', 'variables' => ['product', 'qty'], 'category' => 'inventory', 'is_active' => true]);
        SmsTemplate::create(['name' => 'Subscription Renewed', 'code' => 'sub_renewed', 'message' => 'Your {{plan}} subscription has been renewed. Valid until {{expiry}}. Thank you for being a valued customer!', 'variables' => ['plan', 'expiry'], 'category' => 'billing', 'is_active' => false]);

        // ── Announcements ──
        Announcement::truncate();
        Announcement::insert([
            ['title' => 'New Feature: Bulk Price Update', 'content' => 'You can now update prices for multiple products at once from the Inventory page. This feature was requested by many of our valued customers.', 'type' => 'update', 'status' => 'published', 'scheduled_at' => Carbon::now()->subDays(7), 'expires_at' => Carbon::now()->addDays(23), 'created_by' => 2],
            ['title' => 'Scheduled Maintenance - June 20', 'content' => 'MannaPOS will undergo scheduled maintenance on June 20th from 2AM to 4AM EAT. The system will be briefly unavailable during this window.', 'type' => 'maintenance', 'status' => 'draft', 'scheduled_at' => Carbon::now()->addDays(4), 'expires_at' => null, 'created_by' => 2],
            ['title' => 'New Payment Gateway: Airtel Money', 'content' => 'We are excited to announce that Airtel Money is now available as a payment option in MannaPOS. Enable it in your payment settings.', 'type' => 'update', 'status' => 'published', 'scheduled_at' => Carbon::now()->subDays(14), 'expires_at' => null, 'created_by' => 2],
            ['title' => 'Happy Independence Day!', 'content' => 'Wishing all our Tanzanian customers a happy Independence Day! Our support team will be available as usual.', 'type' => 'holiday', 'status' => 'published', 'scheduled_at' => Carbon::now()->subMonths(3), 'expires_at' => Carbon::now()->subMonths(3)->addDay(), 'created_by' => 2],
        ]);

        // ── Backups ──
        Backup::truncate();
        Backup::insert([
            ['name' => 'Daily Backup - June 15', 'file_path' => 'backups/daily-2025-06-15.zip', 'type' => 'daily', 'size' => 128000000, 'status' => 'completed', 'notes' => 'Automated daily backup', 'created_by' => 2],
            ['name' => 'Weekly Backup - Week 24', 'file_path' => 'backups/weekly-2025-w24.zip', 'type' => 'weekly', 'size' => 892000000, 'status' => 'completed', 'notes' => 'Full system backup', 'created_by' => 2],
            ['name' => 'Manual Backup - Before Update', 'file_path' => 'backups/manual-pre-update.zip', 'type' => 'manual', 'size' => 920000000, 'status' => 'completed', 'notes' => 'Backup taken before system update v2.1.0', 'created_by' => 2],
            ['name' => 'Daily Backup - June 14', 'file_path' => 'backups/daily-2025-06-14.zip', 'type' => 'daily', 'size' => 127500000, 'status' => 'completed', 'notes' => '', 'created_by' => 2],
        ]);

        // ── Customer Groups ──
        CustomerGroup::truncate();
        CustomerGroup::insert([
            ['name' => 'Regular', 'discount' => 0, 'description' => 'Standard customers'],
            ['name' => 'Silver', 'discount' => 5, 'description' => 'Customers with 5+ purchases'],
            ['name' => 'Gold', 'discount' => 10, 'description' => 'VIP customers with 20+ purchases'],
            ['name' => 'Wholesale', 'discount' => 15, 'description' => 'Wholesale bulk buyers'],
        ]);

        // ── Discounts ──
        Discount::truncate();
        Discount::insert([
            ['name' => 'Independence Day Sale', 'amount' => 15, 'type' => 'percentage', 'starts_at' => '2025-12-07', 'ends_at' => '2025-12-10', 'status' => 'active'],
            ['name' => 'Clearance Sale', 'amount' => 30, 'type' => 'percentage', 'starts_at' => '2025-06-15', 'ends_at' => '2025-07-15', 'status' => 'active'],
            ['name' => 'Loyalty Discount', 'amount' => 2000, 'type' => 'fixed', 'starts_at' => '2025-01-01', 'ends_at' => '2025-12-31', 'status' => 'active'],
        ]);

        // ── Expense Categories ──
        ExpenseCategory::truncate();
        ExpenseCategory::insert([
            ['name' => 'Rent', 'description' => 'Shop and office rent payments'],
            ['name' => 'Utilities', 'description' => 'Electricity, water, internet bills'],
            ['name' => 'Salaries', 'description' => 'Employee salaries and wages'],
            ['name' => 'Supplies', 'description' => 'Office and operational supplies'],
            ['name' => 'Marketing', 'description' => 'Advertising and promotional costs'],
            ['name' => 'Maintenance', 'description' => 'Equipment and premises maintenance'],
            ['name' => 'Transport', 'description' => 'Transport and logistics costs'],
        ]);

        // ── Expenses ──
        Expense::truncate();
        Expense::insert([
            ['expense_category_id' => 1, 'reference' => 'EXP-001', 'expense_date' => Carbon::now()->startOfMonth(), 'amount' => 800000, 'payment_method' => 'bank_transfer', 'notes' => 'Monthly rent - Main store', 'created_by' => 2],
            ['expense_category_id' => 2, 'reference' => 'EXP-002', 'expense_date' => Carbon::now()->subDays(5), 'amount' => 120000, 'payment_method' => 'cash', 'notes' => 'Electricity bill - June', 'created_by' => 2],
            ['expense_category_id' => 2, 'reference' => 'EXP-003', 'expense_date' => Carbon::now()->subDays(5), 'amount' => 45000, 'payment_method' => 'cash', 'notes' => 'Internet bill - June', 'created_by' => 2],
            ['expense_category_id' => 4, 'reference' => 'EXP-004', 'expense_date' => Carbon::now()->subDays(10), 'amount' => 95000, 'payment_method' => 'cash', 'notes' => 'Office supplies - paper, pens, register rolls', 'created_by' => 2],
            ['expense_category_id' => 5, 'reference' => 'EXP-005', 'expense_date' => Carbon::now()->subDays(14), 'amount' => 200000, 'payment_method' => 'mpesa', 'notes' => 'Facebook and Instagram ads - June campaign', 'created_by' => 2],
            ['expense_category_id' => 3, 'reference' => 'EXP-006', 'expense_date' => Carbon::now()->startOfMonth(), 'amount' => 1500000, 'payment_method' => 'bank_transfer', 'notes' => 'Monthly salaries', 'created_by' => 2],
        ]);

        // ── Stock Adjustments ──
        StockAdjustment::truncate();
        StockAdjustment::insert([
            ['reference' => 'ADJ-001', 'adjustment_date' => Carbon::now()->subDays(7), 'type' => 'addition', 'product_id' => 1, 'quantity' => 20, 'unit_cost' => 1200, 'reason' => 'Found extra stock during inventory count', 'notes' => 'Inventory discrepancy resolved'],
            ['reference' => 'ADJ-002', 'adjustment_date' => Carbon::now()->subDays(3), 'type' => 'subtraction', 'product_id' => 6, 'quantity' => 2, 'unit_cost' => 2500, 'reason' => 'Damaged goods - toothpaste boxes crushed', 'notes' => 'Written off as damaged'],
            ['reference' => 'ADJ-003', 'adjustment_date' => Carbon::now()->subDays(1), 'type' => 'subtraction', 'product_id' => 4, 'quantity' => 1, 'unit_cost' => 15000, 'reason' => 'Sample given to customer for tasting', 'notes' => 'Marketing sample'],
        ]);

        // ── Stock Transfers ──
        StockTransfer::truncate();
        StockTransfer::insert([
            ['reference' => 'TRF-001', 'from_location' => 'Main Warehouse', 'to_location' => 'Mwamba Store', 'transfer_date' => Carbon::now()->subDays(10), 'status' => 'completed', 'notes' => 'Replenishment for weekend sales'],
            ['reference' => 'TRF-002', 'from_location' => 'Mwamba Store', 'to_location' => 'Kileo Wholesalers', 'transfer_date' => Carbon::now()->subDays(5), 'status' => 'pending', 'notes' => 'Bulk transfer - rice and cooking oil'],
            ['reference' => 'TRF-003', 'from_location' => 'Main Warehouse', 'to_location' => 'Mama Pita Cafe', 'transfer_date' => Carbon::now()->subDays(2), 'status' => 'pending', 'notes' => 'Weekly supply delivery'],
        ]);

        // ── Tax Rates ──
        TaxRate::truncate();
        TaxRate::insert([
            ['name' => 'VAT Standard', 'rate' => 18.00, 'type' => 'percentage', 'status' => 'active'],
            ['name' => 'VAT Reduced', 'rate' => 8.00, 'type' => 'percentage', 'status' => 'active'],
            ['name' => 'VAT Zero', 'rate' => 0.00, 'type' => 'percentage', 'status' => 'active'],
            ['name' => 'Service Charge', 'rate' => 10.00, 'type' => 'percentage', 'status' => 'active'],
            ['name' => 'Withholding Tax', 'rate' => 5.00, 'type' => 'percentage', 'status' => 'active'],
        ]);

        // ── Warranties ──
        Warranty::truncate();
        Warranty::insert([
            ['name' => 'Standard Electronics Warranty', 'duration' => 12, 'duration_unit' => 'months', 'description' => 'Covers manufacturing defects for electronic products'],
            ['name' => 'Extended Electronics Warranty', 'duration' => 24, 'duration_unit' => 'months', 'description' => 'Extended coverage including accidental damage'],
            ['name' => 'Furniture Warranty', 'duration' => 5, 'duration_unit' => 'years', 'description' => 'Covers structural defects for furniture items'],
            ['name' => 'No Warranty', 'duration' => 0, 'duration_unit' => 'months', 'description' => 'No warranty coverage'],
        ]);

        $this->command->info('Admin demo data seeded successfully!');
    }
}
