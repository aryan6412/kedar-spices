<?php
// Database configuration - Supports Railway Cloud MySQL & Local Development
$host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: '3307';
$db   = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'kedar_spices';
$user = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: 'Ary@n1104';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = null;

// Attempt connection with 'root' or 'Aryan' username
$usernames = ['root', 'Aryan'];
$connection_error = '';

foreach ($usernames as $username) {
    try {
        $dsn = "mysql:host=$host;port=$port;charset=$charset";
        $pdo = new PDO($dsn, $username, $pass, $options);
        break; // Stop loop if successful
    } catch (\PDOException $e) {
        $connection_error = $e->getMessage();
    }
}

if (!$pdo) {
    die("Database connection failed for both root and Aryan. Error: " . $connection_error);
}

// Bootstrap Database and Tables
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db`");
    
    // Check if table needs upgrade
    $table_check = $pdo->query("SHOW TABLES LIKE 'hero_config'");
    $table_exists = $table_check->rowCount() > 0;
    $needs_recreate = false;
    
    if ($table_exists) {
        $column_check = $pdo->query("SHOW COLUMNS FROM `hero_config` LIKE 'hero_tag'");
        if ($column_check->rowCount() == 0) {
            $needs_recreate = true;
        }
    } else {
        $needs_recreate = true;
    }
    
    if ($needs_recreate) {
        $pdo->exec("DROP TABLE IF EXISTS `hero_config`");
        $pdo->exec("CREATE TABLE `hero_config` (
            `page_name` VARCHAR(50) PRIMARY KEY,
            `hero_mode` VARCHAR(10) NOT NULL DEFAULT 'slider',
            `hero_video_url` TEXT,
            `slider_images` TEXT,
            `hero_tag` TEXT,
            `hero_title` TEXT,
            `hero_desc` TEXT
        )");
    }
    
    // Create visitor logging table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `visitor_logs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `ip_address` VARCHAR(45) NOT NULL,
        `visited_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `page_name` VARCHAR(50) NOT NULL
    )");

    // Create admin users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_users` (
        `username` VARCHAR(50) PRIMARY KEY,
        `password` VARCHAR(255) NOT NULL,
        `recovery_code` VARCHAR(100) NOT NULL
    )");
    
    // Check if email column exists in admin_users
    $col_check = $pdo->query("SHOW COLUMNS FROM `admin_users` LIKE 'email'");
    if ($col_check->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `admin_users` ADD COLUMN `email` VARCHAR(100) NOT NULL DEFAULT 'ap8307655@gmail.com'");
    }

    // Create password resets table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `password_resets` (
        `email` VARCHAR(100) NOT NULL,
        `token` VARCHAR(100) PRIMARY KEY,
        `expires_at` TIMESTAMP NOT NULL
    )");
    
    // Seed default admin if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM `admin_users`");
    if ($stmt->fetchColumn() == 0) {
        $default_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $insert_admin = $pdo->prepare("INSERT INTO `admin_users` (`username`, `password`, `recovery_code`, `email`) VALUES ('admin', ?, 'Ary@n1104', 'ap8307655@gmail.com')");
        $insert_admin->execute([$default_hash]);
    }
    
    // Seed default configurations if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM `hero_config`");
    if ($stmt->fetchColumn() == 0) {
        $default_slides = json_encode([
            'https://kedarnathspices.com/storage/img/slider/Slider1_1707475446.jpg',
            'https://kedarnathspices.com/storage/img/slider/Slider2_1707734572.jpg',
            'https://kedarnathspices.com/storage/img/slider/Slider4_1707734586.jpg',
            'https://kedarnathspices.com/storage/img/slider/Slider5_1707482259.jpg',
            'https://kedarnathspices.com/storage/img/slider/Slider6_1707734594.jpg'
        ]);
        $default_video = 'https://assets.mixkit.co/videos/preview/mixkit-spices-falling-on-a-surface-40436-large.mp4';
        
        $insert_stmt = $pdo->prepare("INSERT INTO `hero_config` (`page_name`, `hero_mode`, `hero_video_url`, `slider_images`, `hero_tag`, `hero_title`, `hero_desc`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        // Home
        $insert_stmt->execute([
            'home', 
            'video', 
            $default_video, 
            $default_slides, 
            'Spice Capital Unjha, Gujarat', 
            'Authentic Flavors, Direct From The Seed.', 
            'A family-owned legacy since 2019, providing a premium assortment of organic seeds, aromatic spices, and traditional herbs selected from every corner of India.'
        ]);
        
        // Products
        $insert_stmt->execute([
            'products', 
            'slider', 
            $default_video, 
            $default_slides, 
            'Kedarnath Catalogue', 
            'Our Spice Range', 
            'Discover 100% pure, traceable, and export-grade agricultural seeds, aromatic spices, and natural herbs direct from Unjha.'
        ]);
        
        // About Us
        $insert_stmt->execute([
            'about', 
            'slider', 
            $default_video, 
            $default_slides, 
            'Organic Heritage', 
            'Our Rich Legacy', 
            'Learn about our commitment to sustainable farming, pure spice processing, and global export standards since 2019.'
        ]);
        
        // Gallery
        $insert_stmt->execute([
            'gallery', 
            'slider', 
            $default_video, 
            $default_slides, 
            'Media Showcase', 
            'Processing Plants & Farms', 
            'Visual highlights of our state-of-the-art grading machines, seed cleaning units, and natural Indian spice farms.'
        ]);
        
        // Contact
        $insert_stmt->execute([
            'contact', 
            'slider', 
            $default_video, 
            $default_slides, 
            'Get In Touch', 
            'Contact Our Export Desk', 
            'Ready to source the finest spices? Contact our experts to receive a customized quote or trace crops.'
        ]);
        
        // IPM
        $insert_stmt->execute([
            'ipm', 
            'slider', 
            $default_video, 
            $default_slides, 
            'Integrated Pest Management • 5 Stages', 
            "From Farm to Table —\n5 Stage Pure IPM", 
            'Trace our complete 5-stage Integrated Pest Management journey using eco-friendly biological control, solar light traps, and 0.00% chemical residue guarantee.'
        ]);
    }

    // Create products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `products` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `category` VARCHAR(50) NOT NULL,
        `name` VARCHAR(150) NOT NULL,
        `img1` TEXT,
        `img2` TEXT,
        `desc` TEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Seed default products if table is empty
    $prod_count_stmt = $pdo->query("SELECT COUNT(*) FROM `products`");
    if ($prod_count_stmt->fetchColumn() == 0) {
        $default_products = [
            ['category' => 'spices', 'name' => 'Cumin (Jeera)', 'img1' => 'https://kedarnathspices.com/storage/img/product/CuminWhole(Jeera)_1707732502.png', 'img2' => 'https://kedarnathspices.com/storage/img/product/CuminWhole(Jeera)1_1707739303.png', 'desc' => 'High quality premium Indian cumin seeds, rich in flavor and aroma.'],
            ['category' => 'spices', 'name' => 'Fennel (Saunf)', 'img1' => 'https://kedarnathspices.com/storage/img/product/FennelWhole(Saunf)_1707732524.png', 'img2' => 'https://kedarnathspices.com/storage/img/product/FennelWhole(Saunf)1_1707741249.png', 'desc' => 'Aromatic fennel seeds selected from the best farms of Gujarat.'],
            ['category' => 'spices', 'name' => 'Coriander Seeds (Dhaniya)', 'img1' => 'https://kedarnathspices.com/storage/img/product/CorianderSeeds(Dhaniya)_1707732791.png', 'img2' => 'https://kedarnathspices.com/storage/img/product/CorianderSeeds(Dhaniya)1_1707741289.png', 'desc' => 'Premium whole coriander seeds with citrusy, warm, and sweet flavor notes.'],
            ['category' => 'spices', 'name' => 'Fenugreek', 'img1' => 'https://kedarnathspices.com/storage/img/product/FenugreekPowder_1708174845.png', 'img2' => '', 'desc' => 'Golden yellow fenugreek seeds, premium grade for diverse culinary uses.'],
            ['category' => 'herbs', 'name' => 'Psyllium Seeds', 'img1' => 'img/psyllium_seeds.jpg', 'img2' => '', 'desc' => 'Pure psyllium seeds sourced directly from Unjha market.'],
            ['category' => 'herbs', 'name' => 'Psyllium Husk', 'img1' => 'img/psyllium_husk.jpg', 'img2' => '', 'desc' => 'High-fiber psyllium husk (isabgol) processed under strict hygiene standards.'],
            ['category' => 'oilseeds', 'name' => 'Sesame Seeds (Til)', 'img1' => 'img/sesame_seeds.jpg', 'img2' => '', 'desc' => 'Pure natural white sesame seeds, rich in oils and flavor.'],
            ['category' => 'powder', 'name' => 'Turmeric Powder (Haldi)', 'img1' => 'img/turmeric_powder.jpg', 'img2' => '', 'desc' => 'Rich golden turmeric powder with high curcumin content.'],
            ['category' => 'powder', 'name' => 'Red Chilli Powder (Mirch)', 'img1' => 'img/red_chilli_powder.jpg', 'img2' => '', 'desc' => 'Vibrant red, hot, and spicy chili powder sourced from prime crops.']
        ];
        $ins_prod = $pdo->prepare("INSERT INTO `products` (`category`, `name`, `img1`, `img2`, `desc`) VALUES (?, ?, ?, ?, ?)");
        foreach ($default_products as $p) {
            $ins_prod->execute([$p['category'], $p['name'], $p['img1'], $p['img2'], $p['desc']]);
        }
    }

    // Create categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `categories` (
        `slug` VARCHAR(50) PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `icon` TEXT,
        `desc` TEXT,
        `bg` VARCHAR(20) DEFAULT '#FFF5E6'
    )");

    // Seed default categories if empty
    $cat_count_stmt = $pdo->query("SELECT COUNT(*) FROM `categories`");
    if ($cat_count_stmt->fetchColumn() == 0) {
        $default_cats = [
            ['slug' => 'spices', 'name' => 'Spices', 'icon' => 'https://kedarnathspices.com/storage/img/category/Spices_1707477449.png', 'desc' => 'Cleaned, graded, and export-quality whole seeds and pods selected from prime organic farms of Gujarat and Rajasthan.', 'bg' => '#FFF5E6'],
            ['slug' => 'herbs', 'name' => 'Herbs', 'icon' => 'https://kedarnathspices.com/storage/img/category/Herbs_1707477620.png', 'desc' => 'Pure medicinal herbs and high-grade dietary psyllium products processed inside the Spice City Unjha market.', 'bg' => '#E6F5F0'],
            ['slug' => 'oilseeds', 'name' => 'Oil Seeds', 'icon' => 'https://kedarnathspices.com/storage/img/category/OilSeeds_1707985765.png', 'desc' => 'High oil yield seeds including sesame seeds, cleaned and sorted for domestic culinary and international export demands.', 'bg' => '#F9F6EE'],
            ['slug' => 'powder', 'name' => 'Powder', 'icon' => 'https://kedarnathspices.com/storage/img/category/Powder_1707985814.png', 'desc' => 'Finely ground spice powders without fillers, carrying natural oils and rich vibrant coloring for commercial kitchens.', 'bg' => '#FFF0F0'],
            ['slug' => 'roasted', 'name' => 'Roasted', 'icon' => 'https://kedarnathspices.com/storage/img/category/Roasted_1707985824.png', 'desc' => 'Slow-roasted seeds and aromatic spices, roasted at optimal temperatures to lock in rich, roasted flavors and warm notes.', 'bg' => '#FFF9E6']
        ];
        $ins_cat = $pdo->prepare("INSERT INTO `categories` (`slug`, `name`, `icon`, `desc`, `bg`) VALUES (?, ?, ?, ?, ?)");
        foreach ($default_cats as $c) {
            $ins_cat->execute([$c['slug'], $c['name'], $c['icon'], $c['desc'], $c['bg']]);
        }
    }

    // Create map_states table for interactive sourcing map
    $pdo->exec("CREATE TABLE IF NOT EXISTS `map_states` (
        `state_key` VARCHAR(50) PRIMARY KEY,
        `name` VARCHAR(150) NOT NULL,
        `region` VARCHAR(255) NOT NULL,
        `badge` VARCHAR(255) NOT NULL,
        `bg` VARCHAR(20) NOT NULL DEFAULT '#27AE60',
        `image` TEXT,
        `primary_spices` TEXT,
        `description` TEXT,
        `purity` VARCHAR(50) DEFAULT '99.0%+',
        `capacity` VARCHAR(50) DEFAULT '3,000 MT/Yr',
        `grading` VARCHAR(50) DEFAULT 'Sortex Cleaned',
        `top_percent` VARCHAR(20) DEFAULT '50%',
        `left_percent` VARCHAR(20) DEFAULT '50%'
    )");

    $top_check = $pdo->query("SHOW COLUMNS FROM `map_states` LIKE 'top_percent'");
    if ($top_check->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `map_states` ADD COLUMN `top_percent` VARCHAR(20) DEFAULT '50%'");
        $pdo->exec("ALTER TABLE `map_states` ADD COLUMN `left_percent` VARCHAR(20) DEFAULT '50%'");

        $pdo->exec("UPDATE `map_states` SET `top_percent` = '18%', `left_percent` = '37%' WHERE `state_key` = 'kashmir'");
        $pdo->exec("UPDATE `map_states` SET `top_percent` = '28%', `left_percent` = '33%' WHERE `state_key` = 'punjab'");
        $pdo->exec("UPDATE `map_states` SET `top_percent` = '37%', `left_percent` = '27%' WHERE `state_key` = 'rajasthan'");
        $pdo->exec("UPDATE `map_states` SET `top_percent` = '47%', `left_percent` = '13%' WHERE `state_key` = 'gujarat'");
        $pdo->exec("UPDATE `map_states` SET `top_percent` = '45%', `left_percent` = '39%' WHERE `state_key` = 'mp'");
        $pdo->exec("UPDATE `map_states` SET `top_percent` = '64%', `left_percent` = '45%' WHERE `state_key` = 'andhra'");
        $pdo->exec("UPDATE `map_states` SET `top_percent` = '80%', `left_percent` = '34%' WHERE `state_key` = 'kerala'");
        $pdo->exec("UPDATE `map_states` SET `top_percent` = '38%', `left_percent` = '83%' WHERE `state_key` = 'northeast'");
    }

    // Seed default map states if empty
    $map_count_stmt = $pdo->query("SELECT COUNT(*) FROM `map_states`");
    if ($map_count_stmt->fetchColumn() == 0) {
        $default_map_states = [
            'gujarat' => [
                'name' => 'Gujarat (Unjha Hub)',
                'region' => 'Western India • Main Spice Trading Capital',
                'badge' => 'Headquarters & Main Processing Mandi',
                'bg' => '#E0A838',
                'image' => 'https://kedarnathspices.com/storage/img/product/CuminWhole(Jeera)_1707732502.png',
                'primary_spices' => json_encode(['Cumin Seeds (Jeera)', 'Fennel Seeds (Saunf)', 'Psyllium Husk (Isabgol)', 'White Sesame Seeds (Til)', 'Ajwain']),
                'description' => 'Unjha is Asia’s largest spice market yard. Here we process 100% sortex-cleaned cumin, fennel, and premium grade dietary psyllium husk direct from farm auctions.',
                'purity' => '99.5%+', 'capacity' => '5,000 MT/Yr', 'grading' => 'Machine Sortex'
            ],
            'rajasthan' => [
                'name' => 'Rajasthan',
                'region' => 'North-Western India • Seed Spice Belt',
                'badge' => 'Aromatic Seeds Sourcing Zone',
                'bg' => '#D96E48',
                'image' => 'https://kedarnathspices.com/storage/img/product/CorianderSeeds(Dhaniya)_1707732549.png',
                'primary_spices' => json_encode(['Coriander Seeds (Dhaniya)', 'Fenugreek (Methi)', 'Dill Seeds (Suva)', 'Cumin Seeds']),
                'description' => 'Renowned for producing high essential oil content coriander and sun-dried fenugreek seeds with rich aroma and natural golden hue.',
                'purity' => '99.0%+', 'capacity' => '3,500 MT/Yr', 'grading' => 'Sun Cured'
            ],
            'kerala' => [
                'name' => 'Kerala (Malabar Coast)',
                'region' => 'Southern Coast • Spice Garden of India',
                'badge' => 'Tellicherry Pepper & Cardamom Hub',
                'bg' => '#1F7042',
                'image' => 'https://kedarnathspices.com/storage/img/product/1707733475.png',
                'primary_spices' => json_encode(['Tellicherry Black Pepper', 'Green Cardamom (8mm)', 'Clove Whole', 'Nutmeg', 'Star Anise']),
                'description' => 'Sourced from the lush Western Ghats, yielding world-famous bold Tellicherry black pepper and fragrant green cardamom capsules.',
                'purity' => '99.8%+', 'capacity' => '2,000 MT/Yr', 'grading' => 'TGSEB Grade'
            ],
            'kashmir' => [
                'name' => 'Kashmir',
                'region' => 'Northern Himalayas • High Altitude Valley',
                'badge' => 'Saffron & Red Chilli Specialty',
                'bg' => '#E63946',
                'image' => 'https://kedarnathspices.com/storage/img/product/RedChilliPowder(Mirch)_1707733606.png',
                'primary_spices' => json_encode(['Kashmiri Saffron (Zafran)', 'Kashmiri Red Chilli', 'Dry Ginger', 'Shahi Jeera']),
                'description' => 'Famous for intense natural crimson color without heat. Gives curries a rich ruby red texture and delicate fragrance.',
                'purity' => '100% Pure', 'capacity' => '500 MT/Yr', 'grading' => 'Lacha Saffron'
            ],
            'andhra' => [
                'name' => 'Andhra Pradesh & Telangana',
                'region' => 'Southern India • Chilli Capital Guntur',
                'badge' => 'High Heat Chilli & Turmeric',
                'bg' => '#C0392B',
                'image' => 'https://kedarnathspices.com/storage/img/product/TurmericPowder(Haldi)_1707733575.png',
                'primary_spices' => json_encode(['Guntur Red Chilli (Teja)', 'High Curcumin Turmeric (Haldi)', 'Ginger Powder']),
                'description' => 'The global epicenter for pungent Guntur chillies and deep yellow turmeric roots with high natural curcumin levels.',
                'purity' => '99.2%+', 'capacity' => '4,000 MT/Yr', 'grading' => 'ASTA Color 100+'
            ],
            'mp' => [
                'name' => 'Madhya Pradesh',
                'region' => 'Central India • Agrarian Heart',
                'badge' => 'Mustard & Garlic Belt',
                'bg' => '#F39C12',
                'image' => 'https://kedarnathspices.com/storage/img/product/YellowMustardPowder_1707733734.png',
                'primary_spices' => json_encode(['Yellow Mustard Seeds (Rai)', 'White Garlic Flakes', 'Ajwain', 'Black Sesame']),
                'description' => 'Fertile central plains supplying bold yellow mustard seeds, white garlic powder, and aromatic oilseeds.',
                'purity' => '99.0%+', 'capacity' => '3,000 MT/Yr', 'grading' => 'Sortex Bold'
            ],
            'punjab' => [
                'name' => 'Punjab & Haryana',
                'region' => 'Northern Plains • Granary Hub',
                'badge' => 'Basmati Rice & Superfoods',
                'bg' => '#F1C40F',
                'image' => 'https://kedarnathspices.com/storage/img/product/Quinoa_1707733909.png',
                'primary_spices' => json_encode(['1121 Extra Long Basmati Rice', 'Traditional Rice', 'Flax Seeds (Alsi)', 'Quinoa']),
                'description' => 'Sourcing authentic long-grain Basmati rice, premium grains, and golden flax seeds direct from northern mandis.',
                'purity' => '99.9%+', 'capacity' => '6,000 MT/Yr', 'grading' => '1121 Steam'
            ],
            'northeast' => [
                'name' => 'Assam & North-East',
                'region' => 'Eastern Himalayas • Organic Hills',
                'badge' => 'Organic Spices & Superfoods',
                'bg' => '#27AE60',
                'image' => 'https://kedarnathspices.com/storage/img/product/Chia_1707733934.png',
                'primary_spices' => json_encode(['Bhut Jolokia (Ghost Pepper)', 'Lakadong Turmeric (8%+ Curcumin)', 'Organic Ginger']),
                'description' => 'Pristine organic valley producing world-class high-curcumin Lakadong turmeric and extreme heat Ghost Pepper.',
                'purity' => '100% Organic', 'capacity' => '800 MT/Yr', 'grading' => 'Wild Harvested'
            ]
        ];
        $ins_map = $pdo->prepare("INSERT INTO `map_states` (`state_key`, `name`, `region`, `badge`, `bg`, `image`, `primary_spices`, `description`, `purity`, `capacity`, `grading`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($default_map_states as $sk => $s) {
            $ins_map->execute([$sk, $s['name'], $s['region'], $s['badge'], $s['bg'], $s['image'], $s['primary_spices'], $s['description'], $s['purity'], $s['capacity'], $s['grading']]);
        }
    }

    // Create testimonials table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `testimonials` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `role` VARCHAR(255) DEFAULT '',
        `location` VARCHAR(255) DEFAULT '',
        `avatar` TEXT,
        `badge_type` VARCHAR(100) DEFAULT 'Verified B2B',
        `rating` INT DEFAULT 5,
        `comment` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $testi_count_stmt = $pdo->query("SELECT COUNT(*) FROM `testimonials`");
    if ($testi_count_stmt->fetchColumn() == 0) {
        $default_testis = [
            [
                'name' => 'Mr. Hari Bhai',
                'role' => 'Business Owner & Exporter',
                'location' => 'Unjha, Gujarat',
                'avatar' => 'https://kedarnathspices.com/storage/img/testimonial/testimonial_1707718813.png',
                'badge_type' => 'Verified B2B',
                'rating' => 5,
                'comment' => 'I really love working with Kedarnath Spices! It gives our firm the confidence to source high-grade Cumin and Psyllium with strict quality standards at affordable prices. Highly recommended for commercial buyers.'
            ],
            [
                'name' => 'Mr. Pranav Bhai',
                'role' => 'Wholesale Merchant',
                'location' => 'Mumbai, Maharashtra',
                'avatar' => 'https://kedarnathspices.com/storage/img/testimonial/testimonial_1_1707718819.png',
                'badge_type' => 'Verified Client',
                'rating' => 5,
                'comment' => 'Love the spices I get from you guys and the customer service is really helpful! Thanks for providing such a wonderful experience, purity compliance, and fast container delivery.'
            ],
            [
                'name' => 'Rajesh Patel',
                'role' => 'Food Processing Director',
                'location' => 'Ahmedabad, Gujarat',
                'avatar' => '',
                'badge_type' => 'Global Buyer',
                'rating' => 5,
                'comment' => 'Finding lab-tested, pesticide-compliant spices at competitive B2B prices used to be tough. Kedarnath Spices made our supply chain smooth, reliable, and transparent.'
            ]
        ];
        $ins_testi = $pdo->prepare("INSERT INTO `testimonials` (`name`, `role`, `location`, `avatar`, `badge_type`, `rating`, `comment`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($default_testis as $t) {
            $ins_testi->execute([$t['name'], $t['role'], $t['location'], $t['avatar'], $t['badge_type'], $t['rating'], $t['comment']]);
        }
    }
} catch (\PDOException $e) {
    die("Database initialization failed: " . $e->getMessage());
}

