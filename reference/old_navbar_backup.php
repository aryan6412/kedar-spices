<?php
/**
 * BACKUP FILE: Old Navigation Bar and Top Mini-Bar
 * 
 * If you need to restore the old navbar, copy the HTML blocks below
 * and replace the <header> element in index.php or products.php.
 */
?>

<!-- ========================================== -->
<!-- 1. OLD TOP MINI-BAR (Insert right above the <header>) -->
<!-- ========================================== -->
<div class="bg-charcoal text-beige text-xs py-2 px-4 md:px-8 flex flex-col md:flex-row justify-between items-center gap-2 border-b border-white/5">
    <div class="flex items-center gap-4">
        <span class="flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-mustard" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
            <a href="tel:+917600404015" class="hover:text-mustard transition">+91 76004 04015</a> / <a href="tel:+919104082064" class="hover:text-mustard transition">+91 91040 82064</a>
        </span>
        <span class="hidden md:inline-flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-mustard" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <a href="mailto:kedarnathspices@gmail.com" class="hover:text-mustard transition">kedarnathspices@gmail.com</a>
        </span>
    </div>
    <div class="flex items-center gap-4">
        <a href="https://www.instagram.com/kedarnath_spices/" target="_blank" class="hover:text-mustard transition">Instagram</a>
        <a href="https://www.facebook.com/profile.php?id=61555670985387" target="_blank" class="hover:text-mustard transition">Facebook</a>
        <a href="https://www.linkedin.com/in/kedarnath-spices-b21208362" target="_blank" class="hover:text-mustard transition">LinkedIn</a>
    </div>
</div>


<!-- ========================================== -->
<!-- 2. OLD HEADER NAVIGATION (Replace the new <header> element) -->
<!-- ========================================== -->
<header x-data="{ mobileMenuOpen: false, scrolled: false }" 
        @scroll.window="scrolled = (window.pageYOffset > 50) ? true : false"
        :class="scrolled ? 'bg-beige/95 backdrop-blur-md shadow-md py-3' : 'bg-transparent py-5'"
        class="sticky top-0 z-[100] w-full transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 md:px-8 flex justify-between items-center">
        
        <!-- Logo -->
        <a href="index.php" class="flex items-center">
            <img src="https://kedarnathspices.com/storage/img/logo.png" alt="Kedarnath Spices & Herbs" class="h-12 md:h-16 transition-all duration-300 animate-spin-3d">
        </a>

        <!-- Desktop Nav Menu -->
        <nav class="hidden lg:flex items-center gap-8 font-medium">
            <a href="index.php" class="text-charcoal hover:text-terracotta transition pb-1">Home</a>
            <a href="index.php#about-heritage" class="text-charcoal hover:text-terracotta transition pb-1">About Us</a>
            <a href="products.php" class="text-charcoal hover:text-terracotta transition pb-1">Products</a>
            <a href="gallery.php" class="text-charcoal hover:text-terracotta transition pb-1">Gallery</a>
            <a href="index.php#contact-section" class="text-charcoal hover:text-terracotta transition pb-1">Contact Us</a>
        </nav>

        <!-- Quick Inquiry / Contact button -->
        <div class="hidden lg:flex items-center gap-4">
            <a href="https://api.whatsapp.com/send?phone=+917600404015&text=Hi,%20I%20am%20interested%20in%20your%20products." target="_blank" 
               class="bg-terracotta text-white px-5 py-2.5 rounded-full font-semibold hover:bg-terracotta-dark transition flex items-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 duration-200">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.035-4.43c1.654.982 3.511 1.501 5.39 1.502 5.578.002 10.12-4.537 10.123-10.123.002-2.705-1.05-5.249-2.962-7.163C16.73 1.87 14.183.818 11.48.818c-5.584 0-10.126 4.54-10.128 10.126-.001 1.895.493 3.748 1.433 5.385L1.756 22.24l6.336-1.67zm12.355-6.61c-.33-.165-1.951-.963-2.251-1.072-.3-.11-.518-.165-.735.165-.218.33-.842 1.072-1.032 1.292-.19.22-.38.247-.71.082-.33-.165-1.393-.513-2.653-1.637-.98-.874-1.643-1.953-1.835-2.282-.19-.33-.02-.508.145-.671.148-.147.33-.385.495-.578.165-.192.22-.33.33-.55.11-.22.055-.412-.028-.577-.082-.165-.735-1.77-.993-2.42-.258-.627-.518-.543-.735-.543-.19 0-.41-.013-.627-.013-.218 0-.572.082-.871.412-.3.33-1.145 1.117-1.145 2.723 0 1.605 1.17 3.16 1.332 3.38 1.62 2.217 3.12 3.4 5.92 4.606 2.8.12 2.8.843 3.29.843.49 0 1.95-.797 2.225-1.567.275-.77.275-1.43.193-1.567-.083-.137-.303-.22-.633-.385z"/></svg>
                <span>Inquiry</span>
            </a>
        </div>

        <!-- Mobile Menu Toggle Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-charcoal hover:text-terracotta focus:outline-none">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path :class="mobileMenuOpen ? 'hidden' : 'inline-flex'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                <path :class="mobileMenuOpen ? 'inline-flex' : 'hidden'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Mobile Drawer -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-full"
         class="fixed inset-0 z-[110] lg:hidden" style="display: none;">
        <div class="fixed inset-0 bg-charcoal/40 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>
        
        <div class="fixed right-0 top-0 bottom-0 w-4/5 max-w-sm bg-beige-light p-6 shadow-2xl flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-8 border-b border-charcoal/10 pb-4">
                    <img src="https://kedarnathspices.com/storage/img/logo.png" alt="Kedarnath Spices" class="h-12 animate-spin-3d">
                    <button @click="mobileMenuOpen = false" class="text-charcoal focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <nav class="flex flex-col gap-6 text-lg font-semibold">
                    <a href="index.php" class="text-terracotta border-l-4 border-terracotta pl-3" @click="mobileMenuOpen = false">Home</a>
                    <a href="index.php#about-heritage" class="text-charcoal hover:text-terracotta pl-3" @click="mobileMenuOpen = false">About Us</a>
                    <a href="products.php" class="text-charcoal hover:text-terracotta pl-3" @click="mobileMenuOpen = false">Products</a>
                    <a href="gallery.php" class="text-charcoal hover:text-terracotta pl-3" @click="mobileMenuOpen = false">Gallery</a>
                    <a href="index.php#contact-section" class="text-charcoal hover:text-terracotta pl-3" @click="mobileMenuOpen = false">Contact Us</a>
                </nav>
            </div>

            <div class="border-t border-charcoal/10 pt-6">
                <p class="text-sm font-medium mb-3">Get in Touch:</p>
                <a href="tel:+917600404015" class="block text-charcoal font-semibold mb-2">+91 76004 04015</a>
                <a href="mailto:kedarnathspices@gmail.com" class="block text-sm text-charcoal/70 mb-4">kedarnathspices@gmail.com</a>
                
                <a href="https://api.whatsapp.com/send?phone=+917600404015" class="w-full bg-terracotta text-white py-3 rounded-full flex justify-center items-center gap-2 font-bold shadow-md hover:bg-terracotta-dark">
                    <span>Send WhatsApp</span>
                </a>
            </div>
        </div>
    </div>
</header>
