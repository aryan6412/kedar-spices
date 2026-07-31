<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Single-Section Map with Cloud Detail Popup Demo - Kedarnath Spices</title>
    <link rel="stylesheet" href="css/tailwind.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#111B15] text-white font-sans p-4 md:p-8 min-h-screen">

    <div class="max-w-5xl mx-auto text-center space-y-3 mb-8">
        <span class="text-xs font-bold uppercase tracking-widest text-[#E0A838] bg-[#E0A838]/10 px-4 py-1.5 rounded-full border border-[#E0A838]/30">Single Section Concept</span>
        <h1 class="text-3xl md:text-5xl font-serif font-bold text-white">Unified Sourcing Map with Cloud Popup</h1>
        <p class="text-white/70 text-sm md:text-base">Click or hover any state dot on the map below. Details open in an elegant floating cloud popup modal over the map.</p>
    </div>

    <!-- MAP CONTAINER (EXPANDED TO MAX-W-6XL WITH OVERLAID HUD ELEMENTS & CURVY CORNERS) -->
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
                     :style="key==='kashmir' ? 'top:18%;left:37%;' : (key==='punjab' ? 'top:28%;left:33%;' : (key==='rajasthan' ? 'top:37%;left:27%;' : (key==='gujarat' ? 'top:47%;left:13%;' : (key==='mp' ? 'top:45%;left:39%;' : (key==='andhra' ? 'top:64%;left:45%;' : (key==='kerala' ? 'top:80%;left:34%;' : 'top:38%;left:83%;'))))))">
                    
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

    </section>

</body>
</html>
