<?php
session_start();
include 'db.php';

// Kung naka-login na ang user, i-redirect pabalik sa homepage
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$msg = "";
$msg_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Suriin kung may kaparehong email na sa database
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check_res = $check->get_result();

        if ($check_res && $check_res->num_rows > 0) {
            $msg = "May umiiral nang account gamit ang email na ito.";
            $msg_type = "danger";
        } else {
            // Ligtas na password hashing
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $role = 'user';

            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $msg = "Matagumpay ang iyong pagre-rehistro! Pwede ka nang mag-login.";
                $msg_type = "success";
            } else {
                $msg = "Nagka-error sa pagre-rehistro: " . $conn->error;
                $msg_type = "danger";
            }
            $stmt->close();
        }
        $check->close();
    } else {
        $msg = "Paki-sagutan ang lahat ng larangan.";
        $msg_type = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Spirited Finds</title>
    <link rel="shortcut icon" type="image" href="./image/logo.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Uchen&display=swap" rel="stylesheet">
    <!-- icons links -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-image: url(./image/bg1.jpg);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            color: #fff;
            font-family: 'Uchen', sans-serif;
        }
        .signup-container {
            max-width: 400px;
            margin: auto;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            margin-top: 100px;
        }
        .signup-header {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5em;
            color: #6f6f6f;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.5);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            color: #6f6f6f;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            border: 1px solid #6f6f6f;
            border-radius: 5px;
        }
        input:focus {
            border-color: #ffd700;
            box-shadow: 0 0 5px #ffd700;
        }
        .btn-primary {
            background-color: #6f6f6f;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-primary:hover {
            background-color: #ffd700;
            transform: scale(1.05);
        }
        footer {
            background: rgba(0, 0, 0, 0.7);
            padding: 10px 0;
            color: #fff;
            text-align: center;
        }
        .quote {
            text-align: justify;
            margin-top: 20px;
            color: #6f6f6f;
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
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php#about">About</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php#contact">Contact</a></li>
        </ul>
    </div>
</nav>
<!-- navbar end -->

<div class="signup-container">
    <h3 class="signup-header">Become part of the magic! Sign up and unlock your own adventure.</h3>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-<?php echo $msg_type; ?> text-center" role="alert">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form action="signup.php" method="POST">
        <div class="form-group">
            <label for="username">Username / Full Name:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
    </form>
    <p class="quote">"The best way to predict the future is to create it." - Peter Drucker</p>
    <p class="text-center mt-3" style="color: #6f6f6f;">Already have an account? <a href="login.php" style="color: #ffd700;">Login</a></p>
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
                  <li><a href="index.php#about">About</a></li>
                  <li><a href="index.php#contact">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 footer-links">
                <h4>Our Products</h4>
                <p style="color: #f8baba;">Explore our enchanting collection of Ghibli-inspired books, clothing, and accessories, perfect for fans of all ages.</p>
                <ul>
                    <li><a href="index.php#product-cards">Books</a></li>
                    <li><a href="index.php#product-cards">Clothes</a></li>
                    <li><a href="index.php#product-cards">Accessories</a></li>
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
</body>
</html>