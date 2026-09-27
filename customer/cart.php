<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ikinonekta nang direkta base sa Tree Structure (cart.php ay nasa customer/ folder)
include '../config/db.php';

// Safe Helper Function para sa Image Paths
if (!function_exists('get_image_path')) {
    function get_image_path(?string $path) {
        if (empty($path)) {
            return '../assets/images/logo.png';
        }
        $filename = basename(trim($path));
        return '../assets/images/' . $filename;
    }
}

// Suriin kung Naka-login ang User
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? intval($_SESSION['user_id']) : 0;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// RBAC IMPLEMENTATION: Pigilan ang admin na ma-access ang customer cart
if ($is_logged_in && $user_role === 'admin') {
    header("Location: ../admin/dashboard.php");
    exit();
}

// Maximum Item Limit bawat produkto
$max_item_limit = 10;

if ($is_logged_in) {
    // ACTION HANDLER: Magdagdag ng Item sa Database Cart (GET action=add)
    if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
        $product_id = intval($_GET['id']);
        
        // Suriin kung umiiral na ang produkto sa cart ng user
        $chk = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $chk->bind_param("ii", $user_id, $product_id);
        $chk->execute();
        $chk_res = $chk->get_result();
        
        if ($chk_res && $chk_res->num_rows > 0) {
            $row = $chk_res->fetch_assoc();
            // Dagdagan ng 1 pero huwag lalampas sa max limit na 10
            $new_qty = min($row['quantity'] + 1, $max_item_limit);
            $upd = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $upd->bind_param("iii", $new_qty, $user_id, $product_id);
            $upd->execute();
            $upd->close();
        } else {
            // Ipasok ang bagong item sa database table
            $ins = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
            $ins->bind_param("ii", $user_id, $product_id);
            $ins->execute();
            $ins->close();
        }
        $chk->close();
        
        header("Location: cart.php");
        exit();
    }

    // ACTION HANDLER: Baguhin ang Dami / Quantity (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);

        if ($quantity > 0) {
            // I-cap sa maximum na 10 items
            if ($quantity > $max_item_limit) {
                $quantity = $max_item_limit;
            }
            $upd = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $upd->bind_param("iii", $quantity, $user_id, $product_id);
            $upd->execute();
            $upd->close();
        } else {
            // Kapag ginawang 0 ang quantity, burahin na sa database
            $del = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            $del->bind_param("ii", $user_id, $product_id);
            $del->execute();
            $del->close();
        }
        header("Location: cart.php");
        exit();
    }

    // ACTION HANDLER: Alisin ang Item sa Database Cart (GET action=remove)
    if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
        $remove_id = intval($_GET['id']);
        $del = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $del->bind_param("ii", $user_id, $remove_id);
        $del->execute();
        $del->close();
        header("Location: cart.php");
        exit();
    }
}

// Kuhanin ang mga Items sa Cart mula sa Database Table na 'cart' at 'products'
$cart_products = [];
$total_amount = 0;

if ($is_logged_in) {
    $stmt = $conn->prepare("
        SELECT c.cart_id, c.quantity, c.product_id, p.title, p.price, p.image, p.stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?
        ORDER BY c.cart_id DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $qty = intval($row['quantity']);
        $subtotal = $row['price'] * $qty;
        $total_amount += $subtotal;

        $cart_products[] = [
            'cart_id'    => $row['cart_id'],
            'product_id' => $row['product_id'],
            'title'      => $row['title'] ?? 'Untitled Product',
            'image'      => get_image_path($row['image']),
            'price'      => $row['price'],
            'quantity'   => $qty,
            'stock'      => $row['stock'],
            'subtotal'   => $subtotal
        ];
    }
    $stmt->close();
}

// Isinama nang direkta base sa Tree Structure
include '../includes/header.php';
?>

<div class="container my-5" style="min-height: 60vh;">
    <h2 class="section-title mb-4"><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h2>

    <?php if (!$is_logged_in): ?>
        <!-- GUEST STATE: KAILANGAN MAG-LOGIN -->
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
        <!-- LOGGED-IN STATE: IPAPAKITA ANG DB CART ITEMS -->
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
                                    <th>Quantity (Max 10)</th>
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
                                            <form action="cart.php" method="POST" class="d-flex align-items-center" style="max-width: 130px;">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                <input type="number" 
                                                       name="quantity" 
                                                       value="<?php echo $item['quantity']; ?>" 
                                                       min="1" 
                                                       max="<?php echo min($max_item_limit, $item['stock']); ?>" 
                                                       class="form-control form-control-sm mr-2 text-center" 
                                                       onchange="this.form.submit()">
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
// Isinama nang direkta base sa Tree Structure
include '../includes/footer.php'; 
?>