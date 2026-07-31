<?php
// Load Hero Section Configuration from MySQL 8.0 Database
require_once __DIR__ . '/db.php';
$hero_config = get_hero_config('about');
log_visitor_visit('about');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Kedarnath Spices & Herbs | Unjha Organic Heritage</title>
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="Discover Kedarnath Spices & Herbs - A family-owned legacy since 2019 in Unjha, Gujarat providing 100% pure, traceable, and export-grade spices, seeds, and traditional herbs.">
    <meta name="keywords" content="About Kedarnath Spices, Unjha Spice Capital, Spice Exporters India, Organic Herbs, Cumin Seeds, Fennel Seeds, Psyllium Husk">
    
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
    $active_page = 'about';
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
             class="relative min-h-[550px] h-[75vh] md:h-[650px] overflow-hidden bg-charcoal">
        
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
        <div class="absolute bottom-16 md:bottom-20 left-6 right-6 md:right-auto md:left-12 lg:left-24 z-20 max-w-xl md:max-w-3xl text-white space-y-2 md:space-y-3">
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
    </section>

    <!-- Main About Story & Unjha Heritage -->
    <section class="py-24 max-w-7xl mx-auto px-6 md:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            
            <!-- Left Image Container — one unified card: image top + dark stats bottom -->
            <div class="lg:col-span-6 relative">
                <!-- Single unified card: rounded corners, overflow-hidden clips everything flush -->
                <div class="rounded-[32px] overflow-hidden shadow-2xl border-4 border-[#1C2C23]">

                    <!-- Image area (no padding, no white gap, fills 100%) -->
                    <div class="relative">
                        <img src="https://kedarnathspices.com/storage/img/introduction.png" alt="Kedarnath Spices Introduction Map" class="w-full h-auto object-cover block">
                        <!-- Top Right Badge -->
                        <div class="absolute top-4 right-4 z-20">
                            <span class="bg-[#1C2C23]/85 backdrop-blur-md text-[#E0A838] font-bold text-[10px] uppercase tracking-widest px-3 py-1.5 rounded-full border border-white/20 shadow-md">
                                Unjha Market City • Spice Capital 🌾
                            </span>
                        </div>
                    </div>

                    <!-- Stats bar — directly touching the image, no gap -->
                    <div class="bg-gradient-to-r from-[#0D1511] via-[#1C2C23] to-[#0D1511] px-3 py-3 grid grid-cols-4 gap-0 text-center divide-x divide-white/10">

                        <!-- Stat 1: Established Year -->
                        <div class="flex flex-col items-center justify-center px-2 py-1.5 group cursor-default hover:bg-white/5 transition-all duration-300">
                            <svg class="w-3.5 h-3.5 mx-auto mb-0.5 text-[#E0A838] opacity-70 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-xl sm:text-2xl font-black font-serif text-[#E0A838] leading-none" style="text-shadow:0 0 16px rgba(224,168,56,0.8)">2019</p>
                            <p class="text-[7px] sm:text-[8px] font-bold uppercase tracking-widest text-white/55 mt-0.5 leading-tight">Established<br>Year</p>
                        </div>

                        <!-- Stat 2: Crop Traceability -->
                        <div class="flex flex-col items-center justify-center px-2 py-1.5 group cursor-default hover:bg-white/5 transition-all duration-300">
                            <svg class="w-3.5 h-3.5 mx-auto mb-0.5 text-[#27AE60] opacity-70 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <p class="text-xl sm:text-2xl font-black font-serif text-[#27AE60] leading-none" style="text-shadow:0 0 16px rgba(39,174,96,0.8)">100%</p>
                            <p class="text-[7px] sm:text-[8px] font-bold uppercase tracking-widest text-white/55 mt-0.5 leading-tight">Crop<br>Traceability</p>
                        </div>

                        <!-- Stat 3: Spices & Herbs -->
                        <div class="flex flex-col items-center justify-center px-2 py-1.5 group cursor-default hover:bg-white/5 transition-all duration-300">
                            <svg class="w-3.5 h-3.5 mx-auto mb-0.5 text-[#D96E48] opacity-70 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            <p class="text-xl sm:text-2xl font-black font-serif text-[#D96E48] leading-none" style="text-shadow:0 0 16px rgba(217,110,72,0.8)">50+</p>
                            <p class="text-[7px] sm:text-[8px] font-bold uppercase tracking-widest text-white/55 mt-0.5 leading-tight">Spices<br>& Herbs</p>
                        </div>

                        <!-- Stat 4: Export Destinations -->
                        <div class="flex flex-col items-center justify-center px-2 py-1.5 group cursor-default hover:bg-white/5 transition-all duration-300">
                            <svg class="w-3.5 h-3.5 mx-auto mb-0.5 text-[#E0A838] opacity-70 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xl sm:text-2xl font-black font-serif text-[#E0A838] leading-none" style="text-shadow:0 0 16px rgba(224,168,56,0.8)">25+</p>
                            <p class="text-[7px] sm:text-[8px] font-bold uppercase tracking-widest text-white/55 mt-0.5 leading-tight">Export<br>Destinations</p>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Right Content Text Block -->
            <div class="lg:col-span-6 space-y-6">
                <div class="space-y-2">
                    <span class="text-terracotta font-bold uppercase tracking-widest text-xs">Our Organic Heritage</span>
                    <h2 class="text-3xl md:text-4xl font-bold font-serif leading-tight text-charcoal">
                        Welcome to <br class="hidden sm:inline">Kedarnath Spices & Herbs
                    </h2>
                </div>

                <div class="space-y-4 text-charcoal/80 text-sm md:text-base leading-relaxed font-light">
                    <p class="first-letter:text-4xl first-letter:font-serif first-letter:font-bold first-letter:text-terracotta first-letter:mr-2 first-letter:float-left">
                        Created in 2019, <strong>Kedarnath Spices & Herbs</strong> is a family-owned business situated in the heart of Spice City Unjha, Gujarat, India. Our legacy lies within the spices we produce — the tradition and flair of our vibrant community are expressed and tasted in every batch.
                    </p>
                    <p>
                        Kedarnath offers a wide range and large assortment of aromatic herbs, whole seeds, dietary psyllium, and exotic spice powders suitable for traditional, Mediterranean, or exotic cuisines. We guarantee the finest raw materials selected from prime organic farms across India.
                    </p>
                    <p>
                        We have been creating the emotion of <em>"Made in India"</em> by offering naturally healthy spices, natural colors, and herbal commodity ingredients tailored to the unique demands of global kitchens and commercial clients.
                    </p>
                </div>

                <!-- Callout Box -->
                <div class="p-6 rounded-2xl bg-[#FAF3EC] border-l-4 border-[#D96E48] space-y-2">
                    <h4 class="font-bold font-serif text-charcoal text-base">Experienced Sourcing & Manufacturing</h4>
                    <p class="text-xs md:text-sm text-charcoal/70 leading-relaxed font-light">
                        Equipped with modern cleaning, sorting, and grading technology inside Unjha market, our team ensures export-grade quality at competitive factory pricing.
                    </p>
                </div>
            </div>

        </div>
    </section>

    <!-- Interactive India Spice & Grain Sourcing Map Section -->
    <script>
    function publicMapStateManager() {
        return {
            activeState: null,
            states: <?php echo json_encode(get_all_map_states(), JSON_UNESCAPED_UNICODE); ?>
        };
    }
    </script>
    <section x-data="publicMapStateManager()" 
             class="py-24 bg-[#E2EFE7] text-[#1C2C23] relative border-t border-b border-[#27AE60]/15">
        
        <!-- Faint Background Ambient Glow -->
        <div class="absolute -left-20 top-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-[#27AE60]/10 blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 md:px-12 space-y-12 relative z-10">
            
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto space-y-3">
                <span class="inline-block bg-[#27AE60]/15 text-[#27AE60] border border-[#27AE60]/30 px-4 py-1.5 rounded-full font-bold text-xs uppercase tracking-widest">
                    Direct From Farm Roots
                </span>
                <h2 class="text-3xl md:text-5xl font-bold font-serif text-[#1C2C23]">
                    Where Our Spices & Grains Come From
                </h2>
                <p class="text-[#1C2C23]/80 text-sm md:text-base font-light">
                    Click any state pin on the map below to discover what we harvest directly from that region.
                </p>
            </div>

            <!-- MAP CONTAINER WITH CURVY CORNERS AND OVERLAID HUD ELEMENTS -->
            <div class="relative w-full max-w-[850px] mx-auto z-10 rounded-[28px] md:rounded-[36px] shadow-2xl border-4 border-white/30 p-1 bg-[#1C2C23]">

                    <!-- Floating Top Header Overlay DIRECTLY ON MAP IMAGE (Transparent Background) -->
                    <div class="absolute top-2 right-2 left-auto max-w-[70%] md:left-4 md:right-4 md:max-w-none md:top-4 z-30 flex flex-col md:flex-row justify-between items-end md:items-center gap-1.5 md:gap-2 bg-transparent p-2 px-3 md:p-3 md:px-6 pointer-events-none">
                        <div class="text-right md:text-left drop-shadow-[0_2px_12px_rgba(0,0,0,0.95)]">
                            <span class="text-[9px] md:text-[11px] font-extrabold text-[#E0A838] uppercase tracking-[0.2em] flex items-center justify-end md:justify-start gap-1.5 drop-shadow-md">
                                <span class="w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-[#E0A838] animate-ping"></span>
                                LIVE SOURCING MAP
                            </span>
                            <h3 class="text-white text-xs md:text-xl font-bold font-serif leading-tight drop-shadow-lg">Where We Source Our Spices Across India 🌾</h3>
                        </div>
                        <span class="text-[9px] md:text-xs bg-black/40 text-white/90 px-3 py-1 rounded-full border border-white/20 backdrop-blur-md shadow-lg hidden sm:inline-block pointer-events-auto">
                            Tap any Pin to View Spices ☁️
                        </span>
                    </div>

                    <!-- Real India Map Image with Curvy Corners -->
                    <img src="img/india_map.jpg" alt="India Sourcing Map" class="w-full h-auto object-contain select-none drop-shadow-2xl block rounded-[24px] md:rounded-[32px]" draggable="false">

                    <!-- FLOATING ANIMATED 3D BLUE CLICKING HAND GUIDE (Visible until user clicks any pin) -->
                    <div x-show="activeState === null" 
                         x-transition:leave="transition ease-in duration-300 opacity-0 scale-90"
                         class="absolute top-[48%] left-[16%] z-40 pointer-events-none flex items-center gap-2">
                        
                        <!-- 3D Blue Tapping Hand Icon with Pulse Wave -->
                        <div class="relative animate-hand-tap flex items-center justify-center">
                            <div class="absolute w-8 h-8 rounded-full border-2 border-[#2563EB] animate-ping opacity-80"></div>
                            <div class="absolute w-12 h-12 rounded-full bg-[#2563EB]/20 animate-pulse"></div>

                            <svg class="w-10 h-10 drop-shadow-[0_10px_20px_rgba(37,99,235,0.8)]" viewBox="0 0 50 50" fill="none">
                                <path d="M19.5 7.5C17.29 7.5 15.5 9.29 15.5 11.5V32.5L12.1 30.23C10.95 29.46 9.38 29.67 8.47 30.72C7.43 31.92 7.58 33.74 8.8 34.75L17.16 41.67C18.65 42.91 19.5 44.76 19.5 46.72V48.5C19.5 50.71 21.29 52.5 23.5 52.5H35.5C38.81 52.5 41.5 49.81 41.5 46.5V37.5C41.5 35.29 39.71 33.5 37.5 33.5C36.85 33.5 36.24 33.66 35.7 33.94C35.03 32.49 33.56 31.5 31.85 31.5C31.15 31.5 30.49 31.69 29.92 32.02C29.2 30.48 27.63 29.5 25.85 29.5C25.1 29.5 24.39 29.71 23.85 30.07V11.5C23.85 9.29 22.06 7.5 19.5 7.5Z" 
                                      fill="#2563EB" stroke="#FFFFFF" stroke-width="2.5" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <!-- Text Pill Callout -->
                        <div class="bg-[#2563EB] text-white font-extrabold text-[11px] px-3.5 py-1.5 rounded-full shadow-[0_10px_25px_rgba(37,99,235,0.6)] border border-white flex items-center gap-1.5 uppercase tracking-wider whitespace-nowrap">
                            <span>Click Dot to View Details</span>
                        </div>
                    </div>

                    <!-- INTERACTIVE STATE PINS WITH DYNAMIC Z-INDEX STACKING TO PREVENT OVERLAPPING -->
                    <template x-for="(st, key) in states" :key="key">
                        <div class="absolute cursor-pointer transition-all duration-300"
                             :class="activeState === key ? 'z-50 opacity-100' : (activeState !== null ? 'z-10 opacity-40' : 'z-20 opacity-100')"
                             :style="st.top && st.left ? `top:${st.top};left:${st.left};` : 'top:50%;left:50%;'">
                            
                            <div class="relative flex flex-col items-center">
                                
                                <!-- Multi-Layer Pulsing Aura Rings -->
                                <div class="absolute w-10 h-10 -top-2 -left-2 rounded-full animate-ping opacity-40" :style="`background-color: ${st.bg}`"></div>
                                <div class="absolute w-6 h-6 rounded-full animate-pulse opacity-60" :style="`background-color: ${st.bg}`"></div>

                                <!-- Main Clickable Dot Pin Button -->
                                <div @click="activeState = (activeState === key ? null : key)"
                                     :class="activeState === key ? 'scale-140 ring-4 ring-white shadow-[0_0_25px_rgba(255,255,255,0.9)]' : 'group-hover:scale-130 group-hover:ring-2 group-hover:ring-white/80'"
                                     class="w-6 h-6 rounded-full border-2 border-white shadow-xl transition-all duration-300 flex items-center justify-center relative z-10"
                                     :style="`background-color: ${st.bg}`">
                                    <div class="w-2.5 h-2.5 rounded-full bg-white shadow-inner"></div>
                                    <!-- State Name Label -->
                                    <span class="absolute top-7 px-1.5 py-0.5 bg-[#0D1511]/90 backdrop-blur-md border border-white/20 rounded-md text-[8px] md:text-[10px] font-bold text-white uppercase tracking-wider whitespace-nowrap shadow-lg"
                                          x-text="st.name.split(' ')[0]">
                                    </span>
                                </div>

                                <!-- ANCHORED CLOUD SPEECH BUBBLE POPUP (ALWAYS POINTING DIRECTLY AT STATE PIN DOT) -->
                                <div x-show="activeState === key"
                                     x-transition:enter="transition ease-out duration-300 transform"
                                     x-transition:enter-start="opacity-0 scale-75 translate-y-2"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-200 transform"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-75"
                                     :class="key==='kashmir' ? 'top-9 left-1/2 -translate-x-1/2' : (key==='gujarat' ? 'bottom-9 left-0 sm:-left-4' : (key==='northeast' ? 'bottom-9 right-0 sm:-right-4' : 'bottom-9 left-1/2 -translate-x-1/2'))"
                                     class="absolute z-50 w-[260px] sm:w-[320px] text-[#1C2C23] p-3.5 sm:p-5 bg-white border-4 border-black rounded-[28px] sm:rounded-[36px] shadow-[0_20px_60px_rgba(0,0,0,0.7)] space-y-2 select-none"
                                >
                                    <!-- Sleek Crisp Speech Bubble Pointer Tail Arrow Pointing at Dot -->
                                    <div :class="key==='kashmir' ? '-top-3 left-1/2 -translate-x-1/2 border-t-4 border-l-4 border-black rotate-45' : (key==='gujarat' ? '-bottom-3 left-4 border-b-4 border-r-4 border-black rotate-45' : (key==='northeast' ? '-bottom-3 right-4 border-b-4 border-r-4 border-black rotate-45' : '-bottom-3 left-1/2 -translate-x-1/2 border-b-4 border-r-4 border-black rotate-45'))"
                                         class="absolute w-4 h-4 sm:w-5 sm:h-5 bg-white pointer-events-none z-10">
                                    </div>

                                    <!-- Close button -->
                                    <button @click.stop="activeState = null" class="absolute top-2.5 right-2.5 w-6 h-6 rounded-full bg-black text-white hover:bg-[#27AE60] flex items-center justify-center font-bold text-xs transition cursor-pointer z-20 shadow-md">
                                        ✕
                                    </button>

                                    <!-- Header -->
                                    <div class="pr-6 border-b border-black/10 pb-2 space-y-0.5 text-left">
                                        <h3 class="text-xs sm:text-base font-extrabold font-serif text-[#1C2C23] leading-tight" x-text="st.name"></h3>
                                        <p class="text-[9px] sm:text-[10px] text-[#27AE60] font-extrabold uppercase tracking-wider" x-text="st.region"></p>
                                    </div>

                                    <!-- Key Spices List (Full Width Tags) -->
                                    <div class="space-y-1 text-left py-1">
                                        <h4 class="text-[9px] font-extrabold uppercase tracking-wider text-black/50">Primary Harvest Spices:</h4>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="spice in st.primarySpices" :key="spice">
                                                <span class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-bold text-[#1C2C23] bg-[#F2F7F4] border border-black/10 px-2.5 py-1 rounded-lg">
                                                    <span class="text-[#27AE60]">✓</span>
                                                    <span x-text="spice"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- WhatsApp Direct CTA -->
                                    <a :href="`https://api.whatsapp.com/send?phone=+917600404015&text=Enquiry%20for%20${encodeURIComponent(st.name)}`" target="_blank"
                                       class="w-full bg-[#27AE60] hover:bg-black text-white py-2 rounded-full font-extrabold text-[10px] sm:text-xs uppercase tracking-wider text-center flex items-center justify-center gap-1 shadow-md transition-all duration-200 mt-2">
                                        <span>Enquire From <span x-text="st.name.split(' ')[0]"></span> 📲</span>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </template>

                </div>

        </div>
    </section>

    <!-- Corporate Philosophy / Principles Section -->
    <section class="py-24 max-w-7xl mx-auto px-6 md:px-12">
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

    <!-- Client Testimonials Section (Ultra Modern Luxury Showcase) -->
    <section class="py-24 bg-[#E6F3EA] text-[#1E2922] relative overflow-hidden my-16 rounded-3xl shadow-xl border border-[#2E5E3E]/15">
        <!-- Ambient background glows -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-[#2E5E3E]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-[#E0A838]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(#2E5E3E_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 md:px-12 relative z-10 space-y-16">
            
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#2E5E3E]/12 border border-[#2E5E3E]/25 text-[#2E5E3E] text-xs font-bold uppercase tracking-widest">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <span>Client Feedback</span>
                </div>
                <h2 class="text-3xl md:text-5xl font-bold font-serif text-[#1E2922] leading-tight">
                    What Our Commercial <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#D96E48] to-[#E0A838]">Partners Say</span>
                </h2>
                <p class="text-[#1E2922]/75 font-light text-base md:text-lg">
                    Proven track record of delivering clean, IPM-certified spice crops to international and domestic B2B buyers.
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

        </div>
    </section>

    <!-- Call to Action Banner -->
    <section class="py-16 max-w-7xl mx-auto px-6 md:px-12">
        <div class="bg-gradient-to-r from-[#1E2922] via-[#2A3A30] to-[#1E2922] text-white p-10 md:p-14 rounded-3xl shadow-2xl border border-white/10 flex flex-col lg:flex-row justify-between items-center gap-8 text-center lg:text-left">
            <div class="space-y-3 max-w-2xl">
                <span class="text-[#E0A838] font-bold text-xs uppercase tracking-widest">Global Export Desk</span>
                <h3 class="text-3xl md:text-4xl font-bold font-serif leading-tight">Ready to Source Premium Spices Direct From Unjha?</h3>
                <p class="text-white/70 text-sm font-light leading-relaxed">
                    Contact our expert export team to request custom samples, bulk pricing, or crop traceability certificates.
                </p>
            </div>

            <div class="flex flex-wrap justify-center gap-4 shrink-0">
                <a href="https://api.whatsapp.com/send?phone=+917600404015&text=Hi,%20I%20would%20like%20to%20know%20more%20about%20Kedarnath%20Spices." target="_blank"
                   class="bg-[#E0A838] text-[#1E2922] px-8 py-3.5 rounded-full font-bold shadow-lg hover:bg-white transition uppercase tracking-wider text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.035-4.43c1.654.982 3.511 1.501 5.39 1.502 5.578.002 10.12-4.537 10.123-10.123.002-2.705-1.05-5.249-2.962-7.163C16.73 1.87 14.183.818 11.48.818c-5.584 0-10.126 4.54-10.128 10.126-.001 1.895.493 3.748 1.433 5.385L1.756 22.24l6.336-1.67zm12.355-6.61c-.33-.165-1.951-.963-2.251-1.072-.3-.11-.518-.165-.735.165-.218.33-.842 1.072-1.032 1.292-.19.22-.38.247-.71.082-.33-.165-1.393-.513-2.653-1.637-.98-.874-1.643-1.953-1.835-2.282-.19-.33-.02-.508.145-.671.148-.147.33-.385.495-.578.165-.192.22-.33.33-.55.11-.22.055-.412-.028-.577-.082-.165-.735-1.77-.993-2.42-.258-.627-.518-.543-.735-.543-.19 0-.41-.013-.627-.013-.218 0-.572.082-.871.412-.3.33-1.145 1.117-1.145 2.723 0 1.605 1.17 3.16 1.332 3.38 1.62 2.217 3.12 3.4 5.92 4.606 2.8.12 2.8.843 3.29.843.49 0 1.95-.797 2.225-1.567.275-.77.275-1.43.193-1.567-.083-.137-.303-.22-.633-.385z"/></svg>
                    <span>Chat on WhatsApp</span>
                </a>
                <a href="index.php#contact-section"
                   class="bg-white/10 backdrop-blur-sm border-2 border-white/20 hover:bg-white/20 text-white px-8 py-3.5 rounded-full font-bold transition uppercase tracking-wider text-xs">
                    Get In Touch
                </a>
            </div>
        </div>
    </section>

    <!-- Footer Component -->
    <?php require_once __DIR__ . '/footer.php'; ?>

</body>
</html>
