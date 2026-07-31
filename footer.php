<!-- Custom Jagged Separator SVG for Footer (Earthy/Torn Mud transition) -->
<div class="w-full overflow-hidden leading-[0] select-none pointer-events-none translate-y-[4px]">
    <svg viewBox="0 0 1440 80" class="relative block w-full h-[50px] md:h-[90px] overflow-visible" preserveAspectRatio="none">
        <defs>
            <filter id="grunge-edge" x="-10%" y="-10%" width="120%" height="120%">
                <!-- Fractal noise to simulate soil grain and splatter -->
                <feTurbulence type="fractalNoise" baseFrequency="0.04" numOctaves="4" result="noise" />
                <!-- Displace graphic according to noise to make it rough -->
                <feDisplacementMap in="SourceGraphic" in2="noise" scale="35" xChannelSelector="R" yChannelSelector="G" />
            </filter>
        </defs>
        <!-- Wide path coordinates to absorb the edge noise without clipping -->
        <path d="M -50 50 Q 360 40, 720 55 T 1490 50 L 1490 100 L -50 100 Z" class="fill-[#3C1E13]" filter="url(#grunge-edge)"></path>
    </svg>
</div>

<!-- Redesigned Footer (Deep brown with organic accents and plowing farmer graphic) -->
<footer class="bg-[#3C1E13] text-[#FAF6F0] pt-12 pb-8 relative overflow-hidden">
    
    <!-- Faint Background Plowing Farmer Vector Artwork (Very low opacity for high-end look) -->
    <div class="absolute left-[35%] bottom-4 w-[450px] h-auto opacity-[0.035] pointer-events-none select-none text-[#FAF6F0] z-0">
        <svg viewBox="0 0 500 250" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-full">
            <!-- Farmer -->
            <path d="M 380,180 L 390,130 L 410,140 L 400,195 Z" />
            <circle cx="395" cy="115" r="12" />
            <!-- Whip -->
            <path d="M 385,130 C 350,90 280,110 240,135" />
            <!-- Plow -->
            <path d="M 370,210 L 320,205 L 290,150 L 360,145" />
            <path d="M 320,205 L 340,165" />
            <!-- Yoke / Beam -->
            <path d="M 310,195 L 180,170" />
            <!-- Bulls (Left) -->
            <path d="M 190,160 C 180,130 160,110 130,120 C 120,105 100,105 90,120 C 70,125 50,150 60,180 L 70,210 M 110,210 L 120,180" />
            <!-- Bulls (Right) -->
            <path d="M 230,165 C 220,135 200,115 170,125 C 160,110 140,110 130,125 C 110,130 90,155 100,185 L 110,215" />
            <!-- Soil Ground Line -->
            <path d="M 20,225 C 100,220 200,230 300,225 C 400,220 450,228 480,225" />
        </svg>
    </div>

    <div class="max-w-7xl mx-auto px-4 md:px-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 relative z-10">
        
        <!-- Address and Details -->
        <div class="lg:col-span-4 space-y-4">
            <h4 class="text-sm font-bold uppercase tracking-wider text-white">Address:</h4>
            <div class="space-y-4 text-sm text-[#FAF6F0]/80">
                <div>
                    <p class="font-bold text-xs uppercase tracking-wide text-[#E0A838]/90 mb-0.5">Registered Office Address:</p>
                    <p class="leading-relaxed font-light">
                        M/S KEDARNATH INTERNATIONAL,<br>
                        H 401, Cattle Shade, Gunj Bajar,<br>
                        Unjha - 384170, Gujarat, India.
                    </p>
                </div>
                <div>
                    <p class="font-bold text-xs uppercase tracking-wide text-[#E0A838]/90 mb-0.5">Head Office Address:</p>
                    <p class="leading-relaxed font-light">
                        M/S KEDARNATH INTERNATIONAL,<br>
                        H 401, Cattle Shade, Gunj Bajar,<br>
                        Unjha - 384170, Gujarat, India.
                    </p>
                </div>
                <div class="space-y-0.5 text-sm">
                    <p class="font-light"><strong class="font-semibold text-[#FAF6F0]">Tel:</strong> <a href="tel:+917600404015" class="hover:underline hover:text-[#E0A838] transition">+91 76004 04015</a>, <a href="tel:+919104082064" class="hover:underline hover:text-[#E0A838] transition">+91 91040 82064</a></p>
                    <p class="font-light"><strong class="font-semibold text-[#FAF6F0]">Email Id:</strong> <a href="mailto:kedarnathspices@gmail.com" class="hover:underline hover:text-[#E0A838] transition text-sm">kedarnathspices@gmail.com</a></p>
                </div>
            </div>
        </div>

        <!-- Customer Support -->
        <div class="lg:col-span-2 space-y-4">
            <h4 class="text-sm font-bold uppercase tracking-wider text-white">Customer Support</h4>
            <ul class="space-y-2 text-sm text-[#FAF6F0]/75 font-light">
                <li><a href="about.php" class="hover:text-[#E0A838] transition hover:underline">Who We Are</a></li>
                <li><a href="ipm.php" class="hover:text-[#E0A838] transition hover:underline">IPM Sourcing</a></li>
            </ul>
        </div>

        <!-- Quick Links -->
        <div class="lg:col-span-3 space-y-4">
            <h4 class="text-sm font-bold uppercase tracking-wider text-white">Quick Links</h4>
            <ul class="space-y-2 text-sm text-[#FAF6F0]/75 font-light">
                <li><a href="products.php" class="hover:text-[#E0A838] transition hover:underline">Our Products Range</a></li>
                <li><a href="index.php#gallery-section" class="hover:text-[#E0A838] transition hover:underline">Processing Plant Gallery</a></li>
                <li><a href="index.php#contact-section" class="hover:text-[#E0A838] transition hover:underline">Get in touch with us!</a></li>
            </ul>
        </div>

        <!-- Facebook Posts mockup -->
        <div class="lg:col-span-3 space-y-4">
            <h4 class="text-sm font-bold uppercase tracking-wider text-white">Facebook Posts</h4>
            
            <!-- Styled Facebook Card Mockup -->
            <div class="bg-white rounded-xl p-4 text-[#1E2922] shadow-lg max-w-sm border border-white/20 select-none">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#D96E48] rounded-full flex items-center justify-center text-white font-bold text-sm">
                        KS
                    </div>
                    <div>
                        <h5 class="font-bold text-sm text-[#1E2922] hover:underline cursor-pointer"><a href="https://www.facebook.com/profile.php?id=61555670985387" target="_blank">Kedarnath Spices & Herbs</a></h5>
                        <p class="text-[10px] text-[#1E2922]/50">971 followers</p>
                    </div>
                </div>
                
                <div class="mt-4 flex gap-2 border-t border-[#1E2922]/10 pt-3">
                    <a href="https://www.facebook.com/profile.php?id=61555670985387" target="_blank" class="flex-1 bg-[#1877F2] text-white py-1.5 px-3 rounded-lg text-xs font-bold hover:bg-[#166FE5] transition flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M9 8H7v3h2v9h4v-9h3.6l.4-3H13V6c0-.5.5-1 1-1h3V1H13c-3 0-4 2-4 4v3z"/></svg>
                        <span>Follow Page</span>
                    </a>
                    <a href="https://www.facebook.com/profile.php?id=61555670985387" target="_blank" class="bg-[#FAF3EC] hover:bg-[#E0A838]/20 py-1.5 px-3.5 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 border border-black/10">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/></svg>
                        <span>Share</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="max-w-7xl mx-auto px-4 md:px-8 mt-8 pt-6 pb-6 border-t border-white/10 text-xs text-[#FAF6F0]/60 flex flex-col items-center justify-center gap-2 text-center relative z-10">
        <p>© <?php echo date('Y'); ?> Kedarnath Spices & Herbs. All Rights Reserved. Unjha, Gujarat, India.</p>
        <p>Designed & Developed by <a href="https://veloxgroup.co.in/" target="_blank" rel="noopener noreferrer" class="hover:text-[#E0A838] text-white/80 font-semibold underline transition">Velox Group</a></p>
    </div>
