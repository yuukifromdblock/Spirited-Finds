<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include DB configuration
include 'config/db.php';

// Safe Helper Function: Awtomatikong inaayos ang image path gamit ang basename()
if (!function_exists('get_image_path')) {
    function get_image_path(?string $image_name = '') {
        if (empty($image_name)) {
            return 'assets/images/logo.png';
        }
        $filename = basename(trim($image_name));
        return 'assets/images/' . $filename;
    }
}

// Include Header
include 'includes/header.php';

// Processing for Contact Us Form (Safeguarded with Prepared Statements)
$contact_msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send_contact'])) {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!empty($name) && !empty($email) && !empty($message)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $message);
            
            if ($stmt->execute()) {
                $contact_msg = "<div class='alert alert-success rounded-lg'>Thank you! Your message has been sent successfully.</div>";
            } else {
                $contact_msg = "<div class='alert alert-danger rounded-lg'>Error sending message. Please try again later.</div>";
            }
            $stmt->close();
        } else {
            $contact_msg = "<div class='alert alert-warning rounded-lg'>Please enter a valid email address.</div>";
        }
    } else {
        $contact_msg = "<div class='alert alert-warning rounded-lg'>Please fill in all required fields.</div>";
    }
}
?>

<!-- HERO SECTION -->
<section id="home" class="home">
    <div class="content" data-aos="fade-up">
        <h3>Welcome to<br>Spirited Finds</h3>
        <p>Discover magical merchandise inspired by your favorite Studio Ghibli films.</p>
        <a href="#product-cards" class="btn-ghibli-primary">
            <span>Explore Magic</span>
            <i class="fas fa-sparkles"></i>
        </a>
    </div>
</section>

<!-- FEATURED FINDS (TOP 3 CATEGORIES) -->
<section id="box" class="container" data-aos="fade-up">
    <h2 class="section-title">FEATURED FINDS</h2>
    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card category-card">
                <img src="<?php echo get_image_path('My Neighbor Totoro Slippers.png'); ?>" alt="Totoro Slippers" class="category-img">
                <div class="overlay category-overlay">
                    <h3>My Neighbor Totoro Slippers</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card category-card">
                <img src="<?php echo get_image_path('Soot Sprites Phone Case.png'); ?>" alt="Soot Sprites Phone Case" class="category-img">
                <div class="overlay category-overlay">
                    <h3>Soot Sprites Phone Case</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card category-card">
                <img src="<?php echo get_image_path('Bookmarks.png'); ?>" alt="Bookmarks" class="category-img">
                <div class="overlay category-overlay">
                    <h3>Bookmarks Collection</h3>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- BANNER -->
<div class="container" data-aos="fade-up">
    <div class="banner">
        <div class="content">
            <h3>Your Favorite Ghibli Treasures Await</h3>
            <p>From cozy Totoro Plushies to Spirited Away Apparel – Find Your Magic Here.</p>
            <a href="#product-cards" class="btn-ghibli-primary">Shop Collection</a>
        </div>
    </div>
</div>

