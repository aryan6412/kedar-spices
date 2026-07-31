<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
require_once __DIR__ . '/db.php';

// Handle login submission
$login_error = '';
$login_success_msg = '';

if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    try {
        $stmt = $pdo->prepare("SELECT * FROM `admin_users` WHERE `username` = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            header("Location: admin.php");
            exit;
        } else {
            $login_error = 'Invalid username or password.';
        }
    } catch (\PDOException $e) {
        $login_error = 'Database authentication failed: ' . $e->getMessage();
    }
}

// Handle sending password reset OTP
if (isset($_POST['action']) && $_POST['action'] === 'request_reset_otp') {
    $email = trim($_POST['email'] ?? '');
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM `admin_users` WHERE `email` = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate a 6-digit OTP
            $otp = sprintf("%06d", random_int(0, 999999));
            
            // Delete old OTPs/Tokens for this email
            $delete_stmt = $pdo->prepare("DELETE FROM `password_resets` WHERE `email` = ?");
            $delete_stmt->execute([$email]);
            
            // Insert new OTP expiring in 15 minutes
            $insert_stmt = $pdo->prepare("INSERT INTO `password_resets` (`email`, `token`, `expires_at`) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))");
            $insert_stmt->execute([$email, $otp]);
            
            // Log local OTP for developers
            if (!is_dir(__DIR__ . '/uploads')) {
                mkdir(__DIR__ . '/uploads', 0777, true);
            }
            file_put_contents(__DIR__ . '/uploads/reset_email_log.txt', "To: $email\nSubject: Reset Password OTP\nYour OTP: $otp\nDate: " . date('Y-m-d H:i:s') . "\n\n");
            
            // Send standard email
            $to = $email;
            $subject = "Your Password Reset OTP";
            $message = "Your password reset OTP is: " . $otp . "\n\nThis OTP is valid for 15 minutes.";
            $headers = "From: no-reply@kedarnathspices.com\r\nReply-To: no-reply@kedarnathspices.com\r\n";
            @mail($to, $subject, $message, $headers);
            
            // Redirect to OTP verification screen
            header("Location: admin.php?action=verify_otp&email=" . urlencode($email) . "&msg=" . urlencode("OTP has been sent to your email! (Local Developer: OTP logged to uploads/reset_email_log.txt)"));
            exit;
        } else {
            $login_error = "Email address not found.";
        }
    } catch (\PDOException $e) {
        $login_error = "Failed to request OTP: " . $e->getMessage();
    }
}

// Handle password reset via OTP verification
if (isset($_POST['action']) && $_POST['action'] === 'verify_otp_submit') {
    $email = trim($_POST['email'] ?? '');
    $otp = trim($_POST['otp'] ?? '');
    $new_pass = trim($_POST['new_password'] ?? '');
    $confirm_pass = trim($_POST['confirm_password'] ?? '');
    
    if ($new_pass !== $confirm_pass) {
        $login_error = 'Passwords do not match.';
        $_GET['action'] = 'verify_otp';
        $_GET['email'] = $email;
    } elseif (empty($new_pass)) {
        $login_error = 'Password cannot be empty.';
        $_GET['action'] = 'verify_otp';
        $_GET['email'] = $email;
    } else {
        try {
            // Verify if OTP matches and is not expired
            $stmt = $pdo->prepare("SELECT * FROM `password_resets` WHERE `email` = ? AND `token` = ? AND `expires_at` > NOW()");
            $stmt->execute([$email, $otp]);
            $reset_request = $stmt->fetch();
            
            if ($reset_request) {
                $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                
                // Update user password
                $update_stmt = $pdo->prepare("UPDATE `admin_users` SET `password` = ? WHERE `email` = ?");
                $update_stmt->execute([$new_hash, $email]);
                
                // Clean up OTP token
                $delete_stmt = $pdo->prepare("DELETE FROM `password_resets` WHERE `email` = ?");
                $delete_stmt->execute([$email]);
                
                $login_success_msg = 'Password reset successfully! You can now log in.';
            } else {
                $login_error = 'Invalid or expired OTP code.';
                $_GET['action'] = 'verify_otp';
                $_GET['email'] = $email;
            }
        } catch (\PDOException $e) {
            $login_error = 'Failed to reset password: ' . $e->getMessage();
            $_GET['action'] = 'verify_otp';
            $_GET['email'] = $email;
        }
    }
}