/**
 * Fetch hero configuration for a specific page.
 */
function get_hero_config($page_name = 'home') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM `hero_config` WHERE `page_name` = ?");
        $stmt->execute([$page_name]);
        $row = $stmt->fetch();
        if ($row) {
            return [
                'mode' => $row['hero_mode'],
                'video_url' => $row['hero_video_url'],
                'slider_images' => json_decode($row['slider_images'], true) ?: [],
                'tag' => $row['hero_tag'],
                'title' => $row['hero_title'],
                'desc' => $row['hero_desc']
            ];
        }
    } catch (\PDOException $e) {
        // Fallback
    }
    return [
        'mode' => 'slider',
        'video_url' => 'https://assets.mixkit.co/videos/preview/mixkit-spices-falling-on-a-surface-40436-large.mp4',
        'slider_images' => [
            'https://kedarnathspices.com/storage/img/slider/Slider1_1707475446.jpg'
        ],
        'tag' => 'Default Tag',
        'title' => 'Default Title',
        'desc' => 'Default Description'
    ];
}

/**
 * Update hero configuration for a specific page.
 */
function update_hero_config_for_page($page_name, $mode, $video_url, $slider_images, $tag, $title, $desc) {
    global $pdo;
    try {
        $slider_images_json = json_encode(array_values(filter_cleaned_array_for_page($slider_images)));
        $stmt = $pdo->prepare("UPDATE `hero_config` SET `hero_mode` = ?, `hero_video_url` = ?, `slider_images` = ?, `hero_tag` = ?, `hero_title` = ?, `hero_desc` = ? WHERE `page_name` = ?");
        return $stmt->execute([$mode, $video_url, $slider_images_json, $tag, $title, $desc, $page_name]);
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Log a user's page visit to the database.
 * Throttles rapid double-clicks (within 2 seconds) while logging all live user visits.
 */
function log_visitor_visit($page_name) {
    global $pdo;
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if ($ip === '::1') {
            $ip = '127.0.0.1';
        }
        $page_name = strtolower(trim($page_name));
        
        // Check if this IP loaded this page in the last 2 seconds (to prevent double-click duplicates)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `visitor_logs` WHERE `ip_address` = ? AND `page_name` = ? AND `visited_at` > NOW() - INTERVAL 2 SECOND");
        $stmt->execute([$ip, $page_name]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO `visitor_logs` (`ip_address`, `page_name`) VALUES (?, ?)");
            $stmt->execute([$ip, $page_name]);
        }
    } catch (\PDOException $e) {
        // Fail silently
    }
}

/**
 * Fetch total page view count.
 */
function get_total_visits() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `visitor_logs`");
        return (int)$stmt->fetchColumn();
    } catch (\PDOException $e) {
        return 0;
    }
}

