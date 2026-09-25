<?php
session_start();

// Include DB configuration
include 'config/db.php';

// Safe Helper Function para sa Image Paths
if (!function_exists('get_image_path')) {
    function get_image_path(string $image_name = '') {
        if (empty($image_name)) {
            return 'assets/images/logo.png';
        }
        $image_name = trim($image_name);
        if (strpos($image_name, 'assets/') === 0) {
            return $image_name;
        }
        if (strpos($image_name, 'images/') === 0) {
            return 'assets/' . $image_name;
        }
        return 'assets/images/' . $image_name;
    }
}

// Redirect kung nakalog-in na
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT user_id, fullname, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error_msg = "Incorrect password. Please try again.";
            }
        } else {
            $error_msg = "No account is registered with this email address.";
        }
        $stmt->close();
    } else {
        $error_msg = "Please fill in all fields.";
    }
}

// Include Header (Kasama ang global navbar)
include 'includes/header.php';
?>

<!-- Naka-link ang hiwalay na Authentication CSS -->
<link rel="stylesheet" href="assets/global/auth.css">

<!-- AUTHENTICATION HERO SECTION -->
<div class="auth-wrapper" style="background-image: url('<?php echo get_image_path('bg1.jpg'); ?>');">
    <div class="auth-card">
        
        <!-- Animated Header & Standalone Logo -->
        <div class="text-center">
            <div class="auth-logo-standalone">
                <img src="<?php echo get_image_path('logo.png'); ?>" alt="Spirited Finds Logo">
            </div>
            <h2 class="auth-title">Welcome Back!</h2>
            <p class="auth-subtitle">Embark on another magical Ghibli adventure.</p>
        </div>

        <!-- Error Alert Message -->
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show auth-alert text-center mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <!-- Form Elements -->
        <form action="login.php" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="auth-input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="auth-input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-auth-submit">
                <span>Login</span>
                <i class="fas fa-sign-in-alt ms-1"></i>
            </button>
        </form>

        <!-- Embedded Quote Text (Naka-embed na, walang background box) -->
        <div class="auth-quote-embedded">
            <p class="auth-quote-text">"The wind is rising! ... We must try to live!"</p>
        </div>

        <!-- Link pabalik sa Signup -->
        <div class="auth-footer-text">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </div>

    </div>
</div>

<?php
// Include Footer
include 'includes/footer.php';
?>