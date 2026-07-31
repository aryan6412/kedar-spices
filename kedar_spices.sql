-- KEDARNATH SPICES & HERBS - MYSQL DATABASE EXPORT
-- Database: kedar_spices

SET FOREIGN_KEY_CHECKS = 0;

-- 1. HERO CONFIG TABLE
CREATE TABLE IF NOT EXISTS `hero_config` (
    `page_name` VARCHAR(50) PRIMARY KEY,
    `hero_mode` VARCHAR(10) NOT NULL DEFAULT 'slider',
    `hero_video_url` TEXT,
    `slider_images` TEXT,
    `hero_tag` TEXT,
    `hero_title` TEXT,
    `hero_desc` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hero_config` (`page_name`, `hero_mode`, `hero_video_url`, `slider_images`, `hero_tag`, `hero_title`, `hero_desc`) VALUES
('home', 'video', 'https://assets.mixkit.co/videos/preview/mixkit-spices-falling-on-a-surface-40436-large.mp4', '["https:\/\/kedarnathspices.com\/storage\/img\/slider\/Slider1_1707475446.jpg","https:\/\/kedarnathspices.com\/storage\/img\/slider\/Slider2_1707734572.jpg","https:\/\/kedarnathspices.com\/storage\/img\/slider\/Slider4_1707734586.jpg","https:\/\/kedarnathspices.com\/storage\/img\/slider\/Slider5_1707482259.jpg","https:\/\/kedarnathspices.com\/storage\/img\/slider\/Slider6_1707734594.jpg"]', '🔥 Premium Spices Manufacturer & Exporter', 'Pure & Premium Spices Directly From Unjha', 'Sourcing finest seed spices, ground powders, and oilseeds from India\'s spice hub.'),
('about', 'slider', '', '["https:\/\/kedarnathspices.com\/storage\/img\/slider\/Slider1_1707475446.jpg","https:\/\/kedarnathspices.com\/storage\/img\/slider\/Slider2_1707734572.jpg"]', '✨ Legacy of Purity', 'Crafting Purity Since Generations', 'Pioneers in high-purity spice processing, sorting, and worldwide bulk export.'),
('products', 'slider', '', '["https:\/\/kedarnathspices.com\/storage\/img\/slider\/Slider4_1707734586.jpg","https:\/\/kedarnathspices.com\/storage\/img\/slider\/Slider5_1707482259.jpg"]', '🌱 100% Organic & Lab Tested', 'Our Product Portfolio', 'Explore our comprehensive range of raw seed spices, ground powders, and oilseeds.'),
('contact', 'slider', '', '["https:\/\/kedarnathspices.com\/storage\/img\/slider\/Slider6_1707734594.jpg"]', '🤝 Connect With Us', 'Get in Touch for Global Inquiries', 'Reach out for bulk orders, custom packaging requirements, and international trade quotes.')
ON DUPLICATE KEY UPDATE `hero_mode` = VALUES(`hero_mode`), `hero_video_url` = VALUES(`hero_video_url`), `slider_images` = VALUES(`slider_images`);

-- 2. VISITOR LOGS TABLE
CREATE TABLE IF NOT EXISTS `visitor_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ip_address` VARCHAR(45) NOT NULL,
    `visited_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `page_name` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. ADMIN USERS TABLE
CREATE TABLE IF NOT EXISTS `admin_users` (
    `username` VARCHAR(50) PRIMARY KEY,
    `password` VARCHAR(255) NOT NULL,
    `recovery_code` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL DEFAULT 'ap8307655@gmail.com'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admin_users` (`username`, `password`, `recovery_code`, `email`) VALUES
('admin', '$2y$10$wE8wVj/4sHh2eD8g4sE8u.O9/91.u4K56M95D1r4h854m7546D9u2', 'Ary@n1104', 'ap8307655@gmail.com')
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- 4. PASSWORD RESETS TABLE
CREATE TABLE IF NOT EXISTS `password_resets` (
    `email` VARCHAR(100) NOT NULL,
    `token` VARCHAR(100) PRIMARY KEY,
    `expires_at` TIMESTAMP NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. CATEGORIES TABLE
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(50) UNIQUE NOT NULL,
    `name` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`slug`, `name`) VALUES
('spices', 'Whole Spices'),
('powders', 'Ground Powders'),
('seeds', 'Oil & Psyllium Seeds')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 6. PRODUCTS TABLE
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `badge` VARCHAR(50) NOT NULL,
    `purity` VARCHAR(20) NOT NULL,
    `origin` VARCHAR(50) NOT NULL,
    `moisture` VARCHAR(20) NOT NULL,
    `desc` TEXT NOT NULL,
    `image` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`name`, `category`, `badge`, `purity`, `origin`, `moisture`, `desc`, `image`) VALUES
('Psyllium Husk (Isabgol)', 'seeds', 'Superfood Grade', '99% Pure', 'Unjha, Gujarat', '< 8%', 'Premium dietary fiber derived from Plantago ovata seeds.', 'img/psyllium_husk.jpg'),
('Cumin Seeds (Jeera)', 'spices', 'Export Quality', '99.5% Pure', 'Unjha, Gujarat', '< 7%', 'Machine-cleaned, sortex-cleared aromatic cumin seeds.', 'https://kedarnathspices.com/storage/img/product/CuminWhole(Jeera)_1707732502.png'),
('Sesame Seeds (Till)', 'seeds', 'Hulled / Natural', '99.9% Pure', 'Gujarat, India', '< 5%', 'High-grade white and natural sesame seeds with rich oil content.', 'img/sesame_seeds.jpg'),
('Red Chilli Powder', 'powders', 'Stemless Grade', '100% Natural', 'Guntur, Andhra', '< 9%', 'Vibrant red chilli powder with intense aroma and balanced pungency.', 'img/red_chilli_powder.jpg'),
('Turmeric Powder (Haldi)', 'powders', 'High Curcumin', '100% Pure', 'Nizamabad, TS', '< 8%', 'Golden turmeric powder loaded with active curcumin compounds.', 'img/turmeric_powder.jpg')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 7. MAP STATES TABLE
CREATE TABLE IF NOT EXISTS `map_states` (
    `state_key` VARCHAR(50) PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `region` VARCHAR(100) NOT NULL,
    `badge` VARCHAR(100) NOT NULL,
    `bg` VARCHAR(50) NOT NULL,
    `image` TEXT NOT NULL,
    `primary_spices` TEXT NOT NULL,
    `description` TEXT NOT NULL,
    `purity` VARCHAR(20) NOT NULL,
    `capacity` VARCHAR(50) NOT NULL,
    `grading` VARCHAR(50) NOT NULL,
    `top_percent` VARCHAR(10) NOT NULL DEFAULT '50%',
    `left_percent` VARCHAR(10) NOT NULL DEFAULT '50%'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `map_states` (`state_key`, `name`, `region`, `badge`, `bg`, `image`, `primary_spices`, `description`, `purity`, `capacity`, `grading`, `top_percent`, `left_percent`) VALUES
('gujarat', 'Gujarat', 'Unjha Hub • World Capital of Seed Spices', 'Global Processing Headquarters', 'bg-[#1E3A2B]', 'https://kedarnathspices.com/storage/img/product/CuminWhole(Jeera)_1707732502.png', '["Cumin (Jeera)","Fennel (Saunf)","Psyllium Husk","Sesame Seeds"]', 'Home to Asia\'s largest spice market in Unjha. Our primary cleaning, sortex grading, and vacuum packaging plant.', '99.5%', '10,000 MT/Year', 'Sortex Cleaned', '48%', '34%'),
('rajasthan', 'Rajasthan', 'Nagaur & Ramganj Mandi Belt', 'Prime Seed Spice Harvesting', 'bg-[#D96E48]', 'img/psyllium_seeds.jpg', '["Cumin","Coriander (Dhana)","Fenugreek (Methi)","Mustard"]', 'Direct farm-gate procurement from Rajasthan\'s fertile spice growing regions.', '99.0%', '8,500 MT/Year', 'Machine Cleaned', '32%', '30%'),
('andhra', 'Andhra Pradesh', 'Guntur & Prakasam District', 'Spice Capital of Red Chilli', 'bg-[#C0392B]', 'img/red_chilli_powder.jpg', '["Teja Red Chilli","334 Red Chilli","Stemless Chilli"]', 'Direct partnership with Guntur chilli growers for vivid color and pungency.', '100% Pure', '6,000 MT/Year', 'Stemless Grade A', '66%', '52%'),
('kerala', 'Kerala', 'Idukki & Wayanad High Ranges', 'God\'s Own Spice Garden', 'bg-[#27AE60]', 'img/turmeric_powder.jpg', '["Black Pepper","Green Cardamom","Ginger","Nutmeg"]', 'High-altitude Malabar spices processed for maximum essential oil retention.', '99.8%', '3,000 MT/Year', 'Bold Export Grade', '82%', '39%')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 8. TESTIMONIALS TABLE
CREATE TABLE IF NOT EXISTS `testimonials` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `role` VARCHAR(100) NOT NULL,
    `location` VARCHAR(100) NOT NULL,
    `avatar` TEXT NOT NULL,
    `badge_type` VARCHAR(50) NOT NULL,
    `rating` INT NOT NULL DEFAULT 5,
    `comment` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `testimonials` (`name`, `role`, `location`, `avatar`, `badge_type`, `rating`, `comment`) VALUES
('Alexander Wright', 'Chief Procurement Officer', 'Hamburg, Germany', 'https://kedarnathspices.com/storage/img/testimonial/AlexanderWright_1707477150.jpg', 'Verified European Importer', 5, 'Kedarnath Spices has been our trusted partner for Cumin and Psyllium Husk for over 4 years. Their Sortex cleaning standards and low-pesticide compliance are exceptional.'),
('Rajesh Patel', 'Food Processing Director', 'Ahmedabad, Gujarat', '', 'Global Buyer', 5, 'Finding lab-tested, pesticide-compliant spices at competitive B2B prices used to be tough. Kedarnath Spices made our supply chain smooth, reliable, and transparent.')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

SET FOREIGN_KEY_CHECKS = 1;
