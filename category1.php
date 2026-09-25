<?php
session_start();
include 'db.php';

// Kunin ang mga produkto para sa Category 1 (Enchanted Pages)
$stmt = $conn->prepare("SELECT * FROM products WHERE category_id = 1");
$stmt->execute();
$products = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enchanted Pages - Spirited Finds</title>
    <link rel="shortcut icon" type="image" href="./image/logo.png">
    <link rel="stylesheet" href="style.css">
    <!-- bootstrap links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <!-- fonts links -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Uchen&display=swap" rel="stylesheet">
    <!-- icons links -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- animation links -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body {
            background-image: url(./image/bg1.jpg);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
</head>
<body>

<!-- navbar -->
<nav class="navbar navbar-expand-md" id="navbar">
    <!-- Brand -->
    <a class="navbar-brand" href="index.php" id="logo">
        <img src="./image/logo.png" alt="" width="50px"> Spirited Finds
    </a>

    <!-- Toggler/collapsible Button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span><img src="./image/menu.png" alt="" width="30px"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <!-- dropdown -->
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" id="navbardrop" data-toggle="dropdown">
                    Category
                </a>
                <div class="dropdown-menu">
                    <a href="category1.php" class="dropdown-item">Enchanted Pages</a>
                    <a href="category2.php" class="dropdown-item">Spirit Threads</a>
                    <a href="category3.php" class="dropdown-item">Cuddly Companions</a>
                    <a href="category4.php" class="dropdown-item">Magic Trinkets</a>
                    <a href="category5.php" class="dropdown-item">Ghibli Gems</a>
                </div>
            </li>
            <!-- dropdown -->
            <li class="nav-item">
                <a class="nav-link" href="index.php#about">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php#contact">Contact</a>
            </li>
        </ul>
    </div>

    <!-- Icons and Login on the right -->
    <div class="d-flex ml-auto align-items-center">
        <div class="icons">
            <img src="./image/user.png" alt="" width="20px" class="mr-2">
            <img src="./image/heart.png" alt="" width="20px" class="mr-2">
            <img src="./image/add.png" alt="" width="24px" class="mr-2">
        </div>
        <ul class="navbar-nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item"><span class="nav-link text-warning">Hi, <?php echo htmlspecialchars($_SESSION['fullname']); ?></span></li>
                <li class="nav-item"><a class="nav-link btn btn-sm btn-outline-danger text-white ml-2" href="logout.php">Logout</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<!-- navbar end -->

<!-- banner -->
<div class="banner" data-aos="fade-up" data-aos-duration="1500" style="background-image: url('./image/blue1.gif'); background-size: cover; background-position: center;">
    <div class="content">
        <h3>Whimsical Ghibli Tales</h3>
        <h2>SAVE UP TO 50% TODAY!</h2>
        <div id="btnorder"><button>Order Now</button></div>
    </div>
</div>
<!-- banner end -->

<!-- product cards -->
<section id="product-cards1" data-aos="fade-up" data-aos-duration="1500">
    <div class="container">
        <h1>ENCHANTED PAGES</h1>
        <div class="row" style="margin-top:50px;">

            <?php if ($products && $products->num_rows > 0): ?>
                <?php while ($row = $products->fetch_assoc()): ?>
                    <?php 
                        // Ayusin ang image path kung may kasama nang './image/' o wala
                        $imgPath = (strpos($row['image'], 'image/') !== false) ? $row['image'] : './image/' . $row['image'];
                        $originalPrice = $row['price'] * 2; // Compute discounted display price
                    ?>
                    <div class="col-md-3 py-3 py-md-0 mb-4">
                        <div class="card h-100">
                            <div class="overlay">
                                <button type="button" class="btn btn-secondary" title="Quick View"><i><img src="./image/views.png" alt="" width="30px"></i></button>
                                <button type="button" class="btn btn-secondary" title="Add to Wishlist"><i><img src="./image/heart.png" alt="" width="30px"></i></button>
                                <button type="button" class="btn btn-secondary" title="Add to Cart"><i><img src="./image/add.png" alt="" width="30px"></i></button>
                            </div>
                            <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <div class="card-body">
                                <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                                <div class="star">
                                    <?php 
                                    $rating = intval($row['rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="bx bxs-star checked"></i>';
                                        } else {
                                            echo '<i class="bx bxs-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <p><?php echo htmlspecialchars($row['description']); ?></p>
                                <h6>
                                    ₱<?php echo number_format($row['price'], 0); ?> 
                                    <strike>₱<?php echo number_format($originalPrice, 0); ?></strike>
                                    <span><button>Add Cart</button></span>
                                </h6>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h3 class="text-white">Walang produktong nahanap sa kategoryang ito.</h3>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- footer -->
<footer id="footer" style="margin-top: 50px;">
<div class="footer-top">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 footer-content">
                <h3>Spirited Finds</h3>
                <p style="color: #f8baba;">Offering top-quality Ghibli products and merchandise for fans and collectors alike.</p>
                <p style="color: #f8baba;">
                    Pasig city <br>
                    Pag-asa <br>
                    Philippines <br>
                </p>
                <strong><i class="fas fa-phone"></i> Phone: <strong>+1234567890</strong></strong><br>
                <strong><i class="fa-solid fa-envelope"></i> Email: <strong>support@spiritedfinds.com</strong></strong>
            </div>
            <div class="col-lg-3 col-md-6 footer-links">
                <h4>Useful Links</h4>
                <ul>
                  <li><a href="index.php">Home</a></li>
                  <li><a href="index.php#about">About</a></li>
                  <li><a href="index.php#contact">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 footer-links">
                <h4>Our Products</h4>
                <p style="color: #f8baba;">Explore our enchanting collection of Ghibli-inspired books, clothing, and accessories, perfect for fans of all ages.</p>
                <ul>
                    <li><a href="category1.php">Books</a></li>
                    <li><a href="category2.php">Clothes</a></li>
                    <li><a href="category4.php">Accessories</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 footer-links">
                <h4>Our Social Network</h4>
                <p style="color: #f8baba;">Stay updated on our latest products and offers by following us on social media.</p>
                <div class="socail-links mt-3">
                    <a href="#" class="twiiter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="twiiter"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="twiiter"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="container py-4">
    <div class="copyright">
        &copy; Copyright <strong>Spirited Finds</strong>. All Rights Reserved
    </div>
</div>
</footer>
<!-- footer -->

<a href="#" class="arrow"><i><img src="./image/up-arrow.png" alt="" width="50px"></i></a>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>
</body>
</html>