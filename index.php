<?php
session_start();
include("config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Komsan — Bookstore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        html { overflow-y: scroll; }
        body { font-family: 'DM Sans', sans-serif; background: #faf8f5; color: #1a1a1a; }
        h1, h2, h3, .serif { font-family: 'Playfair Display', serif; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        #leftMenu { position: fixed; top: 0; left: -280px; width: 280px; height: 100vh; background: #1a1a1a; z-index: 9999; transition: left 0.3s ease; }
        #leftMenu.open { left: 0; }
        #overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 9998; }
        #overlay.open { display: block; }
        .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; }
        .modal-backdrop.open { display: flex; }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,0.1); }
        .nav-btn { font-family: 'DM Sans', sans-serif; font-weight: 400; letter-spacing: 0.05em; text-transform: uppercase; font-size: 0.75rem; color: #555; transition: color 0.2s; }
        .nav-btn:hover { color: #1a1a1a; }
        .img-fixed { width: 100%; height: 220px; object-fit: cover; }
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
        <nav class="p-6 space-y-1">
            <button onclick="showTab('home'); closeMenu()" class="block w-full text-left text-gray-300 hover:text-white py-2 nav-btn">Home</button>
            <button onclick="showTab('shop'); closeMenu()" class="block w-full text-left text-gray-300 hover:text-white py-2 nav-btn">Shop</button>
            <button onclick="showTab('about'); closeMenu()" class="block w-full text-left text-gray-300 hover:text-white py-2 nav-btn">About</button>
            <button onclick="showTab('services'); closeMenu()" class="block w-full text-left text-gray-300 hover:text-white py-2 nav-btn">Services</button>
        </nav>
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
                <button onclick="showTab('about')" class="nav-btn">About</button>
                <button onclick="showTab('services')" class="nav-btn">Services</button>
            </nav>
            <div class="flex items-center gap-3">
                <button onclick="showTab('login')" class="nav-btn border border-gray-300 rounded px-4 py-1.5 hover:border-gray-700 transition">Login</button>
                <button onclick="showTab('register')" class="text-xs uppercase tracking-widest bg-gray-900 text-white px-4 py-2 rounded hover:bg-gray-700 transition">Register</button>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6">

        <!-- HOME -->
        <div id="home" class="tab-content active py-16">
            <div class="flex flex-col items-center text-center mb-16">
                <p class="text-xs uppercase tracking-widest text-gray-400 mb-3">Welcome to Komsan</p>
                <h1 class="text-5xl md:text-6xl font-black leading-tight mb-5">Take what you want.</h1>
                <p class="text-gray-500 text-lg mb-8">With so many books waiting for you — explore, discover, and read.</p>
                <button onclick="showTab('shop')" class="bg-gray-900 text-white px-8 py-3 rounded text-sm uppercase tracking-wider hover:bg-gray-700 transition">Browse Books</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
                <div class="bg-white rounded-2xl p-7 border border-gray-100 shadow-sm text-center">
                    <div class="text-3xl mb-4">📦</div>
                    <h3 class="serif font-bold text-lg mb-2">Instant Download</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Get your book the moment you pay — no waiting, no shipping, no hassle.</p>
                </div>
                <div class="bg-white rounded-2xl p-7 border border-gray-100 shadow-sm text-center">
                    <div class="text-3xl mb-4">📚</div>
                    <h3 class="serif font-bold text-lg mb-2">Wide Selection</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">From fiction to finance, we carry titles across every genre you love.</p>
                </div>
                <div class="bg-white rounded-2xl p-7 border border-gray-100 shadow-sm text-center">
                    <div class="text-3xl mb-4">🌍</div>
                    <h3 class="serif font-bold text-lg mb-2">Read Anywhere</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Your library is always with you — on any device, anywhere in the world.</p>
                </div>
            </div>
            <div class="bg-gray-900 text-white rounded-2xl px-10 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="serif text-2xl font-bold mb-1">Ready to start reading?</h3>
                    <p class="text-gray-400 text-sm">Browse our full collection and find your next favorite book.</p>
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
                            <button onclick="showTab('login')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Login to Buy</button>
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
                            <button onclick="showTab('login')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Login to Buy</button>
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
                            <button onclick="showTab('login')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Login to Buy</button>
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
                            <button onclick="showTab('login')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Login to Buy</button>
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
                            <button onclick="showTab('login')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Login to Buy</button>
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
                            <button onclick="showTab('login')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Login to Buy</button>
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
                            <button onclick="showTab('login')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Login to Buy</button>
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
                            <button onclick="showTab('login')" class="bg-gray-900 text-white text-xs px-3 py-2 rounded hover:bg-gray-700 transition">Login to Buy</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="noResults" class="hidden text-center py-16 text-gray-400 text-sm">No books found matching your search.</div>
        </div>

        <!-- ABOUT -->
        <div id="about" class="tab-content py-20">
            <div class="mb-12 flex items-end justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest text-gray-400 mb-3">Who we are</p>
                    <h2 class="serif text-5xl font-bold leading-tight">About Komsan</h2>
                </div>
                <button onclick="showTab('home')" class="shrink-0 ml-8 bg-gray-900 text-white px-8 py-3 rounded text-sm uppercase tracking-wider hover:bg-gray-700 transition">← Back</button>
            </div>
            <div class="bg-gray-900 text-white rounded-2xl px-10 py-12 mb-8">
                <p class="serif text-2xl md:text-3xl leading-relaxed italic text-gray-100">"A good book can open worlds, spark ideas, and change lives."</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100">
                    <div class="text-2xl mb-4">📖</div>
                    <h3 class="serif font-bold text-lg mb-2">Our Story</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Komsan was born from a love of reading. We bring stories, knowledge, and inspiration to your fingertips — anytime, anywhere.</p>
                </div>
                <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100">
                    <div class="text-2xl mb-4">🎯</div>
                    <h3 class="serif font-bold text-lg mb-2">Our Mission</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Make reading more accessible, affordable, and enjoyable for everyone — from passionate readers to curious first-timers.</p>
                </div>
                <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100">
                    <div class="text-2xl mb-4">🌟</div>
                    <h3 class="serif font-bold text-lg mb-2">Our Collection</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">From timeless classics to the latest bestsellers — fiction, non-fiction, self-improvement, education, and more, updated regularly.</p>
                </div>
            </div>
            <div class="bg-amber-50 border border-amber-100 rounded-2xl px-10 py-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <p class="serif text-xl text-amber-900 italic">It's not just about selling books — it's about sharing the joy of reading.</p>
                <button onclick="showTab('register')" class="shrink-0 bg-amber-900 text-white px-8 py-3 rounded text-sm uppercase tracking-wider hover:bg-amber-800 transition">Join Now →</button>
            </div>
        </div>

        <!-- SERVICES -->
        <div id="services" class="tab-content py-20">
            <div class="mb-12 flex items-end justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest text-gray-400 mb-3">What we offer</p>
                    <h2 class="serif text-5xl font-bold">Our Services</h2>
                    <p class="text-gray-500 mt-3 max-w-xl">At Komsan, we're more than just a place to buy books — we're here to bring stories, knowledge, and imagination right to your fingertips.</p>
                </div>
                <button onclick="showTab('home')" class="shrink-0 ml-8 bg-gray-900 text-white px-8 py-3 rounded text-sm uppercase tracking-wider hover:bg-gray-700 transition">← Back</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border border-gray-100 rounded-xl p-7 shadow-sm">
                    <div class="text-2xl mb-3">📚</div>
                    <h3 class="serif font-bold text-xl mb-2">Digital Bookstore</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Browse a vast library of eBooks across genres — from thrilling mysteries to empowering self-help guides. Every purchase is instantly available for download.</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-7 shadow-sm">
                    <div class="text-2xl mb-3">✨</div>
                    <h3 class="serif font-bold text-xl mb-2">Personalized Recommendations</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Our smart suggestion system helps you discover your next favorite read based on your interests, reading history, and trending titles.</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-7 shadow-sm">
                    <div class="text-2xl mb-3">☁️</div>
                    <h3 class="serif font-bold text-xl mb-2">Instant Access & Cloud Library</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">No waiting, no shipping — your books are available instantly in your personal cloud library, ready to read on any device.</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-7 shadow-sm">
                    <div class="text-2xl mb-3">🎁</div>
                    <h3 class="serif font-bold text-xl mb-2">Gift a Book</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Send a story to a friend, family member, or colleague with our digital gift service — complete with a personal message.</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-7 shadow-sm md:col-span-2">
                    <div class="text-2xl mb-3">🌍</div>
                    <h3 class="serif font-bold text-xl mb-2">Global Access</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Wherever you are in the world, our store is open 24/7. Download your books anytime, anywhere.</p>
                </div>
            </div>
        </div>

        <!-- LOGIN -->
        <div id="login" class="tab-content py-20">
            <div class="max-w-md mx-auto">
                <div class="text-center mb-8">
                    <h2 class="serif text-4xl font-bold mb-2">Welcome Back</h2>
                    <p class="text-gray-500">Log in to your account</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-8">
                    <?php if (isset($_SESSION["login_error"])): ?>
                        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
                            <?= $_SESSION["login_error"]; unset($_SESSION["login_error"]); ?>
                        </div>
                    <?php endif; ?>
                    <form action="login.php" method="POST">
                        <div class="mb-4">
                            <input type="text" name="Username" placeholder="Username" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-gray-500 transition" required />
                        </div>
                        <div class="mb-4">
                            <input type="password" name="Password" placeholder="Password" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-gray-500 transition" required />
                        </div>
                        <div class="flex justify-between items-center mb-6 text-sm">
                            <label class="flex items-center gap-2 text-gray-500 cursor-pointer">
                                <input type="checkbox" name="keepLoggedIn" class="rounded" /> Keep me logged in
                            </label>
                        </div>
                        <div class="flex gap-3 mb-4">
                            <button type="submit" class="flex-1 bg-gray-900 text-white py-3 rounded-lg text-sm hover:bg-gray-700 transition">Log In</button>
                            <button type="reset" class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-lg text-sm hover:border-gray-400 transition">Cancel</button>
                        </div>
                        <p class="text-center text-sm text-gray-400">Don't have an account? <button type="button" onclick="showTab('register')" class="text-gray-700 hover:underline">Register</button></p>
                    </form>
                </div>
            </div>
        </div>

        <!-- REGISTER -->
        <div id="register" class="tab-content py-20">
            <div class="max-w-md mx-auto">
                <div class="text-center mb-8">
                    <h2 class="serif text-4xl font-bold mb-2">Create Account</h2>
                    <p class="text-gray-500">Fill in your details to get started</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-8">
                    <?php if (isset($_SESSION["register_error"])): ?>
                        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
                            <?= $_SESSION["register_error"]; unset($_SESSION["register_error"]); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION["register_success"])): ?>
                        <div class="bg-green-50 border border-green-200 text-green-600 text-sm rounded-lg px-4 py-3 mb-4">
                            <?= $_SESSION["register_success"]; unset($_SESSION["register_success"]); ?>
                        </div>
                    <?php endif; ?>
                    <form action="register.php" method="POST">
                        <div class="space-y-4 mb-6">
                            <input type="text" name="regUsername" placeholder="Username" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-gray-500 transition" required />
                            <input type="password" name="regPassword" placeholder="Password" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-gray-500 transition" required />
                            <input type="password" name="regConfirmPassword" placeholder="Confirm Password" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-gray-500 transition" required />
                        </div>
                        <div class="flex gap-3 mb-4">
                            <button type="submit" class="flex-1 bg-gray-900 text-white py-3 rounded-lg text-sm hover:bg-gray-700 transition">Register</button>
                            <button type="reset" class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-lg text-sm hover:border-gray-400 transition">Cancel</button>
                        </div>
                        <p class="text-center text-sm text-gray-400">Already have an account? <button type="button" onclick="showTab('login')" class="text-gray-700 hover:underline">Login</button></p>
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

    <script>
        function showTab(id) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            const el = document.getElementById(id);
            if (el) el.classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        function openMenu() {
            document.getElementById('leftMenu').classList.add('open');
            document.getElementById('overlay').classList.add('open');
        }
        function closeMenu() {
            document.getElementById('leftMenu').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
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
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        if (tab) showTab(tab);
    </script>
</body>
</html>