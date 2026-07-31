<?php
// Load Hero Section Configuration from MySQL 8.0 Database (Changeable by Admin)
require_once __DIR__ . '/db.php';
$hero_config = get_hero_config('home');
log_visitor_visit('home');

// Fetch live products grouped by category from MySQL database
$categories = get_products_grouped_by_category();
$first_category_slug = !empty($categories) ? array_key_first($categories) : 'spices';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedarnath Spices & Herbs - Authentic Indian Spices</title>
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="Kedarnath has a wide range, large assortment and quality produce of aromatic herbs and exotic spices which is ideal for satisfying all your desire in the kitchen.">
    <meta name="keywords" content="Kedarnath Spices, Spices, Herbs, Oil Seeds, Roasted Spices, Unjha Spices">
    
    <link rel="shortcut icon" type="image/x-icon" href="https://kedarnathspices.com/favicon.ico">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="css/tailwind.css">
    
    <!-- AlpineJS for micro-interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-organic-pattern font-sans antialiased text-charcoal overflow-x-hidden">

    <!-- Header Navigation -->
    <?php 
    $active_page = 'home';
    require_once __DIR__ . '/navbar.php'; 
    ?>

    <!-- Hero Section (Video / Slider Option) -->
    <?php if ($hero_config['mode'] === 'video'): ?>
    <section class="relative h-screen overflow-hidden bg-charcoal">
        <!-- Background Video Player -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-charcoal/50 z-10"></div> <!-- Dark overlay -->
            <video autoplay muted loop playsinline class="w-full h-full object-cover">
                <source src="<?php echo $hero_config['video_url']; ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    <?php else: ?>
    <!-- Hero Section Slider -->
    <section x-data="{ 
                activeSlide: 0, 
                slides: <?php echo htmlspecialchars(json_encode($hero_config['slider_images']), ENT_QUOTES, 'UTF-8'); ?>,
                init() {
                    setInterval(() => {
                        this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                    }, 5000)
                }
             }" 
             class="relative h-screen overflow-hidden bg-charcoal">
        
        <!-- Background Slide Loop -->
        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="activeSlide === index"
                 x-transition:enter="transition duration-1000 ease-out"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition duration-1000 ease-in"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-cover bg-center"
                 :style="`background-image: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.55)), url('${slide}');`"
             ></div>
        </template>
    <?php endif; ?>

        <!-- Static Slide Content overlay -->
        <div class="absolute bottom-44 sm:bottom-40 md:bottom-32 left-6 right-6 md:right-auto md:left-12 lg:left-24 z-20 max-w-xl md:max-w-3xl text-white space-y-2 md:space-y-3">
            <span class="inline-block bg-white/10 backdrop-blur-md border border-white/20 text-[#E0A838] px-3.5 md:px-4 py-1 md:py-1.5 rounded-full font-bold text-[10px] uppercase tracking-widest">
                <?php echo htmlspecialchars($hero_config['tag']); ?>
            </span>
            <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold font-serif leading-tight">
                <?php echo htmlspecialchars($hero_config['title']); ?>
            </h1>
            <p class="text-xs md:text-sm text-white/80 leading-relaxed max-w-xl font-light line-clamp-3 md:line-clamp-none">
                <?php echo htmlspecialchars($hero_config['desc']); ?>
            </p>
            <div class="flex flex-wrap gap-4 pt-3">
                <a href="products.php" class="bg-[#E0A838] hover:bg-[#FAF3EC] text-[#1E2922] px-6 py-2.5 rounded-full font-bold shadow-lg transform hover:-translate-y-0.5 duration-200 transition uppercase tracking-wider text-[10px]">
                    Our Products
                </a>
                <a href="#contact-section" class="bg-white/10 backdrop-blur-sm border-2 border-white/20 hover:bg-white/20 text-white px-6 py-2.5 rounded-full font-bold transition uppercase tracking-wider text-[10px]">
                    Get In Touch
                </a>
            </div>
        </div>

    </section>

    <!-- Welcome / Introduction Section -->
    <section id="about-heritage" class="py-24 max-w-7xl mx-auto px-4 md:px-8 grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
        
        <!-- Text Column (Left) -->
        <div class="lg:col-span-6 space-y-6 lg:pr-8">
            <div class="space-y-2">
                <span class="text-terracotta font-bold uppercase tracking-widest text-sm">Since 2019</span>
                <h2 class="text-3xl md:text-4xl font-bold font-serif leading-tight text-charcoal">
                    Welcome to <br class="hidden md:inline">Kedarnath Spices & Herbs
                </h2>
                <!-- Leaf ornament -->
                <div class="flex items-center gap-2 pt-1">
                    <div class="h-0.5 w-16 bg-mustard rounded-full"></div>
                    <svg class="w-4 h-4 text-mustard" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.1 5 21C4 16 3.9 10 13 8C8.2 8.2 6.5 11.2 6 13C7 11 9 10 17 8Z"></path></svg>
                    <div class="h-0.5 w-16 bg-mustard rounded-full"></div>
                </div>
            </div>

            <p class="text-charcoal/80 leading-relaxed text-justify">
                Kedarnath was created in 2019 as a family-owned and run business in the heart of Spice City Unjha, Gujarat, India, with a colorful history and exquisite product range.
            </p>
            <p class="text-charcoal/80 leading-relaxed text-justify">
                We strive for service excellence because everything starts from the seed! The taste of India, a pinch of authentic flavors, ideal for flavoring your dishes with that extra something that makes the difference.
            </p>
            <p class="text-charcoal/80 leading-relaxed text-justify">
                We guarantee you the best raw materials selected for you in every corner of India, ensuring superior aromas for choice and variety.
            </p>
            
            <div class="pt-2">
                <a href="#contact-section" class="inline-flex items-center gap-2 text-terracotta font-bold group hover:text-terracotta-dark transition">
                    <span>Learn more about our heritage</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>

        <!-- Layered Image Column (Right) with India Spices Map & Premium Stats Below -->
        <div class="lg:col-span-6 relative">
            <!-- Main India Spices Map Frame — image fully visible, no overlay bars -->
            <div class="relative rounded-t-[32px] overflow-hidden shadow-2xl border-4 border-white border-b-0 bg-white p-2 pb-0">
                <img src="https://kedarnathspices.com/storage/img/introduction.png" alt="Kedarnath Spices Introduction Map" class="w-full h-auto object-cover rounded-t-[24px] transform hover:scale-103 transition-all duration-700 block">
                <!-- Top Tag Overlay (Right Side) -->
                <div class="absolute top-4 right-4 z-20">
                    <span class="bg-[#1C2C23]/85 backdrop-blur-md text-[#E0A838] font-bold text-[10px] uppercase tracking-widest px-3 py-1.5 rounded-full border border-white/20 shadow-md">
                        Unjha Market City • Spice Capital 🌾
                    </span>
                </div>
            </div>

            <!-- ✨ Premium Stats Bar — seamlessly attached below the image -->
            <div class="bg-gradient-to-r from-[#0D1511] via-[#1C2C23] to-[#0D1511] rounded-b-[32px] border-4 border-white border-t-0 px-3 py-3 grid grid-cols-4 gap-0 text-center divide-x divide-white/15 shadow-2xl">

                <!-- Stat 1: Established Year -->
                <div class="flex flex-col items-center justify-center px-2 py-1 group cursor-default hover:bg-white/5 transition-all duration-300 rounded-bl-2xl">
                    <svg class="w-3.5 h-3.5 mx-auto mb-0.5 text-[#E0A838] opacity-75 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-xl sm:text-2xl font-black font-serif text-[#E0A838] leading-none" style="text-shadow:0 0 18px rgba(224,168,56,0.7)">2019</p>
                    <p class="text-[7px] sm:text-[8px] font-bold uppercase tracking-widest text-white/60 mt-0.5 leading-tight">Established<br>Year</p>
                </div>

                <!-- Stat 2: Crop Traceability -->
                <div class="flex flex-col items-center justify-center px-2 py-1 group cursor-default hover:bg-white/5 transition-all duration-300">
                    <svg class="w-3.5 h-3.5 mx-auto mb-0.5 text-[#27AE60] opacity-75 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <p class="text-xl sm:text-2xl font-black font-serif text-[#27AE60] leading-none" style="text-shadow:0 0 18px rgba(39,174,96,0.7)">100%</p>
                    <p class="text-[7px] sm:text-[8px] font-bold uppercase tracking-widest text-white/60 mt-0.5 leading-tight">Crop<br>Traceability</p>
                </div>

                <!-- Stat 3: Spices & Herbs -->
                <div class="flex flex-col items-center justify-center px-2 py-1 group cursor-default hover:bg-white/5 transition-all duration-300">
                    <svg class="w-3.5 h-3.5 mx-auto mb-0.5 text-[#D96E48] opacity-75 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    <p class="text-xl sm:text-2xl font-black font-serif text-[#D96E48] leading-none" style="text-shadow:0 0 18px rgba(217,110,72,0.7)">50+</p>
                    <p class="text-[7px] sm:text-[8px] font-bold uppercase tracking-widest text-white/60 mt-0.5 leading-tight">Spices<br>& Herbs</p>
                </div>

                <!-- Stat 4: Export Destinations -->
                <div class="flex flex-col items-center justify-center px-2 py-1 group cursor-default hover:bg-white/5 transition-all duration-300 rounded-br-2xl">
                    <svg class="w-3.5 h-3.5 mx-auto mb-0.5 text-[#E0A838] opacity-75 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xl sm:text-2xl font-black font-serif text-[#E0A838] leading-none" style="text-shadow:0 0 18px rgba(224,168,56,0.7)">25+</p>
                    <p class="text-[7px] sm:text-[8px] font-bold uppercase tracking-widest text-white/60 mt-0.5 leading-tight">Export<br>Destinations</p>
                </div>

            </div>

        </div>

    </section>

    <!-- Corporate Philosophy / Principles Section -->
    <section class="py-24 max-w-7xl mx-auto px-4 md:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Sticky Left Column -->
            <div class="lg:col-span-4 lg:sticky lg:top-28 space-y-3">
                <span class="text-xs font-bold uppercase tracking-widest text-charcoal/60">Corporate Philosophy</span>
                <h2 class="text-4xl md:text-5xl font-bold font-serif text-[#1F7042] leading-tight">
                    Principles<br>We Stand By
                </h2>
                <!-- Decorative organic arches in background/below text -->
                <div class="hidden lg:block pt-8 opacity-10 text-[#1F7042] max-w-[200px]">
                    <svg viewBox="0 0 100 100" class="w-full h-auto fill-none stroke-current" stroke-width="2.5" stroke-linecap="round">
                        <path d="M 10 90 A 40 40 0 0 1 90 90" />
                        <path d="M 20 90 A 30 30 0 0 1 80 90" />
                        <path d="M 30 90 A 20 20 0 0 1 70 90" />
                        <path d="M 40 90 A 10 10 0 0 1 60 90" />
                    </svg>
                </div>
            </div>

            <!-- Scrollable Cards Column -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Card 1: Mission -->
                <div class="bg-[#FCB61A] text-charcoal rounded-[24px] p-8 md:p-10 shadow-md transition-all duration-300 hover:shadow-lg sticky top-[100px] z-10">
                    <h3 class="text-2xl font-bold font-sans mb-3 text-charcoal">Mission</h3>
                    <p class="text-sm md:text-base leading-relaxed text-charcoal/80">
                        To offer exemplary services for the joy of our customers, and provide quality herbs & spices @ affordable prices.
                    </p>
                </div>

                <!-- Card 2: Vision -->
                <div class="bg-[#FED285] text-charcoal rounded-[24px] p-8 md:p-10 shadow-md transition-all duration-300 hover:shadow-lg sticky top-[112px] z-20">
                    <h3 class="text-2xl font-bold font-sans mb-3 text-charcoal">Vision</h3>
                    <p class="text-sm md:text-base leading-relaxed text-charcoal/80">
                        We want to become the first choice supplier for innovation and sustainable products of natural ingredients for food supplements, natural colors, health supplements, and traditional herbal extracts.
                    </p>
                </div>

                <!-- Card 3: Credo -->
                <div class="bg-[#52B195] text-charcoal rounded-[24px] p-8 md:p-10 shadow-md transition-all duration-300 hover:shadow-lg sticky top-[124px] z-30">
                    <h3 class="text-2xl font-bold font-sans mb-3 text-charcoal">Credo</h3>
                    <p class="text-sm md:text-base leading-relaxed text-charcoal/80">
                        We strive for service excellence because everything starts from the seed! A pinch of authentic Indian flavors to make a difference in every dish.
                    </p>
                </div>

                <!-- Card 4: Goal -->
                <div class="bg-[#FDB07E] text-charcoal rounded-[24px] p-8 md:p-10 shadow-md transition-all duration-300 hover:shadow-lg sticky top-[136px] z-40">
                    <h3 class="text-2xl font-bold font-sans mb-3 text-charcoal">Goal</h3>
                    <p class="text-sm md:text-base leading-relaxed text-charcoal/80">
                        Achieve continued growth through sustained innovation for total customer satisfaction and fair return to all other stakeholders. Meet this objective by producing quality products at optimum cost and marketing them at reasonable prices.
                    </p>
                </div>

                <!-- Card 5: Guiding Principle -->
                <div class="bg-[#D2FA6C] text-charcoal rounded-[24px] p-8 md:p-10 shadow-md transition-all duration-300 hover:shadow-lg sticky top-[148px] z-50">
                    <h3 class="text-2xl font-bold font-sans mb-3 text-charcoal">Guiding Principle</h3>
                    <p class="text-sm md:text-base leading-relaxed text-charcoal/80">
                        Toil and sweat to manage our resources (men, material, and money) in an integrated, efficient, economic, and sustained manner. Earn profit, keeping in view commitment to society and environment.
                    </p>
                </div>

                <!-- Card 6: Quality Perspective -->
                <div class="bg-[#FDA56F] text-charcoal rounded-[24px] p-8 md:p-10 shadow-md transition-all duration-300 hover:shadow-lg sticky top-[160px] z-[60]">
                    <h3 class="text-2xl font-bold font-sans mb-3 text-charcoal">Quality Perspective</h3>
                    <p class="text-sm md:text-base leading-relaxed text-charcoal/80">
                        Make quality a way of life. Adhere to strict global standards of hygiene, purity, and full traceability.
                    </p>
                </div>

                <!-- Card 7: Work Culture Experience -->
                <div class="bg-[#ECEBF2] text-charcoal rounded-[24px] p-8 md:p-10 shadow-md transition-all duration-300 hover:shadow-lg sticky top-[172px] z-[70]">
                    <h3 class="text-2xl font-bold font-sans mb-3 text-charcoal">Work Culture Experience</h3>
                    <p class="text-sm md:text-base leading-relaxed text-charcoal/80">
                        Work is life, life is work. We foster personal integrity, respect for ethics, corporate values, and personal responsibility in every team member.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- Interactive Products Tabs Grid -->
    <section id="products-section" x-data="{ activeTab: '<?php echo htmlspecialchars($first_category_slug); ?>' }" class="py-20 max-w-7xl mx-auto px-4 md:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <span class="text-terracotta font-bold uppercase tracking-widest text-sm">Authentic Selection</span>
            <h2 class="text-4xl md:text-5xl font-bold font-serif text-charcoal">Our Premium Range</h2>
            <p class="text-charcoal/70 font-light">Hover over double-image products to preview raw processing states.</p>
        </div>

        <!-- Category selector horizontal container -->
        <div class="flex overflow-x-auto pb-4 mb-12 scrollbar-hide justify-start sm:justify-center items-center gap-3 sm:gap-4 px-4 -mx-4 sm:mx-0">
            <?php foreach ($categories as $id => $cat): ?>
                <button @click="activeTab = '<?php echo $id; ?>'"
                        :class="activeTab === '<?php echo $id; ?>' ? 'bg-mustard text-white shadow-lg border-mustard font-bold ring-2 ring-mustard/30' : 'bg-white hover:bg-beige text-charcoal border-beige-dark font-semibold'"
                        class="inline-flex items-center justify-center gap-2.5 px-5 py-2.5 sm:px-6 sm:py-3 rounded-full border transition-all duration-300 whitespace-nowrap shrink-0 focus:outline-none text-center shadow-sm">
                    <?php if (!empty($cat['icon'])): ?>
                        <img src="<?php echo htmlspecialchars($cat['icon']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="h-5 w-5 sm:h-6 sm:w-6 object-contain shrink-0">
                    <?php endif; ?>
                    <span class="leading-none text-sm sm:text-base"><?php echo htmlspecialchars($cat['name']); ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Products tab blocks -->
        <?php foreach ($categories as $id => $cat): ?>
            <div x-show="activeTab === '<?php echo $id; ?>'" 
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                
                <?php if (!empty($cat['products'])): ?>
                    <?php foreach ($cat['products'] as $product): ?>
                    <!-- Product Card -->
                    <div class="bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl border border-beige-dark transition-all duration-300 flex flex-col justify-between group">
                        
                        <!-- Image Container with Hover Swap -->
                        <div class="relative overflow-hidden bg-beige-light aspect-square p-6 flex items-center justify-center border-b border-beige-dark">
                            <?php if (!empty($product['img2'])): ?>
                                <!-- Double image swap -->
                                <img src="<?php echo $product['img1']; ?>" alt="<?php echo $product['name']; ?>" 
                                     class="h-full w-full object-contain absolute inset-0 p-8 transition-opacity duration-500 group-hover:opacity-0 z-10">
                                <img src="<?php echo $product['img2']; ?>" alt="<?php echo $product['name']; ?>" 
                                     class="h-full w-full object-contain absolute inset-0 p-8 transition-opacity duration-500 opacity-0 group-hover:opacity-100 z-20">
                            <?php else: ?>
                                <!-- Single image -->
                                <img src="<?php echo $product['img1']; ?>" alt="<?php echo $product['name']; ?>" 
                                     class="h-full w-full object-contain p-8 transform group-hover:scale-105 duration-300">
                            <?php endif; ?>
                            
                            <!-- New Badge for specific features -->
                            <div class="absolute top-4 left-4 bg-terracotta text-white font-bold text-xxs px-2.5 py-1 rounded-full uppercase tracking-wider z-30">
                                Export Quality
                            </div>
                        </div>

                        <!-- Card text info -->
                        <div class="p-6 space-y-4 flex-1 flex flex-col justify-between">
                            <div class="space-y-2">
                                <h3 class="font-bold text-xl font-serif text-charcoal group-hover:text-terracotta transition"><?php echo $product['name']; ?></h3>
                                <p class="text-charcoal/60 text-sm line-clamp-2 leading-relaxed"><?php echo $product['desc']; ?></p>
                            </div>
                            
                            <!-- WhatsApp inquiry trigger -->
                            <a href="https://api.whatsapp.com/send?phone=+917600404015&text=Hi%20Kedarnath%20Spices,%20I%20am%20interested%20in%20inquiring%20about%20<?php echo urlencode($product['name']); ?>." 
                               target="_blank" 
                               class="w-full py-2.5 rounded-full border border-terracotta text-terracotta hover:bg-terracotta hover:text-white font-bold text-center transition flex justify-center items-center gap-2">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.035-4.43c1.654.982 3.511 1.501 5.39 1.502 5.578.002 10.12-4.537 10.123-10.123.002-2.705-1.05-5.249-2.962-7.163C16.73 1.87 14.183.818 11.48.818c-5.584 0-10.126 4.54-10.128 10.126-.001 1.895.493 3.748 1.433 5.385L1.756 22.24l6.336-1.67zm12.355-6.61c-.33-.165-1.951-.963-2.251-1.072-.3-.11-.518-.165-.735.165-.218.33-.842 1.072-1.032 1.292-.19.22-.38.247-.71.082-.33-.165-1.393-.513-2.653-1.637-.98-.874-1.643-1.953-1.835-2.282-.19-.33-.02-.508.145-.671.148-.147.33-.385.495-.578.165-.192.22-.33.33-.55.11-.22.055-.412-.028-.577-.082-.165-.735-1.77-.993-2.42-.258-.627-.518-.543-.735-.543-.19 0-.41-.013-.627-.013-.218 0-.572.082-.871.412-.3.33-1.145 1.117-1.145 2.723 0 1.605 1.17 3.16 1.332 3.38 1.62 2.217 3.12 3.4 5.92 4.606 2.8.12 2.8.843 3.29.843.49 0 1.95-.797 2.225-1.567.275-.77.275-1.43.193-1.567-.083-.137-.303-.22-.633-.385z"/></svg>
                                <span>Inquire Now</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-12 bg-white rounded-3xl border border-beige-dark">
                        <p class="text-charcoal/50 text-sm font-semibold">No products added under this category yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    </section>

    <!-- Client Testimonials Section (Ultra Modern Luxury Showcase) -->
    <section class="py-24 bg-[#E6F3EA] text-[#1E2922] relative overflow-hidden my-12 rounded-3xl border border-[#2E5E3E]/15 shadow-xl">
        <!-- Ambient background glows -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-[#2E5E3E]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-[#E0A838]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(#2E5E3E_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 md:px-12 relative z-10">
            
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#2E5E3E]/12 border border-[#2E5E3E]/25 text-[#2E5E3E] text-xs font-bold uppercase tracking-widest">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <span>Client Feedback</span>
                </div>
                <h2 class="text-3xl md:text-5xl font-bold font-serif text-[#1E2922] leading-tight">
                    Trusted By Industry Leaders & <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#D96E48] to-[#E0A838]">Export Merchants</span>
                </h2>
                <p class="text-[#1E2922]/75 font-light text-base md:text-lg">
                    Discover why food processors, spice wholesalers, and commercial buyers rely on Kedarnath Spices for pure IPM quality.
                </p>
            </div>

            <!-- Testimonials Modern Grid -->
            <?php $testimonials = get_all_testimonials(); ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($testimonials as $t): ?>
                    <div class="bg-[#1C2C23]/90 backdrop-blur-xl p-8 rounded-3xl border border-white/10 hover:border-[#E0A838]/50 transition-all duration-500 flex flex-col justify-between group hover:-translate-y-2 shadow-xl hover:shadow-[0_20px_50px_rgba(224,168,56,0.12)] relative">
                        <!-- Giant Quote Watermark -->
                        <span class="absolute top-6 right-6 text-7xl font-serif text-[#E0A838]/10 group-hover:text-[#E0A838]/25 transition duration-500 pointer-events-none select-none">“</span>
                        
                        <div class="space-y-6 relative z-10">
                            <!-- Rating Stars & Verified Tag -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1 text-[#E0A838]">
                                    <?php 
                                    $rating = (int)($t['rating'] ?: 5);
                                    for ($i = 0; $i < $rating; $i++): 
                                    ?>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <?php endfor; ?>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-[#00FF99]/15 text-[#00FF99] text-[10px] font-extrabold uppercase tracking-wider border border-[#00FF99]/40 flex items-center gap-1.5 shadow-[0_0_12px_rgba(0,255,153,0.25)]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    <?php echo htmlspecialchars($t['badge_type'] ?: 'Verified B2B'); ?>
                                </span>
                            </div>

                            <!-- Review Text -->
                            <p class="text-white/85 text-sm md:text-base leading-relaxed font-light italic">
                                "<?php echo htmlspecialchars($t['comment']); ?>"
                            </p>
                        </div>

                        <!-- Client Profile Footer -->
                        <div class="flex items-center gap-4 pt-6 mt-6 border-t border-white/10 relative z-10">
                            <?php if (!empty($t['avatar'])): ?>
                                <div class="relative shrink-0 w-12 h-12">
                                    <img src="<?php echo htmlspecialchars($t['avatar']); ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" class="w-12 h-12 rounded-full object-cover border-2 border-[#E0A838] shrink-0" style="width: 48px; height: 48px;">
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#E0A838] rounded-full flex items-center justify-center text-[#121E17]">
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#E0A838] to-[#1E2922] p-0.5 flex items-center justify-center shrink-0" style="width: 48px; height: 48px;">
                                    <div class="w-full h-full bg-[#1C2C23] rounded-full flex items-center justify-center text-[#E0A838] font-bold font-serif text-lg">
                                        <?php 
                                        $words = explode(' ', $t['name']);
                                        $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                        echo htmlspecialchars($initials);
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h4 class="font-bold font-serif text-[#FAF3EC] text-base group-hover:text-[#E0A838] transition"><?php echo htmlspecialchars($t['name']); ?></h4>
                                <p class="text-xs text-white/60 font-medium"><?php echo htmlspecialchars($t['role']); ?></p>
                                <span class="text-[11px] text-[#E0A838]/80"><?php echo htmlspecialchars($t['location']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Trust Highlights Footer Counter -->
            <div class="mt-16 pt-10 border-t border-[#1E2922]/15 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="space-y-1">
                    <span class="text-2xl md:text-3xl font-extrabold font-serif text-[#2E5E3E]">100%</span>
                    <p class="text-xs text-[#1E2922]/70 uppercase tracking-widest font-bold">IPM Pure Quality</p>
                </div>
                <div class="space-y-1">
                    <span class="text-2xl md:text-3xl font-extrabold font-serif text-[#2E5E3E]">10,000+</span>
                    <p class="text-xs text-[#1E2922]/70 uppercase tracking-widest font-bold">Tons Dispatched</p>
                </div>
                <div class="space-y-1">
                    <span class="text-2xl md:text-3xl font-extrabold font-serif text-[#2E5E3E]">50+</span>
                    <p class="text-xs text-[#1E2922]/70 uppercase tracking-widest font-bold">Verified B2B Buyers</p>
                </div>
                <div class="space-y-1">
                    <span class="text-2xl md:text-3xl font-extrabold font-serif text-[#2E5E3E]">99.8%</span>
                    <p class="text-xs text-[#1E2922]/70 uppercase tracking-widest font-bold">On-Time Export Dispatch</p>
                </div>
            </div>

        </div>
    </section>

    <!-- Contact Form/Lead Section -->
    <section id="contact-section" class="py-20 max-w-7xl mx-auto px-4 md:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        
        <div class="lg:col-span-5 space-y-6">
            <span class="text-terracotta font-bold uppercase tracking-widest text-sm">Grow With Us</span>
            <h2 class="text-4xl font-bold font-serif leading-tight">Congratulations! You Found the Right Partner.</h2>
            <p class="text-charcoal/80 leading-relaxed">
                We give you trusted quality, high accuracy, and complete transparency. Reach out today to discuss customized crop selections, export load limits, or harvest dates.
            </p>
            
            <div class="space-y-4 pt-2">
                <div class="flex gap-4 items-start">
                    <span class="bg-mustard/15 text-mustard p-3 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </span>
                    <div>
                        <h4 class="font-bold text-charcoal font-serif">Office Location</h4>
                        <p class="text-sm text-charcoal/70">H 401, Cattle Shade, Gunj Bajar, Unjha - 384170, Gujarat, India.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-7 bg-white rounded-3xl p-8 md:p-10 border border-beige-dark shadow-xl">
            <h3 class="text-2xl font-bold font-serif mb-6">Send an Inquiry</h3>
            <form action="#" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-charcoal/80" for="name">Your Name</label>
                        <input class="w-full px-4 py-3 rounded-xl border border-beige-dark bg-beige-light focus:outline-none focus:ring-2 focus:ring-terracotta/25 focus:border-terracotta transition" type="text" id="name" required placeholder="Name">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-charcoal/80" for="mobile">Mobile Number</label>
                        <input class="w-full px-4 py-3 rounded-xl border border-beige-dark bg-beige-light focus:outline-none focus:ring-2 focus:ring-terracotta/25 focus:border-terracotta transition" type="tel" id="mobile" required placeholder="Phone number">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-charcoal/80" for="email">Email Address</label>
                        <input class="w-full px-4 py-3 rounded-xl border border-beige-dark bg-beige-light focus:outline-none focus:ring-2 focus:ring-terracotta/25 focus:border-terracotta transition" type="email" id="email" required placeholder="Email">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-charcoal/80" for="subject">Subject</label>
                        <input class="w-full px-4 py-3 rounded-xl border border-beige-dark bg-beige-light focus:outline-none focus:ring-2 focus:ring-terracotta/25 focus:border-terracotta transition" type="text" id="subject" required placeholder="Spice requirements">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-charcoal/80" for="message">Message</label>
                    <textarea class="w-full px-4 py-3 rounded-xl border border-beige-dark bg-beige-light focus:outline-none focus:ring-2 focus:ring-terracotta/25 focus:border-terracotta transition h-32" id="message" required placeholder="Let us know your specifications..."></textarea>
                </div>
                <button type="submit" class="w-full bg-charcoal hover:bg-terracotta text-white py-3.5 rounded-xl font-bold shadow-lg transition-all duration-300">
                    Send Message
                </button>
            </form>
        </div>
    </section>

    <!-- Footer Component -->
    <?php require_once __DIR__ . '/footer.php'; ?>
