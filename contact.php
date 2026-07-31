<?php
require_once __DIR__ . '/db.php';
$active_page = 'contact';

// Handle form submission
$form_success = false;
$form_error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $company = trim($_POST['company'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $product = trim($_POST['product'] ?? '');

    if ($name && $email && $message) {
        $to      = 'kedarnathspices@gmail.com';
        $headers = "From: $email\r\nReply-To: $email\r\nContent-Type: text/html; charset=UTF-8";
        $body    = "<h2>New Contact Inquiry</h2>
                    <p><strong>Name:</strong> $name</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Phone:</strong> $phone</p>
                    <p><strong>Company:</strong> $company</p>
                    <p><strong>Subject:</strong> $subject</p>
                    <p><strong>Product Interest:</strong> $product</p>
                    <p><strong>Message:</strong><br>$message</p>";
        @mail($to, "New Inquiry from $name – Kedarnath Spices", $body, $headers);
        $form_success = true;
    } else {
        $form_error = 'Please fill in all required fields (Name, Email, Message).';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us – Kedarnath Spices & Herbs | Get in Touch</title>
    <meta name="description" content="Contact Kedarnath Spices & Herbs for export inquiries, product sourcing, and business partnerships. Located in Unjha, Gujarat – the Spice Capital of India.">
    <meta name="keywords" content="Kedarnath Spices Contact, Spice Export Inquiry, Unjha Gujarat, Spice Supplier India">
    <link rel="shortcut icon" type="image/x-icon" href="https://kedarnathspices.com/favicon.ico">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="css/tailwind.css">

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --terracotta: #D96E48;
            --mustard:    #E0A838;
            --forest:     #2E5E3E;
            --charcoal:   #1E2922;
            --cream:      #FAF3EC;
        }

        /* ------------------------------------------------------------- */
        /*  EXPLICIT SPACING & LAYOUT SYSTEM                             */
        /* ------------------------------------------------------------- */

        .contact-hero-padding {
            padding-top: 150px;
            padding-bottom: 110px;
        }
        @media (max-width: 768px) {
            .contact-hero-padding {
                padding-top: 110px;
                padding-bottom: 80px;
            }
        }

        /* Info Card Grid & Box */
        .info-card-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 64px;
        }
        @media (max-width: 1024px) {
            .info-card-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 48px;
            }
        }
        @media (max-width: 640px) {
            .info-card-grid {
                grid-template-columns: repeat(1, 1fr);
                gap: 18px;
                margin-bottom: 40px;
            }
        }

        .info-card-box {
            background: #ffffff;
            border-radius: 28px;
            padding: 36px 24px 32px 24px;
            border: 1px solid rgba(30, 41, 34, 0.08);
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }
        .info-card-box:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -10px rgba(217, 110, 72, 0.15);
        }

        /* Main Form & Sidebar Layout (Equal Height Stretching) */
        .main-content-grid {
            display: grid;
            grid-template-columns: 7fr 5fr;
            gap: 44px;
            align-items: stretch;
        }
        @media (max-width: 1024px) {
            .main-content-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        .form-card-box {
            background: #ffffff;
            border-radius: 32px;
            padding: 40px 36px;
            border: 1px solid rgba(30, 41, 34, 0.08);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        @media (max-width: 640px) {
            .form-card-box {
                padding: 28px 20px;
            }
        }

        /* Form Flex Stretch Logic */
        .form-card-box form {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .message-group-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-bottom: 24px;
        }

        .message-textarea {
            flex: 1;
            min-height: 150px;
            height: 100%;
            resize: none;
        }

        /* Form Row & Group Spacing */
        .form-grid-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        @media (max-width: 640px) {
            .form-grid-row {
                grid-template-columns: 1fr;
                gap: 16px;
                margin-bottom: 16px;
            }
        }

        .form-group-item {
            margin-bottom: 20px;
        }

        .form-group-item label,
        .message-group-item label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(30, 41, 34, 0.7);
            margin-bottom: 8px;
        }

        .form-input-field {
            width: 100%;
            background-color: rgba(250, 243, 236, 0.5);
            border: 1px solid rgba(30, 41, 34, 0.12);
            border-radius: 16px;
            padding: 13px 18px;
            font-size: 14px;
            color: #1E2922;
            transition: all 0.2s ease;
        }
        .form-input-field:focus {
            outline: none;
            border-color: var(--terracotta);
            background-color: #ffffff;
            box-shadow: 0 0 0 3.5px rgba(217, 110, 72, 0.15);
        }

        /* Sidebar Stack */
        .right-sidebar-stack {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .sidebar-card-box {
            background: #ffffff;
            border-radius: 28px;
            padding: 28px 28px;
            border: 1px solid rgba(30, 41, 34, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .cert-grid-2x2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        /* Hero & Section Backgrounds */
        .contact-hero-bg {
            background: linear-gradient(135deg, #1C2C23 0%, #2E5E3E 45%, #3C1E13 100%);
            position: relative;
            overflow: hidden;
        }
        .contact-hero-bg::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(ellipse at 30% 50%, rgba(224,168,56,0.14) 0%, transparent 55%),
                        radial-gradient(ellipse at 75% 30%, rgba(217,110,72,0.12) 0%, transparent 55%),
                        radial-gradient(ellipse at 50% 90%, rgba(46,94,62,0.18) 0%, transparent 55%);
            animation: bgPulse 10s ease-in-out infinite alternate;
        }
        @keyframes bgPulse {
            0%   { transform: translate(0,0) scale(1); }
            100% { transform: translate(2%,2%) scale(1.05); }
        }

        .spice-particle {
            position: absolute;
            border-radius: 50%;
            animation: floatUp 10s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes floatUp {
            0%,100% { transform: translateY(0) rotate(0deg); opacity: 0.35; }
            50%      { transform: translateY(-30px) rotate(180deg); opacity: 0.75; }
        }

        .btn-submit {
            background: linear-gradient(135deg, #D96E48, #C45A35);
            position: relative;
            overflow: hidden;
            transition: all 0.25s ease;
        }
        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
        }
        .btn-submit:hover::before { left: 100%; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(217,110,72,0.4); }

        .map-wrapper {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(30,41,34,0.1);
        }

        .badge-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(224,168,56,0.15);
            border: 1px solid rgba(224,168,56,0.35);
            color: #E0A838;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 999px;
        }

        /* WhatsApp Card Banner Container */
        .whatsapp-card-banner {
            background: linear-gradient(135deg, #1C2C23 0%, #2E5E3E 100%);
            border-radius: 32px;
            padding: 50px 32px;
            box-shadow: 0 16px 40px rgba(30, 41, 34, 0.12);
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-top: 60px;
        }
    </style>
</head>

<body class="bg-[#FAF3EC] font-sans antialiased text-[#1E2922] overflow-x-hidden">

    <!-- Navbar -->
    <?php require_once __DIR__ . '/navbar.php'; ?>

    <!-- ============================================================ -->
    <!--  1. HERO SECTION                                              -->
    <!-- ============================================================ -->
    <section class="contact-hero-bg contact-hero-padding relative">

        <!-- Floating particles -->
        <div class="spice-particle" style="width:10px;height:10px;background:rgba(224,168,56,0.5);top:22%;left:8%;animation-delay:0s;animation-duration:9s;"></div>
        <div class="spice-particle" style="width:6px;height:6px;background:rgba(217,110,72,0.5);top:62%;left:14%;animation-delay:2s;animation-duration:11s;"></div>
        <div class="spice-particle" style="width:8px;height:8px;background:rgba(224,168,56,0.4);top:28%;right:10%;animation-delay:1s;animation-duration:8s;"></div>
        <div class="spice-particle" style="width:5px;height:5px;background:rgba(217,110,72,0.6);top:72%;right:16%;animation-delay:3s;animation-duration:12s;"></div>
        <div class="spice-particle" style="width:12px;height:12px;background:rgba(46,94,62,0.5);top:48%;left:45%;animation-delay:4s;animation-duration:10s;"></div>

        <!-- Abstract ring decorations -->
        <div style="position:absolute;top:-60px;right:-60px;width:340px;height:340px;border-radius:50%;border:1px solid rgba(224,168,56,0.12);pointer-events:none;"></div>
        <div style="position:absolute;top:-20px;right:-20px;width:220px;height:220px;border-radius:50%;border:1px solid rgba(217,110,72,0.10);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-80px;left:-40px;width:300px;height:300px;border-radius:50%;border:1px solid rgba(224,168,56,0.08);pointer-events:none;"></div>

        <div class="max-w-7xl mx-auto px-6 md:px-12 relative z-10 text-center">
            <div class="badge-label mb-6 mx-auto w-fit">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                Unjha, Gujarat – India's Spice Capital
            </div>

            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold font-serif text-white leading-tight mb-6">
                Let's Start a<br>
                <span style="background:linear-gradient(135deg,#E0A838,#D96E48);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Conversation</span>
            </h1>
            <p class="text-base md:text-xl text-white/75 font-light max-w-2xl mx-auto leading-relaxed" style="margin-top: 24px; margin-bottom: 42px;">
                Whether you're an importer, food brand, or distributor — we'd love to connect. 
                Reach out for pricing, samples, or bulk export partnerships.
            </p>

            <!-- Quick action pills -->
            <div class="flex flex-wrap justify-center" style="gap: 16px;">
                <a href="tel:+917600404015" class="flex items-center gap-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    +91 76004 04015
                </a>
                <a href="mailto:kedarnathspices@gmail.com" class="flex items-center gap-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Email Us
                </a>
                <a href="https://api.whatsapp.com/send?phone=+917600404015&text=Hi, I'm interested in your spices." target="_blank" class="flex items-center gap-2.5 bg-[#25D366]/85 hover:bg-[#25D366] border border-[#25D366]/30 text-white px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.035-4.43c1.654.982 3.511 1.501 5.39 1.502 5.578.002 10.12-4.537 10.123-10.123.002-2.705-1.05-5.249-2.962-7.163C16.73 1.87 14.183.818 11.48.818c-5.584 0-10.126 4.54-10.128 10.126-.001 1.895.493 3.748 1.433 5.385L1.756 22.24l6.336-1.67zm12.355-6.61c-.33-.165-1.951-.963-2.251-1.072-.3-.11-.518-.165-.735.165-.218.33-.842 1.072-1.032 1.292-.19.22-.38.247-.71.082-.33-.165-1.393-.513-2.653-1.637-.98-.874-1.643-1.953-1.835-2.282-.19-.33-.02-.508.145-.671.148-.147.33-.385.495-.578.165-.192.22-.33.33-.55.11-.22.055-.412-.028-.577-.082-.165-.735-1.77-.993-2.42-.258-.627-.518-.543-.735-.543-.19 0-.41-.013-.627-.013-.218 0-.572.082-.871.412-.3.33-1.145 1.117-1.145 2.723 0 1.605 1.17 3.16 1.332 3.38 1.62 2.217 3.12 3.4 5.92 4.606 2.8.12 2.8.843 3.29.843.49 0 1.95-.797 2.225-1.567.275-.77.275-1.43.193-1.567-.083-.137-.303-.22-.633-.385z"/></svg>
                    WhatsApp
                </a>
            </div>
        </div>

        <!-- Seamless Wave Divider Inside Hero Bottom -->
        <div class="absolute bottom-0 left-0 right-0 w-full overflow-hidden leading-none z-10 pointer-events-none select-none">
            <svg viewBox="0 0 1440 48" preserveAspectRatio="none" class="w-full h-8 md:h-12 block">
                <path d="M0,48 C360,0 1080,48 1440,0 L1440,48 Z" fill="#FAF3EC"/>
            </svg>
        </div>
    </section>

    <!-- ============================================================ -->
    <!--  2. UNIFIED CONTENT SECTION (CARDS + FORM + MAP + CTA)       -->
    <!-- ============================================================ -->
    <section style="padding-top: 50px; padding-bottom: 70px; background: #FAF3EC;">
        <div class="max-w-7xl mx-auto px-6 md:px-12">

            <!-- A. Top 4 Contact Info Cards -->
            <div class="info-card-grid">

                <!-- Card: Phone -->
                <div class="info-card-box group">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto transition-all group-hover:scale-110" style="background:linear-gradient(135deg,rgba(217,110,72,0.14),rgba(217,110,72,0.05)); margin-bottom: 20px;">
                        <svg class="w-7 h-7 text-[#D96E48]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <h3 class="font-bold text-xs uppercase tracking-wider text-[#1E2922]/70" style="margin-bottom: 12px;">Call Us</h3>
                    <a href="tel:+917600404015" class="block text-[#D96E48] font-bold text-sm md:text-base hover:underline" style="margin-bottom: 4px;">+91 76004 04015</a>
                    <a href="tel:+919104082064" class="block text-[#D96E48] font-bold text-sm md:text-base hover:underline">+91 91040 82064</a>
                    <p class="text-xs text-[#1E2922]/50 font-light" style="margin-top: 16px;">Mon–Sat, 9 AM – 6 PM IST</p>
                </div>

                <!-- Card: Email -->
                <div class="info-card-box group">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto transition-all group-hover:scale-110" style="background:linear-gradient(135deg,rgba(224,168,56,0.14),rgba(224,168,56,0.05)); margin-bottom: 20px;">
                        <svg class="w-7 h-7 text-[#E0A838]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-bold text-xs uppercase tracking-wider text-[#1E2922]/70" style="margin-bottom: 12px;">Email Us</h3>
                    <a href="mailto:kedarnathspices@gmail.com" class="block text-[#D96E48] font-bold text-xs md:text-sm hover:underline break-all" style="margin-bottom: 4px;">kedarnathspices@gmail.com</a>
                    <p class="text-xs text-[#1E2922]/50 font-light" style="margin-top: 20px;">Reply within 24 hours</p>
                </div>

                <!-- Card: Address -->
                <div class="info-card-box group">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto transition-all group-hover:scale-110" style="background:linear-gradient(135deg,rgba(46,94,62,0.14),rgba(46,94,62,0.05)); margin-bottom: 20px;">
                        <svg class="w-7 h-7 text-[#2E5E3E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-xs uppercase tracking-wider text-[#1E2922]/70" style="margin-bottom: 12px;">Visit Us</h3>
                    <p class="text-xs text-[#1E2922]/80 font-light leading-relaxed">H 401, Cattle Shade, Gunj Bajar<br>Unjha – 384170, Gujarat, India</p>
                </div>

                <!-- Card: WhatsApp -->
                <div class="info-card-box group cursor-pointer" onclick="window.open('https://api.whatsapp.com/send?phone=+917600404015&text=Hi, I want to inquire about your spices.','_blank')">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto transition-all group-hover:scale-110" style="background:linear-gradient(135deg,rgba(37,211,102,0.14),rgba(37,211,102,0.05)); margin-bottom: 20px;">
                        <svg class="w-7 h-7" style="fill:#25D366;" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.035-4.43c1.654.982 3.511 1.501 5.39 1.502 5.578.002 10.12-4.537 10.123-10.123.002-2.705-1.05-5.249-2.962-7.163C16.73 1.87 14.183.818 11.48.818c-5.584 0-10.126 4.54-10.128 10.126-.001 1.895.493 3.748 1.433 5.385L1.756 22.24l6.336-1.67zm12.355-6.61c-.33-.165-1.951-.963-2.251-1.072-.3-.11-.518-.165-.735.165-.218.33-.842 1.072-1.032 1.292-.19.22-.38.247-.71.082-.33-.165-1.393-.513-2.653-1.637-.98-.874-1.643-1.953-1.835-2.282-.19-.33-.02-.508.145-.671.148-.147.33-.385.495-.578.165-.192.22-.33.33-.55.11-.22.055-.412-.028-.577-.082-.165-.735-1.77-.993-2.42-.258-.627-.518-.543-.735-.543-.19 0-.41-.013-.627-.013-.218 0-.572.082-.871.412-.3.33-1.145 1.117-1.145 2.723 0 1.605 1.17 3.16 1.332 3.38 1.62 2.217 3.12 3.4 5.92 4.606 2.8.12 2.8.843 3.29.843.49 0 1.95-.797 2.225-1.567.275-.77.275-1.43.193-1.567-.083-.137-.303-.22-.633-.385z"/></svg>
                    </div>
                    <h3 class="font-bold text-xs uppercase tracking-wider text-[#1E2922]/70" style="margin-bottom: 12px;">WhatsApp</h3>
                    <p class="text-[#25D366] font-bold text-sm md:text-base" style="margin-bottom: 4px;">Chat Instantly</p>
                    <p class="text-xs text-[#1E2922]/50 font-light" style="margin-top: 16px;">Quick response guaranteed</p>
                </div>

            </div>

            <!-- B. Main Form + Map Grid (Left & Right Equal Height Stretch) -->
            <div class="main-content-grid">

                <!-- LEFT: Contact Form -->
                <div class="form-card-box">
                    
                    <div style="margin-bottom: 28px;">
                        <span class="badge-label inline-flex" style="margin-bottom: 14px;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Send an Inquiry
                        </span>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold font-serif text-[#1E2922]" style="margin-top: 8px; margin-bottom: 12px;">Tell Us What You Need</h2>
                        <p class="text-sm text-[#1E2922]/65 font-light leading-relaxed">Fill in the form and our export team will get back to you within one business day with pricing, samples, and shipping options.</p>
                    </div>

                    <?php if ($form_success): ?>
                    <div class="success-banner" style="margin-bottom: 24px;">
                        <div class="w-10 h-10 rounded-xl bg-[#E0A838]/20 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-[#E0A838]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-white text-sm">Message Sent Successfully!</p>
                            <p class="text-white/70 text-sm font-light mt-0.5">Thank you for reaching out. Our team will contact you within 24 hours.</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($form_error): ?>
                    <div class="bg-red-50 border-l-4 border-red-400 rounded-2xl p-4 flex gap-3 items-start" style="margin-bottom: 24px;">
                        <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-red-700 text-sm font-medium"><?php echo htmlspecialchars($form_error); ?></p>
                    </div>
                    <?php endif; ?>

                    <form method="POST" x-data="{ loading: false }" @submit="loading = true">
                        <input type="hidden" name="contact_submit" value="1">

                        <!-- Name + Company row -->
                        <div class="form-grid-row">
                            <div class="form-group-item" style="margin-bottom: 0;">
                                <label>Full Name <span class="text-[#D96E48]">*</span></label>
                                <input type="text" name="name" required
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                       placeholder="Arjun Patel"
                                       class="form-input-field">
                            </div>
                            <div class="form-group-item" style="margin-bottom: 0;">
                                <label>Company / Brand</label>
                                <input type="text" name="company"
                                       value="<?php echo htmlspecialchars($_POST['company'] ?? ''); ?>"
                                       placeholder="ABC Imports Ltd."
                                       class="form-input-field">
                            </div>
                        </div>

                        <!-- Email + Phone -->
                        <div class="form-grid-row">
                            <div class="form-group-item" style="margin-bottom: 0;">
                                <label>Email Address <span class="text-[#D96E48]">*</span></label>
                                <input type="email" name="email" required
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                       placeholder="you@company.com"
                                       class="form-input-field">
                            </div>
                            <div class="form-group-item" style="margin-bottom: 0;">
                                <label>Phone / WhatsApp</label>
                                <input type="tel" name="phone"
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                       placeholder="+91 98765 43210"
                                       class="form-input-field">
                            </div>
                        </div>

                        <!-- Product Interest -->
                        <div class="form-group-item">
                            <label>Product of Interest</label>
                            <select name="product" class="form-input-field cursor-pointer">
                                <option value="">— Select a Category —</option>
                                <option value="Cumin Seeds (Jeera)">Cumin Seeds (Jeera)</option>
                                <option value="Fennel Seeds (Saunf)">Fennel Seeds (Saunf)</option>
                                <option value="Coriander Seeds (Dhaniya)">Coriander Seeds (Dhaniya)</option>
                                <option value="Fenugreek (Methi)">Fenugreek (Methi)</option>
                                <option value="Psyllium (Isabgol)">Psyllium (Isabgol)</option>
                                <option value="Sesame Seeds (Til)">Sesame Seeds (Til)</option>
                                <option value="Mustard Seeds">Mustard Seeds</option>
                                <option value="Spice Powders">Spice Powders</option>
                                <option value="Herbs">Herbs</option>
                                <option value="Multiple / Custom Blend">Multiple / Custom Blend</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Subject -->
                        <div class="form-group-item">
                            <label>Subject</label>
                            <input type="text" name="subject"
                                   value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>"
                                   placeholder="Export Inquiry / Sample Request / Pricing"
                                   class="form-input-field">
                        </div>

                        <!-- Message (Dynamically stretched to match right side bottom!) -->
                        <div class="message-group-item">
                            <label>Your Message <span class="text-[#D96E48]">*</span></label>
                            <textarea name="message" required
                                      placeholder="Tell us your requirements — quantity, destination country, delivery timeline, certifications needed, etc."
                                      class="form-input-field message-textarea"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn-submit w-full text-white font-bold py-4 rounded-2xl text-sm tracking-wide flex items-center justify-center gap-2.5 shadow-lg"
                                :disabled="loading">
                            <template x-if="!loading">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    Send Inquiry
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    Sending...
                                </span>
                            </template>
                        </button>

                        <p class="text-center text-[11px] text-[#1E2922]/45 font-light" style="margin-top: 14px;">
                            🔒 Your information is confidential and strictly used for responding to your inquiry.
                        </p>
                    </form>
                </div>

                <!-- RIGHT: Map + Business Hours + Certifications -->
                <div class="right-sidebar-stack">

                    <!-- Google Map Embed -->
                    <div class="sidebar-card-box">
                        <h3 class="text-base font-bold font-serif text-[#1E2922]" style="margin-bottom: 14px; display:flex; align-items:center; gap:8px;">
                            <svg class="w-5 h-5 text-[#D96E48]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            Find Us on Map
                        </h3>
                        <div class="map-wrapper">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3668.291!2d72.3999!3d23.7753!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395c4a4ed5f1a3b7%3A0x1234567890abcdef!2sUnjha%2C%20Gujarat%20384170!5e0!3m2!1sen!2sin!4v1690000000000"
                                width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade" title="Kedarnath Spices Location - Unjha Gujarat">
                            </iframe>
                        </div>
                        <a href="https://maps.google.com/?q=Unjha+Gujarat+384170" target="_blank"
                           class="flex items-center gap-2 text-xs font-bold text-[#D96E48] hover:underline" style="margin-top: 12px;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Open in Google Maps
                        </a>
                    </div>

                    <!-- Business Hours -->
                    <div class="sidebar-card-box">
                        <h3 class="text-base font-bold font-serif text-[#1E2922]" style="margin-bottom: 16px; display:flex; align-items:center; gap:8px;">
                            <svg class="w-5 h-5 text-[#E0A838]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Business Hours
                        </h3>
                        <div>
                            <?php
                            $hours = [
                                ['day' => 'Monday – Friday', 'time' => '9:00 AM – 6:30 PM', 'open' => true],
                                ['day' => 'Saturday',        'time' => '9:00 AM – 4:00 PM', 'open' => true],
                                ['day' => 'Sunday',          'time' => 'Closed',             'open' => false],
                            ];
                            foreach ($hours as $h): ?>
                            <div class="flex justify-between items-center py-2.5 border-b border-[#1E2922]/6 last:border-0">
                                <span class="text-xs md:text-sm font-medium text-[#1E2922]/80"><?php echo $h['day']; ?></span>
                                <span class="text-xs md:text-sm font-bold <?php echo $h['open'] ? 'text-[#2E5E3E]' : 'text-red-400'; ?>">
                                    <?php echo $h['time']; ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="rounded-2xl flex items-center gap-2.5" style="background:rgba(46,94,62,0.08); padding: 12px 14px; margin-top: 16px;">
                            <div class="w-2 h-2 rounded-full bg-[#2E5E3E] animate-pulse shrink-0"></div>
                            <p class="text-xs text-[#2E5E3E] font-semibold">Export Inquiries processed within 24 business hours</p>
                        </div>
                    </div>

                    <!-- Export Certifications -->
                    <div class="bg-[#1C2C23] rounded-3xl p-6 md:p-7 text-white shadow-md">
                        <h3 class="text-base font-bold font-serif text-white" style="margin-bottom: 16px; display:flex; align-items:center; gap:8px;">
                            <svg class="w-5 h-5 text-[#E0A838]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            Export Quality Assured
                        </h3>
                        <div class="cert-grid-2x2">
                            <?php
                            $certs = [
                                ['icon' => '🌿', 'name' => 'APEDA Certified', 'desc' => 'Agri Export License'],
                                ['icon' => '✅', 'name' => 'FSSAI Approved',  'desc' => 'Food Safety Compliant'],
                                ['icon' => '🌾', 'name' => 'Organic Graded',  'desc' => 'Premium Quality'],
                                ['icon' => '🚢', 'name' => 'Export Ready',    'desc' => 'Global Shipping'],
                            ];
                            foreach ($certs as $cert): ?>
                            <div class="bg-white/8 rounded-2xl border border-white/10 hover:bg-white/12 transition" style="padding: 16px 14px;">
                                <span class="text-xl"><?php echo $cert['icon']; ?></span>
                                <p class="font-bold text-xs text-white" style="margin-top: 6px; margin-bottom: 2px;"><?php echo $cert['name']; ?></p>
                                <p class="text-[10px] text-white/55 font-light"><?php echo $cert['desc']; ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>

            <!-- C. WhatsApp Card Banner (Seamlessly inside #FAF3EC section right above footer) -->
            <div class="whatsapp-card-banner">
                <!-- Inner ambient glow -->
                <div style="position:absolute;top:-50px;right:-50px;width:250px;height:250px;border-radius:50%;background:rgba(37,211,102,0.1);filter:blur(50px);pointer-events:none;"></div>
                
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto" style="background:rgba(37,211,102,0.15);border:1px solid rgba(37,211,102,0.3); margin-bottom: 20px;">
                    <svg class="w-8 h-8" style="fill:#25D366;" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.035-4.43c1.654.982 3.511 1.501 5.39 1.502 5.578.002 10.12-4.537 10.123-10.123.002-2.705-1.05-5.249-2.962-7.163C16.73 1.87 14.183.818 11.48.818c-5.584 0-10.126 4.54-10.128 10.126-.001 1.895.493 3.748 1.433 5.385L1.756 22.24l6.336-1.67zm12.355-6.61c-.33-.165-1.951-.963-2.251-1.072-.3-.11-.518-.165-.735.165-.218.33-.842 1.072-1.032 1.292-.19.22-.38.247-.71.082-.33-.165-1.393-.513-2.653-1.637-.98-.874-1.643-1.953-1.835-2.282-.19-.33-.02-.508.145-.671.148-.147.33-.385.495-.578.165-.192.22-.33.33-.55.11-.22.055-.412-.028-.577-.082-.165-.735-1.77-.993-2.42-.258-.627-.518-.543-.735-.543-.19 0-.41-.013-.627-.013-.218 0-.572.082-.871.412-.3.33-1.145 1.117-1.145 2.723 0 1.605 1.17 3.16 1.332 3.38 1.62 2.217 3.12 3.4 5.92 4.606 2.8.12 2.8.843 3.29.843.49 0 1.95-.797 2.225-1.567.275-.77.275-1.43.193-1.567-.083-.137-.303-.22-.633-.385z"/></svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold font-serif text-white" style="margin-bottom: 12px;">Need a Quick Answer?</h2>
                <p class="text-white/70 font-light text-base md:text-lg max-w-xl mx-auto leading-relaxed" style="margin-bottom: 32px;">Chat directly on WhatsApp for instant pricing, sample requests, or export documentation queries.</p>
                <a href="https://api.whatsapp.com/send?phone=+917600404015&text=Hi, I want to inquire about Kedarnath Spices products." target="_blank"
                   class="inline-flex items-center gap-3 bg-[#25D366] hover:bg-[#20BA56] text-white font-bold px-8 py-4 rounded-2xl text-sm md:text-base shadow-2xl transition-all hover:-translate-y-1 hover:shadow-green-500/30">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.035-4.43c1.654.982 3.511 1.501 5.39 1.502 5.578.002 10.12-4.537 10.123-10.123.002-2.705-1.05-5.249-2.962-7.163C16.73 1.87 14.183.818 11.48.818c-5.584 0-10.126 4.54-10.128 10.126-.001 1.895.493 3.748 1.433 5.385L1.756 22.24l6.336-1.67z"/></svg>
                    Chat on WhatsApp Now
                </a>
            </div>

        </div>
    </section>

    <!-- Footer Component -->
    <?php require_once __DIR__ . '/footer.php'; ?>

</body>
</html>