/**
 * Fetch unique daily visitors count for today (last 24 hours).
 */
function get_today_unique_visitors() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(DISTINCT `ip_address`) FROM `visitor_logs` WHERE `visited_at` >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        return (int)$stmt->fetchColumn();
    } catch (\PDOException $e) {
        return 0;
    }
}

/**
 * Fetch visitor counts for the last $days days.
 */
function get_daily_visitor_stats($days = 7) {
    global $pdo;
    try {
        $stats = [];
        // Pre-fill last $days dates with 0
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $stats[$date] = 0;
        }
        
        // Fetch counts from DB
        $stmt = $pdo->prepare("SELECT DATE(`visited_at`) as visit_date, COUNT(DISTINCT `ip_address`) as visit_count FROM `visitor_logs` WHERE `visited_at` >= DATE_SUB(CURDATE(), INTERVAL ? DAY) GROUP BY visit_date");
        $stmt->execute([$days]);
        while ($row = $stmt->fetch()) {
            $date = $row['visit_date'];
            if (isset($stats[$date])) {
                $stats[$date] = (int)$row['visit_count'];
            }
        }
        return $stats;
    } catch (\PDOException $e) {
        return [];
    }
}

/**
 * Fetch total page view counts grouped by page name.
 */
function get_page_visitor_stats() {
    global $pdo;
    try {
        $stats = [
            'home' => 0,
            'products' => 0,
            'about' => 0,
            'ipm' => 0
        ];
        
        $stmt = $pdo->query("SELECT `page_name`, COUNT(*) as visit_count FROM `visitor_logs` GROUP BY `page_name`");
        while ($row = $stmt->fetch()) {
            $page = strtolower(trim($row['page_name']));
            $stats[$page] = (int)$row['visit_count'];
        }
        return $stats;
    } catch (\PDOException $e) {
        return ['home' => 0, 'products' => 0, 'about' => 0, 'ipm' => 0];
    }
}

