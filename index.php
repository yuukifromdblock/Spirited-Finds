<?php
session_start();
include 'db.php';

// Processing para sa Contact Us Form
$contact_msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_contact'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $message = $conn->real_escape_string($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        $sql = "INSERT INTO contact_messages (name, email, phone, message) VALUES ('$name', '$email', '$phone', '$message')";
        if ($conn->query($sql) === TRUE) {
            $contact_msg = "<div class='alert alert-success'>Maraming salamat! Naitabi na ang iyong mensahe.</div>";
        } else {
            $contact_msg = "<div class='alert alert-danger'>Nagka-error sa pagpapadala: " . $conn->error . "</div>";
        }
    } else {
        $contact_msg = "<div class='alert alert-warning'>Paki-fill up ang lahat ng kinakailangang field.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spirited Finds</title>
    <link rel="shortcut icon" type="image" href="./image/logo.png">
    <link rel="stylesheet" href="style.css">
    <!-- bootstrap links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
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
</head>
<body>
    <div class="all-content">
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
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <!-- dynamic dropdown -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" id="navbardrop" data-toggle="dropdown">
                            Category
                        </a>
                        <div class="dropdown-menu">
                            <?php
                            $cat_sql = "SELECT * FROM categories";
                            $cat_res = $conn->query($cat_sql);
                            if ($cat_res && $cat_res->num_rows > 0) {
                                while($cat = $cat_res->fetch_assoc()) {
                                    // BAGO: Nakaturo na sa category1.php, category2.php, atbp.
                                    echo '<a href="category' . $cat['category_id'] . '.php" class="dropdown-item">' . htmlspecialchars($cat['category_name']) . '</a>';
                                }
                            } else {
                                echo '<a href="#" class="dropdown-item">Walang kategorya</a>';
                            }
                            ?>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>

            <!-- Icons and Login/Logout on the right -->
            <div class="d-flex ml-auto align-items-center">
                <div class="icons">
                    <img src="./image/user.png" alt="" width="20px" class="mr-2">
                    <img src="./image/heart.png" alt="" width="20px" class="mr-2">
                    <img src="./image/add.png" alt="Cart" width="24px" class="mr-2 toggle-cart" style="cursor:pointer;">
                </div>
                <li class="nav-item list-unstyled">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a class="nav-link" href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['fullname']); ?>)</a>
                    <?php else: ?>
                        <a class="nav-link" href="login.php">Login</a>
                    <?php endif; ?>
                </li>
            </div>
        </nav>
        <!-- navbar end -->

        <!-- home section -->
        <section id="home">
            <div class="home">
                <div class="content" data-aos="zoom-out-right">
                    <h3>Welcome to<br>Spirited Finds</h3>
                    <p>Explore magical products from your favorite films.</p>
                    <a href="#product-cards" class="btn">Order Now</a>
                </div>
            </div>
        </section>
        <!-- home section end -->

        <!-- top cards -->
        <div class="container" id="box" data-aos="fade-up" data-aos-duration="1500">
            <h1>FEATURED FINDS</h1>
            <div class="row">
                <div class="col-md-4 py-3 py-md-0">
                    <div class="card">
                        <div class="overlay">
                            <h3 class="text-center">My Neighbor Totoro Slippers</h3>
                        </div>
                        <img src="./image/My Neighbor Totoro Slippers.png" alt="">
                    </div>
                </div>
                <div class="col-md-4 py-3 py-md-0">
                    <div class="card">
                        <div class="overlay">
                            <h3 class="text-center">Soot Sprites Phone Case</h3>
                        </div>
                        <img src="./image/Soot Sprites Phone Case.png" alt="">
                    </div>
                </div>
                <div class="col-md-4 py-3 py-md-0">
                    <div class="card">
                        <div class="overlay">
                            <h3 class="text-center">Bookmarks</h3>
                        </div>
                        <img src="./image/Bookmarks.png" alt="">
                    </div>
                </div>
            </div>
        </div>
        <!-- top cards end -->

        <!-- banner -->
        <div class="banner" data-aos="fade-up" data-aos-duration="1500">
            <div class="content">
                <h3>Your Favorite Ghibli Treasures Await</h3>
                <p>From Totoro Plushies to Spirited Away Apparel – Find Your Ghibli Favorite</p>
                <div id="btnorder"><button onclick="location.href='#product-cards'">Order Now</button></div>
            </div>
        </div>
        <!-- banner end -->

        <!-- DYNAMIC MYSTICAL PRODUCT SHOWCASE -->
        <section id="product-cards" data-aos="fade-up" data-aos-duration="1500">
            <div class="container">
                <h1>MYSTICAL PRODUCT SHOWCASE</h1>
                <div class="row" style="margin-top:50px;">
                    <?php
                    $showcase_sql = "SELECT * FROM products WHERE is_featured = 1";
                    $showcase_res = $conn->query($showcase_sql);

                    if ($showcase_res && $showcase_res->num_rows > 0) {
                        while ($prod = $showcase_res->fetch_assoc()) {
                            ?>
                            <div class="col-md-3 py-3 py-md-0 mb-4">
                                <div class="card">
                                    <div class="overlay">
                                        <button type="button" class="btn btn-secondary" title="Quick View"><i><img src="./image/views.png" alt="" width="30px"></i></button>
                                        <button type="button" class="btn btn-secondary" title="Add to Wishlist"><i><img src="./image/heart.png" alt="" width="30px"></i></button>
                                        <button type="button" class="btn btn-secondary" title="Add to Cart"><i><img src="./image/add.png" alt="" width="30px"></i></button>
                                    </div>
                                    <img src="<?php echo htmlspecialchars($prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['title']); ?>">
                                    <div class="card-body">
                                        <h3><?php echo htmlspecialchars($prod['title']); ?></h3>
                                        <div class="star">
                                            <?php
                                            $rating = (int)$prod['rating'];
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $rating) {
                                                    echo '<i class="bx bxs-star checked"></i>';
                                                } else {
                                                    echo '<i class="bx bxs-star"></i>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <p><?php echo htmlspecialchars($prod['description']); ?></p>
                                        <h6>₱<?php echo number_format($prod['price'], 2); ?> <span><button>Add Cart</button></span></h6>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<div class='col-12 text-center'><p>Walang nahanap na mga produkto sa showcase.</p></div>";
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- DYNAMIC SPECIAL EDITION FINDS -->
        <section id="product-cards" data-aos="fade-up" data-aos-duration="1500">
            <div class="container">
                <h1>SPECIAL EDITION FINDS</h1>
                <div class="row" style="margin-top: 50px;">
                    <?php
                    $special_sql = "SELECT * FROM products WHERE is_special = 1";
                    $special_res = $conn->query($special_sql);

                    if ($special_res && $special_res->num_rows > 0) {
                        while ($sprod = $special_res->fetch_assoc()) {
                            ?>
                            <div class="col-md-3 py-3 py-md-0 mb-4">
                                <div class="card">
                                    <div class="overlay">
                                        <button type="button" class="btn btn-secondary" title="Quick View"><i><img src="./image/views.png" alt="" width="30px"></i></button>
                                        <button type="button" class="btn btn-secondary" title="Add to Wishlist"><i><img src="./image/heart.png" alt="" width="30px"></i></button>
                                        <button type="button" class="btn btn-secondary" title="Add to Cart"><i><img src="./image/add.png" alt="" width="30px"></i></button>
                                    </div>
                                    <img src="<?php echo htmlspecialchars($sprod['image']); ?>" alt="<?php echo htmlspecialchars($sprod['title']); ?>">
                                    <div class="card-body">
                                        <h3><?php echo htmlspecialchars($sprod['title']); ?></h3>
                                        <div class="star">
                                            <?php
                                            $srating = (int)$sprod['rating'];
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $srating) {
                                                    echo '<i class="bx bxs-star checked"></i>';
                                                } else {
                                                    echo '<i class="bx bxs-star"></i>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <p><?php echo htmlspecialchars($sprod['description']); ?></p>
                                        <h6>₱<?php echo number_format($sprod['price'], 2); ?> <span><button>Add Cart</button></span></h6>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<div class='col-12 text-center'><p>Walang nahanap na special edition products.</p></div>";
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- gallary -->
        <section id="gallary" data-aos="fade-up" data-aos-duration="1500">
            <div class="container">
                <h1>UPCOMING TREASURES</h1>
                <div class="row" style="margin-top: 30px;">
                    <div class="col-md-4 py-3 py-md-0">
                        <div class="card">
                            <div class="overlay"><h2 class="text-center">Switch Sticker</h2></div>
                            <img src="./image/vinyl protector sticker for nintendo switch.png" alt="">
                        </div>
                    </div>
                    <div class="col-md-4 py-3 py-md-0">
                        <div class="card">
                            <div class="overlay"><h2 class="text-center">Sprite Puzzle</h2></div>
                            <img src="./image/Soot Sprites Puzzle.png" alt="">
                        </div>
                    </div>
                    <div class="col-md-4 py-3 py-md-0">
                        <div class="card">
                            <div class="overlay"><h2 class="text-center">Ghibli DVDs</h2></div>
                            <img src="./image/DVDs & Blue-ray discs.png" alt="">
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 30px;" data-aos="fade-up" data-aos-duration="1500">
                    <div class="col-md-4 py-3 py-md-0">
                        <div class="card">
                            <div class="overlay"><h2 class="text-center">Duvet Set</h2></div>
                            <img src="./image/Duvet Cover Soft Sets.jpg" alt="">
                        </div>
                    </div>
                    <div class="col-md-4 py-3 py-md-0">
                        <div class="card">
                            <div class="overlay"><h2 class="text-center">Totoro Crochet</h2></div>
                            <img src="./image/Crochet Totoro.png" alt="">
                        </div>
                    </div>
                    <div class="col-md-4 py-3 py-md-0">
                        <div class="card">
                            <div class="overlay"><h2 class="text-center">Ghibli Calendar</h2></div>
                            <img src="./image/Calendar.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- about -->
        <section id="about">	
            <div class="container" id="about" data-aos="fade-up" data-aos-duration="1500">
                <h1>ABOUT US</h1>
                <div class="row">
                    <div class="col-md-6 py-3 py-md-0">
                        <div class="card"><img src="./image/about.jpg" alt=""></div>
                    </div>
                    <div class="col-md-6 py-3 py-md-0">
                        <p>Welcome to Ghibli Dreams, where the magic of Studio Ghibli comes to life! As passionate fans, we created this store to bring the enchanting worlds of My Neighbor Totoro, Spirited Away, and other Ghibli classics into your everyday life. From cozy Totoro plushies to stunning art prints, each product is carefully selected to capture the charm and wonder of these beloved films. At Spirited Finds, we prioritize quality and attention to detail, ensuring that every item reflects the beauty and creativity of Studio Ghibli. Whether you’re a long-time fan or just discovering the magic, we’re here to help you find your next favorite piece of Ghibli-inspired magic. Let the adventure begin with Spirited Finds!</p>
                        <div id="bt"><button>Read More...</button></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- DYNAMIC CONTACT US FORM -->
        <section id="contact">	
            <div class="container" id="contact" data-aos="fade-up" data-aos-duration="1500">
                <h1>CONTACT US</h1>
                <?php echo $contact_msg; ?>
                <form action="index.php#contact" method="POST">
                    <div class="row">
                        <div class="col-md-4 py-1 py-md-0">
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" id="usr" placeholder="Name" required>
                            </div>
                        </div>
                        <div class="col-md-4 py-1 py-md-0">
                            <div class="form-group">
                                <input type="email" class="form-control" name="email" id="eml" placeholder="Email" required>
                            </div>
                        </div>
                        <div class="col-md-4 py-1 py-md-0">
                            <div class="form-group">
                                <input type="text" class="form-control" name="phone" id="phn" placeholder="Phone">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="5" name="message" id="comment" placeholder="Message" required></textarea>
                    </div>
                    <div id="messagebtn"><button type="submit" name="send_contact">Send Message</button></div>
                </form>
            </div>
        </section>

        <!-- offer -->
        <div class="container" id="offer">
            <div class="row justify-content-between">
                <div class="col-md-3 py-3 py-md-0">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <h5 class="custom-text3">Free Shipping</h5>
                    <p class="custom-text3">On orders over ₱1000</p>
                </div>
                <div class="col-md-3 py-3 py-md-0">
                    <i class="fa-solid fa-truck"></i>
                    <h5 class="custom-text3">Fast Delivery</h5>
                    <p class="custom-text3">World wide</p>
                </div>
                <div class="col-md-3 py-3 py-md-0">
                    <i class="fa-solid fa-thumbs-up"></i>
                    <h5 class="custom-text3">Big Choice</h5>
                    <p class="custom-text3">Of products</p>
                </div>
                <div class="col-md-3 py-3 py-md-0">
                    <i class="fa-solid fa-rotate-left"></i> 
                    <h5 class="custom-text3">Easy Returns</h5>
                    <p class="custom-text3">Smooth Returns Experience</p>
                </div>
            </div>
        </div>

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
                                <li><a href="#about">About</a></li>
                                <li><a href="#contact">Contact</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-3 col-md-6 footer-links">
                            <h4>Our Products</h4>
                            <ul>
                                <li><a href="#product-cards">Apparel</a></li>
                                <li><a href="#product-cards">Accessories</a></li>
                                <li><a href="#product-cards">Collectibles</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-3 col-md-6 footer-links">
                            <h4>Our Social Network</h4>
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

        <!-- Cart Popout -->
        <div class="cart-container" id="cart">
            <div class="cart-header">
                <h2>Your Cart</h2>
                <span class="close-cart">&times;</span>
            </div>
            <div class="cart-items">
                <div class="cart-item">
                    <img src="./image/Ghibli Studio Tote Bag.png" alt="Ghibli Studio Tote Bag">
                    <div class="item-details">
                        <h3>Ghibli Tote</h3>
                        <p>Ghibli Studio-themed tote bag.</p>
                        <h6>₱350 <strike>₱700</strike></h6>
                    </div>
                </div>
            </div>
            <div class="cart-footer">
                <p class="total-price">Total: ₱350</p>
                <button class="checkout-btn">Checkout</button>
            </div>
        </div>

        <script>
            const closeCart = document.querySelector('.close-cart');
            const cartContainer = document.getElementById('cart');

            closeCart.addEventListener('click', () => {
                cartContainer.classList.remove('active');
            });

            document.querySelector('.toggle-cart').addEventListener('click', () => {
                cartContainer.classList.toggle('active');
            });
        </script>

        <a href="#" class="arrow"><i><img src="./image/up-arrow.png" alt="" width="50px"></i></a>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>