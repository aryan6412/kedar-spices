<?php
// Load Hero Section Configuration from MySQL 8.0 Database
require_once __DIR__ . '/db.php';
$hero_config = get_hero_config('ipm');
log_visitor_visit('ipm');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The IPM Journey: Farm to Table - Kedarnath Spices & Herbs | Unjha Organic Heritage</title>
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="Trace the interactive Farm-to-Table IPM Story of Kedarnath Spices. Discover how pure cumin, fennel, and psyllium travel from Gujarat farmers to global customers with zero chemical residue.">
    <meta name="keywords" content="IPM Spice Journey, Farm to Table Spices, Unjha Cumin Farmer, Zero Residue Spice Process, Biological Pest Control, Export Grade Spices">
    
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
<body class="bg-[#F2F7F4] font-sans antialiased text-[#1C2C23] overflow-x-hidden">

    <!-- Header Navigation -->
    <?php 
    $active_page = 'ipm';
    require_once __DIR__ . '/navbar.php'; 
    ?>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- IPM HERO SECTION — Admin Controlled (Video / Slider)        -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <?php if ($hero_config['mode'] === 'video'): ?>
    <!-- VIDEO HERO -->
    <section class="relative h-screen overflow-hidden bg-[#0D1C13]">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-[#0D1C13]/55 z-10"></div>
            <video autoplay muted loop playsinline class="w-full h-full object-cover">
                <source src="<?php echo htmlspecialchars($hero_config['video_url']); ?>" type="video/mp4">
            </video>
        </div>

    <?php else: ?>
    <!-- SLIDER HERO -->
    <section x-data="{
                activeSlide: 0,
                slides: <?php echo htmlspecialchars(json_encode($hero_config['slider_images']), ENT_QUOTES, 'UTF-8'); ?>,
                init() {
                    setInterval(() => {
                        this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                    }, 5000)
                }
             }"
             class="relative h-screen overflow-hidden bg-[#0D1C13]">

        <!-- Slide backgrounds -->
        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="activeSlide === index"
                 x-transition:enter="transition duration-1000 ease-out"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition duration-1000 ease-in"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-cover bg-center"
                 :style="`background-image: linear-gradient(rgba(13,28,19,0.6), rgba(13,28,19,0.75)), url('${slide}');`"
            ></div>
        </template>

        <!-- 5 Stage Interactive Slider Indicators -->
        <div class="absolute bottom-6 right-6 md:right-12 z-30 hidden sm:flex items-center gap-2 bg-black/40 backdrop-blur-md p-2 rounded-2xl border border-white/20">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="activeSlide = index"
                        :class="activeSlide === index ? 'bg-[#27AE60] text-white border-[#27AE60]' : 'bg-white/10 text-white/70 hover:bg-white/20 border-white/10'"
                        class="px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider border transition-all duration-300 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full" :class="activeSlide === index ? 'bg-white animate-pulse' : 'bg-white/40'"></span>
                    <span x-text="['01 Seeds', '02 Bio-Control', '03 Solar Traps', '04 Lab Test', '05 Packaging'][index] || ('Stage ' + (index + 1))"></span>
                </button>
            </template>
        </div>

        <!-- Mobile Dots -->
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 sm:hidden flex gap-2">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="activeSlide = index"
                        :class="activeSlide === index ? 'bg-[#27AE60] w-6' : 'bg-white/40 w-2'"
                        class="h-2 rounded-full transition-all duration-300"></button>
            </template>
        </div>

    <?php endif; ?>

        <!-- Decorative glow blobs -->
        <div class="absolute top-24 right-16 w-80 h-80 bg-[#27AE60]/10 rounded-full blur-3xl z-10 pointer-events-none"></div>
        <div class="absolute bottom-40 left-10 w-60 h-60 bg-[#E0A838]/10 rounded-full blur-3xl z-10 pointer-events-none"></div>

        <!-- Hero Content -->
        <div class="absolute bottom-16 sm:bottom-20 left-6 right-6 md:left-12 lg:left-24 z-20 max-w-3xl space-y-5 text-white">

            <!-- Eyebrow Tag -->
            <span class="inline-flex items-center gap-2 bg-[#27AE60]/20 border border-[#27AE60]/40 text-[#27AE60] text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full backdrop-blur-md">
                <span class="w-1.5 h-1.5 rounded-full bg-[#27AE60] animate-pulse inline-block"></span>
                <?php echo htmlspecialchars($hero_config['tag']); ?>
            </span>

            <!-- Main Headline -->
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold font-serif leading-[1.1] tracking-tight">
                <?php echo nl2br(htmlspecialchars($hero_config['title'])); ?>
            </h1>

            <!-- Subtitle -->
            <p class="text-white/70 text-sm sm:text-base md:text-lg leading-relaxed font-light max-w-xl">
                <?php echo htmlspecialchars($hero_config['desc']); ?>
            </p>

            <!-- Static IPM badges -->
            <div class="flex flex-wrap items-center gap-3 pt-1">
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 px-4 py-2 rounded-full text-white text-xs font-bold uppercase tracking-wider">
                    🐞 Biological Control First
                </div>
                <div class="flex items-center gap-2 bg-[#27AE60]/15 backdrop-blur-md border border-[#27AE60]/30 px-4 py-2 rounded-full text-[#27AE60] text-xs font-bold uppercase tracking-wider">
                    ✅ 0.00% Chemical Residue
                </div>
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 px-4 py-2 rounded-full text-white text-xs font-bold uppercase tracking-wider">
                    📜 EU &amp; US FDA Compliant
                </div>
            </div>

            <!-- Scroll down -->
            <div class="pt-4 flex items-center gap-2 text-white/40 text-[10px] uppercase tracking-widest">
                <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                <span>Explore Our IPM Journey</span>
            </div>
        </div>

    </section>

    <!-- STRICT SEQUENTIAL SCROLL-ACTIVATED STEPPER TIMELINE SECTION -->
    <section x-data="{
        activeStep: 1,
        updateActiveStep() {
            const steps = [1, 2, 3, 4, 5];
            const viewportTarget = window.innerHeight * 0.45;
            let closestStep = 1;
            let minDiff = Infinity;

            steps.forEach(step => {
                const headerEl = document.getElementById('step-header-' + step);
                if (headerEl) {
                    const rect = headerEl.getBoundingClientRect();
                    const diff = Math.abs(rect.top - viewportTarget);
                    if (diff < minDiff) {
                        minDiff = diff;
                        closestStep = step;
                    }
                }
            });

            if (this.activeStep !== closestStep) {
                this.activeStep = closestStep;
            }
        },
        init() {
            let ticking = false;
            const onScroll = () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        this.updateActiveStep();
                        ticking = false;
                    });
                    ticking = true;
                }
            };
            window.addEventListener('scroll', onScroll, { passive: true });
            this.updateActiveStep();
        }
    }" class="py-16 md:py-24 bg-[#F2F7F4] text-[#1C2C23] relative border-b border-[#27AE60]/15">
        <div class="max-w-4xl mx-auto px-6 md:px-12 relative z-10">
            
            <div class="space-y-16 relative">
                
                <!-- Stage 1 -->
                <div class="relative pl-14 space-y-3 cursor-pointer group min-h-[160px]">
                    <!-- Connecting Line to Stage 2 -->
                    <div class="absolute left-[19px] top-5 bottom-[-64px] w-1 bg-gradient-to-b from-[#27AE60] to-[#E0A838] pointer-events-none z-0"></div>

                    <!-- Number 1 Circle Badge -->
                    <div :class="activeStep === 1 ? 'bg-[#27AE60] text-white ring-4 ring-[#27AE60]/40 scale-125 shadow-2xl z-20 animate-pulse' : 'bg-[#27AE60] text-white scale-100 group-hover:scale-115 z-10'"
                         class="absolute left-0 top-0.5 w-10 h-10 rounded-full font-extrabold text-base flex items-center justify-center shadow-lg transition-all duration-700 ease-out transform-gpu">
                        <span class="relative z-10">1</span>
                        <span x-show="activeStep === 1" class="absolute inset-0 rounded-full bg-[#27AE60] animate-ping opacity-75 pointer-events-none"></span>
                    </div>

                    <div id="step-header-1" class="space-y-1 scroll-mt-32">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold uppercase tracking-widest text-[#27AE60]">Stage 01 • Soil Sourcing</span>
                            <span x-show="activeStep === 1" class="text-xs font-bold text-[#27AE60] transition-opacity duration-500 animate-pulse">● Active Stage</span>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold font-serif text-[#1C2C23] group-hover:text-[#27AE60] transition-colors duration-500">
                            Soil Bio-Priming in Unjha Fields
                        </h3>
                        <p class="text-sm text-[#1C2C23]/80 leading-relaxed font-light">
                            Every seed spice begins with trusted farmers in Unjha, Gujarat. Before sowing, soil is bio-primed using natural <strong class="text-[#1C2C23]">Neem Cake</strong> and <strong class="text-[#27AE60]">Trichoderma viride</strong> to naturally repel root pathogens.
                        </p>
                    </div>

                    <!-- Stage 1 Inline Details Box -->
                    <div :class="activeStep === 1 ? 'max-h-[800px] opacity-100 translate-y-0 pt-3' : 'max-h-0 opacity-0 -translate-y-4 pointer-events-none pt-0'"
                         class="overflow-hidden transition-all duration-800 ease-[cubic-bezier(0.16,1,0.3,1)] transform-gpu">
                        <div class="relative bg-white/95 backdrop-blur-md border-2 border-[#27AE60]/25 rounded-3xl p-6 shadow-xl space-y-4">
                            
                            <div class="relative rounded-2xl overflow-hidden shadow-lg border border-[#27AE60]/20 bg-[#1C2C23] group/img h-64 sm:h-72">
                                <img src="img/real_stage1_soil.jpg" 
                                     alt="Soil Bio-Priming in Unjha Fields Photo" 
                                     class="w-full h-full object-cover rounded-2xl group-hover/img:scale-105 transition-transform duration-700">
                                <span class="absolute top-3 right-3 bg-[#27AE60] text-white text-[10px] font-extrabold px-3.5 py-1.5 rounded-full uppercase shadow-md border border-white/20">
                                    🌱 Soil Bio-Priming • Unjha Gujarat
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div class="bg-[#F2F7F4] p-3.5 rounded-2xl border border-[#27AE60]/15 shadow-sm">
                                    <span class="text-[#1C2C23]/60 text-[10px] font-bold block uppercase">Bio Agent</span>
                                    <span class="font-bold text-[#27AE60]">Neem & Trichoderma</span>
                                </div>
                                <div class="bg-[#F2F7F4] p-3.5 rounded-2xl border border-[#27AE60]/15 shadow-sm">
                                    <span class="text-[#1C2C23]/60 text-[10px] font-bold block uppercase">Germination</span>
                                    <span class="font-bold text-[#27AE60]">99.8% Healthy</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stage 2 -->
                <div class="relative pl-14 space-y-3 cursor-pointer group min-h-[160px]">
                    <!-- Connecting Line to Stage 3 -->
                    <div class="absolute left-[19px] top-5 bottom-[-64px] w-1 bg-gradient-to-b from-[#E0A838] to-[#27AE60] pointer-events-none z-0"></div>

                    <!-- Number 2 Circle Badge -->
                    <div :class="activeStep === 2 ? 'bg-[#E0A838] text-[#1C2C23] ring-4 ring-[#E0A838]/40 scale-125 shadow-2xl z-20 animate-pulse' : 'bg-[#E0A838] text-[#1C2C23] scale-100 group-hover:scale-115 z-10'"
                         class="absolute left-0 top-0.5 w-10 h-10 rounded-full font-extrabold text-base flex items-center justify-center shadow-lg transition-all duration-700 ease-out transform-gpu">
                        <span class="relative z-10">2</span>
                        <span x-show="activeStep === 2" class="absolute inset-0 rounded-full bg-[#E0A838] animate-ping opacity-75 pointer-events-none"></span>
                    </div>

                    <div id="step-header-2" class="space-y-1 scroll-mt-32">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold uppercase tracking-widest text-[#E0A838]">Stage 02 • Bio Defenders</span>
                            <span x-show="activeStep === 2" class="text-xs font-bold text-[#E0A838] transition-opacity duration-500 animate-pulse">● Active Stage</span>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold font-serif text-[#1C2C23] group-hover:text-[#E0A838] transition-colors duration-500">
                            Biological Protection & Solar Traps
                        </h3>
                        <p class="text-sm text-[#1C2C23]/80 leading-relaxed font-light">
                            Instead of toxic synthetic pesticides, our IPM farms release beneficial predator insects (<strong class="text-[#1C2C23]">Ladybird Beetles</strong>, <strong class="text-[#27AE60]">Parasitic Wasps</strong>) and deploy solar-powered pheromone traps for 100% natural crop equilibrium.
                        </p>
                    </div>

                    <!-- Stage 2 Inline Details Box -->
                    <div :class="activeStep === 2 ? 'max-h-[800px] opacity-100 translate-y-0 pt-3' : 'max-h-0 opacity-0 -translate-y-4 pointer-events-none pt-0'"
                         class="overflow-hidden transition-all duration-800 ease-[cubic-bezier(0.16,1,0.3,1)] transform-gpu">
                        <div class="relative bg-white/95 backdrop-blur-md border-2 border-[#E0A838]/25 rounded-3xl p-6 shadow-xl space-y-4">
                            
                            <div class="relative rounded-2xl overflow-hidden shadow-lg border border-[#E0A838]/20 bg-[#1C2C23] group/img h-64 sm:h-72">
                                <img src="img/real_stage2_bio.jpg" 
                                     alt="Ladybird Beetle Biological Defense Photo" 
                                     class="w-full h-full object-cover rounded-2xl group-hover/img:scale-105 transition-transform duration-700">
                                <span class="absolute top-3 right-3 bg-[#E0A838] text-[#1C2C23] text-[10px] font-extrabold px-3.5 py-1.5 rounded-full uppercase shadow-md border border-white/20">
                                    🐞 Ladybird Beetle Biological Defender
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div class="bg-[#F2F7F4] p-3.5 rounded-2xl border border-[#27AE60]/15 shadow-sm">
                                    <span class="text-[#1C2C23]/60 text-[10px] font-bold block uppercase">Predators</span>
                                    <span class="font-bold text-[#27AE60]">Ladybird Beetles & Wasps</span>
                                </div>
                                <div class="bg-[#F2F7F4] p-3.5 rounded-2xl border border-[#27AE60]/15 shadow-sm">
                                    <span class="text-[#1C2C23]/60 text-[10px] font-bold block uppercase">Pheromone Traps</span>
                                    <span class="font-bold text-[#E0A838]">Solar Automated</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stage 3 -->
                <div class="relative pl-14 space-y-3 cursor-pointer group min-h-[160px]">
                    <!-- Connecting Line to Stage 4 -->
                    <div class="absolute left-[19px] top-5 bottom-[-64px] w-1 bg-gradient-to-b from-[#27AE60] to-[#E0A838] pointer-events-none z-0"></div>

                    <!-- Number 3 Circle Badge -->
                    <div :class="activeStep === 3 ? 'bg-[#27AE60] text-white ring-4 ring-[#27AE60]/40 scale-125 shadow-2xl z-20 animate-pulse' : 'bg-[#27AE60] text-white scale-100 group-hover:scale-115 z-10'"
                         class="absolute left-0 top-0.5 w-10 h-10 rounded-full font-extrabold text-base flex items-center justify-center shadow-lg transition-all duration-700 ease-out transform-gpu">
                        <span class="relative z-10">3</span>
                        <span x-show="activeStep === 3" class="absolute inset-0 rounded-full bg-[#27AE60] animate-ping opacity-75 pointer-events-none"></span>
                    </div>

                    <div id="step-header-3" class="space-y-1 scroll-mt-32">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold uppercase tracking-widest text-[#27AE60]">Stage 03 • Sortex Machine</span>
                            <span x-show="activeStep === 3" class="text-xs font-bold text-[#27AE60] transition-opacity duration-500 animate-pulse">● Active Stage</span>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold font-serif text-[#1C2C23] group-hover:text-[#27AE60] transition-colors duration-500">
                            Peak Oil Harvest & Optical Sortex Grading
                        </h3>
                        <p class="text-sm text-[#1C2C23]/80 leading-relaxed font-light">
                            Harvested at peak natural aroma maturity, raw seeds undergo triple-stage destoning, magnetic metal separation, and color laser Sortex grading to achieve <strong class="text-[#27AE60]">99.5%+ purity</strong>.
                        </p>
                    </div>

                    <!-- Stage 3 Inline Details Box -->
                    <div :class="activeStep === 3 ? 'max-h-[800px] opacity-100 translate-y-0 pt-3' : 'max-h-0 opacity-0 -translate-y-4 pointer-events-none pt-0'"
                         class="overflow-hidden transition-all duration-800 ease-[cubic-bezier(0.16,1,0.3,1)] transform-gpu">
                        <div class="relative bg-white/95 backdrop-blur-md border-2 border-[#27AE60]/25 rounded-3xl p-6 shadow-xl space-y-4">
                            
                            <div class="relative rounded-2xl overflow-hidden shadow-lg border border-[#27AE60]/20 bg-[#1C2C23] group/img h-64 sm:h-72">
                                <img src="img/real_stage3_sortex.jpg" 
                                     alt="Optical Sortex Grain Sorting Photo" 
                                     class="w-full h-full object-cover rounded-2xl group-hover/img:scale-105 transition-transform duration-700">
                                <span class="absolute top-3 right-3 bg-[#27AE60] text-white text-[10px] font-extrabold px-3.5 py-1.5 rounded-full uppercase shadow-md border border-white/20">
                                    🔬 Optical Laser Sortex Machine
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div class="bg-[#F2F7F4] p-3.5 rounded-2xl border border-[#27AE60]/15 shadow-sm">
                                    <span class="text-[#1C2C23]/60 text-[10px] font-bold block uppercase">Destoning</span>
                                    <span class="font-bold text-[#27AE60]">Triple Stage</span>
                                </div>
                                <div class="bg-[#F2F7F4] p-3.5 rounded-2xl border border-[#27AE60]/15 shadow-sm">
                                    <span class="text-[#1C2C23]/60 text-[10px] font-bold block uppercase">Optical Sortex</span>
                                    <span class="font-bold text-[#27AE60]">Color Laser 99.5%+</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stage 4 -->
                <div class="relative pl-14 space-y-3 cursor-pointer group min-h-[160px]">
                    <!-- Connecting Line to Stage 5 -->
                    <div class="absolute left-[19px] top-5 bottom-[-64px] w-1 bg-gradient-to-b from-[#E0A838] to-[#27AE60] pointer-events-none z-0"></div>

                    <!-- Number 4 Circle Badge -->
                    <div :class="activeStep === 4 ? 'bg-[#E0A838] text-[#1C2C23] ring-4 ring-[#E0A838]/40 scale-125 shadow-2xl z-20 animate-pulse' : 'bg-[#E0A838] text-[#1C2C23] scale-100 group-hover:scale-115 z-10'"
                         class="absolute left-0 top-0.5 w-10 h-10 rounded-full font-extrabold text-base flex items-center justify-center shadow-lg transition-all duration-700 ease-out transform-gpu">
                        <span class="relative z-10">4</span>
                        <span x-show="activeStep === 4" class="absolute inset-0 rounded-full bg-[#E0A838] animate-ping opacity-75 pointer-events-none"></span>
                    </div>

                    <div id="step-header-4" class="space-y-1 scroll-mt-32">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold uppercase tracking-widest text-[#E0A838]">Stage 04 • GC-MS Lab Test</span>
                            <span x-show="activeStep === 4" class="text-xs font-bold text-[#E0A838] transition-opacity duration-500 animate-pulse">● Active Stage</span>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold font-serif text-[#1C2C23] group-hover:text-[#E0A838] transition-colors duration-500">
                            500+ Compound Laboratory GC-MS Testing
                        </h3>
                        <p class="text-sm text-[#1C2C23]/80 leading-relaxed font-light">
                            Representative samples from every lot are screened via GC-MS/MS and LC-MS/MS in accredited labs, certifying <strong class="text-[#27AE60]">0.00% pesticide residue</strong> and full EU MRL & US FDA compliance.
                        </p>
                    </div>

                    <!-- Stage 4 Inline Details Box -->
                    <div :class="activeStep === 4 ? 'max-h-[800px] opacity-100 translate-y-0 pt-3' : 'max-h-0 opacity-0 -translate-y-4 pointer-events-none pt-0'"
                         class="overflow-hidden transition-all duration-800 ease-[cubic-bezier(0.16,1,0.3,1)] transform-gpu">
                        <div class="relative bg-white/95 backdrop-blur-md border-2 border-[#E0A838]/25 rounded-3xl p-6 shadow-xl space-y-4">
                            
                            <div class="relative rounded-2xl overflow-hidden shadow-lg border border-[#E0A838]/20 bg-[#1C2C23] group/img h-64 sm:h-72">
                                <img src="img/real_stage4_lab.jpg" 
                                     alt="GC-MS Laboratory Testing Photo" 
                                     class="w-full h-full object-cover rounded-2xl group-hover/img:scale-105 transition-transform duration-700">
                                <span class="absolute top-3 right-3 bg-[#E0A838] text-[#1C2C23] text-[10px] font-extrabold px-3.5 py-1.5 rounded-full uppercase shadow-md border border-white/20">
                                    📜 Accredited GC-MS Lab Testing
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div class="bg-[#F2F7F4] p-3.5 rounded-2xl border border-[#27AE60]/15 shadow-sm">
                                    <span class="text-[#1C2C23]/60 text-[10px] font-bold block uppercase">Compounds Tested</span>
                                    <span class="font-bold text-[#27AE60]">500+ Screened</span>
                                </div>
                                <div class="bg-[#F2F7F4] p-3.5 rounded-2xl border border-[#27AE60]/15 shadow-sm">
                                    <span class="text-[#1C2C23]/60 text-[10px] font-bold block uppercase">Pesticides</span>
                                    <span class="font-bold text-[#27AE60]">0.00% Zero Residue</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stage 5 -->
                <div class="relative pl-14 space-y-3 cursor-pointer group min-h-[160px]">
                    <!-- Number 5 Circle Badge -->
                    <div :class="activeStep === 5 ? 'bg-[#27AE60] text-white ring-4 ring-[#27AE60]/40 scale-125 shadow-2xl z-20 animate-pulse' : 'bg-[#27AE60] text-white scale-100 group-hover:scale-115 z-10'"
                         class="absolute left-0 top-0.5 w-10 h-10 rounded-full font-extrabold text-base flex items-center justify-center shadow-lg transition-all duration-700 ease-out transform-gpu">
                        <span class="relative z-10">5</span>
                        <span x-show="activeStep === 5" class="absolute inset-0 rounded-full bg-[#27AE60] animate-ping opacity-75 pointer-events-none"></span>
                    </div>

                    <div id="step-header-5" class="space-y-1 scroll-mt-32">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold uppercase tracking-widest text-[#27AE60]">Stage 05 • Global Delivery</span>
                            <span x-show="activeStep === 5" class="text-xs font-bold text-[#27AE60] transition-opacity duration-500 animate-pulse">● Active Stage</span>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold font-serif text-[#1C2C23] group-hover:text-[#27AE60] transition-colors duration-500">
                            Vacuum Aroma-Lock Packaging & Global Delivery
                        </h3>
                        <p class="text-sm text-[#1C2C23]/80 leading-relaxed font-light">
                            Spices are sealed into multi-wall aroma barrier bags with batch QR code traceability and shipped directly from Unjha, India to global customer kitchens worldwide.
                        </p>
                    </div>

                    <!-- Stage 5 Inline Details Box -->
                    <div :class="activeStep === 5 ? 'max-h-[800px] opacity-100 translate-y-0 pt-3' : 'max-h-0 opacity-0 -translate-y-4 pointer-events-none pt-0'"
                         class="overflow-hidden transition-all duration-800 ease-[cubic-bezier(0.16,1,0.3,1)] transform-gpu">
                        <div class="relative bg-white/95 backdrop-blur-md border-2 border-[#27AE60]/25 rounded-3xl p-6 shadow-xl space-y-4">
                            
                            <div class="relative rounded-2xl overflow-hidden shadow-lg border border-[#27AE60]/20 bg-[#1C2C23] group/img h-64 sm:h-72">
                                <img src="img/real_stage5_delivery.jpg" 
                                     alt="Vacuum Sealed Spice Packaging and Shipping Photo" 
                                     class="w-full h-full object-cover rounded-2xl group-hover/img:scale-105 transition-transform duration-700">
                                <span class="absolute top-3 right-3 bg-[#27AE60] text-white text-[10px] font-extrabold px-3.5 py-1.5 rounded-full uppercase shadow-md border border-white/20">
                                    📦 Vacuum Packaging & Global Freight
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div class="bg-[#F2F7F4] p-3.5 rounded-2xl border border-[#27AE60]/15 shadow-sm">
                                    <span class="text-[#1C2C23]/60 text-[10px] font-bold block uppercase">Packaging</span>
                                    <span class="font-bold text-[#27AE60]">Vacuum Aroma Lock</span>
                                </div>
                                <div class="bg-[#F2F7F4] p-3.5 rounded-2xl border border-[#27AE60]/15 shadow-sm">
                                    <span class="text-[#1C2C23]/60 text-[10px] font-bold block uppercase">Traceability</span>
                                    <span class="font-bold text-[#E0A838]">Batch QR Code</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- IPM OVERVIEW SECTION -->
    <section class="relative py-20 md:py-28 px-6 md:px-12 bg-cover bg-center overflow-hidden border-b border-white/10" style="background-image: url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=1920&auto=format&fit=crop');">
        
        <!-- Dark Vignette Overlay for Crisp White Text Contrast -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#0D1C13]/90 via-[#0D1C13]/85 to-[#0D1C13]/90 backdrop-blur-[2px] pointer-events-none"></div>

        <div class="max-w-6xl mx-auto relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left Side: Real Photorealistic 3D IPM Diagram Poster Card -->
                <div class="lg:col-span-6 relative">
                    <img src="img/real_ipm_overview.jpg" 
                         alt="What is IPM Realistic Diagram Poster" 
                         class="w-full h-auto object-cover rounded-[32px] shadow-[0_25px_60px_rgba(0,0,0,0.5)] border border-white/20 hover:scale-102 transition-transform duration-500">
                </div>

                <!-- Right Side: White Typography Text (Exactly as in Reference Screenshot) -->
                <div class="lg:col-span-6 space-y-6 text-white">
                    
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-serif leading-tight text-white">
                        IPM Overview
                    </h2>

                    <div class="space-y-6 text-white/90 text-sm sm:text-base md:text-lg leading-relaxed font-light">
                        <p>
                            IPM is an ecosystem-based strategy that focuses on long-term prevention of pests or their damage through a combination of techniques such as biological control, habitat manipulation, modification of cultural practices, and use of resistant varieties.
                        </p>
                        <p>
                            Pesticides are used only after monitoring indicates they are needed according to established guidelines, and treatments are made with the goal of removing only the target organism. Pest control materials are selected and applied in a manner that minimizes risks to human health, beneficial and nontarget organisms, and the environment.
                        </p>
                    </div>

                    <!-- Clean Highlight Badges -->
                    <div class="pt-4 flex flex-wrap items-center gap-3 text-xs font-semibold">
                        <div class="bg-white/10 backdrop-blur-md border border-white/25 px-4 py-2 rounded-full text-white">
                            🐞 Biological Control First
                        </div>
                        <div class="bg-white/10 backdrop-blur-md border border-white/25 px-4 py-2 rounded-full text-white">
                            ✨ 0.00% Chemical Residue
                        </div>
                        <div class="bg-white/10 backdrop-blur-md border border-white/25 px-4 py-2 rounded-full text-white">
                            📜 EU & US FDA Compliant
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- SECTION 1: BENEFITS OF IPM SECTION -->
    <section class="py-16 md:py-24 bg-[#F2F7F4] text-[#1C2C23] relative border-b border-[#27AE60]/15">
        <div class="max-w-6xl mx-auto px-6 md:px-12 relative z-10 space-y-12">
            
            <!-- Section Header -->
            <div class="text-center space-y-3">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-serif text-[#1C2C23]">
                    Benefits of <span class="text-[#27AE60]">IPM</span>
                </h2>
                <p class="text-sm md:text-base text-[#1C2C23]/70 font-light max-w-xl mx-auto">
                    Sustainable agricultural practices engineered for health, environmental longevity, and economic efficiency.
                </p>
            </div>

            <!-- 6 Benefits Cards Grid (3 columns x 2 rows) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Benefit 1: Environmental Protection -->
                <div class="bg-white rounded-2xl p-6 border-2 border-[#27AE60]/30 shadow-sm hover:shadow-xl hover:border-[#27AE60] transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-4 right-4 text-[#27AE60]/20 group-hover:text-[#27AE60]/40 transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z M12 7v5l3 3"/>
                        </svg>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-lg font-bold text-[#27AE60]">Environmental Protection</h3>
                        <p class="text-xs sm:text-sm text-[#1C2C23]/80 leading-relaxed font-light">
                            Reduces reliance on harmful pesticides, protecting soil, water, and non-target species.
                        </p>
                    </div>
                </div>

                <!-- Benefit 2: Human & Animal Safety -->
                <div class="bg-white rounded-2xl p-6 border-2 border-[#27AE60]/30 shadow-sm hover:shadow-xl hover:border-[#27AE60] transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-4 right-4 text-[#27AE60]/20 group-hover:text-[#27AE60]/40 transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-lg font-bold text-[#27AE60]">Human & Animal Safety</h3>
                        <p class="text-xs sm:text-sm text-[#1C2C23]/80 leading-relaxed font-light">
                            Minimizes exposure to toxic chemicals, ensuring safer conditions for both humans and animals.
                        </p>
                    </div>
                </div>

                <!-- Benefit 3: Cost-Effective -->
                <div class="bg-white rounded-2xl p-6 border-2 border-[#27AE60]/30 shadow-sm hover:shadow-xl hover:border-[#27AE60] transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-4 right-4 text-[#27AE60]/20 group-hover:text-[#27AE60]/40 transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-lg font-bold text-[#27AE60]">Cost-Effective</h3>
                        <p class="text-xs sm:text-sm text-[#1C2C23]/80 leading-relaxed font-light">
                            Prevents unnecessary spraying, saving money on inputs.
                        </p>
                    </div>
                </div>

                <!-- Benefit 4: Sustainable Agriculture -->
                <div class="bg-white rounded-2xl p-6 border-2 border-[#27AE60]/30 shadow-sm hover:shadow-xl hover:border-[#27AE60] transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-4 right-4 text-[#27AE60]/20 group-hover:text-[#27AE60]/40 transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 002.5-2.5V11a2 2 0 00-2-2h-1a2 2 0 01-2-2V4.5A2.5 2.5 0 0012.5 2h-1A2.5 2.5 0 009 4.5"/>
                        </svg>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-lg font-bold text-[#27AE60]">Sustainable Agriculture</h3>
                        <p class="text-xs sm:text-sm text-[#1C2C23]/80 leading-relaxed font-light">
                            Maintains biodiversity and natural pest control agents.
                        </p>
                    </div>
                </div>

                <!-- Benefit 5: Delays Pest Resistance -->
                <div class="bg-white rounded-2xl p-6 border-2 border-[#27AE60]/30 shadow-sm hover:shadow-xl hover:border-[#27AE60] transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-4 right-4 text-[#27AE60]/20 group-hover:text-[#27AE60]/40 transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-lg font-bold text-[#27AE60]">Delays Pest Resistance</h3>
                        <p class="text-xs sm:text-sm text-[#1C2C23]/80 leading-relaxed font-light">
                            Reduces chances of pests adapting to chemicals.
                        </p>
                    </div>
                </div>

                <!-- Benefit 6: Improved Crop Yield & Quality -->
                <div class="bg-white rounded-2xl p-6 border-2 border-[#27AE60]/30 shadow-sm hover:shadow-xl hover:border-[#27AE60] transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-4 right-4 text-[#27AE60]/20 group-hover:text-[#27AE60]/40 transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-lg font-bold text-[#27AE60]">Improved Crop Yield & Quality</h3>
                        <p class="text-xs sm:text-sm text-[#1C2C23]/80 leading-relaxed font-light">
                            Keeps pest damage within limits, ensuring healthier crops and better yield.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- SECTION 2: BACKWARD INTEGRATION & PROCESSING (PRODUCTS COVERED) -->
    <section class="py-16 md:py-24 bg-[#F2F7F4] text-[#1C2C23] relative">
        <div class="max-w-6xl mx-auto px-6 md:px-12 relative z-10 space-y-12">
            
            <!-- Section Header -->
            <div class="text-center space-y-3">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-serif text-[#27AE60]">
                    Backward Integration & Processing
                </h2>
                <p class="text-sm md:text-base text-[#1C2C23]/70 font-light max-w-xl mx-auto">
                    Complete farm-to-factory traceability and quality control across all our premium organic spice varieties.
                </p>
            </div>

            <!-- Products Covered Box Container -->
            <div class="relative bg-[#EBF5EE] border-2 border-[#27AE60]/30 rounded-[32px] p-8 md:p-12 space-y-8 shadow-sm">
                
                <!-- Products Covered Tab Badge (Top Left) -->
                <div class="absolute -top-5 left-8 bg-[#27AE60] text-white px-6 py-2 rounded-xl text-sm font-extrabold shadow-md tracking-wider">
                    Products Covered
                </div>

                <!-- Products Pill Grid (4 columns responsive) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 pt-4">
                    
                    <!-- 1. Cumin Seeds -->
                    <div class="bg-white border border-[#27AE60]/30 rounded-2xl p-4 flex items-center gap-3.5 shadow-sm hover:border-[#27AE60] hover:shadow-md hover:scale-102 transition-all duration-300 group">
                        <div class="w-10 h-10 rounded-xl bg-[#27AE60]/10 flex items-center justify-center text-xl shrink-0 group-hover:bg-[#27AE60] group-hover:text-white transition-colors duration-300">
                            🌾
                        </div>
                        <span class="text-sm font-bold text-[#1C2C23]">Cumin Seeds</span>
                    </div>

                    <!-- 2. Ajwain Seeds -->
                    <div class="bg-white border border-[#27AE60]/30 rounded-2xl p-4 flex items-center gap-3.5 shadow-sm hover:border-[#27AE60] hover:shadow-md hover:scale-102 transition-all duration-300 group">
                        <div class="w-10 h-10 rounded-xl bg-[#27AE60]/10 flex items-center justify-center text-xl shrink-0 group-hover:bg-[#27AE60] group-hover:text-white transition-colors duration-300">
                            🌿
                        </div>
                        <span class="text-sm font-bold text-[#1C2C23]">Ajwain Seeds</span>
                    </div>

                    <!-- 3. Coriander Seeds -->
                    <div class="bg-white border border-[#27AE60]/30 rounded-2xl p-4 flex items-center gap-3.5 shadow-sm hover:border-[#27AE60] hover:shadow-md hover:scale-102 transition-all duration-300 group">
                        <div class="w-10 h-10 rounded-xl bg-[#27AE60]/10 flex items-center justify-center text-xl shrink-0 group-hover:bg-[#27AE60] group-hover:text-white transition-colors duration-300">
                            🍃
                        </div>
                        <span class="text-sm font-bold text-[#1C2C23]">Coriander Seeds</span>
                    </div>

                    <!-- 4. Flax-seeds -->
                    <div class="bg-white border border-[#27AE60]/30 rounded-2xl p-4 flex items-center gap-3.5 shadow-sm hover:border-[#27AE60] hover:shadow-md hover:scale-102 transition-all duration-300 group">
                        <div class="w-10 h-10 rounded-xl bg-[#27AE60]/10 flex items-center justify-center text-xl shrink-0 group-hover:bg-[#27AE60] group-hover:text-white transition-colors duration-300">
                            🌾
                        </div>
                        <span class="text-sm font-bold text-[#1C2C23]">Flax-seeds</span>
                    </div>

                    <!-- 5. Fennel Seeds -->
                    <div class="bg-white border border-[#27AE60]/30 rounded-2xl p-4 flex items-center gap-3.5 shadow-sm hover:border-[#27AE60] hover:shadow-md hover:scale-102 transition-all duration-300 group">
                        <div class="w-10 h-10 rounded-xl bg-[#27AE60]/10 flex items-center justify-center text-xl shrink-0 group-hover:bg-[#27AE60] group-hover:text-white transition-colors duration-300">
                            🌾
                        </div>
                        <span class="text-sm font-bold text-[#1C2C23]">Fennel Seeds</span>
                    </div>

                    <!-- 6. Black Cumin Seeds -->
                    <div class="bg-white border border-[#27AE60]/30 rounded-2xl p-4 flex items-center gap-3.5 shadow-sm hover:border-[#27AE60] hover:shadow-md hover:scale-102 transition-all duration-300 group">
                        <div class="w-10 h-10 rounded-xl bg-[#27AE60]/10 flex items-center justify-center text-xl shrink-0 group-hover:bg-[#27AE60] group-hover:text-white transition-colors duration-300">
                            🌰
                        </div>
                        <span class="text-sm font-bold text-[#1C2C23]">Black Cumin Seeds</span>
                    </div>

                    <!-- 7. Fenugreek Seeds -->
                    <div class="bg-white border border-[#27AE60]/30 rounded-2xl p-4 flex items-center gap-3.5 shadow-sm hover:border-[#27AE60] hover:shadow-md hover:scale-102 transition-all duration-300 group">
                        <div class="w-10 h-10 rounded-xl bg-[#27AE60]/10 flex items-center justify-center text-xl shrink-0 group-hover:bg-[#27AE60] group-hover:text-white transition-colors duration-300">
                            🫘
                        </div>
                        <span class="text-sm font-bold text-[#1C2C23]">Fenugreek Seeds</span>
                    </div>

                    <!-- 8. Dill Seeds -->
                    <div class="bg-white border border-[#27AE60]/30 rounded-2xl p-4 flex items-center gap-3.5 shadow-sm hover:border-[#27AE60] hover:shadow-md hover:scale-102 transition-all duration-300 group">
                        <div class="w-10 h-10 rounded-xl bg-[#27AE60]/10 flex items-center justify-center text-xl shrink-0 group-hover:bg-[#27AE60] group-hover:text-white transition-colors duration-300">
                            🌿
                        </div>
                        <span class="text-sm font-bold text-[#1C2C23]">Dill Seeds</span>
                    </div>

                    <!-- 9. Fenugreek Leaves -->
                    <div class="bg-white border border-[#27AE60]/30 rounded-2xl p-4 flex items-center gap-3.5 shadow-sm hover:border-[#27AE60] hover:shadow-md hover:scale-102 transition-all duration-300 group sm:col-start-1 md:col-start-2 lg:col-start-2">
                        <div class="w-10 h-10 rounded-xl bg-[#27AE60]/10 flex items-center justify-center text-xl shrink-0 group-hover:bg-[#27AE60] group-hover:text-white transition-colors duration-300">
                            🌿
                        </div>
                        <span class="text-sm font-bold text-[#1C2C23]">Fenugreek Leaves</span>
                    </div>

                    <!-- 10. Celery Seeds -->
                    <div class="bg-white border border-[#27AE60]/30 rounded-2xl p-4 flex items-center gap-3.5 shadow-sm hover:border-[#27AE60] hover:shadow-md hover:scale-102 transition-all duration-300 group lg:col-start-3">
                        <div class="w-10 h-10 rounded-xl bg-[#27AE60]/10 flex items-center justify-center text-xl shrink-0 group-hover:bg-[#27AE60] group-hover:text-white transition-colors duration-300">
                            🍃
                        </div>
                        <span class="text-sm font-bold text-[#1C2C23]">Celery Seeds</span>
                    </div>

                </div>

            </div>

            <!-- Direct Order CTA Button -->
            <div class="pt-4 text-center">
                <a href="https://api.whatsapp.com/send?phone=+917600404015&text=Hello%20Kedarnath%20Spices,%20I%20want%20to%20order%20IPM%20Certified%20Spices" target="_blank"
                   class="inline-flex items-center justify-center gap-3 bg-[#27AE60] hover:bg-[#1C2C23] text-white px-8 py-4 rounded-full font-bold text-xs uppercase tracking-wider transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105">
                    <span>Order IPM Certified Spices on WhatsApp 📲</span>
                </a>
            </div>

        </div>
    </section>

    <!-- Footer Component -->
    <?php require_once __DIR__ . '/footer.php'; ?>

</body>
</html>