/**
 * Helper to clean array elements.
 */
function filter_cleaned_array_for_page($arr) {
    $cleaned = [];
    if (!is_array($arr)) {
        return $cleaned;
    }
    foreach ($arr as $item) {
        $val = trim($item);
        if ($val !== '') {
            $cleaned[] = $val;
        }
    }
    return $cleaned;
}

/**
 * Fetch all products from MySQL DB.
 */
function get_all_products() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM `products` ORDER BY `id` DESC");
        return $stmt->fetchAll();
    } catch (\PDOException $e) {
        return [];
    }
}

/**
 * Fetch single product by ID.
 */
function get_product_by_id($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM `products` WHERE `id` = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Add a new product.
 */
function add_product($category, $name, $img1, $img2, $desc) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO `products` (`category`, `name`, `img1`, `img2`, `desc`) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$category, $name, $img1, $img2, $desc]);
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Update an existing product.
 */
function update_product($id, $category, $name, $img1, $img2, $desc) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE `products` SET `category` = ?, `name` = ?, `img1` = ?, `img2` = ?, `desc` = ? WHERE `id` = ?");
        return $stmt->execute([$category, $name, $img1, $img2, $desc, $id]);
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Delete a product by ID.
 */
function delete_product($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM `products` WHERE `id` = ?");
        return $stmt->execute([$id]);
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Fetch all categories from MySQL DB.
 */
function get_all_categories() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM `categories` ORDER BY `name` ASC");
        return $stmt->fetchAll();
    } catch (\PDOException $e) {
        return [];
    }
}

