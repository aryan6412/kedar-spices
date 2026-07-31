<?php
// Load Hero Section Configuration from MySQL 8.0 Database (Changeable by Admin)
require_once __DIR__ . '/db.php';
$hero_config = get_hero_config('products');
log_visitor_visit('products');

// Fetch live products grouped by category from MySQL database
$categories = get_products_grouped_by_category();

// Helper to convert PHP array to JS-friendly format for Alpine
$js_categories = [];
foreach ($categories as $id => $cat) {
    $js_categories[] = [
        'id' => $id,
        'name' => $cat['name'],
        'icon' => $cat['icon'],
        'desc' => $cat['desc'],
        'bg' => $cat['bg']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Premium Products - Kedarnath Spices & Herbs</title>
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="Explore our premium range of organic seeds, aromatic spices, and traditional herbs. Fully traceable export-quality crops from Unjha, Gujarat.">
    <meta name="keywords" content="Spices, Herbs, Oil Seeds, Roasted Spices, Cumin, Fennel, Psyllium, Unjha">
    
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
    $active_page = 'products';
    require_once __DIR__ . '/navbar.php'; 
    ?>

    <!-- Hero Section (Video / Slider Option) -->
    <?php if ($hero_config['mode'] === 'video'): ?>
    <section class="relative h-[450px] md:h-[600px] overflow-hidden bg-charcoal">
        <!-- Background Video Player -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-charcoal/60 z-10"></div> <!-- Dark overlay -->
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
             class="relative h-[450px] md:h-[600px] overflow-hidden bg-charcoal">
        
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
                 :style="`background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.6)), url('${slide}');`"
            ></div>
        </template>
    <?php endif; ?>

        <!-- Static Slide Content overlay -->
        <div class="absolute bottom-12 md:bottom-16 left-6 right-6 md:right-auto md:left-12 lg:left-24 z-20 max-w-xl md:max-w-3xl text-white space-y-2 md:space-y-3">
            <span class="inline-block bg-white/10 backdrop-blur-md border border-white/20 text-[#E0A838] px-3.5 md:px-4 py-1 md:py-1.5 rounded-full font-bold text-[10px] uppercase tracking-widest">
                <?php echo htmlspecialchars($hero_config['tag']); ?>
            </span>
            <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold font-serif leading-tight">
                <?php echo htmlspecialchars($hero_config['title']); ?>
            </h1>
            <p class="text-xs md:text-sm text-white/80 leading-relaxed max-w-xl font-light line-clamp-3 md:line-clamp-none">
                <?php echo htmlspecialchars($hero_config['desc']); ?>
            </p>
        </div>

        <?php if ($hero_config['mode'] === 'slider'): ?>
        <!-- Slider Dots -->
        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex gap-3 z-20">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="activeSlide = index" 
                        :class="activeSlide === index ? 'bg-mustard w-8' : 'bg-white/40 hover:bg-white/70 w-3'"
                        class="h-3 rounded-full transition-all duration-300 focus:outline-none"></button>
            </template>
        </div>
        <?php endif; ?>
    </section>

    <!-- 3D Category Carousel Showcase Section -->
    <section class="py-24 bg-[#FCFAF7] border-b border-beige-dark/20 relative overflow-hidden" 
             x-data="{
                currentIndex: 0,
                activeCategory: 'spices',
                categories: <?php echo htmlspecialchars(json_encode($js_categories), ENT_QUOTES, 'UTF-8'); ?>,
                prev() {
                    this.currentIndex = (this.currentIndex === 0) ? this.categories.length - 1 : this.currentIndex - 1;
                    this.activeCategory = this.categories[this.currentIndex].id;
                },
                next() {
                    this.currentIndex = (this.currentIndex === this.categories.length - 1) ? 0 : this.currentIndex + 1;
                    this.activeCategory = this.categories[this.currentIndex].id;
                },
                setIndex(idx) {
                    this.currentIndex = idx;
                    this.activeCategory = this.categories[idx].id;
                }
             }">

        <!-- Subtle ambient blobs only -->
        <div class="absolute pointer-events-none" style="top:-80px;left:-80px;width:420px;height:420px;border-radius:9999px;background:rgba(217,110,72,0.10);filter:blur(80px);z-index:0;"></div>
        <div class="absolute pointer-events-none" style="bottom:-40px;right:-40px;width:360px;height:360px;border-radius:9999px;background:rgba(224,168,56,0.08);filter:blur(70px);z-index:0;"></div>
        <div class="absolute pointer-events-none" style="bottom:0;left:50%;transform:translateX(-50%);width:500px;height:200px;border-radius:9999px;background:rgba(46,94,62,0.06);filter:blur(80px);z-index:0;"></div>
        
        <div class="max-w-7xl mx-auto px-4 md:px-8 relative" style="z-index:10;">
            <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
                <span class="text-terracotta font-bold uppercase tracking-widest text-sm">Explore Categories</span>
                <h2 class="text-4xl md:text-5xl font-bold font-serif text-charcoal">Redefining Spice Standards</h2>
                <p class="text-charcoal/70 font-light">Browse our categories in 3D. Click or slide to filter the crop grid below.</p>
            </div>

            <!-- 3D Fan-out Carousel Viewport -->
            <div class="relative h-[400px] w-full flex justify-center items-center overflow-hidden py-6">
                
                <template x-for="(cat, idx) in categories" :key="cat.id">
                    <!-- Carousel Card Container -->
                    <div x-show="Math.abs(idx - currentIndex) <= 1 || (currentIndex === 0 && idx === categories.length - 1) || (currentIndex === categories.length - 1 && idx === 0)"
                         @click="setIndex(idx)"
                         :class="{
                            'scale-90 -rotate-6 -translate-x-36 md:-translate-x-60 opacity-60 z-10 pointer-events-auto': (idx === currentIndex - 1) || (currentIndex === 0 && idx === categories.length - 1),
                            'scale-100 rotate-0 translate-x-0 opacity-100 z-30 pointer-events-auto': idx === currentIndex,
                            'scale-90 rotate-6 translate-x-36 md:translate-x-60 opacity-60 z-10 pointer-events-auto': (idx === currentIndex + 1) || (currentIndex === categories.length - 1 && idx === 0)
                         }"
                         class="absolute w-[280px] md:w-[325px] h-[350px] transition-all duration-500 transform-gpu cursor-pointer group"
                    >
                        <!-- Layered Terracotta Offset Shadow (Stacked look) -->
                        <div class="absolute inset-0 bg-[#E06A3F] rounded-3xl translate-x-3.5 translate-y-3.5 z-0"></div>
                        
                        <!-- Main Card Body -->
                        <div class="absolute inset-0 border border-charcoal/10 rounded-3xl p-8 flex flex-col justify-between z-10 shadow-lg transition duration-300"
                             :style="`background-color: ${cat.bg};`"
                        >
                            <!-- Header content -->
                            <div>
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-2xl font-bold font-serif text-charcoal" x-text="cat.name"></h3>
                                    <img :src="cat.icon" :alt="cat.name" class="w-12 h-12 object-contain shrink-0 filter brightness-[0.98]">
                                </div>
                                <div class="w-full h-px bg-charcoal/10 mb-6"></div>
                                <p class="text-sm md:text-base text-charcoal/70 leading-relaxed font-light line-clamp-6" x-text="cat.desc"></p>
                            </div>
                            
                            <!-- Bottom Action indicator -->
                            <div class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-terracotta group-hover:text-terracotta-dark duration-200">
                                <span>Filter Products</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </div>
                        </div>
                    </div>
                </template>

            </div>

            <!-- Interactive Carousel Arrows Controls -->
            <div class="flex justify-center items-center gap-4 mt-8">
                <button @click="prev()" class="w-12 h-12 bg-white hover:bg-beige border-2 border-beige-dark text-charcoal rounded-full flex items-center justify-center shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <div class="flex gap-2">
                    <template x-for="(cat, idx) in categories" :key="cat.id">
                        <button @click="setIndex(idx)"
                                :class="idx === currentIndex ? 'bg-[#E06A3F] w-6' : 'bg-charcoal/20 w-2'"
                                class="h-2 rounded-full transition-all duration-300"></button>
                    </template>
                </div>
                <button @click="next()" class="w-12 h-12 bg-white hover:bg-beige border-2 border-beige-dark text-charcoal rounded-full flex items-center justify-center shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>

        </div>

        <!-- Filtered Products Showcase Grid (Linked directly to Alpine carousel state) -->
        <div class="max-w-7xl mx-auto px-4 md:px-8 mt-24 pt-16 border-t border-beige-dark/20">
            
            <!-- Active Filter Title Header -->
            <div class="mb-12 text-center md:text-left">
                <h3 class="text-3xl font-bold font-serif text-charcoal">
                    Showing <span class="text-terracotta" x-text="categories[currentIndex].name"></span> Selection
                </h3>
                <p class="text-sm text-charcoal/60 mt-2 font-light">Hover over double-image products to preview raw processing states.</p>
            </div>

            <?php foreach ($categories as $cat_slug => $cat_data): ?>
                <!-- Dynamic Category Showcase Grid -->
                <div x-show="activeCategory === '<?php echo htmlspecialchars($cat_slug); ?>'" x-transition class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php if (!empty($cat_data['products'])): ?>
                        <?php foreach ($cat_data['products'] as $product): ?>
                            <!-- Product Card -->
                            <div class="bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl border border-beige-dark transition-all duration-300 flex flex-col justify-between group">
                                <!-- Image Container with Hover Swap -->
                                <div class="relative overflow-hidden bg-beige-light aspect-square p-6 flex items-center justify-center border-b border-beige-dark">
                                    <?php if (!empty($product['img2'])): ?>
                                        <!-- Double image swap -->
                                        <img src="<?php echo htmlspecialchars($product['img1']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="h-full w-full object-contain absolute inset-0 p-8 transition-opacity duration-500 group-hover:opacity-0 z-10">
                                        <img src="<?php echo htmlspecialchars($product['img2']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="h-full w-full object-contain absolute inset-0 p-8 transition-opacity duration-500 opacity-0 group-hover:opacity-100 z-20">
                                    <?php else: ?>
                                        <!-- Single image -->
                                        <img src="<?php echo htmlspecialchars($product['img1']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="h-full w-full object-contain p-8 transform group-hover:scale-105 duration-300">
                                    <?php endif; ?>
                                    <div class="absolute top-4 left-4 bg-terracotta text-white font-bold text-xxs px-2.5 py-1 rounded-full uppercase tracking-wider z-30">
                                        Export Quality
                                    </div>
                                </div>
                                <!-- Card Text -->
                                <div class="p-6 space-y-4 flex-1 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <h3 class="font-bold text-xl font-serif text-charcoal group-hover:text-terracotta transition"><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <p class="text-charcoal/60 text-sm line-clamp-2 leading-relaxed"><?php echo htmlspecialchars($product['desc']); ?></p>
                                    </div>
                                    <!-- WhatsApp inquiry trigger -->
                                    <a href="https://api.whatsapp.com/send?phone=+917600404015&text=Hi%20Kedarnath%20Spices,%20I%20am%20interested%20in%20inquiring%20about%20<?php echo urlencode($product['name']); ?>." 
                                       target="_blank" 
                                       class="w-full py-2.5 rounded-full border border-terracotta text-terracotta hover:bg-terracotta hover:text-white font-bold text-center transition flex justify-center items-center gap-2">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.035-4.43c1.654.982 3.511 1.501 5.39 1.502 5.578.002 10.12-4.537 10.123-10.123.002-2.705-1.05-5.249-2.962-7.163C16.73 1.87 14.183.818 11.48.818c-5.584 0-10.126 4.54-10.128 10.126-.001 1.895.493 3.748 1.433 5.385L1.756 22.24l6.336-1.67zm12.355-6.61c-.33-.165-1.951-.963-2.251-1.072-.3-.11-.518-.165-.735.165-.218.33-.842 1.072-1.032 1.292-.19.22-.38.247-.71.082-.33-.165-1.393-.513-2.653-1.637-.98-.874-1.643-1.953-1.835-2.282-.19-.33-.02-.508.145-.671.148-.147.33-.385.495-.578.165-.192.22-.33.33-.55.11-.22.055-.412-.028-.577-.082-.165-.735-1.77-.993-2.42-.258-.627-.518-.543-.735-.543-.19 0-.41-.013-.627-.013-.218 0-.572.082-.871.412-.3.33-1.145 1.117-1.145 2.723 0 1.605 1.17 3.16 1.332 3.38 1.62 2.217 3.12 3.4 5.92 4.606 2.8.12 2.8.843 3.29.843.49 0 1.95-.797 2.225-1.567.275-.77.275-1.43.193-1.567-.083-.137-.303-.22-.633-.385z"/></svg>
                                        <span>Inquire</span>
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

        </div>
    </section>

    <!-- Footer Component -->
    <?php require_once __DIR__ . '/footer.php'; ?>

</body>
</html>
