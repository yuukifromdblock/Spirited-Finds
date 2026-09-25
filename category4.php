<?php
session_start();
include 'db.php';

// Fetch products for Category 4 (Magic Trinkets)
$category_id = 4;
$query = "SELECT * FROM products WHERE category_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$products_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spirited Finds - Magic Trinkets</title>
    <link rel="shortcut icon" type="image" href="./image/logo.png">
    <link rel="stylesheet" href="style.css">
    
    <!-- bootstrap links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
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
            background-image: url(./image/bg4.jpg);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
</head>
<body>

<!-- navbar -->
<nav class="navbar navbar-expand-md" id="navbar">
    <a class="navbar-brand" href="index.php" id="logo">
        <img src="./image/logo.png" alt="" width="50px"> Spirited Finds
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span><img src="./image/menu.png" alt="" width="30px"></span>
    </button>

    <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
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
            <li class="nav-item">
                <a class="nav-link" href="index.php#about">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php#contact">Contact</a>
            </li>
        </ul>
    </div>

    <div class="d-flex ml-auto align-items-center">
        <div class="icons">
            <img src="./image/user.png" alt="" width="20px" class="mr-2">
            <img src="./image/heart.png" alt="" width="20px" class="mr-2">
            <img src="./image/add.png" alt="" width="24px" class="mr-2">
        </div>
        <li class="nav-item list-unstyled">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a class="nav-link" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="nav-link" href="login.php">Login</a>
            <?php endif; ?>
        </li>
    </div>
</nav>
<!-- navbar end -->

<!-- banner -->
<div class="banner" data-aos="fade-up" data-aos-duration="1500" style="background-image: url('./image/blue.gif'); background-size: cover; background-position: center;">
    <div class="content">
        <h3>Enchanting Ghibli Gear</h3>
        <h2>SAVE UP TO 50% ON SELECT ACCESSORIES!</h2>
        <div id="btnorder"><button>Order Now</button></div>
    </div>
</div>
<!-- banner end -->