</footer>

<!-- WhatsApp Floating Button -->
<a id="WhatsApp" href="https://api.whatsapp.com/send?phone=+917600404015&text=Hi" target="_blank" 
   class="fixed bottom-6 right-6 z-50 bg-[#25D366] hover:bg-[#20BA56] text-white w-14 h-14 rounded-full flex items-center justify-center shadow-2xl hover:shadow-green-500/20 transform hover:-translate-y-1 duration-200 transition-all group animate-bounce">
    <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.035-4.43c1.654.982 3.511 1.501 5.39 1.502 5.578.002 10.12-4.537 10.123-10.123.002-2.705-1.05-5.249-2.962-7.163C16.73 1.87 14.183.818 11.48.818c-5.584 0-10.126 4.54-10.128 10.126-.001 1.895.493 3.748 1.433 5.385L1.756 22.24l6.336-1.67zm12.355-6.61c-.33-.165-1.951-.963-2.251-1.072-.3-.11-.518-.165-.735.165-.218.33-.842 1.072-1.032 1.292-.19.22-.38.247-.71.082-.33-.165-1.393-.513-2.653-1.637-.98-.874-1.643-1.953-1.835-2.282-.19-.33-.02-.508.145-.671.148-.147.33-.385.495-.578.165-.192.22-.33.33-.55.11-.22.055-.412-.028-.577-.082-.165-.735-1.77-.993-2.42-.258-.627-.518-.543-.735-.543-.19 0-.41-.013-.627-.013-.218 0-.572.082-.871.412-.3.33-1.145 1.117-1.145 2.723 0 1.605 1.17 3.16 1.332 3.38 1.62 2.217 3.12 3.4 5.92 4.606 2.8.12 2.8.843 3.29.843.49 0 1.95-.797 2.225-1.567.275-.77.275-1.43.193-1.567-.083-.137-.303-.22-.633-.385z"/></svg>
</a>
