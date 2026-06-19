<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BlogSeeder extends Seeder
{
    public function run()
    {
        // Truncate to avoid duplicate slug errors on re-seed (SQLite-safe)
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        Blog::truncate();
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $posts = [
            [
                'title'       => '5 Smart Ways to Manage Inventory and Never Run Out of Stock',
                'category'    => 'Inventory',
                'excerpt'     => 'Learn how modern POS systems can automate reordering, track supplier deliveries, and keep your shelves perfectly stocked at all times.',
                'cover_image' => 'https://images.unsplash.com/photo-1556740738-b6a63e27c4df?w=1200&auto=format&fit=crop&q=80',
                'author_name' => 'Amina Hassan',
                'author_avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&auto=format&fit=crop&q=60',
                'author_title' => 'Business Operations Expert',
                'read_time'   => 8,
                'published_at' => Carbon::now()->subDays(2),
                'content'     => '<p class="lead">Running out of stock is one of the costliest mistakes a retail business can make. Not only do you lose the immediate sale, you also risk losing that customer to a competitor — permanently. With MannaPOS, you can set this problem aside for good.</p>

<h2>1. Set Automatic Low-Stock Alerts</h2>
<p>MannaPOS lets you define a minimum stock threshold for every product. When stock drops below that level, the system instantly notifies you via the dashboard and email. No more manually checking shelves every day.</p>

<h2>2. Use Real-Time Stock Tracking</h2>
<p>Every sale, return, and stock adjustment is reflected immediately across all your devices. Whether you have one shop or five branches, you always know exactly how many units you have.</p>

<h2>3. Automate Purchase Orders</h2>
<p>When a product hits its reorder point, MannaPOS can automatically generate a draft purchase order for your supplier. All you have to do is review and approve — saving hours of manual work every week.</p>

<h2>4. Analyse Sales Velocity</h2>
<p>The analytics dashboard shows you which products sell fastest on which days. Use this data to order more before busy periods like weekends, holidays, or local events.</p>

<h2>5. Conduct Regular Stock Audits</h2>
<p>Use the built-in stock-take feature to scan and reconcile physical stock with system records. Spot shrinkage early and keep your inventory data accurate.</p>

<p>Inventory management does not have to be stressful. With the right system in place, you can focus on growing your business instead of worrying about empty shelves. <strong>Start your free MannaPOS trial today</strong> and experience the difference.</p>',
            ],
            [
                'title'       => 'How Mobile Payments Are Changing Retail in East Africa',
                'category'    => 'Payments',
                'excerpt'     => 'From M-Pesa to Tigopesa — discover how accepting mobile money can increase your sales and attract more customers to your store.',
                'cover_image' => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=1200&auto=format&fit=crop&q=80',
                'author_name' => 'David Mwangi',
                'author_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&auto=format&fit=crop&q=60',
                'author_title' => 'Fintech & Payments Writer',
                'read_time'   => 6,
                'published_at' => Carbon::now()->subDays(7),
                'content'     => '<p class="lead">Mobile money has revolutionised the way East Africans pay for goods and services. With over 60% of transactions in Tanzania now happening via mobile money, retailers who do not accept these payments are leaving serious money on the table.</p>

<h2>The Mobile Money Revolution</h2>
<p>M-Pesa, Airtel Money, Tigopesa, and HaloPesa have collectively onboarded millions of users who prefer cashless transactions. These are not just urban consumers — mobile money penetration extends deep into rural markets.</p>

<h2>Benefits of Accepting Mobile Payments</h2>
<ul>
<li><strong>No change problems:</strong> Eliminate the daily headache of not having enough small bills.</li>
<li><strong>Faster checkouts:</strong> A mobile payment takes seconds — less time per customer means more customers served.</li>
<li><strong>Automatic records:</strong> Every mobile payment is automatically recorded in MannaPOS — no manual entry needed.</li>
<li><strong>Increased customer trust:</strong> Customers feel safer paying digitally, especially for larger purchases.</li>
</ul>

<h2>How MannaPOS Handles Mobile Payments</h2>
<p>MannaPOS integrates directly with all major mobile money networks. When a customer pays, the transaction is confirmed in real time and recorded against the sale automatically. Your end-of-day reconciliation becomes a breeze.</p>

<p>The future of retail payments in Africa is mobile. Make sure your business is ready.</p>',
            ],
            [
                'title'       => 'Using Sales Data to Make Smarter Business Decisions Every Day',
                'category'    => 'Analytics',
                'excerpt'     => 'Your POS holds a goldmine of insights. Here is how to read your reports, spot your best sellers, and grow your profits consistently.',
                'cover_image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&auto=format&fit=crop&q=80',
                'author_name' => 'Grace Omondi',
                'author_avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=80&auto=format&fit=crop&q=60',
                'author_title' => 'Data Analyst & Business Consultant',
                'read_time'   => 10,
                'published_at' => Carbon::now()->subDays(14),
                'content'     => '<p class="lead">Data is the new currency of business. Every transaction processed through your MannaPOS system generates valuable intelligence — but only if you know how to use it. Here is a practical guide to turning raw sales numbers into business growth.</p>

<h2>Understanding Your Sales Reports</h2>
<p>MannaPOS generates daily, weekly, and monthly sales summaries automatically. The key metrics to track are: total revenue, average transaction value, number of transactions, and gross profit margin.</p>

<h2>Identifying Your Best Sellers</h2>
<p>Sort your product list by quantity sold and by revenue generated — these are often different products. Your high-quantity sellers drive foot traffic; your high-revenue sellers drive profit. You need both.</p>

<h2>Spotting Slow-Moving Stock</h2>
<p>Products that have not sold in 30 days are costing you storage space and tying up capital. Use the dead-stock report to identify these items and run targeted promotions to move them out.</p>

<h2>Peak Hours and Staff Planning</h2>
<p>The hourly sales breakdown shows you exactly when your shop is busiest. Use this to schedule your best staff during peak hours and reduce costs during slow periods.</p>

<h2>Customer Retention Analysis</h2>
<p>Track how many of your customers are repeat buyers versus first-time visitors. If your repeat rate is below 40%, you need a stronger loyalty programme. MannaPOS makes it easy to set one up in minutes.</p>',
            ],
            [
                'title'       => 'How to Build a Loyal Customer Base for Your Retail Business',
                'category'    => 'Customer Management',
                'excerpt'     => 'Customer loyalty programmes can increase revenue by 25%. Learn how to set one up with MannaPOS and keep your customers coming back.',
                'cover_image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1200&auto=format&fit=crop&q=80',
                'author_name' => 'Sarah Kimani',
                'author_avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=80&auto=format&fit=crop&q=60',
                'author_title' => 'Retail Marketing Strategist',
                'read_time'   => 7,
                'published_at' => Carbon::now()->subDays(21),
                'content'     => '<p class="lead">Acquiring a new customer costs five times more than retaining an existing one. Yet most small businesses spend 80% of their marketing budget chasing new customers. With MannaPOS, you have all the tools you need to build fierce customer loyalty.</p>

<h2>The Power of a Customer Profile</h2>
<p>When you create a customer profile in MannaPOS, you capture their name, contact details, and purchase history. Over time, this profile becomes a powerful tool for personalised marketing.</p>

<h2>Setting Up a Points Loyalty Programme</h2>
<p>MannaPOS supports a built-in points system. For every shilling spent, customers earn points they can redeem on future purchases. This simple mechanic dramatically increases visit frequency and basket size.</p>

<h2>Birthday and Anniversary Rewards</h2>
<p>Use the customer database to send personalised offers on birthdays and purchase anniversaries. Customers who receive birthday messages spend an average of 24% more in that visit.</p>

<h2>Targeted Promotions</h2>
<p>Segment your customers by purchase history and send relevant promotions. If a customer always buys beverages, offer them a discount on a new beverage line. Relevance is everything in modern marketing.</p>',
            ],
            [
                'title'       => 'Point of Sale Security: Protecting Your Business and Customer Data',
                'category'    => 'Security',
                'excerpt'     => 'Cyber threats are real and growing. Learn how MannaPOS protects your business with bank-grade security features built for retail.',
                'cover_image' => 'https://images.unsplash.com/photo-1555949963-ff9fe0c870eb?w=1200&auto=format&fit=crop&q=80',
                'author_name' => 'James Otieno',
                'author_avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=80&auto=format&fit=crop&q=60',
                'author_title' => 'Cybersecurity Specialist',
                'read_time'   => 9,
                'published_at' => Carbon::now()->subDays(28),
                'content'     => '<p class="lead">A data breach can destroy a small business overnight — not just financially, but reputationally. In 2024, 43% of cyber attacks targeted small businesses. Understanding how to protect your POS system is no longer optional; it is essential.</p>

<h2>Role-Based Access Control</h2>
<p>Not every employee needs access to everything. MannaPOS lets you define exactly what each user can see and do. Cashiers can process sales but cannot view financial reports. Managers can view reports but cannot change system settings. This principle of least privilege dramatically reduces your risk exposure.</p>

<h2>End-to-End Encryption</h2>
<p>All data transmitted between MannaPOS terminals and our servers is encrypted using TLS 1.3 — the same protocol used by major banks. Your customer payment data is never stored in plain text.</p>

<h2>Automatic Backups</h2>
<p>MannaPOS backs up your entire database to secure cloud storage every hour. Even if your hardware is stolen or damaged, your sales history, customer data, and inventory records are safe and recoverable within minutes.</p>

<h2>Audit Trails</h2>
<p>Every action in MannaPOS is logged with a timestamp and user ID. If something goes wrong — a suspicious void, a price change, a deleted record — you can trace exactly who did what and when.</p>',
            ],
            [
                'title'       => 'Multi-Branch Management: Running Multiple Stores from One Dashboard',
                'category'    => 'Operations',
                'excerpt'     => 'Expanding to a second or third location? MannaPOS makes multi-branch management effortless with real-time sync across all your shops.',
                'cover_image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&auto=format&fit=crop&q=80',
                'author_name' => 'Peter Ndege',
                'author_avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=80&auto=format&fit=crop&q=60',
                'author_title' => 'Retail Expansion Consultant',
                'read_time'   => 8,
                'published_at' => Carbon::now()->subDays(35),
                'content'     => '<p class="lead">Opening a second branch is an exciting milestone — but it introduces new complexity. Suddenly you are managing two sets of staff, two stock rooms, and two cash registers. Without the right system, it can quickly become overwhelming. MannaPOS was built for exactly this challenge.</p>

<h2>Centralised Dashboard</h2>
<p>From a single login, see the sales, stock levels, and performance of every branch in real time. Compare branches side by side, spot underperformers, and make decisions backed by live data.</p>

<h2>Shared Product Catalogue</h2>
<p>Maintain one master product list that syncs across all branches automatically. Update a price or add a new product once, and it instantly applies everywhere. No more tedious manual updates at each location.</p>

<h2>Inter-Branch Stock Transfers</h2>
<p>When one branch is overstocked and another is running low, you can initiate a stock transfer directly in MannaPOS. The system records the movement, updates both locations, and generates a transfer receipt.</p>

<h2>Branch-Level Permissions</h2>
<p>Branch managers see only their own branch data. Head office can see everything. This structure keeps sensitive financial data secure while giving each manager the information they need to do their job.</p>',
            ],
            [
                'title'       => 'How to Choose the Right POS System for Your Business in 2025',
                'category'    => 'Guide',
                'excerpt'     => 'With dozens of POS options available, making the wrong choice is costly. Here is a practical framework for evaluating and selecting the perfect system.',
                'cover_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&auto=format&fit=crop&q=80',
                'author_name' => 'Fatuma Ally',
                'author_avatar' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=80&auto=format&fit=crop&q=60',
                'author_title' => 'Technology Advisor for SMEs',
                'read_time'   => 12,
                'published_at' => Carbon::now()->subDays(42),
                'content'     => '<p class="lead">Choosing a POS system is one of the most important technology decisions a small business owner will make. Get it right and it becomes the backbone of your operations. Get it wrong and you waste months — and thousands of shillings — trying to fix it. Here is how to make the right choice from day one.</p>

<h2>Step 1: Define Your Non-Negotiables</h2>
<p>Before looking at any system, write down the three to five features your business absolutely cannot operate without. For most retailers, this includes inventory tracking, multiple payment methods, and sales reporting. For restaurants, it might include table management and kitchen display integration.</p>

<h2>Step 2: Evaluate Total Cost of Ownership</h2>
<p>The monthly subscription is just the starting point. Factor in hardware costs, training time, integration fees, and transaction charges. A seemingly cheap system can become expensive once all costs are included.</p>

<h2>Step 3: Test the Support Quality</h2>
<p>Send a support request to each vendor before you sign up. How quickly do they respond? Is the support available in your local language and time zone? Good support is worth paying a premium for.</p>

<h2>Step 4: Check Integration Capabilities</h2>
<p>Your POS needs to work with your accounting software, your e-commerce platform, and your payment providers. Before committing, verify that the integrations you need are available and well-supported.</p>

<h2>Step 5: Demand a Free Trial</h2>
<p>Any reputable POS vendor will offer a free trial. Use it fully — import your actual products, run test transactions, and generate reports. The goal is to simulate your real-world usage before you commit.</p>',
            ],
            [
                'title'       => 'The Complete Guide to End-of-Day Reconciliation for Retailers',
                'category'    => 'Operations',
                'excerpt'     => 'End-of-day cash-up does not have to be stressful. MannaPOS automates the reconciliation process so you can close faster and sleep easier.',
                'cover_image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=1200&auto=format&fit=crop&q=80',
                'author_name' => 'Moses Baraka',
                'author_avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=80&auto=format&fit=crop&q=60',
                'author_title' => 'Accounting & Finance Specialist',
                'read_time'   => 7,
                'published_at' => Carbon::now()->subDays(49),
                'content'     => '<p class="lead">Every retailer knows the anxiety of end-of-day cash-up. Counting the till, matching receipts, reconciling mobile payments — it is time-consuming and error-prone when done manually. MannaPOS turns a 45-minute headache into a 5-minute routine.</p>

<h2>What is Cash-Up Reconciliation?</h2>
<p>Reconciliation is the process of verifying that the physical cash in your till matches the sales recorded by your POS. Any discrepancy — whether from theft, error, or a missed entry — should be identified and investigated daily.</p>

<h2>How MannaPOS Automates the Process</h2>
<p>At the end of each shift, MannaPOS generates a detailed Closing Report that shows: total sales by payment method, expected cash in drawer, total mobile money received, voids and refunds, and staff performance breakdown.</p>

<h2>The Closing Checklist</h2>
<ol>
<li>Count physical cash and enter the amount into MannaPOS</li>
<li>The system instantly calculates the variance</li>
<li>Review any variances above your threshold</li>
<li>Print or email the daily summary report</li>
<li>Close the shift — the system locks that day\'s data</li>
</ol>

<h2>Tracking Discrepancies Over Time</h2>
<p>MannaPOS keeps a history of every closing variance. If one cashier consistently shows small shortages, that pattern becomes visible immediately — long before it becomes a serious problem.</p>',
            ],
            [
                'title'       => 'Receipt Customisation: Why Your Receipt is a Powerful Marketing Tool',
                'category'    => 'Marketing',
                'excerpt'     => 'Most businesses print generic receipts. Here is how to turn yours into a branded, customer-retaining marketing asset with MannaPOS.',
                'cover_image' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=1200&auto=format&fit=crop&q=80',
                'author_name' => 'Lilian Moshi',
                'author_avatar' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=80&auto=format&fit=crop&q=60',
                'author_title' => 'Brand & Marketing Consultant',
                'read_time'   => 5,
                'published_at' => Carbon::now()->subDays(56),
                'content'     => '<p class="lead">Your receipt is the last thing a customer sees before they leave your store. Most businesses waste this valuable touchpoint with a boring, logo-free printout. With MannaPOS, your receipt can do real marketing work — every single day.</p>

<h2>Add Your Logo and Brand Colours</h2>
<p>MannaPOS lets you upload your logo and customise the header of every receipt. A branded receipt looks professional and makes your business memorable. Customers who receive a polished receipt are more likely to trust your brand and return.</p>

<h2>Include a Promotional Message</h2>
<p>The receipt footer is prime real estate. Use it to advertise an upcoming sale, announce a new product, or remind customers of your loyalty programme. You can update this message daily without any printing costs.</p>

<h2>Add Your Social Media Handles</h2>
<p>Print your Instagram and WhatsApp Business number on every receipt. Customers who follow you on social media are far more likely to return and to recommend you to friends.</p>

<h2>QR Code for Digital Receipts</h2>
<p>MannaPOS can generate a QR code on the receipt that links to the customer\'s digital receipt online. This reduces paper waste, gives customers a permanent record, and drives traffic to your online presence.</p>',
            ],
            [
                'title'       => 'Offline Mode: How MannaPOS Keeps Working When the Internet Goes Down',
                'category'    => 'Technology',
                'excerpt'     => 'Internet outages should never stop your business. Discover how MannaPOS offline mode ensures zero downtime, no matter the connectivity.',
                'cover_image' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=1200&auto=format&fit=crop&q=80',
                'author_name' => 'Hassan Juma',
                'author_avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=80&auto=format&fit=crop&q=60',
                'author_title' => 'Software Architect, MannaPOS',
                'read_time'   => 6,
                'published_at' => Carbon::now()->subDays(63),
                'content'     => '<p class="lead">In markets where internet connectivity can be inconsistent, a cloud-only POS system is a liability. MannaPOS was designed from the ground up to work seamlessly both online and offline — because your business cannot afford to stop.</p>

<h2>How Offline Mode Works</h2>
<p>MannaPOS stores a complete copy of your product catalogue, pricing, and customer data locally on your device. When the internet drops, the system automatically switches to offline mode — no manual intervention needed, no lost sales.</p>

<h2>What Works Offline</h2>
<ul>
<li>Processing sales and accepting cash payments</li>
<li>Printing receipts</li>
<li>Applying discounts and promotions</li>
<li>Looking up customer profiles</li>
<li>Viewing current stock levels</li>
</ul>

<h2>Automatic Sync When Reconnected</h2>
<p>The moment connectivity is restored, MannaPOS automatically syncs all offline transactions to the cloud. Your reports, inventory levels, and customer data are updated within seconds — with no duplicates and no data loss.</p>

<h2>Conflict Resolution</h2>
<p>If the same product was sold at two branches while both were offline, our intelligent sync engine resolves any conflicts automatically, using timestamps and transaction IDs to ensure data integrity.</p>',
            ],
        ];

        foreach ($posts as $post) {
            Blog::create(array_merge($post, ['views' => rand(80, 2500)]));
        }
    }
}
