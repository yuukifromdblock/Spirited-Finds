<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Dynamic Path Resolver for Database Configuration
$db_path = file_exists('config/db.php') ? 'config/db.php' : '../config/db.php';
include $db_path;

// Safe Helper Function for Image Paths
if (!function_exists('get_image_path')) {
    function get_image_path(?string $path) {
        if (empty($path)) {
            return '../assets/images/logo.png';
        }
        $filename = basename(trim($path));
        return '../assets/images/' . $filename;
    }
}

// Check User Authentication Status
$is_logged_in = isset($_SESSION['user_id']);

// Initialize Session Cart array if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ACTION HANDLER: Update Item Quantity
if ($is_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        header("Location: cart.php");
        exit();
    }
}

// ACTION HANDLER: Remove Item from Cart
if ($is_logged_in && isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $remove_id = intval($_GET['id']);
    unset($_SESSION['cart'][$remove_id]);
    header("Location: cart.php");
    exit();
}

// Fetch Cart Product Details for Authenticated Users
$cart_products = [];
$total_amount = 0;

if ($is_logged_in && !empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $pid = $row['product_id'];
        $qty = $_SESSION['cart'][$pid];
        $subtotal = $row['price'] * $qty;
        $total_amount += $subtotal;

        $cart_products[] = [
            'product_id' => $pid,
            'title'      => $row['title'] ?? $row['product_name'] ?? 'Untitled Product',
            'image'      => get_image_path($row['image']),
            'price'      => $row['price'],
            'quantity'   => $qty,
            'subtotal'   => $subtotal
        ];
    }
    $stmt->close();
}

// Dynamic Path Resolver for Header
$header_path = file_exists('includes/header.php') ? 'includes/header.php' : '../includes/header.php';
include $header_path;
?>

<div class="container my-5" style="min-height: 60vh;">
    <h2 class="section-title mb-4"><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h2>

    <?php if (!$is_logged_in): ?>
        <!-- GUEST STATE: PROMPT TO LOGIN OR REGISTER -->
        <div class="card shadow-sm border-0 rounded-lg p-5 text-center my-5 mx-auto" style="max-width: 500px;">
            <div class="mb-3">
                <i class="fas fa-user-lock fa-4x text-muted"></i>
            </div>
            <h3 class="font-weight-bold mb-2">Authentication Required</h3>
            <p class="text-muted mb-4">Please log in or create an account to view your shopping cart and proceed to checkout.</p>
            <div class="d-grid gap-2">
                <a href="../login.php?redirect=customer/cart.php" class="btn btn-ghibli-primary py-2 font-weight-bold">Log In</a>
                <a href="../signup.php" class="btn btn-outline-secondary py-2 font-weight-bold">Create an Account</a>
            </div>
        </div>

    <?php elseif (!empty($cart_products)): ?>
        <!-- AUTHENTICATED STATE: SHOW CART ITEMS -->
        <div class="row">
            <!-- CART ITEMS TABLE -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_products as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                                     style="width: 60px; height: 60px; object-fit: cover;" 
                                                     class="rounded mr-3"
                                                     onerror="this.onerror=null; this.src='../assets/images/logo.png';">
                                                <span class="font-weight-bold"><?php echo htmlspecialchars($item['title']); ?></span>
                                            </div>
                                        </td>
                                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                        <td>
                                            <form action="cart.php" method="POST" class="d-flex align-items-center" style="max-width: 120px;">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="99" class="form-control form-control-sm mr-2 text-center" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="font-weight-bold text-success">₱<?php echo number_format($item['subtotal'], 2); ?></td>
                                        <td class="text-center">
                                            <a href="cart.php?action=remove&id=<?php echo $item['product_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Are you sure you want to remove this item from your cart?');">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ORDER SUMMARY CARD -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-lg p-4">
                    <h4 class="font-weight-bold mb-3">Order Summary</h4>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>₱<?php echo number_format($total_amount, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping Fee</span>
                        <span class="text-muted">Calculated at Checkout</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong class="h5 font-weight-bold mb-0">Total</strong>
                        <strong class="h5 font-weight-bold text-success mb-0">₱<?php echo number_format($total_amount, 2); ?></strong>
                    </div>

                    <a href="checkout.php" class="btn btn-ghibli-primary w-100 py-2">Proceed to Checkout</a>
                    <a href="../index.php" class="btn btn-link w-100 mt-2 text-decoration-none text-muted">Continue Shopping</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- EMPTY CART STATE -->
        <div class="text-center py-5 bg-white rounded-lg shadow-sm">
            <i class="fas fa-shopping-basket fa-4x text-muted mb-3"></i>
            <h3>Your Cart is Currently Empty</h3>
            <p class="text-muted">Discover and add your favorite Studio Ghibli merchandise!</p>
            <a href="../index.php" class="btn btn-ghibli-primary mt-3">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php 
// Dynamic Path Resolver for Footer
$footer_path = file_exists('includes/footer.php') ? 'includes/footer.php' : '../includes/footer.php';
include $footer_path; 
?>