// Capture redirect success messages
if (isset($_GET['msg']) && !empty($_GET['msg'])) {
    $login_success_msg = $_GET['msg'];
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Analytics JSON API endpoint for live 1-minute polling
if (isset($_GET['action']) && $_GET['action'] === 'api_analytics') {
    header('Content-Type: application/json');
    echo json_encode([
        'total_visits' => get_total_visits(),
        'today_unique' => get_today_unique_visitors(),
        'page_stats' => get_page_visitor_stats()
    ]);
    exit;
}

// Authentication gate
$is_authenticated = $_SESSION['admin_logged_in'] ?? false;

// Determine selected page to edit
$pages_allowed = ['home', 'products', 'about', 'gallery', 'contact', 'ipm'];
$selected_page = trim($_GET['page'] ?? 'home');
if (!in_array($selected_page, $pages_allowed)) {
    $selected_page = 'home';
}

// Handle Product Actions (Add, Edit, Delete)
if ($is_authenticated && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $act = $_POST['action'];
    
    if ($act === 'add_product') {
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? 'spices');
        $desc = trim($_POST['desc'] ?? '');
        $img1 = trim($_POST['img1_url'] ?? '');
        $img2 = trim($_POST['img2_url'] ?? '');

        if (isset($_FILES['img1_file']) && $_FILES['img1_file']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['img1_file']['tmp_name'];
            $clean = time() . '_p1_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['img1_file']['name']);
            if (move_uploaded_file($tmp, __DIR__ . '/uploads/' . $clean)) {
                $img1 = 'uploads/' . $clean;
            }
        }

        if (isset($_FILES['img2_file']) && $_FILES['img2_file']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['img2_file']['tmp_name'];
            $clean = time() . '_p2_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['img2_file']['name']);
            if (move_uploaded_file($tmp, __DIR__ . '/uploads/' . $clean)) {
                $img2 = 'uploads/' . $clean;
            }
        }

        if (add_product($category, $name, $img1, $img2, $desc)) {
            $success_message = "Product '$name' added successfully!";
        } else {
            $error_message = "Failed to add product '$name'.";
        }
    } elseif ($act === 'edit_product') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? 'spices');
        $desc = trim($_POST['desc'] ?? '');
        $img1 = trim($_POST['img1_url'] ?? '');
        $img2 = trim($_POST['img2_url'] ?? '');

        if (isset($_FILES['img1_file']) && $_FILES['img1_file']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['img1_file']['tmp_name'];
            $clean = time() . '_p1_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['img1_file']['name']);
            if (move_uploaded_file($tmp, __DIR__ . '/uploads/' . $clean)) {
                $img1 = 'uploads/' . $clean;
            }
        }

        if (isset($_FILES['img2_file']) && $_FILES['img2_file']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['img2_file']['tmp_name'];
            $clean = time() . '_p2_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['img2_file']['name']);
            if (move_uploaded_file($tmp, __DIR__ . '/uploads/' . $clean)) {
                $img2 = 'uploads/' . $clean;
            }
        }

        if (update_product($id, $category, $name, $img1, $img2, $desc)) {
            $success_message = "Product '$name' updated successfully!";
        } else {
            $error_message = "Failed to update product.";
        }
    } elseif ($act === 'delete_product') {
        $id = intval($_POST['id'] ?? 0);
        if (delete_product($id)) {
            $success_message = "Product deleted successfully!";
        } else {
            $error_message = "Failed to delete product.";
        }
    } elseif ($act === 'add_category') {
        $slug = trim($_POST['slug'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        $desc = trim($_POST['desc'] ?? '');
        $bg = trim($_POST['bg'] ?? '#FFF5E6');

        if (add_category($slug, $name, $icon, $desc, $bg)) {
            $success_message = "Category '$name' added successfully!";
        } else {
            $error_message = "Failed to add category '$name'. Slug may already exist.";
        }
    } elseif ($act === 'edit_category') {
        $slug = trim($_POST['slug'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        $desc = trim($_POST['desc'] ?? '');
        $bg = trim($_POST['bg'] ?? '#FFF5E6');

        if (update_category($slug, $name, $icon, $desc, $bg)) {
            $success_message = "Category '$name' updated successfully!";
        } else {
            $error_message = "Failed to update category '$name'.";
        }
    } elseif ($act === 'delete_category') {
        $slug = trim($_POST['slug'] ?? '');
        if (delete_category($slug)) {
            $success_message = "Category deleted successfully!";
        } else {
            $error_message = "Failed to delete category.";
        }
    } elseif ($act === 'update_map_state') {
        $state_key = trim($_POST['state_key'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $region = trim($_POST['region'] ?? '');
        $badge = trim($_POST['badge'] ?? '');
        $bg = trim($_POST['bg'] ?? '#27AE60');
        $image = trim($_POST['image'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $purity = trim($_POST['purity'] ?? '99.0%+');
        $capacity = trim($_POST['capacity'] ?? '3,000 MT/Yr');
        $grading = trim($_POST['grading'] ?? 'Sortex Cleaned');

        $spices_raw = $_POST['primary_spices'] ?? '';
        if (is_array($spices_raw)) {
            $spices = $spices_raw;
        } else {
            $spices = array_filter(array_map('trim', explode(',', $spices_raw)));
        }

        $top = trim($_POST['top_percent'] ?? '50%');
        $left = trim($_POST['left_percent'] ?? '50%');

        if (update_map_state($state_key, $name, $region, $badge, $bg, $image, $spices, $description, $purity, $capacity, $grading, $top, $left)) {
            $success_message = "Sourcing State '$name' spices and details updated successfully!";
        } else {
            $error_message = "Failed to update state '$name'.";
        }
    } elseif ($act === 'add_map_state') {
        $key = trim($_POST['state_key'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $region = trim($_POST['region'] ?? '');
        $badge = trim($_POST['badge'] ?? '');
        $bg = trim($_POST['bg'] ?? '#27AE60');
        $image = trim($_POST['image'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $purity = trim($_POST['purity'] ?? '99.0%+');
        $capacity = trim($_POST['capacity'] ?? '3,000 MT/Yr');
        $grading = trim($_POST['grading'] ?? 'Sortex Cleaned');
        $top = trim($_POST['top_percent'] ?? '50%');
        $left = trim($_POST['left_percent'] ?? '50%');

        $spices_raw = $_POST['primary_spices'] ?? '';
        if (is_array($spices_raw)) {
            $spices = $spices_raw;
        } else {
            $spices = array_filter(array_map('trim', explode(',', $spices_raw)));
        }

        if (add_map_state($key, $name, $region, $badge, $bg, $image, $spices, $description, $purity, $capacity, $grading, $top, $left)) {
            $success_message = "New Sourcing State '$name' added to map successfully!";
        } else {
            $error_message = "Failed to add state '$name' (Key may already exist).";
        }
    } elseif ($act === 'add_testimonial') {
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $avatar = trim($_POST['avatar'] ?? '');
        $badge_type = trim($_POST['badge_type'] ?? 'Verified B2B');
        $rating = (int)($_POST['rating'] ?? 5);
        $comment = trim($_POST['comment'] ?? '');

        if (!empty($_FILES['avatar_file']['name'])) {
            $uploaded_url = handle_image_upload('avatar_file');
            if ($uploaded_url) {
                $avatar = $uploaded_url;
            }
        }

        if (add_testimonial($name, $role, $location, $avatar, $badge_type, $rating, $comment)) {
            $success_message = "Testimonial from '$name' added successfully!";
        } else {
            $error_message = "Failed to add testimonial.";
        }
    } elseif ($act === 'edit_testimonial') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $avatar = trim($_POST['avatar'] ?? '');
        $badge_type = trim($_POST['badge_type'] ?? 'Verified B2B');
        $rating = (int)($_POST['rating'] ?? 5);
        $comment = trim($_POST['comment'] ?? '');

        if (!empty($_FILES['avatar_file']['name'])) {
            $uploaded_url = handle_image_upload('avatar_file');
            if ($uploaded_url) {
                $avatar = $uploaded_url;
            }
        }

        if (update_testimonial($id, $name, $role, $location, $avatar, $badge_type, $rating, $comment)) {
            $success_message = "Testimonial from '$name' updated successfully!";
        } else {
            $error_message = "Failed to update testimonial.";
        }
    } elseif ($act === 'delete_testimonial') {
        $id = (int)($_POST['id'] ?? 0);
        if (delete_testimonial($id)) {
            $success_message = "Testimonial deleted successfully!";
        } else {
            $error_message = "Failed to delete testimonial.";
        }
    }
}

// Handle configuration updates (requires authentication)
if ($is_authenticated && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_settings') {
    $page_name = trim($_POST['page_name'] ?? 'home');
    if (!in_array($page_name, $pages_allowed)) {
        $page_name = 'home';
    }
    
    $hero_mode = trim($_POST['hero_mode'] ?? 'slider');
    $hero_video_url = trim($_POST['hero_video_url'] ?? '');
    $slider_images_raw = $_POST['slider_images'] ?? [];
    $hero_tag = trim($_POST['hero_tag'] ?? '');
    $hero_title = trim($_POST['hero_title'] ?? '');
    $hero_desc = trim($_POST['hero_desc'] ?? '');

    // Handle Local File Upload for Background Video
    if (isset($_FILES['video_upload']) && $_FILES['video_upload']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['video_upload']['tmp_name'];
        $clean_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['video_upload']['name']);
        $target = __DIR__ . '/uploads/' . $clean_name;
        
        if (move_uploaded_file($file_tmp, $target)) {
            $hero_video_url = 'uploads/' . $clean_name;
        }
    }

    // Handle Local Multiple File Uploads for Slider Images
    if (isset($_FILES['slider_uploads']) && is_array($_FILES['slider_uploads']['name'])) {
        foreach ($_FILES['slider_uploads']['name'] as $key => $name) {
            if ($_FILES['slider_uploads']['error'][$key] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['slider_uploads']['tmp_name'][$key];
                $clean_name = time() . '_' . $key . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $name);
                $target = __DIR__ . '/uploads/' . $clean_name;
                
                if (move_uploaded_file($file_tmp, $target)) {
                    $slider_images_raw[] = 'uploads/' . $clean_name;
                }
            }
        }
    }

    // Clean and validate slider images
    $slider_images = filter_cleaned_array_for_page($slider_images_raw);

    // Fallback default
    if (empty($slider_images)) {
        $slider_images = [
            'https://kedarnathspices.com/storage/img/slider/Slider1_1707475446.jpg'
        ];
    }

    // Save to MySQL DB
    if (update_hero_config_for_page($page_name, $hero_mode, $hero_video_url, $slider_images, $hero_tag, $hero_title, $hero_desc)) {
        $success_message = "Hero settings for page '" . ucfirst($page_name) . "' updated successfully!";
        $selected_page = $page_name;
    } else {
        $error_message = 'Failed to write configurations to MySQL database.';
    }
}

// Load configurations for the selected page
$config = get_hero_config($selected_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kedarnath Spices & Herbs</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="https://kedarnathspices.com/favicon.ico">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="css/tailwind.css">
    
    <!-- AlpineJS for UI switching and dynamic form inputs -->
    <style>
        [x-cloak] { display: none !important; }
        input[type="file"]::file-selector-button {
            background-color: #8E44AD !important;
            color: #FFFFFF !important;
            font-weight: 800 !important;
            border: none !important;
            border-radius: 0.75rem !important;
            padding: 0.5rem 1rem !important;
            margin-right: 1rem !important;
            font-size: 0.75rem !important;
            cursor: pointer !important;
            box-shadow: 0 2px 4px rgba(142,68,173,0.25) !important;
            transition: all 0.2s ease !important;
        }
        input[type="file"]::file-selector-button:hover {
            background-color: #732D91 !important;
        }
        input[type="file"] {
            color: #8E44AD !important;
            font-weight: 700 !important;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
    function dashboardManager() {
        return {
            totalVisits: <?php echo get_total_visits(); ?>,
            todayUnique: <?php echo get_today_unique_visitors(); ?>,
            pageStats: <?php echo json_encode(get_page_visitor_stats()); ?>,
            lastUpdated: 'Just now',
            isRefreshing: false,
            standardPages: {
                'home': 'Home Page',
                'products': 'Products Page',
                'about': 'About Us',
                'ipm': 'IPM Sourcing'
            },
            getMaxVal() {
                const vals = Object.values(this.pageStats);
                return Math.max(...vals, 1);
            },
            getTotalSum() {
                const vals = Object.values(this.pageStats);
                return vals.reduce(function(a, b) { return a + b; }, 0) || 1;
            },
            getBarHeight(count) {
                const max = this.getMaxVal();
                const pct = Math.round((count / max) * 100);
                return Math.max(pct, 8);
            },
            getSharePct(count) {
                const total = this.getTotalSum();
                return Math.round((count / total) * 100);
            },
            fetchAnalytics() {
                var self = this;
                self.isRefreshing = true;
                fetch('admin.php?action=api_analytics')
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data && data.page_stats) {
                            self.pageStats = data.page_stats;
                            self.totalVisits = data.total_visits;
                            self.todayUnique = data.today_unique;
                            var d = new Date();
                            self.lastUpdated = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                        }
                    })
                    .catch(function(err) { console.error('Analytics poll failed:', err); })
                    .finally(function() {
                        setTimeout(function() { self.isRefreshing = false; }, 600);
                    });
            },
            init() {
                var self = this;
                setInterval(function() {
                    self.fetchAnalytics();
                }, 60000);
            }
        };
    }

    function productPanelManager() {
        return {
            filterCat: 'all', 
            showAddModal: false, 
            showEditModal: false, 
            showCatModal: false,
            showAddCatModal: false,
            showEditCatModal: false,
            editCat: { slug: '', name: '', icon: '', desc: '', bg: '#FFF5E6' },
            categories: <?php echo json_encode(get_all_categories()); ?>,
            editProduct: { id: 0, name: '', category: 'spices', desc: '', img1_url: '', img2_url: '' } 
        };
    }

    function mapStateManager() {
        return {
            states: <?php echo json_encode(get_all_map_states()); ?>,
            selectedKey: 'gujarat',
            newSpice: '',
            newTop: '32%',
            newLeft: '44%',
            showAddStateModal: false,
            get stateList() {
                var self = this;
                return Object.keys(self.states).map(function(k) { return Object.assign({ key: k }, self.states[k]); });
            },
            get currentState() {
                return this.states[this.selectedKey] || {};
            },
            addSpice() {
                if (this.newSpice.trim()) {
                    if (!this.currentState.primarySpices) {
                        this.currentState.primarySpices = [];
                    }
                    this.currentState.primarySpices.push(this.newSpice.trim());
                    this.newSpice = '';
                }
            },
            removeSpice(idx) {
                if (this.currentState.primarySpices) {
                    this.currentState.primarySpices.splice(idx, 1);
                }
            }
        };
    }

    function testimonialsManager() {
        return {
            showAddModal: false,
            showEditModal: false,
            editTesti: { id: 0, name: '', role: '', location: '', badge_type: 'Verified B2B', rating: 5, avatar: '', comment: '' },
            testiList: <?php echo json_encode(get_all_testimonials()); ?>,
            editById(id) {
                var found = this.testiList.find(function(t) { return t.id == id; });
                if (found) {
                    this.editTesti = Object.assign({}, found);
                    this.showEditModal = true;
                }
            }
        };
    }
    </script>
</head>
<body class="bg-[#FAF3EC] font-sans antialiased text-[#1E2922] min-h-screen">

    <?php if (!$is_authenticated): ?>
        <!-- Simple Login Container Panel -->
        <div class="min-h-screen flex items-center justify-center py-12 px-4 md:px-8" 
             x-data="{ mode: '<?php echo htmlspecialchars($_GET['action'] ?? 'login'); ?>', email: '<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>' }">
            <div class="w-full max-w-md bg-white rounded-3xl p-8 border border-[#1E2922]/10 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#D96E48] to-[#E0A838]"></div>
                
                <div class="text-center space-y-2 mb-8 mt-2">
                    <img src="https://kedarnathspices.com/storage/img/logo.png" alt="Kedarnath Spices" class="h-16 mx-auto mb-4 animate-spin-3d">
                    <h2 class="text-3xl font-bold font-serif" x-text="mode === 'login' ? 'Login Page' : (mode === 'forgot' ? 'Reset Password' : 'Verify OTP')">Login Page</h2>
                </div>

                <?php if (!empty($login_error)): ?>
                    <div class="bg-red-50 text-red-600 border border-red-200 text-sm px-4 py-3 rounded-xl mb-6">
                        <?php echo htmlspecialchars($login_error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($login_success_msg)): ?>
                    <div class="bg-green-50 text-green-700 border border-green-200 text-sm px-4 py-3 rounded-xl mb-6">
                        <?php echo htmlspecialchars($login_success_msg); ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <div x-show="mode === 'login'" x-transition>
                    <form method="POST" action="admin.php" class="space-y-5">
                        <input type="hidden" name="action" value="login">
                        
                        <div class="space-y-1.5">
                            <label for="username" class="text-xs font-bold uppercase tracking-wider text-charcoal/70">Username</label>
                            <input type="text" id="username" name="username" required placeholder="admin"
                                   class="w-full px-4 py-3 rounded-xl border border-charcoal/20 focus:outline-none focus:border-[#D96E48] transition bg-[#FAF3EC]/30">
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <label for="password" class="text-xs font-bold uppercase tracking-wider text-charcoal/70">Password</label>
                                <button type="button" @click="mode = 'forgot'" class="text-xs text-[#D96E48] hover:underline font-semibold focus:outline-none">Forgot Password?</button>
                            </div>
                            <input type="password" id="password" name="password" required placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl border border-charcoal/20 focus:outline-none focus:border-[#D96E48] transition bg-[#FAF3EC]/30">
                        </div>

                        <button type="submit" 
                                class="w-full py-3 bg-[#D96E48] text-white font-bold rounded-xl shadow-md hover:bg-opacity-95 transform hover:-translate-y-0.5 duration-200 transition">
                            Sign In
                        </button>
                    </form>
                </div>

                <!-- Forgot Password / Request OTP Form -->
                <div x-show="mode === 'forgot'" x-transition style="display: none;">
                    <form method="POST" action="admin.php" class="space-y-5">
                        <input type="hidden" name="action" value="request_reset_otp">
                        
                        <div class="space-y-1.5">
                            <label for="reset_email" class="text-xs font-bold uppercase tracking-wider text-charcoal/70">Account Email Address</label>
                            <input type="email" id="reset_email" name="email" required placeholder="ap8307655@gmail.com" value="ap8307655@gmail.com"
                                   class="w-full px-4 py-3 rounded-xl border border-charcoal/20 focus:outline-none focus:border-[#D96E48] transition bg-[#FAF3EC]/30">
                            <p class="text-[10px] text-charcoal/40">Enter the email registered to your administrator account.</p>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <button type="button" @click="mode = 'login'" class="text-xs text-charcoal/50 hover:underline font-semibold focus:outline-none">Back to Login</button>
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-[#D96E48] text-white font-bold rounded-xl shadow-md hover:bg-opacity-95 transform hover:-translate-y-0.5 duration-200 transition">
                                Send OTP
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Verify OTP & Reset Password Form -->
                <div x-show="mode === 'verify_otp'" x-transition style="display: none;">
                    <form method="POST" action="admin.php" class="space-y-5">
                        <input type="hidden" name="action" value="verify_otp_submit">
                        <input type="hidden" name="email" :value="email">
                        
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-charcoal/70 block">Resetting password for:</label>
                            <p class="text-sm font-semibold text-charcoal/80" x-text="email"></p>
                        </div>

                        <div class="space-y-1.5">
                            <label for="otp" class="text-xs font-bold uppercase tracking-wider text-charcoal/70 block">OTP Code (6 Digits)</label>
                            <input type="text" id="otp" name="otp" required placeholder="123456" pattern="[0-9]{6}" maxlength="6"
                                   class="w-full px-4 py-3 rounded-xl border border-charcoal/20 focus:outline-none focus:border-[#D96E48] transition bg-[#FAF3EC]/30 font-mono tracking-widest text-center text-lg">
                        </div>

                        <div class="space-y-1.5">
                            <label for="new_password" class="text-xs font-bold uppercase tracking-wider text-charcoal/70 block">New Password</label>
                            <input type="password" id="new_password" name="new_password" required placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl border border-charcoal/20 focus:outline-none focus:border-[#D96E48] transition bg-[#FAF3EC]/30">
                        </div>

                        <div class="space-y-1.5">
                            <label for="confirm_password" class="text-xs font-bold uppercase tracking-wider text-charcoal/70 block">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl border border-charcoal/20 focus:outline-none focus:border-[#D96E48] transition bg-[#FAF3EC]/30">
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <button type="button" @click="mode = 'login'" class="text-xs text-charcoal/50 hover:underline font-semibold focus:outline-none">Back to Login</button>
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-[#D96E48] text-white font-bold rounded-xl shadow-md hover:bg-opacity-95 transform hover:-translate-y-0.5 duration-200 transition">
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    <?php else: ?>
        <?php $current_tab = $_GET['tab'] ?? 'dashboard'; ?>
        <!-- Super Admin Redesigned Dashboard Panel -->
        <div class="flex min-h-screen flex-col lg:flex-row" x-data="{ mobileMenuOpen: false }">
            
            <!-- Left Sidebar Navigation Panel (Sticky height on desktop with scrollable menu) -->
            <aside class="w-full lg:w-64 bg-[#1E2922] text-[#FAF3EC] flex flex-col justify-between shrink-0 z-40 border-r border-white/5 lg:sticky lg:top-0 lg:h-screen lg:max-h-screen">
                <div class="overflow-y-auto flex-grow flex flex-col justify-between">
                    <div>
                        <!-- Sidebar Header / Branding -->
                        <div class="p-6 flex items-center justify-between border-b border-white/10">
                            <a href="index.php" target="_blank" title="View Live Website" class="flex items-center group py-1">
                                <img src="https://kedarnathspices.com/storage/img/logo.png" alt="Kedarnath Spices" class="h-16 md:h-20 w-auto object-contain transition transform group-hover:scale-105" style="max-height: 72px;">
                            </a>
                            
                            <!-- Mobile menu trigger -->
                            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-white/70 hover:text-white focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>
                        </div>

                        <!-- Sidebar Menu Items -->
                        <div :class="mobileMenuOpen ? 'block' : 'hidden lg:block'" class="px-4 py-6 space-y-6">
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-white/30 tracking-widest uppercase px-3 mb-2">Main Menu</p>
                                
                                <!-- Tab: Dashboard Link -->
                                <a href="admin.php?tab=dashboard"
                                   class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition text-left focus:outline-none <?php echo $current_tab === 'dashboard' ? 'bg-white/10 text-[#E0A838]' : 'text-white/70 hover:bg-white/5 hover:text-white'; ?>">
                                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                                    <span>Dashboard</span>
                                </a>
                            </div>

                            <!-- Hero Customization submenu -->
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-white/30 tracking-widest uppercase px-3 mb-2">Customize Hero sections</p>
                                
                                <a href="admin.php?tab=hero&page=home"
                                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition text-left <?php echo ($current_tab === 'hero' && $selected_page === 'home') ? 'bg-white/10 text-[#E0A838]' : 'text-white/70 hover:bg-white/5 hover:text-white'; ?>">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    <span>Homepage Hero</span>
                                </a>
                                
                                <a href="admin.php?tab=hero&page=products"
                                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition text-left <?php echo ($current_tab === 'hero' && $selected_page === 'products') ? 'bg-white/10 text-[#E0A838]' : 'text-white/70 hover:bg-white/5 hover:text-white'; ?>">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    <span>Products Hero</span>
                                </a>

                                <a href="admin.php?tab=hero&page=about"
                                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition text-left <?php echo ($current_tab === 'hero' && $selected_page === 'about') ? 'bg-white/10 text-[#E0A838]' : 'text-white/70 hover:bg-white/5 hover:text-white'; ?>">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    <span>About Us Hero</span>
                                </a>


                                <a href="admin.php?tab=hero&page=contact"
                                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition text-left <?php echo ($current_tab === 'hero' && $selected_page === 'contact') ? 'bg-white/10 text-[#E0A838]' : 'text-white/70 hover:bg-white/5 hover:text-white'; ?>">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    <span>Contact Hero</span>
                                </a>

                                <a href="admin.php?tab=hero&page=ipm"
                                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition text-left <?php echo ($current_tab === 'hero' && $selected_page === 'ipm') ? 'bg-white/10 text-[#E0A838]' : 'text-white/70 hover:bg-white/5 hover:text-white'; ?>">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    <span>IPM Page Hero</span>
                                </a>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-white/30 tracking-widest uppercase px-3 mb-2">Showcase Data</p>
                                
                                <!-- Active Manage Products Tab Link -->
                                <a href="admin.php?tab=products"
                                   class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold transition text-left focus:outline-none <?php echo $current_tab === 'products' ? 'bg-white/10 text-[#E0A838]' : 'text-white/70 hover:bg-white/5 hover:text-white'; ?>">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <span>Manage Products</span>
                                    </div>
                                    <span class="text-[10px] bg-[#D96E48] px-2 py-0.5 rounded-full text-white font-bold"><?php echo count(get_all_products()); ?></span>
                                </a>

                                 <!-- Active Sourcing Map Spices Tab Link -->
                                <a href="admin.php?tab=map"
                                   class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold transition text-left focus:outline-none <?php echo $current_tab === 'map' ? 'bg-white/10 text-[#E0A838]' : 'text-white/70 hover:bg-white/5 hover:text-white'; ?>">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                        <span>Sourcing Map Spices</span>
                                    </div>
                                    <span class="text-[10px] bg-[#27AE60] px-2 py-0.5 rounded-full text-white font-bold"><?php echo count(get_all_map_states()); ?></span>
                                </a>

                                <!-- Active Client Testimonials Tab Link -->
                                <a href="admin.php?tab=testimonials"
                                   class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold transition text-left focus:outline-none <?php echo $current_tab === 'testimonials' ? 'bg-white/10 text-[#E0A838]' : 'text-white/70 hover:bg-white/5 hover:text-white'; ?>">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                        <span>Client Testimonials</span>
                                    </div>
                                    <span class="text-[10px] bg-[#8E44AD] px-2 py-0.5 rounded-full text-white font-bold"><?php echo count(get_all_testimonials()); ?></span>
                                </a>

                                <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-white/40 cursor-not-allowed text-left focus:outline-none">
                                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2z"></path></svg>
                                    <span>Inquiries</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Footer Widget (Fixed at bottom of sidebar) -->
                    <div class="p-4 border-t border-white/10 flex items-center justify-between bg-[#1E2922] shrink-0 sticky bottom-0 z-20">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#E0A838] text-[#1E2922] flex items-center justify-center font-bold text-sm shadow-md shrink-0">
                                A
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs font-bold text-white truncate">Admin</p>
                                <p class="text-[9px] text-white/60 truncate">ap8307655@gmail.com</p>
                            </div>
                        </div>
                        <!-- Logout Trigger -->
                        <a href="admin.php?action=logout" class="p-2.5 bg-red-500/20 hover:bg-red-600 text-red-300 hover:text-white rounded-xl transition flex items-center gap-1.5 shrink-0" title="Sign Out">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span class="text-xs font-bold hidden xl:inline">Logout</span>
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Right Main Content Panel -->
            <main class="flex-grow flex flex-col p-6 md:p-8 space-y-6 overflow-x-hidden">
                
                <!-- Dashboard Content Area Title Block -->
                <div class="flex justify-between items-center border-b border-charcoal/10 pb-4">
                    <div>
                        <h2 class="text-3xl font-bold font-serif capitalize">
                            <?php 
                            if ($current_tab === 'dashboard') echo 'Dashboard Overview';
                            elseif ($current_tab === 'products') echo 'Manage Showcase Products';
                            elseif ($current_tab === 'map') echo 'Sourcing Map State Spices';
                            elseif ($current_tab === 'testimonials') echo 'Client Feedback & Testimonials';
                            elseif ($current_tab === 'hero') echo 'Hero Settings: ' . ucfirst($selected_page) . ' Page';
                            ?>
                        </h2>
                        <p class="text-xs text-charcoal/50 uppercase tracking-widest font-bold mt-1">Welcome back, Admin</p>
                    </div>
                    <div class="flex items-center gap-3">

                        <!-- Header Prominent Logout Button -->
                        <a href="admin.php?action=logout" 
                           class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 hover:bg-red-600 hover:text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>

                <!-- Tab 1: Dashboard Overview Panel with 1-Minute Live Polling -->
                <?php if ($current_tab === 'dashboard'): ?>
                <div x-data="dashboardManager()" class="space-y-6">
                    
                    <!-- Row of 4 stats counters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Stat 1 -->
                        <div class="bg-white rounded-2xl p-6 border border-[#1E2922]/10 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-charcoal/50 uppercase tracking-wider">Home Hero Mode</p>
                                <p class="text-2xl font-bold text-[#D96E48] font-serif uppercase mt-1">
                                    <?php $home_cfg = get_hero_config('home'); echo htmlspecialchars($home_cfg['mode']); ?>
                                </p>
                            </div>
                            <div class="p-3 bg-[#D96E48]/10 text-[#D96E48] rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>

                        <!-- Stat 2 -->
                        <div class="bg-white rounded-2xl p-6 border border-[#1E2922]/10 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-charcoal/50 uppercase tracking-wider">Products Hero Mode</p>
                                <p class="text-2xl font-bold text-[#E0A838] font-serif uppercase mt-1">
                                    <?php $prod_cfg = get_hero_config('products'); echo htmlspecialchars($prod_cfg['mode']); ?>
                                </p>
                            </div>
                            <div class="p-3 bg-[#E0A838]/10 text-[#E0A838] rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>

                        <!-- Stat 3 (Live updated) -->
                        <div class="bg-white rounded-2xl p-6 border border-[#1E2922]/10 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-charcoal/50 uppercase tracking-wider">Visitors Today</p>
                                <p class="text-3xl font-bold text-charcoal mt-1" x-text="todayUnique"></p>
                            </div>
                            <div class="p-3 bg-[#1E2922]/10 text-[#1E2922] rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>

                        <!-- Stat 4 (Live updated) -->
                        <div class="bg-white rounded-2xl p-6 border border-[#1E2922]/10 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-charcoal/50 uppercase tracking-wider">Total Page Views</p>
                                <p class="text-3xl font-bold text-charcoal mt-1" x-text="totalVisits"></p>
                            </div>
                            <div class="p-3 bg-green-100 text-green-700 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row containing charts representation -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Chart block 1: Analytics with 1-Min Auto Polling -->
                        <div class="bg-white rounded-2xl p-6 border border-[#1E2922]/10 shadow-sm lg:col-span-2 space-y-4">
                            <div class="flex items-center justify-between border-b border-charcoal/10 pb-3">
                                <div>
                                    <h3 class="font-serif font-bold text-lg text-charcoal">Website Visits by Page</h3>
                                    <p class="text-xs text-charcoal/50">Total page view hits recorded per page (Auto 1 min refresh)</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="fetchAnalytics()" title="Click to refresh analytics now" class="text-xs font-bold bg-[#D96E48]/10 text-[#D96E48] hover:bg-[#D96E48] hover:text-white px-3 py-1 rounded-full uppercase tracking-wider transition flex items-center gap-1.5 cursor-pointer">
                                        <span class="w-2 h-2 rounded-full bg-green-500 animate-ping"></span>
                                        <span x-text="isRefreshing ? 'Refreshing...' : 'Live (Auto 1m)'">Live (Auto 1m)</span>
                                    </button>
                                    <span class="text-[10px] text-charcoal/40 font-mono" x-text="'Updated: ' + lastUpdated">Updated: Just now</span>
                                </div>
                            </div>

                            <!-- Visual Bar Chart Container (Dynamically rendered via Alpine) -->
                            <div class="h-64 flex items-end justify-around px-4 pb-2 border-b border-l border-charcoal/20 gap-3 pt-8">
                                <template x-for="(displayLabel, pageKey) in standardPages" :key="pageKey">
                                    <div class="flex-1 flex flex-col items-center h-full justify-end group relative">
                                        <!-- Tooltip on Hover -->
                                        <div class="opacity-0 group-hover:opacity-100 pointer-events-none transition-all duration-200 absolute -top-12 bg-[#1E2922] text-white text-[10px] font-bold py-1.5 px-3 rounded-lg shadow-xl whitespace-nowrap z-30">
                                            <span x-text="(pageStats[pageKey] || 0) + ' Page View' + ((pageStats[pageKey] || 0) === 1 ? '' : 's') + ' (' + getSharePct(pageStats[pageKey] || 0) + '%)'"></span>
                                        </div>

                                        <!-- Count Badge Above Bar -->
                                        <span class="text-xs font-bold text-[#D96E48] mb-2 font-mono group-hover:scale-110 transition-transform" x-text="pageStats[pageKey] || 0">
                                        </span>

                                        <!-- Bar Column -->
                                        <div class="w-full max-w-[50px] rounded-t-xl transition-all duration-500 shadow-sm border border-charcoal/10 group-hover:brightness-110"
                                             :class="(pageStats[pageKey] || 0) > 0 ? 'bg-gradient-to-t from-[#1E2922] via-[#D96E48] to-[#E0A838]' : 'bg-gray-200'"
                                             :style="'height: ' + getBarHeight(pageStats[pageKey] || 0) + '%;'">
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- X-Axis Labels -->
                            <div class="flex justify-around text-xs font-bold text-charcoal/60 px-1 pt-2">
                                <template x-for="(displayLabel, pageKey) in standardPages" :key="pageKey">
                                    <span class="flex-1 text-center truncate px-1" :title="displayLabel" x-text="displayLabel">
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Chart block 2: Occupancy/Showcase balance -->
                        <div class="bg-white rounded-2xl p-6 border border-[#1E2922]/10 shadow-sm flex flex-col justify-between">
                            <h3 class="font-serif font-bold text-lg text-charcoal border-b border-charcoal/10 pb-2">Active Hero Balance</h3>
                            
                            <div class="flex-grow flex items-center justify-center py-6">
                                <div class="relative w-36 h-36 rounded-full border-[16px] border-[#FAF3EC] flex items-center justify-center">
                                    <!-- Segment coloring using relative borders -->
                                    <div class="absolute inset-0 rounded-full border-[16px] border-[#D96E48] border-r-transparent border-b-transparent"></div>
                                    <div class="text-center">
                                        <p class="text-2xl font-bold font-serif">83%</p>
                                        <p class="text-[9px] font-bold text-charcoal/50 uppercase tracking-wider mt-0.5">Optimized</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-center gap-6 text-xs text-charcoal/70">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#D96E48]"></span>
                                    <span>Slider Banners</span>
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#FAF3EC] border border-[#1E2922]/10"></span>
                                    <span>Loop Video</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom slider images table overview -->
                    <div class="bg-white rounded-2xl border border-[#1E2922]/10 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-charcoal/10">
                            <h3 class="font-serif font-bold text-lg text-charcoal">Active Homepage Banners</h3>
                            <p class="text-xs text-charcoal/50 mt-1">Image URLs currently looping on the homepage hero section.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-[#FAF3EC] text-charcoal/60 text-xs font-bold uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3.5">Slide Number</th>
                                        <th class="px-6 py-3.5">Preview</th>
                                        <th class="px-6 py-3.5">URL Location</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-charcoal/10">
                                    <?php foreach ($home_cfg['slider_images'] as $index => $img): ?>
                                        <tr class="hover:bg-beige/25 transition">
                                            <td class="px-6 py-4 font-bold text-[#D96E48]">#<?php echo $index + 1; ?></td>
                                            <td class="px-6 py-4">
                                                <img src="<?php echo $img; ?>" alt="Slide" class="h-10 w-16 object-cover rounded-lg border border-charcoal/10">
                                            </td>
                                            <td class="px-6 py-4 font-mono text-xs text-charcoal/70 truncate max-w-md"><?php echo htmlspecialchars($img); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php elseif ($current_tab === 'products'): ?>
                <div x-data="productPanelManager()" class="space-y-6">

                    <!-- Alert Banners -->
                    <?php if (!empty($success_message)): ?>
                        <div class="bg-green-50 text-green-700 border border-green-200 text-sm px-6 py-4 rounded-2xl flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold"><?php echo htmlspecialchars($success_message); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="bg-red-50 text-red-700 border border-red-200 text-sm px-6 py-4 rounded-2xl flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold"><?php echo htmlspecialchars($error_message); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Top Action Bar & Category Pills -->
                    <div class="bg-white rounded-2xl p-6 border border-[#1E2922]/10 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="font-serif font-bold text-xl text-charcoal">Catalog Products (<?php echo count(get_all_products()); ?> Items)</h3>
                            <p class="text-xs text-charcoal/50 mt-0.5">Manage, add, edit, or remove agricultural seeds, categories, and spice products.</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Manage Categories Button -->
                            <button type="button" @click="showCatModal = true" 
                                    class="px-4 py-2.5 bg-[#1E2922] text-[#E0A838] text-xs font-bold rounded-xl shadow-md hover:bg-black transition flex items-center gap-2 cursor-pointer border border-[#E0A838]/30">
                                <span>🏷️ Manage Categories</span>
                            </button>

                            <button @click="showAddModal = true" 
                                    style="background-color: #D96E48; color: #FFFFFF;"
                                    class="px-5 py-2.5 text-white text-xs font-bold rounded-xl shadow-md hover:opacity-90 transition flex items-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add New Product
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Category Filter Tabs -->
                    <div class="flex flex-wrap gap-2">
                        <button @click="filterCat = 'all'" :class="filterCat === 'all' ? 'bg-[#1E2922] text-[#E0A838] font-bold' : 'bg-white text-charcoal/70 hover:bg-charcoal/5'" class="px-4 py-2 rounded-xl text-xs font-semibold border border-charcoal/10 transition">
                            All Categories
                        </button>
                        <template x-for="cat in categories" :key="cat.slug">
                            <button @click="filterCat = cat.slug" 
                                    :class="filterCat === cat.slug ? 'bg-[#1E2922] text-[#E0A838] font-bold' : 'bg-white text-charcoal/70 hover:bg-charcoal/5'" 
                                    class="px-4 py-2 rounded-xl text-xs font-semibold border border-charcoal/10 transition flex items-center gap-1.5 capitalize">
                                <span x-text="cat.name"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Products Table -->
                    <div class="bg-white rounded-2xl border border-[#1E2922]/10 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-[#FAF3EC] text-charcoal/60 text-xs font-bold uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3.5">Image</th>
                                        <th class="px-6 py-3.5">Product Name</th>
                                        <th class="px-6 py-3.5">Category</th>
                                        <th class="px-6 py-3.5">Description</th>
                                        <th class="px-6 py-3.5 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-charcoal/10">
                                    <?php 
                                    $all_prods = get_all_products();
                                    foreach ($all_prods as $prod): 
                                    ?>
                                        <tr x-show="filterCat === 'all' || filterCat === '<?php echo htmlspecialchars($prod['category']); ?>'" class="hover:bg-[#FAF3EC]/50 transition">
                                            <td class="px-6 py-4">
                                                <div class="w-14 h-14 bg-[#FAF3EC] border border-charcoal/10 rounded-xl p-1 flex items-center justify-center overflow-hidden shrink-0">
                                                    <img src="<?php echo htmlspecialchars($prod['img1'] ?: 'https://kedarnathspices.com/storage/img/logo.png'); ?>" 
                                                         alt="<?php echo htmlspecialchars($prod['name']); ?>" 
                                                         class="w-full h-full object-contain">
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 font-bold text-charcoal">
                                                <?php echo htmlspecialchars($prod['name']); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-3 py-1 bg-[#D96E48]/10 text-[#D96E48] text-xs font-bold rounded-full uppercase tracking-wider">
                                                    <?php echo htmlspecialchars($prod['category']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-xs text-charcoal/70 max-w-xs truncate">
                                                <?php echo htmlspecialchars($prod['desc']); ?>
                                            </td>
                                            <td class="px-6 py-4 text-right space-x-2">
                                                <button type="button" 
                                                        @click="editProduct = { 
                                                            id: <?php echo $prod['id']; ?>, 
                                                            name: '<?php echo addslashes($prod['name']); ?>', 
                                                            category: '<?php echo addslashes($prod['category']); ?>', 
                                                            desc: '<?php echo addslashes($prod['desc']); ?>', 
                                                            img1_url: '<?php echo addslashes($prod['img1']); ?>', 
                                                            img2_url: '<?php echo addslashes($prod['img2']); ?>' 
                                                        }; showEditModal = true"
                                                        class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg font-bold text-xs hover:bg-blue-100 transition">
                                                    Edit
                                                </button>
                                                
                                                <form method="POST" action="admin.php?tab=products" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                    <input type="hidden" name="action" value="delete_product">
                                                    <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                                                    <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg font-bold text-xs hover:bg-red-100 transition">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Add Product Modal -->
                    <div x-show="showAddModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
                        <div class="bg-white rounded-3xl p-6 md:p-8 max-w-lg w-full shadow-2xl border border-charcoal/10 relative overflow-hidden" @click.outside="showAddModal = false">
                            <h3 class="text-2xl font-serif font-bold text-charcoal border-b border-charcoal/10 pb-3 mb-6">Add New Product</h3>
                            
                            <form method="POST" action="admin.php?tab=products" enctype="multipart/form-data" class="space-y-4">
                                <input type="hidden" name="action" value="add_product">
                                
                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Product Name</label>
                                    <input type="text" name="name" required placeholder="e.g. Black Pepper (Kali Mirch)" class="w-full px-4 py-2.5 rounded-xl border border-charcoal/20 text-sm">
                                </div>

                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Category</label>
                                    <select name="category" required class="w-full px-4 py-2.5 rounded-xl border border-charcoal/20 text-sm bg-white capitalize">
                                        <template x-for="cat in categories" :key="cat.slug">
                                            <option :value="cat.slug" x-text="cat.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Primary Image (File Upload or URL)</label>
                                    <input type="file" name="img1_file" accept="image/*" class="w-full text-xs text-charcoal/50 mb-2">
                                    <input type="text" name="img1_url" placeholder="Or enter Image URL" class="w-full px-4 py-2 rounded-xl border border-charcoal/20 text-xs">
                                </div>

                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Secondary Image (Optional)</label>
                                    <input type="file" name="img2_file" accept="image/*" class="w-full text-xs text-charcoal/50 mb-2">
                                    <input type="text" name="img2_url" placeholder="Or enter Image URL" class="w-full px-4 py-2 rounded-xl border border-charcoal/20 text-xs">
                                </div>

                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Description</label>
                                    <textarea name="desc" rows="3" required placeholder="Describe purity, origin, and characteristics..." class="w-full px-4 py-2.5 rounded-xl border border-charcoal/20 text-sm"></textarea>
                                </div>

                                <div class="flex justify-end gap-3 pt-4 border-t border-charcoal/10">
                                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 bg-gray-100 text-charcoal/70 font-bold rounded-xl text-xs hover:bg-gray-200 transition">Cancel</button>
                                    <button type="submit" class="px-6 py-2.5 bg-[#D96E48] text-white font-bold rounded-xl text-xs hover:bg-opacity-90 transition">Save Product</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Edit Product Modal -->
                    <div x-show="showEditModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
                        <div class="bg-white rounded-3xl p-6 md:p-8 max-w-lg w-full shadow-2xl border border-charcoal/10 relative overflow-hidden" @click.outside="showEditModal = false">
                            <h3 class="text-2xl font-serif font-bold text-charcoal border-b border-charcoal/10 pb-3 mb-6">Edit Product</h3>
                            
                            <form method="POST" action="admin.php?tab=products" enctype="multipart/form-data" class="space-y-4">
                                <input type="hidden" name="action" value="edit_product">
                                <input type="hidden" name="id" :value="editProduct.id">
                                
                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Product Name</label>
                                    <input type="text" name="name" x-model="editProduct.name" required class="w-full px-4 py-2.5 rounded-xl border border-charcoal/20 text-sm">
                                </div>

                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Category</label>
                                    <select name="category" x-model="editProduct.category" required class="w-full px-4 py-2.5 rounded-xl border border-charcoal/20 text-sm bg-white capitalize">
                                        <template x-for="cat in categories" :key="cat.slug">
                                            <option :value="cat.slug" x-text="cat.name" :selected="editProduct.category === cat.slug"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Primary Image</label>
                                    <input type="file" name="img1_file" accept="image/*" class="w-full text-xs text-charcoal/50 mb-2">
                                    <input type="text" name="img1_url" x-model="editProduct.img1_url" placeholder="Or enter Image URL" class="w-full px-4 py-2 rounded-xl border border-charcoal/20 text-xs">
                                </div>

                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Secondary Image (Optional)</label>
                                    <input type="file" name="img2_file" accept="image/*" class="w-full text-xs text-charcoal/50 mb-2">
                                    <input type="text" name="img2_url" x-model="editProduct.img2_url" placeholder="Or enter Image URL" class="w-full px-4 py-2 rounded-xl border border-charcoal/20 text-xs">
                                </div>

                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Description</label>
                                    <textarea name="desc" x-model="editProduct.desc" rows="3" required class="w-full px-4 py-2.5 rounded-xl border border-charcoal/20 text-sm"></textarea>
                                </div>

                                <div class="flex justify-end gap-3 pt-4 border-t border-charcoal/10">
                                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 bg-gray-100 text-charcoal/70 font-bold rounded-xl text-xs hover:bg-gray-200 transition">Cancel</button>
                                    <button type="submit" class="px-6 py-2.5 bg-[#D96E48] text-white font-bold rounded-xl text-xs hover:bg-opacity-90 transition">Update Product</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- CATEGORY MANAGEMENT MODAL -->
                    <div x-show="showCatModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-3xl w-full p-6 md:p-8 space-y-6 shadow-2xl relative border border-charcoal/10" @click.away="showCatModal = false">
                            <div class="flex items-center justify-between border-b border-charcoal/10 pb-4">
                                <div>
                                    <h3 class="text-xl font-bold font-serif text-charcoal flex items-center gap-2">🏷️ Manage Product Categories</h3>
                                    <p class="text-xs text-charcoal/50">Add, edit, or delete catalog categories displayed on live website.</p>
                                </div>
                                <button type="button" @click="showCatModal = false" class="text-charcoal/40 hover:text-charcoal font-bold text-xl">✕</button>
                            </div>

                            <div class="flex justify-end">
                                <button type="button" @click="showAddCatModal = true" class="px-4 py-2 bg-[#27AE60] hover:bg-[#1F7042] text-white font-bold rounded-xl text-xs transition shadow flex items-center gap-1.5 cursor-pointer">
                                    <span>+ Add New Category</span>
                                </button>
                            </div>

                            <!-- Categories Table -->
                            <div class="border rounded-2xl overflow-hidden border-charcoal/10 shadow-sm">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead class="bg-[#1E2922] text-[#E0A838] font-bold uppercase tracking-wider">
                                        <tr>
                                            <th class="p-4 w-16">Icon</th>
                                            <th class="p-4">Category Name</th>
                                            <th class="p-4">Slug (ID)</th>
                                            <th class="p-4">Description</th>
                                            <th class="p-4 text-right whitespace-nowrap">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        <template x-for="cat in categories" :key="cat.slug">
                                            <tr class="hover:bg-amber-50/30 transition">
                                                <td class="p-4">
                                                    <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-200 p-1 flex items-center justify-center shrink-0">
                                                        <img :src="cat.icon || 'https://via.placeholder.com/40'" class="w-full h-full object-contain">
                                                    </div>
                                                </td>
                                                <td class="p-4 font-bold text-charcoal text-sm capitalize" x-text="cat.name"></td>
                                                <td class="p-4 font-mono text-gray-500 font-semibold" x-text="cat.slug"></td>
                                                <td class="p-4 text-charcoal/60 leading-relaxed max-w-xs text-xs line-clamp-2" x-text="cat.desc"></td>
                                                <td class="p-4 text-right whitespace-nowrap">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button type="button" @click="editCat = Object.assign({}, cat); showEditCatModal = true" 
                                                                class="px-3.5 py-1.5 bg-amber-50 text-amber-700 hover:bg-amber-500 hover:text-white rounded-xl font-bold border border-amber-300 shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                            <span>Edit</span>
                                                        </button>

                                                        <form method="POST" action="admin.php?tab=products" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                            <input type="hidden" name="action" value="delete_category">
                                                            <input type="hidden" name="slug" :value="cat.slug">
                                                            <button type="submit" 
                                                                    class="px-3.5 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl font-bold border border-red-200 shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                <span>Delete</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ADD CATEGORY MODAL -->
                    <div x-show="showAddCatModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-lg w-full p-6 md:p-8 space-y-6 shadow-2xl relative border border-charcoal/10" @click.away="showAddCatModal = false">
                            <div class="flex items-center justify-between border-b border-charcoal/10 pb-3">
                                <h3 class="text-xl font-bold font-serif text-charcoal">➕ Add New Category</h3>
                                <button type="button" @click="showAddCatModal = false" class="text-charcoal/40 font-bold text-xl">✕</button>
                            </div>

                            <form method="POST" action="admin.php?tab=products" class="space-y-4">
                                <input type="hidden" name="action" value="add_category">

                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Category Slug (ID)</label>
                                    <input type="text" name="slug" required placeholder="e.g. grains or pulses" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#27AE60]">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Category Display Name</label>
                                    <input type="text" name="name" required placeholder="e.g. Grains & Pulses" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#27AE60]">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Category Icon URL</label>
                                    <input type="text" name="icon" placeholder="https://kedarnathspices.com/storage/img/category/..." class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#27AE60]">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Category Description</label>
                                    <textarea name="desc" rows="3" placeholder="Category description..." class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#27AE60]"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Background Tint Color (Hex)</label>
                                    <input type="color" name="bg" value="#FFF5E6" class="w-12 h-8 rounded border cursor-pointer">
                                </div>

                                <div class="flex justify-end gap-3 pt-4 border-t">
                                    <button type="button" @click="showAddCatModal = false" class="px-4 py-2 bg-gray-100 text-xs font-bold rounded-xl">Cancel</button>
                                    <button type="submit" class="px-5 py-2 bg-[#27AE60] text-white text-xs font-bold rounded-xl shadow">+ Add Category</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- EDIT CATEGORY MODAL -->
                    <div x-show="showEditCatModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-lg w-full p-6 md:p-8 space-y-6 shadow-2xl relative border border-charcoal/10" @click.away="showEditCatModal = false">
                            <div class="flex items-center justify-between border-b border-charcoal/10 pb-3">
                                <h3 class="text-xl font-bold font-serif text-charcoal">✏️ Edit Category</h3>
                                <button type="button" @click="showEditCatModal = false" class="text-charcoal/40 font-bold text-xl">✕</button>
                            </div>

                            <form method="POST" action="admin.php?tab=products" class="space-y-4">
                                <input type="hidden" name="action" value="edit_category">
                                <input type="hidden" name="slug" :value="editCat.slug">

                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Category Display Name</label>
                                    <input type="text" name="name" x-model="editCat.name" required class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#27AE60]">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Category Icon URL</label>
                                    <input type="text" name="icon" x-model="editCat.icon" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#27AE60]">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Category Description</label>
                                    <textarea name="desc" x-model="editCat.desc" rows="3" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#27AE60]"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Background Tint Color (Hex)</label>
                                    <input type="color" name="bg" x-model="editCat.bg" class="w-12 h-8 rounded border cursor-pointer">
                                </div>

                                <div class="flex justify-end gap-3 pt-4 border-t">
                                    <button type="button" @click="showEditCatModal = false" class="px-4 py-2 bg-gray-100 text-xs font-bold rounded-xl">Cancel</button>
                                    <button type="submit" class="px-5 py-2 bg-[#27AE60] text-white text-xs font-bold rounded-xl shadow">Save Category</button>
                                </div>
                            </form>
                        </div>
                    </div>

                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Primary Image</label>
                                    <input type="file" name="img1_file" accept="image/*" class="w-full text-xs text-charcoal/50 mb-2">
                                    <input type="text" name="img1_url" x-model="editProduct.img1_url" placeholder="Image URL" class="w-full px-4 py-2 rounded-xl border border-charcoal/20 text-xs">
                                </div>

                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Secondary Image (Optional)</label>
                                    <input type="file" name="img2_file" accept="image/*" class="w-full text-xs text-charcoal/50 mb-2">
                                    <input type="text" name="img2_url" x-model="editProduct.img2_url" placeholder="Image URL" class="w-full px-4 py-2 rounded-xl border border-charcoal/20 text-xs">
                                </div>

                                <div>
                                    <label class="text-xs font-bold uppercase text-charcoal/70 block mb-1">Description</label>
                                    <textarea name="desc" rows="3" x-model="editProduct.desc" required class="w-full px-4 py-2.5 rounded-xl border border-charcoal/20 text-sm"></textarea>
                                </div>

                                <div class="flex justify-end gap-3 pt-4 border-t border-charcoal/10">
                                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 bg-gray-100 text-charcoal/70 font-bold rounded-xl text-xs hover:bg-gray-200 transition">Cancel</button>
                                    <button type="submit" class="px-6 py-2.5 bg-[#D96E48] text-white font-bold rounded-xl text-xs hover:bg-opacity-90 transition">Update Product</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                <?php elseif ($current_tab === 'map'): ?>
                <div x-data="mapStateManager()" class="space-y-6">

                    <!-- Alert Banners -->
                    <?php if (!empty($success_message)): ?>
                        <div class="bg-green-50 text-green-700 border border-green-200 text-sm px-6 py-4 rounded-2xl flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold"><?php echo htmlspecialchars($success_message); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="bg-red-50 text-red-700 border border-red-200 text-sm px-6 py-4 rounded-2xl flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold"><?php echo htmlspecialchars($error_message); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- State Selector Toolbar (Dropdown + Pills + Add New State Button) -->
                    <div class="bg-white p-5 rounded-3xl border border-charcoal/10 shadow-sm space-y-4">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full bg-[#27AE60] animate-pulse"></span>
                                <div>
                                    <h4 class="text-sm font-bold text-charcoal">Select State to Manage Items & Details</h4>
                                    <p class="text-xs text-charcoal/50">Choose any sourcing state below or add a new state pin to the map.</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                                <!-- Add New State Button -->
                                <button type="button" @click="showAddStateModal = true" 
                                        style="background-color: #27AE60; color: #FFFFFF;"
                                        class="px-4 py-2.5 text-white font-bold rounded-xl text-xs transition shadow-md hover:opacity-90 flex items-center gap-2 cursor-pointer">
                                    <span>+ Add New State to Map</span>
                                </button>

                                <!-- Dropdown Select -->
                                <div class="flex items-center gap-2">
                                    <select x-model="selectedKey" class="px-4 py-2.5 bg-[#1E2922] text-[#E0A838] font-bold rounded-xl text-xs border border-charcoal/20 shadow-md focus:outline-none cursor-pointer">
                                        <?php foreach (get_all_map_states() as $st_k => $st_v): ?>
                                            <option value="<?php echo htmlspecialchars($st_k); ?>"><?php echo htmlspecialchars($st_v['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- State Selector Pills Grid -->
                        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gray-100">
                            <template x-for="st in stateList" :key="st.key">
                                <button type="button" @click="selectedKey = st.key"
                                        :class="selectedKey === st.key ? 'bg-[#1E2922] text-[#E0A838] border-[#1E2922] shadow-md font-bold' : 'bg-gray-50 text-charcoal/70 hover:bg-gray-100 border-gray-200 font-medium'"
                                        class="px-4 py-2 rounded-xl text-xs border transition flex items-center gap-2 cursor-pointer">
                                    <span class="w-2.5 h-2.5 rounded-full shadow-sm shrink-0" :style="`background-color: ${st.bg}`"></span>
                                    <span x-text="st.name"></span>
                                    <span class="text-[10px] bg-black/10 px-2 py-0.5 rounded-full font-bold" x-text="st.primarySpices ? st.primarySpices.length : 0"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- State Edit Form Card -->
                    <div class="bg-white rounded-3xl border border-charcoal/10 shadow-xl overflow-hidden">
                        <div class="p-6 bg-gradient-to-r from-[#1E2922] via-[#27AE60] to-[#1E2922] text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center font-bold text-lg text-[#E0A838]">
                                    🗺️
                                </div>
                                <div>
                                    <h3 class="text-xl font-serif font-bold text-white" x-text="currentState.name">State Name</h3>
                                    <p class="text-xs text-white/70" x-text="currentState.region">Region Subtitle</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/10 border border-white/20 text-[#E0A838]" x-text="currentState.badge">Badge</span>
                        </div>

                        <form method="POST" action="admin.php?tab=map" class="p-6 md:p-8 space-y-6">
                            <input type="hidden" name="action" value="update_map_state">
                            <input type="hidden" name="state_key" :value="selectedKey">

                            <!-- Primary Harvest Spices Manager (Add / Delete Tags) -->
                            <div class="bg-gradient-to-br from-green-50/70 to-emerald-50/40 p-6 rounded-2xl border-2 border-[#27AE60]/30 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="block text-sm font-bold text-charcoal flex items-center gap-2">
                                            <span>🌿 Primary Harvest Spices (State Specific Tags)</span>
                                        </label>
                                        <p class="text-xs text-charcoal/60">Add, edit, or delete items that appear inside the state map popup modal.</p>
                                    </div>
                                    <span class="text-xs font-bold bg-[#27AE60]/20 text-[#1F7042] px-3 py-1 rounded-full" x-text="`${currentState.primarySpices ? currentState.primarySpices.length : 0} Spices Added`"></span>
                                </div>

                                <!-- Current Spice Tags Grid -->
                                <div class="flex flex-wrap items-center gap-2 pt-2">
                                    <template x-for="(spice, idx) in currentState.primarySpices" :key="idx">
                                        <div class="flex items-center gap-2 bg-white border-2 border-[#27AE60]/40 px-3.5 py-1.5 rounded-full text-xs font-bold text-[#1C2C23] shadow-sm hover:border-red-400 transition group">
                                            <span class="text-[#27AE60]">✓</span>
                                            <input type="text" name="primary_spices[]" :value="spice" @input="currentState.primarySpices[idx] = $event.target.value"
                                                   class="bg-transparent border-b border-transparent hover:border-gray-300 focus:border-[#27AE60] focus:outline-none text-xs font-bold text-[#1C2C23] w-auto">
                                            <button type="button" @click="removeSpice(idx)" 
                                                    class="text-red-400 hover:text-red-600 font-black text-sm ml-1 px-1 rounded hover:bg-red-50 transition"
                                                    title="Delete this spice">×</button>
                                        </div>
                                    </template>
                                </div>

                                <!-- Add New Spice Input -->
                                <div class="pt-3 border-t border-[#27AE60]/20 flex items-center gap-3">
                                    <input type="text" x-model="newSpice" @keydown.enter.prevent="addSpice()"
                                           placeholder="Enter new spice name (e.g. Cumin Seeds, Green Cardamom)" 
                                           class="flex-1 px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-[#27AE60] focus:outline-none font-semibold">
                                    <button type="button" @click="addSpice()" 
                                            class="px-5 py-2.5 bg-[#27AE60] hover:bg-[#1F7042] text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-md">
                                        <span>+ Add Spice</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Basic State Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal/70 mb-2">State Display Name</label>
                                    <input type="text" name="name" x-model="currentState.name" required
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#27AE60] focus:outline-none font-bold">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal/70 mb-2">Region Subtitle</label>
                                    <input type="text" name="region" x-model="currentState.region" required
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#27AE60] focus:outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal/70 mb-2">Badge Title</label>
                                    <input type="text" name="badge" x-model="currentState.badge" required
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#27AE60] focus:outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal/70 mb-2">Accent Color (Hex)</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="bg" x-model="currentState.bg" class="w-10 h-10 rounded-lg cursor-pointer border-0">
                                        <input type="text" name="bg" x-model="currentState.bg" required
                                               class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#27AE60] focus:outline-none font-mono">
                                    </div>
                                </div>
                            </div>

                            <!-- State Image URL -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-charcoal/70 mb-2">Popup Product Image URL</label>
                                <input type="text" name="image" x-model="currentState.image" required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#27AE60] focus:outline-none">
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-charcoal/70 mb-2">State Description Paragraph</label>
                                <textarea name="description" x-model="currentState.description" rows="3" required
                                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#27AE60] focus:outline-none leading-relaxed"></textarea>
                            </div>

                            <!-- Stats Row (Purity, Capacity, Grading) -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2 border-t border-gray-100">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal/70 mb-2">Purity Guarantee</label>
                                    <input type="text" name="purity" :value="currentState.stats ? currentState.stats.purity : '99.0%+'" 
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#27AE60] focus:outline-none font-semibold">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal/70 mb-2">Annual Capacity</label>
                                    <input type="text" name="capacity" :value="currentState.stats ? currentState.stats.capacity : '3,000 MT/Yr'"
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#27AE60] focus:outline-none font-semibold">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal/70 mb-2">Grading Standard</label>
                                    <input type="text" name="grading" :value="currentState.stats ? currentState.stats.grading : 'Machine Sortex'"
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#27AE60] focus:outline-none font-semibold">
                                </div>
                            </div>

                            <!-- Map Pin Position Coordinates (Top% and Left%) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-gray-100 bg-gray-50/50 p-4 rounded-2xl border border-gray-200">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal/70 mb-2">Map Pin Vertical Position (Top %)</label>
                                    <input type="text" name="top_percent" :value="currentState.top || '50%'" required
                                           placeholder="e.g. 45%" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-mono font-bold focus:ring-2 focus:ring-[#27AE60] focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal/70 mb-2">Map Pin Horizontal Position (Left %)</label>
                                    <input type="text" name="left_percent" :value="currentState.left || '50%'" required
                                           placeholder="e.g. 35%" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-mono font-bold focus:ring-2 focus:ring-[#27AE60] focus:outline-none">
                                </div>
                            </div>

                            <!-- Submit & Delete Action Row -->
                            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                                <!-- Delete State Button -->
                                <button type="submit" name="action" value="delete_map_state" 
                                        onclick="return confirm('Are you sure you want to delete this state pin from the map?');"
                                        class="px-5 py-3 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold rounded-xl text-xs transition border border-red-200 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Delete State</span>
                                </button>

                                <button type="submit" class="px-8 py-3.5 bg-[#27AE60] hover:bg-[#1F7042] text-white font-bold rounded-xl text-sm transition shadow-lg flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Save State Spices & Details</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- ADD NEW STATE MODAL -->
                    <div x-show="showAddStateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 md:p-8 space-y-6 shadow-2xl relative border border-charcoal/10" @click.away="showAddStateModal = false">
                            <div class="flex items-center justify-between border-b border-charcoal/10 pb-4">
                                <h3 class="text-xl font-bold font-serif text-charcoal">➕ Add New Sourcing State Pin to Map</h3>
                                <button type="button" @click="showAddStateModal = false" class="text-charcoal/40 hover:text-charcoal font-bold text-xl">✕</button>
                            </div>

                            <form method="POST" action="admin.php?tab=map" class="space-y-4">
                                <input type="hidden" name="action" value="add_map_state">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">State Key (ID)</label>
                                        <input type="text" name="state_key" required placeholder="e.g. up or maharashtra" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#27AE60]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">State Display Name</label>
                                        <input type="text" name="name" required placeholder="e.g. Uttar Pradesh" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#27AE60]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Region Subtitle</label>
                                        <input type="text" name="region" required placeholder="e.g. Northern Plains • Spice Sourcing" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#27AE60]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Badge Title</label>
                                        <input type="text" name="badge" required placeholder="e.g. Premium Sourcing Zone" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#27AE60]">
                                    </div>

                                    <!-- Location Presets helper bar -->
                                    <div class="md:col-span-2 bg-green-50/80 p-3 rounded-2xl border border-green-200 space-y-2">
                                        <span class="font-bold text-[#1F7042] text-xs block">📍 Map Position Quick Presets (Click to Auto-Fill):</span>
                                        <div class="flex flex-wrap gap-1.5">
                                            <button type="button" @click="newTop = '32%'; newLeft = '44%'" class="px-2.5 py-1 bg-white hover:bg-[#27AE60] hover:text-white border border-green-300 rounded-lg font-bold text-[10px] transition">Uttar Pradesh (32%, 44%)</button>
                                            <button type="button" @click="newTop = '54%'; newLeft = '24%'" class="px-2.5 py-1 bg-white hover:bg-[#27AE60] hover:text-white border border-green-300 rounded-lg font-bold text-[10px] transition">Maharashtra (54%, 24%)</button>
                                            <button type="button" @click="newTop = '45%'; newLeft = '39%'" class="px-2.5 py-1 bg-white hover:bg-[#27AE60] hover:text-white border border-green-300 rounded-lg font-bold text-[10px] transition">Madhya Pradesh (45%, 39%)</button>
                                            <button type="button" @click="newTop = '37%'; newLeft = '60%'" class="px-2.5 py-1 bg-white hover:bg-[#27AE60] hover:text-white border border-green-300 rounded-lg font-bold text-[10px] transition">Bihar (37%, 60%)</button>
                                            <button type="button" @click="newTop = '46%'; newLeft = '72%'" class="px-2.5 py-1 bg-white hover:bg-[#27AE60] hover:text-white border border-green-300 rounded-lg font-bold text-[10px] transition">West Bengal (46%, 72%)</button>
                                            <button type="button" @click="newTop = '70%'; newLeft = '32%'" class="px-2.5 py-1 bg-white hover:bg-[#27AE60] hover:text-white border border-green-300 rounded-lg font-bold text-[10px] transition">Karnataka (70%, 32%)</button>
                                            <button type="button" @click="newTop = '82%'; newLeft = '42%'" class="px-2.5 py-1 bg-white hover:bg-[#27AE60] hover:text-white border border-green-300 rounded-lg font-bold text-[10px] transition">Tamil Nadu (82%, 42%)</button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Map Position Top % (Vertical)</label>
                                        <input type="text" name="top_percent" x-model="newTop" required placeholder="e.g. 32%" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-mono font-bold focus:ring-2 focus:ring-[#27AE60]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Map Position Left % (Horizontal)</label>
                                        <input type="text" name="left_percent" x-model="newLeft" required placeholder="e.g. 44%" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-mono font-bold focus:ring-2 focus:ring-[#27AE60]">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Accent Color (Hex)</label>
                                    <input type="color" name="bg" value="#27AE60" class="w-12 h-8 rounded border">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Product Image URL</label>
                                    <input type="text" name="image" value="https://kedarnathspices.com/storage/img/product/CuminWhole(Jeera)_1707732502.png" required class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#27AE60]">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Primary Harvest Spices (Comma Separated)</label>
                                    <input type="text" name="primary_spices" placeholder="Cumin Seeds, Coriander Seeds, Turmeric" required class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#27AE60]">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Description Paragraph</label>
                                    <textarea name="description" rows="2" required placeholder="State sourcing description..." class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#27AE60]"></textarea>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-charcoal/70 mb-1">Purity</label>
                                        <input type="text" name="purity" value="99.0%+" class="w-full px-3 py-1.5 bg-gray-50 border rounded-lg text-xs font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-charcoal/70 mb-1">Capacity</label>
                                        <input type="text" name="capacity" value="3,000 MT/Yr" class="w-full px-3 py-1.5 bg-gray-50 border rounded-lg text-xs font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-charcoal/70 mb-1">Grading</label>
                                        <input type="text" name="grading" value="Machine Sortex" class="w-full px-3 py-1.5 bg-gray-50 border rounded-lg text-xs font-bold">
                                    </div>
                                </div>

                                <div class="flex justify-end gap-3 pt-4 border-t border-charcoal/10">
                                    <button type="button" @click="showAddStateModal = false" class="px-4 py-2 bg-gray-100 text-charcoal/70 font-bold rounded-xl text-xs hover:bg-gray-200 transition">Cancel</button>
                                    <button type="submit" class="px-6 py-2.5 bg-[#27AE60] text-white font-bold rounded-xl text-xs hover:bg-[#1F7042] transition shadow-md">+ Add State to Map</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                <?php elseif ($current_tab === 'testimonials'): ?>
                <div x-data="testimonialsManager()" class="space-y-6">
                    
                    <!-- Alert Banners -->
                    <?php if (!empty($success_message)): ?>
                        <div class="bg-green-50 text-green-700 border border-green-200 text-sm px-6 py-4 rounded-2xl flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold"><?php echo htmlspecialchars($success_message); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="bg-red-50 text-red-700 border border-red-200 text-sm px-6 py-4 rounded-2xl flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold"><?php echo htmlspecialchars($error_message); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Header & Add Button -->
                    <div class="bg-white rounded-2xl p-6 border border-[#1E2922]/10 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="font-serif font-bold text-xl text-charcoal">💬 Client Feedback & Testimonials (<?php echo count(get_all_testimonials()); ?> Reviews)</h3>
                            <p class="text-xs text-charcoal/50 mt-0.5">Manage buyer testimonials displayed on Homepage and About page client feedback grids.</p>
                        </div>

                        <button @click="showAddModal = true" 
                                style="background-color: #8E44AD; color: #FFFFFF;"
                                class="px-5 py-2.5 text-white text-xs font-bold rounded-xl shadow-md hover:opacity-90 transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Add New Testimonial</span>
                        </button>
                    </div>

                    <!-- Testimonials Table -->
                    <div class="bg-white rounded-2xl border border-[#1E2922]/10 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-[#FAF3EC] text-charcoal/60 text-xs font-bold uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3.5">Client Profile</th>
                                        <th class="px-6 py-3.5">Badge & Rating</th>
                                        <th class="px-6 py-3.5">Feedback / Comment</th>
                                        <th class="px-6 py-3.5 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-charcoal/10">
                                    <?php 
                                    $all_testis = get_all_testimonials();
                                    foreach ($all_testis as $t): 
                                    ?>
                                        <tr class="hover:bg-gray-50/80 transition">
                                            <!-- Profile info -->
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <?php if (!empty($t['avatar'])): ?>
                                                        <img src="<?php echo htmlspecialchars($t['avatar']); ?>" alt="" class="w-12 h-12 rounded-full object-cover border-2 border-[#E0A838] shrink-0" style="width: 44px; height: 44px;">
                                                    <?php else: ?>
                                                        <div class="w-12 h-12 rounded-full bg-[#1E2922] text-[#E0A838] font-bold font-serif flex items-center justify-center text-sm border-2 border-[#E0A838] shrink-0" style="width: 44px; height: 44px;">
                                                            <?php 
                                                            $w = explode(' ', $t['name']);
                                                            echo strtoupper(substr($w[0],0,1) . (isset($w[1]) ? substr($w[1],0,1) : ''));
                                                            ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h4 class="font-bold text-charcoal text-sm font-serif"><?php echo htmlspecialchars($t['name']); ?></h4>
                                                        <p class="text-xs text-charcoal/60 font-medium"><?php echo htmlspecialchars($t['role']); ?></p>
                                                        <p class="text-[11px] text-[#D96E48] font-semibold"><?php echo htmlspecialchars($t['location']); ?></p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Badge & Rating -->
                                            <td class="px-6 py-4">
                                                <div class="space-y-1.5">
                                                    <span class="inline-block px-3 py-1 rounded-full bg-[#00FF99]/15 text-[#00AA66] text-[10px] font-extrabold uppercase tracking-wider border border-[#00FF99]/40 shadow-sm">
                                                        ✓ <?php echo htmlspecialchars($t['badge_type'] ?: 'Verified B2B'); ?>
                                                    </span>
                                                    <div class="flex items-center text-[#E0A838]">
                                                        <?php for($i=0; $i<(int)($t['rating'] ?: 5); $i++): ?>
                                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Comment -->
                                            <td class="px-6 py-4 max-w-md">
                                                <p class="text-xs text-charcoal/80 font-serif italic line-clamp-3">
                                                    "<?php echo htmlspecialchars($t['comment']); ?>"
                                                </p>
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button type="button" 
                                                            @click="editById(<?php echo (int)$t['id']; ?>)"
                                                            class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-bold rounded-lg text-xs transition border border-blue-200">
                                                        Edit
                                                    </button>
                                                    <form method="POST" action="admin.php?tab=testimonials" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this testimonial?');">
                                                        <input type="hidden" name="action" value="delete_testimonial">
                                                        <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                                        <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white font-bold rounded-lg text-xs transition border border-red-200">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ADD TESTIMONIAL MODAL -->
                    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-xl w-full p-6 md:p-8 space-y-6 shadow-2xl relative border border-charcoal/10" @click.away="showAddModal = false">
                            <div class="flex items-center justify-between border-b border-charcoal/10 pb-4">
                                <h3 class="text-xl font-bold font-serif text-charcoal">➕ Add New Client Feedback / Testimonial</h3>
                                <button type="button" @click="showAddModal = false" class="text-charcoal/40 hover:text-charcoal font-bold text-xl">✕</button>
                            </div>

                            <form method="POST" action="admin.php?tab=testimonials" enctype="multipart/form-data" class="space-y-4">
                                <input type="hidden" name="action" value="add_testimonial">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Client Name</label>
                                        <input type="text" name="name" required placeholder="e.g. Mr. Hari Bhai" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#8E44AD]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Role / Designation</label>
                                        <input type="text" name="role" required placeholder="e.g. Business Owner & Exporter" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#8E44AD]">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Location</label>
                                        <input type="text" name="location" required placeholder="e.g. Unjha, Gujarat" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#8E44AD]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Badge Type</label>
                                        <select name="badge_type" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#8E44AD]">
                                            <option value="Verified B2B">Verified B2B</option>
                                            <option value="Verified Client">Verified Client</option>
                                            <option value="Global Buyer">Global Buyer</option>
                                            <option value="Direct Importer">Direct Importer</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Rating (Stars)</label>
                                        <select name="rating" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#8E44AD]">
                                            <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                            <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                            <option value="3">⭐⭐⭐ (3/5)</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Avatar Image URL (or upload below)</label>
                                    <input type="text" name="avatar" placeholder="https://example.com/avatar.jpg" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#8E44AD]">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Upload Photo (Optional)</label>
                                    <input type="file" name="avatar_file" accept="image/*" class="w-full text-xs text-charcoal/60 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#8E44AD] file:text-white cursor-pointer">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Client Feedback / Comment</label>
                                    <textarea name="comment" rows="3" required placeholder="Write client experience testimonial..." class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#8E44AD] leading-relaxed"></textarea>
                                </div>

                                <div class="flex justify-end gap-3 pt-4 border-t border-charcoal/10">
                                    <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-gray-100 text-charcoal/70 font-bold rounded-xl text-xs hover:bg-gray-200 transition">Cancel</button>
                                    <button type="submit" style="background-color: #8E44AD; color: #FFFFFF;" class="px-6 py-2.5 text-white font-bold rounded-xl text-xs hover:opacity-90 transition shadow-md">+ Add Testimonial</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- EDIT TESTIMONIAL MODAL -->
                    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
                        <div class="bg-white rounded-3xl max-w-xl w-full p-6 md:p-8 space-y-6 shadow-2xl relative border border-charcoal/10" @click.away="showEditModal = false">
                            <div class="flex items-center justify-between border-b border-charcoal/10 pb-4">
                                <h3 class="text-xl font-bold font-serif text-charcoal">✏️ Edit Client Feedback / Testimonial</h3>
                                <button type="button" @click="showEditModal = false" class="text-charcoal/40 hover:text-charcoal font-bold text-xl">✕</button>
                            </div>

                            <form method="POST" action="admin.php?tab=testimonials" enctype="multipart/form-data" class="space-y-4">
                                <input type="hidden" name="action" value="edit_testimonial">
                                <input type="hidden" name="id" :value="editTesti.id">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Client Name</label>
                                        <input type="text" name="name" :value="editTesti.name" required class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#8E44AD]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Role / Designation</label>
                                        <input type="text" name="role" :value="editTesti.role" required class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#8E44AD]">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Location</label>
                                        <input type="text" name="location" :value="editTesti.location" required class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#8E44AD]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Badge Type</label>
                                        <select name="badge_type" :value="editTesti.badge_type" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#8E44AD]">
                                            <option value="Verified B2B">Verified B2B</option>
                                            <option value="Verified Client">Verified Client</option>
                                            <option value="Global Buyer">Global Buyer</option>
                                            <option value="Direct Importer">Direct Importer</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal/70 mb-1">Rating (Stars)</label>
                                        <select name="rating" :value="editTesti.rating" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#8E44AD]">
                                            <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                            <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                            <option value="3">⭐⭐⭐ (3/5)</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Avatar Image URL</label>
                                    <input type="text" name="avatar" :value="editTesti.avatar" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#8E44AD]">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Replace Avatar Photo</label>
                                    <input type="file" name="avatar_file" accept="image/*" class="w-full text-xs text-charcoal/60 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#8E44AD] file:text-white cursor-pointer">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-charcoal/70 mb-1">Client Feedback / Comment</label>
                                    <textarea name="comment" rows="3" required :value="editTesti.comment" class="w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#8E44AD] leading-relaxed"></textarea>
                                </div>

                                <div class="flex justify-end gap-3 pt-4 border-t border-charcoal/10">
                                    <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-100 text-charcoal/70 font-bold rounded-xl text-xs hover:bg-gray-200 transition">Cancel</button>
                                    <button type="submit" style="background-color: #8E44AD; color: #FFFFFF;" class="px-6 py-2.5 text-white font-bold rounded-xl text-xs hover:opacity-90 transition shadow-md">Update Testimonial</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                <?php elseif ($current_tab === 'hero'): ?>
                <div class="space-y-6">
                    
                    <!-- Alert Banners inside custom tab -->
                    <?php if (!empty($success_message)): ?>
                        <div class="bg-green-50 text-green-700 border border-green-200 text-sm px-6 py-4 rounded-2xl flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold"><?php echo htmlspecialchars($success_message); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="bg-red-50 text-red-700 border border-red-200 text-sm px-6 py-4 rounded-2xl flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold"><?php echo htmlspecialchars($error_message); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="bg-white rounded-3xl border border-[#1E2922]/10 shadow-lg overflow-hidden"
                         x-data="{ 
                             heroMode: '<?php echo htmlspecialchars($config['mode']); ?>',
                             images: <?php echo json_encode($config['slider_images'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
                             addImage() {
                                 this.images.push('');
                             },
                             removeImage(index) {
                                 if (this.images.length !== 1) {
                                     this.images.splice(index, 1);
                                 } else {
                                     alert('At least one slider image is required.');
                                 }
                             }
                         }">
                        
                        <div class="bg-[#1E2922] text-[#FAF3EC] px-6 py-6 md:px-8 flex justify-between items-center">
                            <div>
                                <h3 class="text-xl md:text-2xl font-bold font-serif">Modify Hero settings: <?php echo ucfirst($selected_page); ?> Page</h3>
                                <p class="text-xs text-[#FAF3EC]/80 font-light mt-1">Decoupled content fields synced live to MySQL database.</p>
                            </div>
                            <span class="px-3 py-1 bg-white/10 border border-white/20 text-xs rounded-full font-semibold uppercase tracking-wider text-[#E0A838]">
                                Target: <?php echo $selected_page; ?>
                            </span>
                        </div>

                        <!-- Settings Form (Multipart) -->
                        <form method="POST" action="admin.php?tab=hero&page=<?php echo urlencode($selected_page); ?>" enctype="multipart/form-data" class="p-6 md:p-8 space-y-8">
                            <input type="hidden" name="action" value="save_settings">
                            <input type="hidden" name="page_name" value="<?php echo htmlspecialchars($selected_page); ?>">

                            <!-- Text Customization Section -->
                            <div class="space-y-4">
                                <h4 class="text-base font-bold font-serif border-b border-[#1E2922]/10 pb-2">1. Custom Hero Text Overlay</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-1.5">
                                        <label for="hero_tag" class="text-xs font-bold uppercase tracking-wider text-charcoal/70">Top Tag / Badge text</label>
                                        <input type="text" id="hero_tag" name="hero_tag" required
                                               value="<?php echo htmlspecialchars($config['tag']); ?>"
                                               class="w-full px-4 py-3 rounded-xl border border-charcoal/20 focus:outline-none focus:border-[#D96E48] transition bg-white text-sm">
                                        <p class="text-[10px] text-charcoal/40">Small tag showing above heading (e.g. "Spice Capital Unjha, Gujarat").</p>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label for="hero_title" class="text-xs font-bold uppercase tracking-wider text-charcoal/70">Hero Main Title</label>
                                        <input type="text" id="hero_title" name="hero_title" required
                                               value="<?php echo htmlspecialchars($config['title']); ?>"
                                               class="w-full px-4 py-3 rounded-xl border border-charcoal/20 focus:outline-none focus:border-[#D96E48] transition bg-white text-sm">
                                        <p class="text-[10px] text-charcoal/40">Main heading rendered in bold serif typography.</p>
                                    </div>
                                </div>

                                <div class="space-y-1.5">
                                    <label for="hero_desc" class="text-xs font-bold uppercase tracking-wider text-charcoal/70 block">Hero Subtitle / Description text</label>
                                    <textarea id="hero_desc" name="hero_desc" rows="3" required
                                              class="w-full px-4 py-3 rounded-xl border border-charcoal/20 focus:outline-none focus:border-[#D96E48] transition bg-white text-sm leading-relaxed"><?php echo htmlspecialchars($config['desc']); ?></textarea>
                                    <p class="text-[10px] text-charcoal/40">Paragraph text explaining page context, direct farm-sourcing, or premium export features.</p>
                                </div>
                            </div>

                            <!-- Mode Selection Radio Buttons -->
                            <div class="space-y-3">
                                <h4 class="text-base font-bold font-serif border-b border-[#1E2922]/10 pb-2">2. Active Hero Display Mode</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label class="border-2 rounded-2xl p-4 flex items-center gap-3 cursor-pointer transition select-none <?php echo ($config['mode'] === 'slider') ? 'border-[#D96E48] bg-[#FAF3EC]/60 shadow-sm' : 'border-charcoal/10 hover:border-charcoal/30'; ?>">
                                        <input type="radio" name="hero_mode" value="slider" <?php echo ($config['mode'] === 'slider') ? 'checked' : ''; ?> class="w-5 h-5 accent-[#D96E48] cursor-pointer">
                                        <div>
                                            <span class="font-bold text-sm text-charcoal block">🖼️ Image Slider Mode</span>
                                            <span class="text-xs text-charcoal/60">Rotates premium product banner images</span>
                                        </div>
                                    </label>

                                    <label class="border-2 rounded-2xl p-4 flex items-center gap-3 cursor-pointer transition select-none <?php echo ($config['mode'] === 'video') ? 'border-[#E0A838] bg-[#FAF3EC]/60 shadow-sm' : 'border-charcoal/10 hover:border-charcoal/30'; ?>">
                                        <input type="radio" name="hero_mode" value="video" <?php echo ($config['mode'] === 'video') ? 'checked' : ''; ?> class="w-5 h-5 accent-[#E0A838] cursor-pointer">
                                        <div>
                                            <span class="font-bold text-sm text-charcoal block">🎬 Looping Video Mode</span>
                                            <span class="text-xs text-charcoal/60">Plays background atmospheric video loop</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- 3. Slider Upload & Banner Images Configuration -->
                            <div class="space-y-6 pt-4 border-t border-charcoal/10">
                                <div class="flex justify-between items-center border-b border-[#1E2922]/10 pb-2">
                                    <h4 class="text-base font-bold font-serif text-[#D96E48]">3. Image Slider Banner Uploads & URLs</h4>
                                    <button type="button" @click="addImage()" 
                                            class="bg-white border border-[#D96E48] text-[#D96E48] hover:bg-[#D96E48] hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1 focus:outline-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        + Add URL Row
                                    </button>
                                </div>

                                <div class="border border-charcoal/10 p-5 rounded-2xl bg-[#FAF3EC]/40 space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-charcoal/80 block">📤 Upload Local Image Slide(s)</label>
                                    <input type="file" name="slider_uploads[]" accept="image/*" multiple
                                           class="w-full text-xs text-charcoal/70 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#D96E48] file:text-white hover:file:bg-opacity-90 cursor-pointer">
                                    <p class="text-[10px] text-charcoal/50">Select one or more local banner images (.jpg, .png, .webp). They will upload automatically into uploads/ and append to the slide list below on Save.</p>
                                </div>

                                <div class="space-y-3">
                                    <template x-for="(imgUrl, index) in images" :key="index">
                                        <div class="flex gap-2 items-center">
                                            <div class="flex-grow relative">
                                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-charcoal/40" x-text="'Slide ' + (index + 1) + ':'"></span>
                                                <input type="text" name="slider_images[]" required
                                                       x-model="images[index]"
                                                       placeholder="https://example.com/image.jpg"
                                                       class="w-full pl-20 pr-4 py-3 rounded-xl border border-charcoal/20 focus:outline-none focus:border-[#D96E48] transition bg-white text-xs font-mono">
                                            </div>
                                            <button type="button" @click="removeImage(index)" 
                                                    class="p-3 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-xl transition shrink-0" 
                                                    title="Remove image">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 4. Video Configuration -->
                            <div class="space-y-6 pt-4 border-t border-charcoal/10">
                                <h4 class="text-base font-bold font-serif border-b border-[#1E2922]/10 pb-2 text-[#E0A838]">4. Background Video Configuration</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                                    <div class="space-y-2 border border-charcoal/10 p-5 rounded-2xl bg-[#FAF3EC]/40">
                                        <label class="text-xs font-bold uppercase tracking-wider text-charcoal/80 block">🎬 Upload Local Video (.mp4)</label>
                                        <input type="file" name="video_upload" accept="video/mp4" 
                                               class="w-full text-xs text-charcoal/70 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#E0A838] file:text-[#1E2922] hover:file:bg-opacity-90 cursor-pointer">
                                        <p class="text-[10px] text-charcoal/50">Upload a local `.mp4` video. Replaces current background video.</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="hero_video_url" class="text-xs font-bold uppercase tracking-wider text-charcoal/80 block">🌐 Loop Video Web URL</label>
                                        <input type="text" id="hero_video_url" name="hero_video_url" 
                                               value="<?php echo htmlspecialchars($config['video_url']); ?>"
                                               placeholder="https://example.com/video.mp4"
                                               class="w-full px-4 py-3 rounded-xl border border-charcoal/20 focus:outline-none focus:border-[#E0A838] transition bg-white text-xs font-mono">
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Save actions -->
                            <div class="flex justify-end gap-4 border-t border-[#1E2922]/10 pt-6">
                                <a href="admin.php?tab=dashboard" class="px-6 py-3 bg-[#FAF3EC] border border-[#1E2922]/10 font-bold rounded-xl hover:bg-charcoal/5 transition">Cancel</a>
                                <button type="submit" 
                                        class="px-8 py-3 bg-[#D96E48] text-white font-bold rounded-xl shadow-md hover:bg-opacity-95 transform hover:-translate-y-0.5 duration-200 transition">
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
                <?php endif; ?>

            </main>
        </div>
    <?php endif; ?>

</body>
</html>
