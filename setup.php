<?php
/**
 * InfluenceOS – One-Time Setup Script
 * 
 * Run this via browser: http://localhost/influenceos/setup.php
 * It creates the database, tables, and seeds data with proper bcrypt hashes.
 * 
 * DELETE THIS FILE after successful setup.
 */

$host = 'localhost';
$user = 'root';
$pass = '';

// Default password for all seed users
$defaultPassword = 'Password@123';
$hashedPassword = password_hash($defaultPassword, PASSWORD_BCRYPT);

echo "<h1>InfluenceOS – Setup</h1>";
echo "<pre>";

try {
    // Connect without database first
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Step 1: Run schema.sql
    echo "Step 1: Running schema.sql...\n";
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    $pdo->exec($schema);
    echo "✓ Schema created successfully.\n\n";

    // Switch to the database
    $pdo->exec("USE influenceos");

    // Step 2: Insert niche multipliers
    echo "Step 2: Inserting niche multipliers...\n";
    $stmt = $pdo->prepare("INSERT IGNORE INTO niche_multipliers (niche, multiplier) VALUES (?, ?)");
    $niches = [
        ['fashion', 1.8], ['tech', 2.0], ['gaming', 1.6], ['lifestyle', 1.5],
        ['beauty', 1.9], ['fitness', 1.7], ['food', 1.4], ['travel', 1.6],
        ['finance', 2.5], ['education', 1.3], ['other', 1.0]
    ];
    foreach ($niches as $n) {
        $stmt->execute($n);
    }
    echo "✓ Niche multipliers inserted.\n\n";

    // Step 3: Insert users with real bcrypt hashes
    echo "Step 3: Inserting seed users (password: $defaultPassword)...\n";
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $users = [
        ['Admin User',      'admin@influenceos.com', $hashedPassword, 'admin'],
        ['Riya Sharma',     'riya@agency.com',       $hashedPassword, 'agency'],
        ['Ankit Verma',     'ankit@creator.com',     $hashedPassword, 'creator'],
        ['Priya Kapoor',    'priya@creator.com',     $hashedPassword, 'creator'],
        ['TechBrand India', 'brand@techbrand.com',   $hashedPassword, 'brand'],
    ];
    foreach ($users as $u) {
        $stmt->execute($u);
    }
    echo "✓ 5 users inserted (all passwords: $defaultPassword).\n\n";

    // Step 4: Insert creator profiles
    echo "Step 4: Inserting creator profiles...\n";
    $stmt = $pdo->prepare("INSERT IGNORE INTO creators (user_id, handle, platform, followers, prev_followers, avg_views, niche, content_freq, audience_age, audience_gender, audience_country, bio, is_public, public_slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([3, '@ankit_tech',    'youtube',   250000, 230000, 45000, 'tech',    8,  '18-34', '72% Male',   'India', 'Tech reviewer and gadget enthusiast from Mumbai.', 1, 'ankit-tech']);
    $stmt->execute([4, '@priya_fashion', 'instagram', 180000, 165000, 22000, 'fashion', 12, '18-30', '85% Female', 'India', 'Fashion creator and lifestyle blogger from Delhi.',  1, 'priya-fashion']);
    echo "✓ 2 creator profiles inserted.\n\n";

    // Step 5: Insert analytics
    echo "Step 5: Inserting analytics records...\n";
    $stmt = $pdo->prepare("INSERT IGNORE INTO analytics (creator_id, record_date, likes, comments, shares, saves, reach, engagement_rate, growth_rate, save_share_ratio, consistency_score, authenticity_score, performance_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $today = date('Y-m-d');
    $minus15 = date('Y-m-d', strtotime('-15 days'));
    $minus30 = date('Y-m-d', strtotime('-30 days'));
    $stmt->execute([1, $minus30, 12000, 850, 620, 430, 90000, 5.39, 8.70, 1.17, 88.00, 91.00, 73.68]);
    $stmt->execute([1, $minus15, 13500, 920, 700, 510, 95000, 5.65, 8.70, 1.27, 90.00, 91.00, 75.28]);
    $stmt->execute([1, $today,   14200, 980, 760, 550, 98000, 5.88, 8.70, 1.31, 92.00, 91.00, 76.47]);
    $stmt->execute([2, $minus30,  9000, 620, 410, 380, 72000, 5.57, 9.09, 1.08, 92.00, 88.00, 74.52]);
    $stmt->execute([2, $today,   10200, 710, 470, 430, 80000, 6.32, 9.09, 1.22, 95.00, 88.00, 77.02]);
    echo "✓ 5 analytics records inserted.\n\n";

    // Step 6: Insert campaigns
    echo "Step 6: Inserting campaigns...\n";
    $stmt = $pdo->prepare("INSERT IGNORE INTO campaigns (agency_user_id, brand_name, name, description, budget, kpi_reach, kpi_impressions, kpi_clicks, kpi_conversions, kpi_engagement, start_date, end_date, status, actual_revenue) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([2, 'TechBrand India', 'TechBrand Q1 Launch',   'Product launch campaign for the new TechBrand X1 smartphone', 150000.00, 500000, 1200000, 25000, 1500, 30000, date('Y-m-d', strtotime('-20 days')), date('Y-m-d', strtotime('+10 days')), 'active', 280000.00]);
    $stmt->execute([2, 'FashionHub',      'Summer Collection 2025', 'Summer fashion campaign targeting young women 18–30',          80000.00,  300000,  750000, 15000,  900, 20000, date('Y-m-d', strtotime('-5 days')),  date('Y-m-d', strtotime('+25 days')), 'active', 0.00]);
    echo "✓ 2 campaigns inserted.\n\n";

    // Step 7: Insert campaign_creators
    echo "Step 7: Inserting campaign-creator assignments...\n";
    $stmt = $pdo->prepare("INSERT IGNORE INTO campaign_creators (campaign_id, creator_id, fit_score, deliverable_status, deliverable_url, actual_reach, actual_engagement, actual_conversions) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 1, 87.50, 'approved',  'https://youtube.com/watch?v=demo123',  310000, 18200, 920]);
    $stmt->execute([2, 2, 82.00, 'submitted', 'https://instagram.com/p/demo456',       95000,  6100,   0]);
    echo "✓ 2 campaign-creator assignments inserted.\n\n";

    // Step 8: Insert deals
    echo "Step 8: Inserting deals...\n";
    $stmt = $pdo->prepare("INSERT IGNORE INTO deals (creator_id, agency_user_id, brand_name, campaign_id, deal_amount, commission_pct, payment_status, due_date, paid_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 2, 'TechBrand India', 1, 45000.00, 15.00, 'paid',    date('Y-m-d', strtotime('-10 days')), date('Y-m-d', strtotime('-5 days'))]);
    $stmt->execute([2, 2, 'FashionHub',      2, 28000.00, 15.00, 'pending', date('Y-m-d', strtotime('+15 days')), null]);
    echo "✓ 2 deals inserted.\n\n";

    // Step 9: Insert invoices
    echo "Step 9: Inserting invoices...\n";
    $stmt = $pdo->prepare("INSERT IGNORE INTO invoices (deal_id, invoice_number, amount, issued_date, due_date, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 'INV-2025-001', 45000.00, date('Y-m-d', strtotime('-20 days')), date('Y-m-d', strtotime('-10 days')), 'paid']);
    $stmt->execute([2, 'INV-2025-002', 28000.00, $today, date('Y-m-d', strtotime('+15 days')), 'sent']);
    echo "✓ 2 invoices inserted.\n\n";

    // Step 10: Insert activity log
    echo "Step 10: Inserting activity log...\n";
    $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, module, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([1, 'Admin logged in',                'auth',      '127.0.0.1']);
    $stmt->execute([2, 'Created campaign: TechBrand Q1', 'campaigns', '127.0.0.1']);
    $stmt->execute([3, 'Updated creator profile',        'creators',  '127.0.0.1']);
    $stmt->execute([2, 'Generated invoice INV-2025-001', 'finance',   '127.0.0.1']);
    echo "✓ 4 activity logs inserted.\n\n";

    // Validation
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "VALIDATION:\n";
    $count = $pdo->query("SELECT COUNT(*) as c FROM users")->fetch();
    echo "Users: {$count['c']} (expected: 5) " . ($count['c'] == 5 ? "✓" : "✗") . "\n";
    $count = $pdo->query("SELECT COUNT(*) as c FROM analytics")->fetch();
    echo "Analytics: {$count['c']} (expected: 5) " . ($count['c'] == 5 ? "✓" : "✗") . "\n";
    $count = $pdo->query("SELECT COUNT(*) as c FROM creators")->fetch();
    echo "Creators: {$count['c']} (expected: 2) " . ($count['c'] == 2 ? "✓" : "✗") . "\n";
    $count = $pdo->query("SELECT COUNT(*) as c FROM campaigns")->fetch();
    echo "Campaigns: {$count['c']} (expected: 2) " . ($count['c'] == 2 ? "✓" : "✗") . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "✅ SETUP COMPLETE!\n";
    echo "All seed users use password: $defaultPassword\n";
    echo "Login credentials:\n";
    echo "  Admin:   admin@influenceos.com\n";
    echo "  Agency:  riya@agency.com\n";
    echo "  Creator: ankit@creator.com\n";
    echo "  Creator: priya@creator.com\n";
    echo "  Brand:   brand@techbrand.com\n\n";
    echo "⚠️  DELETE THIS FILE (setup.php) after setup!\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
