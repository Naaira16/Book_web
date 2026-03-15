<?php
session_start();
include("config.php");

if (!isset($_SESSION["username"])) {
    header("Location: index.php?tab=login");
    exit();
}

$profile_success = "";
$profile_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "update_profile") {
    $new_username = trim($_POST["new_username"]);
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $current_id = $_SESSION["user_id"];

    if ($new_username !== $_SESSION["username"]) {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($check, "si", $new_username, $current_id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        if (mysqli_stmt_num_rows($check) > 0) {
            $profile_error = "Username already taken.";
        }
    }

    if (!$profile_error) {
        if (!empty($new_password)) {
            if ($new_password !== $confirm_password) {
                $profile_error = "Passwords do not match.";
            } else {
                $hashed = md5($new_password);
                $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, password = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "ssi", $new_username, $hashed, $current_id);
                mysqli_stmt_execute($stmt);
            }
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET username = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $new_username, $current_id);
            mysqli_stmt_execute($stmt);
        }
        if (!$profile_error) {
            $_SESSION["username"] = $new_username;
            $profile_success = "Profile updated successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Komsan — Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        html {
            overflow-y: scroll;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #faf8f5;
            color: #1a1a1a;
        }

        h1,
        h2,
        h3,
        .serif {
            font-family: 'Playfair Display', serif;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        #leftMenu {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            height: 100vh;
            background: #1a1a1a;
            z-index: 9999;
            transition: left 0.3s ease;
        }

        #leftMenu.open {
            left: 0;
        }

        #overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 9998;
        }

        #overlay.open {
            display: block;
        }

        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal-backdrop.open {
            display: flex;
        }

        .card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
        }

        .nav-btn {
            font-family: 'DM Sans', sans-serif;
            font-weight: 400;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-size: 0.75rem;
            color: #555;
            transition: color 0.2s;
        }

        .nav-btn:hover {
            color: #1a1a1a;
        }

        .img-fixed {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
    </style>
</head>

<body>

    <div id="overlay" onclick="closeMenu()"></div>

    <!-- Left Side Menu -->
    <div id="leftMenu">
        <div class="p-6 border-b border-gray-700 flex justify-between items-center">
            <span class="serif text-white text-xl font-bold">Menu</span>
            <button onclick="closeMenu()" class="text-gray-400 hover:text-white text-xl">✕</button>
        </div>
        <div class="p-6 border-b border-gray-800">
            <p class="text-gray-400 text-xs uppercase tracking-widest mb-1">Logged in as</p>
            <p class="text-white font-bold"><?= htmlspecialchars($_SESSION["username"]) ?></p>
        </div>
        <nav class="p-6 space-y-1">
            <button onclick="showTab('home'); closeMenu()" class="block w-full text-left text-gray-300 hover:text-white py-2 nav-btn">Home</button>
            <button onclick="showTab('shop'); closeMenu()" class="block w-full text-left text-gray-300 hover:text-white py-2 nav-btn">Shop</button>
            <button onclick="showTab('library'); closeMenu()" class="block w-full text-left text-gray-300 hover:text-white py-2 nav-btn">My Library</button>
            <button onclick="showTab('profile'); closeMenu()" class="block w-full text-left text-gray-300 hover:text-white py-2 nav-btn">Profile</button>
        </nav>
        <div class="p-6 border-t border-gray-800">
            <a href="logout.php" class="block text-red-400 hover:text-red-300 py-2 nav-btn">Sign Out</a>
        </div>
    </div>

    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button onclick="openMenu()" class="text-gray-600 hover:text-black p-1">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 6h18M3 12h18M3 18h18" />
                    </svg>
                </button>
                <button onclick="showTab('home')" class="serif font-bold text-xl tracking-tight">Komsan</button>
            </div>
            <nav class="hidden md:flex items-center gap-8">
                <button onclick="showTab('home')" class="nav-btn">Home</button>
                <button onclick="showTab('shop')" class="nav-btn">Shop</button>
                <button onclick="showTab('library')" class="nav-btn">My Library</button>
                <button onclick="showTab('profile')" class="nav-btn">Profile</button>
            </nav>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 hidden md:block">Hi, <strong class="text-gray-900"><?= htmlspecialchars($_SESSION["username"]) ?></strong></span>
                <a href="logout.php" class="text-xs uppercase tracking-widest bg-gray-900 text-white px-4 py-2 rounded hover:bg-gray-700 transition">Sign Out</a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6">

        <!-- HOME -->
        <div id="home" class="tab-content active py-16">
            <div class="flex flex-col items-center text-center mb-16">
                <p class="text-xs uppercase tracking-widest text-gray-400 mb-3">Welcome back</p>
                <h1 class="text-5xl md:text-6xl font-black leading-tight mb-5">Hello, <?= htmlspecialchars($_SESSION["username"]) ?>.</h1>
                <p class="text-gray-500 text-lg">What would you like to read today?</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
                <div class="bg-white rounded-2xl p-7 border border-gray-100 shadow-sm text-center cursor-pointer card-hover" onclick="showTab('library')">
                    <div class="text-3xl mb-4">📚</div>
                    <h3 class="serif font-bold text-lg mb-2">My Library</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">View all the books you've added to your collection.</p>
                </div>
                <div class="bg-white rounded-2xl p-7 border border-gray-100 shadow-sm text-center cursor-pointer card-hover" onclick="showTab('shop')">
                    <div class="text-3xl mb-4">🛒</div>
                    <h3 class="serif font-bold text-lg mb-2">Browse Shop</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Discover new titles and add them to your collection.</p>
                </div>
                <div class="bg-white rounded-2xl p-7 border border-gray-100 shadow-sm text-center cursor-pointer card-hover" onclick="showTab('profile')">
                    <div class="text-3xl mb-4">👤</div>
                    <h3 class="serif font-bold text-lg mb-2">My Profile</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Manage your account details and preferences.</p>
                </div>
            </div>
            <div class="bg-gray-900 text-white rounded-2xl px-10 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="serif text-2xl font-bold mb-1">Continue exploring.</h3>
                    <p class="text-gray-400 text-sm">New titles added every week — don't miss out.</p>
                </div>
                <button onclick="showTab('shop')" class="shrink-0 bg-white text-gray-900 px-8 py-3 rounded text-sm uppercase tracking-wider hover:bg-gray-100 transition">Shop Now →</button>
            </div>
        </div>

        <!-- SHOP -->
        <div id="shop" class="tab-content py-16">
            <div class="mb-10 flex items-end justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest text-gray-400 mb-2">Our collection</p>
                    <h2 class="serif text-4xl font-bold">Books for Sale</h2>
                </div>
                <button onclick="showTab('home')" class="shrink-0 ml-8 bg-gray-900 text-white px-8 py-3 rounded text-sm uppercase tracking-wider hover:bg-gray-700 transition">← Back</button>
            </div>
            <!-- Search -->
            <div class="mb-8">
                <input type="text" id="searchInput" onkeyup="filterBooks()" placeholder="Search books..." class="w-full md:w-96 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-gray-500 transition" />
            </div>
            <div id="bookGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <div class="book-card card-hover bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm" data-title="The Mindfulness Journal">
                    <img src="img/book1.jpg" class="img-fixed" />
                    <div class="p-5">
                        <h3 class="serif font-bold text-lg mb-2">The Mindfulness Journal</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4">A thoughtful guide to mindful living with daily prompts and reflections.</p>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900">$5.99</span>
                            <div class="flex gap-2">
                                <button onclick="openBuyModal('The Mindfulness Journal','img/book1.jpg','$5.99')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Buy Now</button>
                                <button onclick="addToLibrary('The Mindfulness Journal','img/book1.jpg','$5.99')" class="border border-gray-200 text-xs px-3 py-2 rounded hover:border-gray-400 transition">+ Library</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="book-card card-hover bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm" data-title="A Teaspoon Of Earth And Sea">
                    <img src="img/book2.jpg" class="img-fixed" />
                    <div class="p-5">
                        <h3 class="serif font-bold text-lg mb-2">A Teaspoon Of Earth And Sea</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4">An evocative tale of two sisters and their parallel lives across the globe.</p>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900">$6.99</span>
                            <div class="flex gap-2">
                                <button onclick="openBuyModal('A Teaspoon Of Earth And Sea','img/book2.jpg','$6.99')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Buy Now</button>
                                <button onclick="addToLibrary('A Teaspoon Of Earth And Sea','img/book2.jpg','$6.99')" class="border border-gray-200 text-xs px-3 py-2 rounded hover:border-gray-400 transition">+ Library</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="book-card card-hover bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm" data-title="Seven Endless Forest">
                    <img src="img/book3.jpg" class="img-fixed" />
                    <div class="p-5">
                        <h3 class="serif font-bold text-lg mb-2">Seven Endless Forest</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4">A sweeping fantasy epic through ancient forests and forgotten kingdoms.</p>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900">$5.99</span>
                            <div class="flex gap-2">
                                <button onclick="openBuyModal('Seven Endless Forest','img/book3.jpg','$5.99')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Buy Now</button>
                                <button onclick="addToLibrary('Seven Endless Forest','img/book3.jpg','$5.99')" class="border border-gray-200 text-xs px-3 py-2 rounded hover:border-gray-400 transition">+ Library</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="book-card card-hover bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm" data-title="The Road">
                    <img src="img/book4.jpg" class="img-fixed" />
                    <div class="p-5">
                        <h3 class="serif font-bold text-lg mb-2">The Road</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4">A stark, beautiful story of a father and son journeying through a desolate land.</p>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900">$4.99</span>
                            <div class="flex gap-2">
                                <button onclick="openBuyModal('The Road','img/book4.jpg','$4.99')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Buy Now</button>
                                <button onclick="addToLibrary('The Road','img/book4.jpg','$4.99')" class="border border-gray-200 text-xs px-3 py-2 rounded hover:border-gray-400 transition">+ Library</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="book-card card-hover bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm" data-title="The Little Prince">
                    <img src="img/book5.jpg" class="img-fixed" />
                    <div class="p-5">
                        <h3 class="serif font-bold text-lg mb-2">The Little Prince</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4">A timeless classic about love, loss, and the importance of staying young at heart.</p>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900">$5.99</span>
                            <div class="flex gap-2">
                                <button onclick="openBuyModal('The Little Prince','img/book5.jpg','$5.99')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Buy Now</button>
                                <button onclick="addToLibrary('The Little Prince','img/book5.jpg','$5.99')" class="border border-gray-200 text-xs px-3 py-2 rounded hover:border-gray-400 transition">+ Library</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="book-card card-hover bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm" data-title="The Psychology Of Money">
                    <img src="img/book6.jpg" class="img-fixed" />
                    <div class="p-5">
                        <h3 class="serif font-bold text-lg mb-2">The Psychology Of Money</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4">Timeless lessons on wealth, greed, and happiness through 19 compelling stories.</p>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900">$7.99</span>
                            <div class="flex gap-2">
                                <button onclick="openBuyModal('The Psychology Of Money','img/book6.jpg','$7.99')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Buy Now</button>
                                <button onclick="addToLibrary('The Psychology Of Money','img/book6.jpg','$7.99')" class="border border-gray-200 text-xs px-3 py-2 rounded hover:border-gray-400 transition">+ Library</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="book-card card-hover bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm" data-title="Intuition">
                    <img src="img/book7.jpg" class="img-fixed" />
                    <div class="p-5">
                        <h3 class="serif font-bold text-lg mb-2">Intuition</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4">Exploring the hidden power of gut feelings and instinctive decision-making.</p>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900">$5.99</span>
                            <div class="flex gap-2">
                                <button onclick="openBuyModal('Intuition','img/book7.jpg','$5.99')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Buy Now</button>
                                <button onclick="addToLibrary('Intuition','img/book7.jpg','$5.99')" class="border border-gray-200 text-xs px-3 py-2 rounded hover:border-gray-400 transition">+ Library</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="book-card card-hover bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm" data-title="Poetry">
                    <img src="img/book9.avif" class="img-fixed" />
                    <div class="p-5">
                        <h3 class="serif font-bold text-lg mb-2">Poetry</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4">A curated anthology of verses that stir the soul and sharpen the mind.</p>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900">$8.99</span>
                            <div class="flex gap-2">
                                <button onclick="openBuyModal('Poetry','img/book9.avif','$8.99')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Buy Now</button>
                                <button onclick="addToLibrary('Poetry','img/book9.avif','$8.99')" class="border border-gray-200 text-xs px-3 py-2 rounded hover:border-gray-400 transition">+ Library</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div id="noResults" class="hidden text-center py-16 text-gray-400 text-sm">No books found matching your search.</div>
        </div>

        <!-- MY LIBRARY -->
        <div id="library" class="tab-content py-20">
            <div class="mb-12 flex items-end justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest text-gray-400 mb-3">Your collection</p>
                    <h2 class="serif text-5xl font-bold leading-tight">My Library</h2>
                </div>
                <button onclick="showTab('home')" class="shrink-0 ml-8 bg-gray-900 text-white px-8 py-3 rounded text-sm uppercase tracking-wider hover:bg-gray-700 transition">← Back</button>
            </div>
            <div id="libraryEmpty" class="bg-white border border-gray-100 rounded-2xl shadow-sm px-10 py-20 flex flex-col items-center text-center">
                <div class="text-5xl mb-6">📭</div>
                <h3 class="serif font-bold text-2xl mb-3">Your library is empty</h3>
                <p class="text-gray-500 text-sm mb-8 max-w-sm">You haven't added any books yet. Head over to the shop and find something you love.</p>
                <button onclick="showTab('shop')" class="bg-gray-900 text-white px-8 py-3 rounded text-sm uppercase tracking-wider hover:bg-gray-700 transition">Browse Shop →</button>
            </div>
            <div id="libraryGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 hidden"></div>
        </div>

        <!-- PROFILE -->
        <div id="profile" class="tab-content py-20">
            <div class="mb-12 flex items-end justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest text-gray-400 mb-3">Your account</p>
                    <h2 class="serif text-5xl font-bold leading-tight">My Profile</h2>
                </div>
                <button onclick="showTab('home')" class="shrink-0 ml-8 bg-gray-900 text-white px-8 py-3 rounded text-sm uppercase tracking-wider hover:bg-gray-700 transition">← Back</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Avatar -->
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-8 flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full bg-gray-900 flex items-center justify-center text-white text-3xl font-black serif mb-4">
                        <?= strtoupper(substr($_SESSION["username"], 0, 1)) ?>
                    </div>
                    <h3 class="serif font-bold text-xl mb-1"><?= htmlspecialchars($_SESSION["username"]) ?></h3>
                    <p class="text-gray-400 text-sm mb-2">Member</p>
                    <p class="text-gray-400 text-xs mb-6">Joined <?= date("F Y") ?></p>
                    <a href="logout.php" class="w-full text-center border border-red-200 text-red-500 py-2 rounded-lg text-sm hover:bg-red-50 transition">Sign Out</a>
                </div>
                <!-- Edit form -->
                <div class="md:col-span-2 bg-white border border-gray-100 rounded-2xl shadow-sm p-8">
                    <h3 class="serif font-bold text-xl mb-6">Edit Account</h3>
                    <?php if ($profile_success): ?>
                        <div class="bg-green-50 border border-green-200 text-green-600 text-sm rounded-lg px-4 py-3 mb-6">
                            <?= $profile_success ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($profile_error): ?>
                        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
                            <?= $profile_error ?>
                        </div>
                    <?php endif; ?>
                    <form action="dashboard.php?tab=profile" method="POST">
                        <input type="hidden" name="action" value="update_profile" />
                        <div class="space-y-4 mb-6">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-widest text-gray-400">Username</label>
                                <input type="text" name="new_username" value="<?= htmlspecialchars($_SESSION["username"]) ?>"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-gray-500 transition" required />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-widest text-gray-400">New Password <span class="normal-case text-gray-300">(leave blank to keep current)</span></label>
                                <input type="password" name="new_password" placeholder="New password"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-gray-500 transition" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs uppercase tracking-widest text-gray-400">Confirm New Password</label>
                                <input type="password" name="confirm_password" placeholder="Confirm new password"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-gray-500 transition" />
                            </div>
                        </div>
                        <button type="submit" class="bg-gray-900 text-white px-8 py-3 rounded-lg text-sm hover:bg-gray-700 transition">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 mt-16 py-12 bg-white">
        <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row justify-between gap-8">
            <div>
                <h3 class="serif font-bold text-xl mb-1">Komsan</h3>
                <p class="text-gray-500 text-sm mb-4">Bringing stories to your fingertips.</p>
                <div class="flex gap-4 text-gray-400 text-lg">
                    <a href="https://web.facebook.com/na.1606y" class="hover:text-gray-700 transition"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com/na.1606y/" class="hover:text-gray-700 transition"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.youtube.com/@savnabuntheng2464" class="hover:text-gray-700 transition"><i class="fab fa-youtube"></i></a>
                    <a href="https://www.tiktok.com/@na_1606y" class="hover:text-gray-700 transition"><i class="fab fa-tiktok"></i></a>
                    <a href="http://www.t.me/na_1606y" class="hover:text-gray-700 transition"><i class="fab fa-telegram"></i></a>
                </div>
            </div>
            <div>
                <h4 class="font-medium mb-2 text-sm uppercase tracking-wider text-gray-400">Contact</h4>
                <p class="text-gray-600 text-sm">savnabuntheng@gmail.com</p>
                <p class="text-gray-600 text-sm">0719366411</p>
            </div>
        </div>
        <div class="max-w-6xl mx-auto px-6 mt-8 pt-6 border-t border-gray-100">
            <p class="text-gray-400 text-xs">© 2025 Komsan. All rights reserved.</p>
        </div>
    </footer>

    <!-- Buy Modal -->
    <div id="buyModal" class="modal-backdrop" onclick="closeModal('buyModal')">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-8" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="serif font-bold text-xl">Confirm Purchase</h3>
                <button onclick="closeModal('buyModal')" class="text-gray-400 hover:text-gray-700">✕</button>
            </div>
            <p class="text-gray-500 text-sm mb-1">You are buying:</p>
            <p id="buyModalTitle" class="serif font-bold text-lg mb-1"></p>
            <p id="buyModalPrice" class="text-gray-900 font-bold mb-4"></p>
            <p class="text-gray-400 text-sm mb-6">After purchase, this book will be added to your library automatically.</p>
            <div class="flex gap-3">
                <button onclick="closeModal('buyModal')" class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-lg text-sm hover:border-gray-400 transition">Cancel</button>
                <button onclick="confirmBuy()" class="flex-1 bg-gray-900 text-white py-3 rounded-lg text-sm hover:bg-gray-700 transition">Confirm & Buy</button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-6 right-6 bg-gray-900 text-white text-sm px-5 py-3 rounded-xl shadow-lg z-50 hidden">
        ✓ <span id="toastMsg"></span>
    </div>

    <script>
        let library = [];
        let pendingBook = null;

        function openBuyModal(title, img, price) {
            pendingBook = {
                title,
                img,
                price
            };
            document.getElementById('buyModalTitle').textContent = title;
            document.getElementById('buyModalPrice').textContent = price;
            openModal('buyModal');
        }

        function confirmBuy() {
            if (!pendingBook) return;
            closeModal('buyModal');
            addToLibrary(pendingBook.title, pendingBook.img, pendingBook.price, true);
            pendingBook = null;
        }

        function addToLibrary(title, img, price, fromBuy = false) {
            if (library.find(b => b.title === title)) {
                showToast('"' + title + '" is already in your library!');
                return;
            }
            library.push({
                title,
                img,
                price
            });
            renderLibrary();
            showToast(fromBuy ? 'Purchased! "' + title + '" added to your library.' : '"' + title + '" added to your library!');
        }

        function renderLibrary() {
            const grid = document.getElementById('libraryGrid');
            const empty = document.getElementById('libraryEmpty');
            if (library.length === 0) {
                empty.classList.remove('hidden');
                grid.classList.add('hidden');
                return;
            }
            empty.classList.add('hidden');
            grid.classList.remove('hidden');
            grid.innerHTML = library.map(book => `
                <div class="card-hover bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm">
                    <img src="${book.img}" class="img-fixed" />
                    <div class="p-5">
                        <h3 class="serif font-bold text-lg mb-2">${book.title}</h3>
                        <div class="flex items-center justify-between mt-4">
                            <span class="font-bold text-gray-900">${book.price}</span>
                            <button onclick="removeFromLibrary('${book.title}')" class="border border-red-200 text-red-400 text-xs px-3 py-2 rounded hover:bg-red-50 transition">Remove</button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function removeFromLibrary(title) {
            library = library.filter(b => b.title !== title);
            renderLibrary();
            showToast('"' + title + '" removed from your library.');
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').textContent = msg;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }

        function filterBooks() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.book-card');
            let visible = 0;
            cards.forEach(card => {
                const title = card.getAttribute('data-title').toLowerCase();
                if (title.includes(query)) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            document.getElementById('noResults').classList.toggle('hidden', visible > 0);
        }

        function showTab(id) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            const el = document.getElementById(id);
            if (el) el.classList.add('active');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function openMenu() {
            document.getElementById('leftMenu').classList.add('open');
            document.getElementById('overlay').classList.add('open');
        }

        function closeMenu() {
            document.getElementById('leftMenu').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
        }

        function openModal(id) {
            document.getElementById(id).classList.add('open');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }

        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        if (tab) showTab(tab);
    </script>
</body>

</html>