/**
 * Add a new category.
 */
function add_category($slug, $name, $icon, $desc, $bg = '#FFF5E6') {
    global $pdo;
    try {
        $clean_slug = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', $slug));
        if (empty($clean_slug)) return false;
        $stmt = $pdo->prepare("INSERT INTO `categories` (`slug`, `name`, `icon`, `desc`, `bg`) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$clean_slug, $name, $icon, $desc, $bg]);
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Update an existing category.
 */
function update_category($slug, $name, $icon, $desc, $bg = '#FFF5E6') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE `categories` SET `name` = ?, `icon` = ?, `desc` = ?, `bg` = ? WHERE `slug` = ?");
        return $stmt->execute([$name, $icon, $desc, $bg, $slug]);
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Delete a category by slug.
 */
function delete_category($slug) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM `categories` WHERE `slug` = ?");
        return $stmt->execute([$slug]);
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Fetch products grouped by category for products.php page rendering (Dynamically from DB).
 */
function get_products_grouped_by_category() {
    $all_cats = get_all_categories();
    $categories = [];
    foreach ($all_cats as $c) {
        $slug = strtolower($c['slug']);
        $categories[$slug] = [
            'name' => $c['name'],
            'icon' => $c['icon'],
            'desc' => $c['desc'],
            'bg' => $c['bg'] ?: '#FFF5E6',
            'products' => []
        ];
    }

    $all_prods = get_all_products();
    foreach ($all_prods as $p) {
        $cat = strtolower($p['category']);
        if (isset($categories[$cat])) {
            $categories[$cat]['products'][] = [
                'id' => $p['id'],
                'name' => $p['name'],
                'img1' => $p['img1'],
                'img2' => $p['img2'],
                'desc' => $p['desc']
            ];
        }
    }

    return $categories;
}

/**
 * Fetch all map states formatted as a state-keyed array for AlpineJS.
 */
function get_all_map_states() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM `map_states`");
        $rows = $stmt->fetchAll();
        $states = [];
        foreach ($rows as $r) {
            $key = $r['state_key'];
            $spices = json_decode($r['primary_spices'], true) ?: [];
            $states[$key] = [
                'name' => $r['name'],
                'region' => $r['region'],
                'badge' => $r['badge'],
                'bg' => $r['bg'],
                'image' => $r['image'],
                'primarySpices' => array_values($spices),
                'description' => $r['description'],
                'top' => $r['top_percent'] ?? '50%',
                'left' => $r['left_percent'] ?? '50%',
                'stats' => [
                    'purity' => $r['purity'],
                    'capacity' => $r['capacity'],
                    'grading' => $r['grading']
                ]
            ];
        }
        return $states;
    } catch (\PDOException $e) {
        return [];
    }
}

/**
 * Add a new map state.
 */
function add_map_state($key, $name, $region, $badge, $bg, $image, $primary_spices, $description, $purity, $capacity, $grading, $top = '50%', $left = '50%') {
    global $pdo;
    try {
        $clean_key = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $key));
        if (empty($clean_key)) return false;
        $spices_json = json_encode(array_values(array_filter(array_map('trim', (array)$primary_spices))));
        $stmt = $pdo->prepare("INSERT INTO `map_states` (`state_key`, `name`, `region`, `badge`, `bg`, `image`, `primary_spices`, `description`, `purity`, `capacity`, `grading`, `top_percent`, `left_percent`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$clean_key, $name, $region, $badge, $bg, $image, $spices_json, $description, $purity, $capacity, $grading, $top, $left]);
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Update map state details.
 */
function update_map_state($key, $name, $region, $badge, $bg, $image, $primary_spices, $description, $purity, $capacity, $grading, $top = '50%', $left = '50%') {
    global $pdo;
    try {
        $spices_json = json_encode(array_values(array_filter(array_map('trim', (array)$primary_spices))));
        $stmt = $pdo->prepare("UPDATE `map_states` SET `name` = ?, `region` = ?, `badge` = ?, `bg` = ?, `image` = ?, `primary_spices` = ?, `description` = ?, `purity` = ?, `capacity` = ?, `grading` = ?, `top_percent` = ?, `left_percent` = ? WHERE `state_key` = ?");
        return $stmt->execute([$name, $region, $badge, $bg, $image, $spices_json, $description, $purity, $capacity, $grading, $top, $left, $key]);
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Fetch all client testimonials.
 */
function get_all_testimonials() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM `testimonials` ORDER BY `id` ASC");
        return $stmt->fetchAll();
    } catch (\PDOException $e) {
        return [];
    }
}

/**
 * Add a new testimonial.
 */
function add_testimonial($name, $role, $location, $avatar, $badge_type, $rating, $comment) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO `testimonials` (`name`, `role`, `location`, `avatar`, `badge_type`, `rating`, `comment`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $role, $location, $avatar, $badge_type, (int)$rating, $comment]);
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Update an existing testimonial.
 */
function update_testimonial($id, $name, $role, $location, $avatar, $badge_type, $rating, $comment) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE `testimonials` SET `name` = ?, `role` = ?, `location` = ?, `avatar` = ?, `badge_type` = ?, `rating` = ?, `comment` = ? WHERE `id` = ?");
        return $stmt->execute([$name, $role, $location, $avatar, $badge_type, (int)$rating, $comment, (int)$id]);
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Delete a testimonial by ID.
 */
function delete_testimonial($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM `testimonials` WHERE `id` = ?");
        return $stmt->execute([(int)$id]);
    } catch (\PDOException $e) {
        return false;
    }
}

/**
 * Universal Image Upload Helper Function.
 */
if (!function_exists('handle_image_upload')) {
    function handle_image_upload($field_name) {
        if (isset($_FILES[$field_name]) && $_FILES[$field_name]['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES[$field_name]['tmp_name'];
            $clean_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES[$field_name]['name']);
            $target_dir = __DIR__ . '/uploads';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            if (move_uploaded_file($tmp, $target_dir . '/' . $clean_name)) {
                return 'uploads/' . $clean_name;
            }
        }
        return null;
    }
}