<!-- MYSTICAL PRODUCT SHOWCASE -->
<section id="product-cards" class="container my-5" data-aos="fade-up">
    <h2 class="section-title">MYSTICAL PRODUCT SHOWCASE</h2>
    <div class="row mt-4 justify-content-center">
        <?php
        $showcase_sql = "SELECT * FROM products WHERE is_featured = 1";
        $showcase_res = $conn->query($showcase_sql);

        if ($showcase_res && $showcase_res->num_rows > 0) {
            while ($prod = $showcase_res->fetch_assoc()) {
                ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="product-card">
                        <div class="product-img-wrapper">
                            <!-- Helper function resolves path dynamically -->
                            <img src="<?php echo get_image_path($prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['title']); ?>">
                            
                            <!-- Floating Whimsical Actions -->
                            <div class="product-actions">
                                <a href="product_details.php?id=<?php echo $prod['product_id']; ?>" class="action-btn" title="Quick View">
                                    <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                </a>
                                <button type="button" class="action-btn" title="Add to Wishlist">
                                    <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="product-card-body">
                            <h3><?php echo htmlspecialchars($prod['title']); ?></h3>
                            <div class="star-rating">
                                <?php
                                $rating = (int)($prod['rating'] ?? 5);
                                for ($i = 1; $i <= 5; $i++) {
                                    echo ($i <= $rating) ? '<i class="bx bxs-star"></i>' : '<i class="bx bx-star"></i>';
                                }
                                ?>
                            </div>
                            <p class="product-desc"><?php echo htmlspecialchars($prod['description'] ?? ''); ?></p>
                            
                            <div class="product-footer">
                                <span class="price-tag">₱<?php echo number_format($prod['price'], 2); ?></span>
                                <a href="product_details.php?id=<?php echo $prod['product_id']; ?>" class="btn-add-cart-sm">
                                    <i class="fas fa-shopping-cart"></i> Add Cart
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='col-12 text-center'><p class='text-muted'>No featured products available at the moment.</p></div>";
        }
        ?>
    </div>
</section>

<!-- SPECIAL EDITION FINDS -->
<section class="container my-5" data-aos="fade-up">
    <h2 class="section-title">SPECIAL EDITION FINDS</h2>
    <div class="row mt-4 justify-content-center">
        <?php
        $special_sql = "SELECT * FROM products WHERE is_special = 1";
        $special_res = $conn->query($special_sql);

        if ($special_res && $special_res->num_rows > 0) {
            while ($sprod = $special_res->fetch_assoc()) {
                ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="product-card">
                        <div class="product-img-wrapper">
                            <img src="<?php echo get_image_path($sprod['image']); ?>" alt="<?php echo htmlspecialchars($sprod['title']); ?>">
                            
                            <div class="product-actions">
                                <a href="product_details.php?id=<?php echo $sprod['product_id']; ?>" class="action-btn" title="Quick View">
                                    <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                </a>
                            </div>
                        </div>

                        <div class="product-card-body">
                            <h3><?php echo htmlspecialchars($sprod['title']); ?></h3>
                            <div class="star-rating">
                                <?php
                                $srating = (int)($sprod['rating'] ?? 5);
                                for ($i = 1; $i <= 5; $i++) {
                                    echo ($i <= $srating) ? '<i class="bx bxs-star"></i>' : '<i class="bx bx-star"></i>';
                                }
                                ?>
                            </div>
                            <p class="product-desc"><?php echo htmlspecialchars($sprod['description'] ?? ''); ?></p>
                            
                            <div class="product-footer">
                                <span class="price-tag">₱<?php echo number_format($sprod['price'], 2); ?></span>
                                <a href="product_details.php?id=<?php echo $sprod['product_id']; ?>" class="btn-add-cart-sm">
                                    <svg viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.83-3.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4h-.01l-1.1 2-2.76 5H8.53l-.13-.27L6.16 6l-.95-2-.94-2H1v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.13 0-.25-.11-.25-.25z"/></svg>
                                    Add Cart
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='col-12 text-center'><p class='text-muted'>No special edition items found.</p></div>";
        }
        ?>
    </div>
</section>

<!-- ABOUT US -->
<section id="about" class="container my-5" data-aos="fade-up">
    <h2 class="section-title">ABOUT US</h2>
    <div class="row align-items-center">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <img src="<?php echo get_image_path('about.jpg'); ?>" alt="About Spirited Finds" class="img-fluid">
            </div>
        </div>
        <div class="col-md-6">
            <p>Welcome to <strong>Spirited Finds</strong>, where the magical worlds of Studio Ghibli come to life! As passionate fans, we curated this shop to bring the warmth, nostalgia, and charm of My Neighbor Totoro, Spirited Away, and Howl's Moving Castle right into your daily routine.</p>
            <p>From plushies and collectibles to cozy apparel, every item in our collection is carefully selected to capture the artistic heart of Ghibli storytelling.</p>
        </div>
    </div>
</section>

<!-- CONTACT US -->
<section id="contact" class="container my-5" data-aos="fade-up">
    <h2 class="section-title">CONTACT US</h2>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php echo $contact_msg; ?>
            <form action="index.php#contact" method="POST">
                <div class="form-row row">
                    <div class="col-md-4 mb-3">
                        <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="text" class="form-control" name="phone" placeholder="Phone Number">
                    </div>
                </div>
                <div class="form-group mb-3">
                    <textarea class="form-control" rows="5" name="message" placeholder="Write your message..." required></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" name="send_contact" class="btn-ghibli-primary border-0">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- OFFERS / FEATURES (4 CARDS CENTERED) -->
<section id="offer" class="container my-5" data-aos="fade-up">
    <div class="row justify-content-center">
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="offer-card">
                <svg viewBox="0 0 24 24"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                <h5>Free Shipping</h5>
                <small class="text-muted">For orders over ₱1,000</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="offer-card">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                <h5>Fast Delivery</h5>
                <small class="text-muted">Safe and reliable shipping</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="offer-card">
                <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-5.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
                <h5>100% Authentic</h5>
                <small class="text-muted">Guaranteed quality items</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="offer-card">
                <svg viewBox="0 0 24 24"><path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.83 20 14.02 20 12c0-4.42-3.58-8-8-8zm-6 7c0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 9.17 4 10.98 4 12c0 4.42 3.58 8 8 8v4l5-5-5-5v3c-3.31 0-6-2.69-6-6z"/></svg>
                <h5>Easy Returns</h5>
                <small class="text-muted">Friendly customer support</small>
            </div>
        </div>
    </div>
</section>

<?php
// Include Footer
include 'includes/footer.php';
?>