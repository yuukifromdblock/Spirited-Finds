<?php
session_start();
include 'config/db.php';

function getImagePath(?string $imagePath): string {
    if (empty($imagePath)) return 'assets/images/logo.png';
    $filename = basename($imagePath);
    return 'assets/images/' . $filename;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['id']);

$query = "SELECT p.*, c.category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          WHERE p.product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Product not found!'); window.location.href='index.php';</script>";
    exit();
}

$product = $result->fetch_assoc();

$related_query = "SELECT * FROM products WHERE category_id = ? AND product_id != ? LIMIT 4";
$related_stmt = $conn->prepare($related_query);
$related_stmt->bind_param("ii", $product['category_id'], $product_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spirited Finds - <?php echo htmlspecialchars($product['title']); ?></title>
    <link rel="shortcut icon" type="image" href="assets/images/logo.png">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    
    <!-- Global Ghibli Style Sheet -->
    <link rel="stylesheet" href="assets/global/style.css">
    
    <!-- Icons & Fonts -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-md" id="navbar">
    <a class="navbar-brand" href="index.php" id="logo">
        <img src="assets/images/logo.png" alt="Logo" class="ghibli-logo-img" width="45px"> Spirited Finds
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span><img src="assets/images/menu.png" alt="" width="30px"></span>
    </button>

    <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" id="navbardrop" data-toggle="dropdown">
                    Category
                </a>
                <div class="dropdown-menu ghibli-dropdown-menu">
                    <a href="category.php?id=1" class="dropdown-item">Enchanted Pages</a>
                    <a href="category.php?id=2" class="dropdown-item">Spirit Threads</a>
                    <a href="category.php?id=3" class="dropdown-item">Cuddly Companions</a>
                    <a href="category.php?id=4" class="dropdown-item">Magic Trinkets</a>
                    <a href="category.php?id=5" class="dropdown-item">Ghibli Gems</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php#about">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php#contact">Contact</a>
            </li>
        </ul>
    </div>

    <div class="nav-icons-wrapper ml-3">
        <a href="customer/cart.php" class="nav-icon-btn" title="Cart"><i class="fas fa-shopping-bag"></i></a>
        <a href="customer/wishlist.php" class="nav-icon-btn" title="Wishlist"><i class="fas fa-heart"></i></a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="btn-ghibli-outline ml-2">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn-ghibli-outline ml-2">Login</a>
        <?php endif; ?>
    </div>
</nav>

<!-- Main Details Section -->
<section class="product-details-section">
    <div class="container" data-aos="fade-up" data-aos-duration="1000">
        <div class="product-details-container">
            <div class="row align-items-center">
                <!-- Image Display -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="product-main-image-box">
                        <img src="<?php echo htmlspecialchars(getImagePath($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <span class="product-category-badge">
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Ghibli Treasure'); ?>
                    </span>
                    
                    <h1 class="product-details-title"><?php echo htmlspecialchars($product['title']); ?></h1>

                    <!-- Ratings -->
                    <div class="rating-stars mb-3">
                        <?php 
                        $rating = (int)$product['rating'];
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $rating ? '<i class="bx bxs-star"></i>' : '<i class="bx bx-star"></i>';
                        }
                        ?>
                        <span class="rating-text">(<?php echo $rating; ?>.0)</span>
                    </div>

                    <!-- Price and Stock Status -->
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="product-details-price mr-3">₱<?php echo number_format($product['price'], 2); ?></div>
                        <?php if ($product['stock'] > 0): ?>
                            <span class="badge-ghibli-stock"><i class="fas fa-check-circle mr-1"></i> In Stock (<?php echo $product['stock']; ?>)</span>
                        <?php else: ?>
                            <span class="badge-ghibli-out"><i class="fas fa-times-circle mr-1"></i> Out of Stock</span>
                        <?php endif; ?>
                    </div>

                    <p class="product-description-text">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </p>

                    <!-- Quantity & Action Form -->
                    <form action="customer/cart.php" method="POST" class="d-flex align-items-center gap-3 mt-4">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="hidden" name="action" value="add">
                        
                        <label for="quantity" class="font-weight-bold mb-0 mr-2" style="color: var(--ghibli-forest);">Qty:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="qty-input-ghibli mr-3" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>

                        <button type="submit" class="btn-ghibli-primary mr-2" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>

                        <a href="customer/wishlist.php?action=add&id=<?php echo $product['product_id']; ?>" class="btn-wishlist-ghibli" title="Add to Wishlist">
                            <i class="fas fa-heart"></i>
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        <?php if ($related_result && $related_result->num_rows > 0): ?>
            <h2 class="section-title" style="margin-top: 70px;">You Might Also Like</h2>
            <div class="row">
                <?php while ($related = $related_result->fetch_assoc()): ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <img src="<?php echo htmlspecialchars(getImagePath($related['image'])); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                <div class="product-actions">
                                    <a href="product_details.php?id=<?php echo $related['product_id']; ?>" class="action-btn" title="View Details"><i class="fas fa-eye"></i></a>
                                    <a href="customer/wishlist.php?action=add&id=<?php echo $related['product_id']; ?>" class="action-btn" title="Add to Wishlist"><i class="fas fa-heart"></i></a>
                                </div>
                            </div>
                            <div class="product-card-body">
                                <h3><?php echo htmlspecialchars($related['title']); ?></h3>
                                <div class="star-rating">
                                    <?php 
                                    $rel_rating = (int)$related['rating'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rel_rating ? '★' : '☆';
                                    }
                                    ?>
                                </div>
                                <p class="product-desc"><?php echo htmlspecialchars($related['description']); ?></p>
                                <div class="product-footer">
                                    <span class="price-tag">₱<?php echo number_format($related['price'], 2); ?></span>
                                    
                                    <!-- Direct Add to Cart Form -->
                                    <form action="customer/cart.php" method="POST" class="m-0">
                                        <input type="hidden" name="product_id" value="<?php echo $related['product_id']; ?>">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-add-cart-sm">
                                            <i class="fas fa-shopping-cart"></i> Add Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Footer -->
<footer id="footer">
    <div class="container pb-5">
        <div class="row text-left">
            <div class="col-lg-3 col-md-6 mb-4">
                <h3 style="font-family: 'Fredoka', cursive;">Spirited Finds</h3>
                <p>Offering top-quality Ghibli merchandise and magical finds for fans of all ages.</p>
                <p><i class="fas fa-location-dot"></i> Pasig City, Metro Manila</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h4>Useful Links</h4>
                <ul class="list-unstyled">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#about">About Us</a></li>
                    <li><a href="index.php#contact">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h4>Categories</h4>
                <ul class="list-unstyled">
                    <li><a href="category.php?id=1">Enchanted Pages</a></li>
                    <li><a href="category.php?id=2">Spirit Threads</a></li>
                    <li><a href="category.php?id=4">Magic Trinkets</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h4>Connect With Us</h4>
                <p>Follow our magical journey on social media.</p>
            </div>
        </div>
    </div>
</footer>

<a href="#" class="arrow-top"><i class="fas fa-arrow-up"></i></a>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>
</body>
</html>