<!-- product cards -->
<section id="product-cards4" data-aos="fade-up" data-aos-duration="1500">
    <div class="container">
        <h1>MAGIC TRINKETS</h1>
        <div class="row" style="margin-top:50px;">
            <?php if ($products_result && $products_result->num_rows > 0): ?>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <div class="col-md-3 py-3 py-md-0 mb-4">
                        <div class="card">
                            <div class="overlay">
                                <button type="button" class="btn btn-secondary" title="Quick View"><i><img src="./image/views.png" alt="" width="30px"></i></button>
                                <button type="button" class="btn btn-secondary" title="Add to Wishlist"><i><img src="./image/heart.png" alt="" width="30px"></i></button>
                                <button type="button" class="btn btn-secondary" title="Add to Cart"><i><img src="./image/add.png" alt="" width="30px"></i></button>
                            </div>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                            <div class="card-body">
                                <h4><?php echo htmlspecialchars($product['title']); ?></h4>
                                <div class="star">
                                    <?php 
                                    $rating = (int)$product['rating'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating ? '<i class="bx bxs-star checked"></i>' : '<i class="bx bxs-star"></i>';
                                    }
                                    ?>
                                </div>
                                <p><?php echo htmlspecialchars($product['description']); ?></p>
                                <h6>₱<?php echo number_format($product['price'], 2); ?> <span><button>Add Cart</button></span></h6>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Hardcoded Template Items Fallback -->
                <div class="col-md-3 py-3 py-md-0">
                    <div class="card">
                        <div class="overlay">
                            <button type="button" class="btn btn-secondary" title="Quick View"><i><img src="./image/views.png" alt="" width="30px"></i></button>
                            <button type="button" class="btn btn-secondary" title="Add to Wishlist"><i><img src="./image/heart.png" alt="" width="30px"></i></button>
                            <button type="button" class="btn btn-secondary" title="Add to Cart"><i><img src="./image/add.png" alt="" width="30px"></i></button>
                        </div>
                        <img src="./image/Ghibli Jibbitz.png" alt="">
                        <div class="card-body">
                            <h4>Ghibli Jibbitz</h4>
                            <div class="star">
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star"></i>
                                <i class="bx bxs-star"></i>
                            </div>
                            <p>Fun Ghibli-themed Jibbitz for your shoes.</p>
                            <h6>₱200 <strike>₱400</strike><span><button>Add Cart</button></span></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 py-3 py-md-0">
                    <div class="card">
                        <div class="overlay">
                            <button type="button" class="btn btn-secondary" title="Quick View"><i><img src="./image/views.png" alt="" width="30px"></i></button>
                            <button type="button" class="btn btn-secondary" title="Add to Wishlist"><i><img src="./image/heart.png" alt="" width="30px"></i></button>
                            <button type="button" class="btn btn-secondary" title="Add to Cart"><i><img src="./image/add.png" alt="" width="30px"></i></button>
                        </div>
                        <img src="./image/Hair Accessories.png" alt="">
                        <div class="card-body">
                            <h4>Hair Accessories</h4>
                            <div class="star">
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star"></i>
                                <i class="bx bxs-star"></i>
                            </div>
                            <p>Ghibli-themed hair accessories.</p>
                            <h6>₱250 <strike>₱500</strike><span><button>Add Cart</button></span></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 py-3 py-md-0">
                    <div class="card">
                        <div class="overlay">
                            <button type="button" class="btn btn-secondary" title="Quick View"><i><img src="./image/views.png" alt="" width="30px"></i></button>
                            <button type="button" class="btn btn-secondary" title="Add to Wishlist"><i><img src="./image/heart.png" alt="" width="30px"></i></button>
                            <button type="button" class="btn btn-secondary" title="Add to Cart"><i><img src="./image/add.png" alt="" width="30px"></i></button>
                        </div>
                        <img src="./image/No-Face Dress Towel.png" alt="">
                        <div class="card-body">
                            <h4>No-Face Towel</h4>
                            <div class="star">
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star"></i>
                            </div>
                            <p>Soft dress towel featuring No-Face.</p>
                            <h6>₱600 <strike>₱1,200</strike><span><button>Add Cart</button></span></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 py-3 py-md-0">
                    <div class="card">
                        <div class="overlay">
                            <button type="button" class="btn btn-secondary" title="Quick View"><i><img src="./image/views.png" alt="" width="30px"></i></button>
                            <button type="button" class="btn btn-secondary" title="Add to Wishlist"><i><img src="./image/heart.png" alt="" width="30px"></i></button>
                            <button type="button" class="btn btn-secondary" title="Add to Cart"><i><img src="./image/add.png" alt="" width="30px"></i></button>
                        </div>
                        <img src="./image/Ponyo Charm Keychain.png" alt="">
                        <div class="card-body">
                            <h4>Ponyo Charm</h4>
                            <div class="star">
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star checked"></i>
                                <i class="bx bxs-star checked"></i>
                            </div>
                            <p>Charming Ponyo keychain for added flair.</p>
                            <h6>₱175 <strike>₱350</strike><span><button>Add Cart</button></span></h6>
                        </div>
                    </div>
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
                    <p style="color: #f8baba;"> Offering top-quality ghillie suits and camo gear for outdoor enthusiasts and professionals alike.</p>
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
                        <li><a href="#">Services</a></li>
                        <li><a href="#">Privacy policy</a></li>
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
                        <a href="#" class="twiiter"><i class="fa-brands fa-google-plus"></i></a>
                        <a href="#" class="twiiter"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="twiiter"><i class="fa-brands fa-linkedin-in"></i></a>
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
<!-- footer end -->

<a href="#" class="arrow"><i><img src="./image/up-arrow.png" alt="" width="50px"></i></a>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>
</body>
</html>