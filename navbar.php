<?php
// Determine active page automatically if not explicitly set
if (!isset($active_page)) {
    $current_script = basename($_SERVER['SCRIPT_NAME']);
    if ($current_script === 'about.php') {
        $active_page = 'about';
    } elseif ($current_script === 'products.php') {
        $active_page = 'products';
    } elseif ($current_script === 'ipm.php') {
        $active_page = 'ipm';
    } elseif ($current_script === 'contact.php') {
        $active_page = 'contact';
    } else {
        $active_page = 'home';
    }
}
?>
<!-- Header Navigation Component -->
<header x-data="{ mobileMenuOpen: false, scrolled: false }" 
        @scroll.window="scrolled = (window.pageYOffset > 50) ? true : false"
        :class="scrolled ? 'bg-transparent py-3' : 'bg-transparent py-6'"
        class="fixed top-0 left-0 right-0 z-[100] w-full transition-all duration-300">
    <div class="max-w-7xl mx-auto px-6 md:px-12 flex justify-between items-center">
        
        <!-- Logo -->
        <a href="index.php" class="flex items-center gap-3">
            <img src="https://kedarnathspices.com/storage/img/logo.png" alt="Kedarnath Spices & Herbs" class="h-12 md:h-16 transition-all duration-300 animate-spin-3d">
        </a>

        <!-- Desktop Nav Menu -->
        <div class="hidden lg:flex items-center gap-4">
            <nav class="bg-[#1E2922]/80 backdrop-blur-md px-6 py-2.5 rounded-full border border-white/15 flex items-center gap-7 text-white/90 text-xs font-semibold uppercase tracking-wider shadow-lg">
                <a href="index.php" class="<?php echo ($active_page === 'home') ? 'text-[#E0A838] font-bold' : 'hover:text-[#E0A838]'; ?> transition">Home</a>
                <a href="about.php" class="<?php echo ($active_page === 'about') ? 'text-[#E0A838] font-bold' : 'hover:text-[#E0A838]'; ?> transition">About Us</a>
                <a href="products.php" class="<?php echo ($active_page === 'products') ? 'text-[#E0A838] font-bold' : 'hover:text-[#E0A838]'; ?> transition">Products</a>
                <a href="ipm.php" class="<?php echo ($active_page === 'ipm') ? 'text-[#E0A838] font-bold' : 'hover:text-[#E0A838]'; ?> transition">IPM Sourcing</a>
                <a href="contact.php" class="bg-[#FAF3EC] text-[#1E2922] px-5 py-2 rounded-full font-bold hover:bg-[#E0A838] hover:text-[#1E2922] transition shadow-md <?php echo ($active_page === 'contact') ? 'bg-[#E0A838] text-[#1E2922]' : ''; ?>">Contact Us</a>
            </nav>
        </div>

        <!-- Mobile Menu Toggle Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden w-11 h-11 bg-[#1E2922]/80 backdrop-blur-md border border-white/15 rounded-full flex items-center justify-center text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Mobile Drawer Menu (Solid High Contrast Dark Background) -->
    <div x-show="mobileMenuOpen" 
         @click.away="mobileMenuOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="lg:hidden bg-[#1C2C23] border-b-2 border-[#E0A838]/40 px-6 py-6 space-y-3 text-center shadow-2xl rounded-b-3xl mt-2 mx-3">
        <a href="index.php" class="block py-3 px-4 rounded-xl text-base uppercase tracking-wider transition <?php echo ($active_page === 'home') ? 'bg-[#E0A838]/15 text-[#E0A838] font-extrabold border border-[#E0A838]/30' : 'text-white font-semibold hover:text-[#E0A838] hover:bg-white/5'; ?>">Home</a>
        <a href="about.php" class="block py-3 px-4 rounded-xl text-base uppercase tracking-wider transition <?php echo ($active_page === 'about') ? 'bg-[#E0A838]/15 text-[#E0A838] font-extrabold border border-[#E0A838]/30' : 'text-white font-semibold hover:text-[#E0A838] hover:bg-white/5'; ?>">About Us</a>
        <a href="products.php" class="block py-3 px-4 rounded-xl text-base uppercase tracking-wider transition <?php echo ($active_page === 'products') ? 'bg-[#E0A838]/15 text-[#E0A838] font-extrabold border border-[#E0A838]/30' : 'text-white font-semibold hover:text-[#E0A838] hover:bg-white/5'; ?>">Products</a>
        <a href="ipm.php" class="block py-3 px-4 rounded-xl text-base uppercase tracking-wider transition <?php echo ($active_page === 'ipm') ? 'bg-[#E0A838]/15 text-[#E0A838] font-extrabold border border-[#E0A838]/30' : 'text-white font-semibold hover:text-[#E0A838] hover:bg-white/5'; ?>">IPM Sourcing</a>
        <a href="contact.php" @click="mobileMenuOpen = false" class="block py-3.5 rounded-xl font-bold uppercase tracking-widest text-xs shadow-lg hover:bg-white transition mt-4 <?php echo ($active_page === 'contact') ? 'bg-white text-[#1C2C23]' : 'bg-[#E0A838] text-[#1C2C23]'; ?>">Contact Us</a>
    </div>
</header>
