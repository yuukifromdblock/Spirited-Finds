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

// Redirect kung naka-login na ang user
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
            $msg = "An account with this email address already exists.";
            $msg_type = "danger";
        } else {
            // Safe password hashing
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $role = 'user';

            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $msg = "Registration successful! You can now log in to your account.";
                $msg_type = "success";
            } else {
                $msg = "There was an error during registration. Please try again.";
                $msg_type = "danger";
            }
            $stmt->close();
        }
        $check->close();
    } else {
        $msg = "Please fill in all required fields.";
        $msg_type = "warning";
    }
}

// Include Header (Navbar at CSS imports)
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
            <h2 class="auth-title">Join the Magic!</h2>
            <p class="auth-subtitle">Create an account & start your Ghibli collection.</p>
        </div>

        <!-- Alert Notification Message -->
        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show text-center mb-4" role="alert">
                <i class="fas <?php echo ($msg_type === 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-1"></i>
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <!-- Signup Form -->
        <form action="signup.php" method="POST" class="auth-form">
            <div class="form-group">
                <label for="username" class="form-label">Full Name / Username</label>
                <div class="auth-input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Howls Moving Castle" required>
                </div>
            </div>

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
                <span>Create Account</span>
                <i class="fas fa-user-plus ms-1"></i>
            </button>
        </form>

        <!-- Embedded Quote Text -->
        <div class="auth-quote-embedded">
            <p class="auth-quote-text">"Always believe in yourself. Do this and no matter where you are, you will have nothing to fear."</p>
        </div>

        <!-- Link pabalik sa Login -->
        <div class="auth-footer-text">
            Already have an account? <a href="login.php">Login here</a>
        </div>

    </div>
</div>

<?php
// Include Footer
include 'includes/footer.php';
?>