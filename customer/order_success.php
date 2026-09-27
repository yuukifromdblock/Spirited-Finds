<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ikinonekta pabalik sa root directory base sa Tree Structure
include '../config/db.php';

// Auth Guard: Kailangan naka-login at may valid na order_id
if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: ../index.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = intval($_SESSION['user_id']);

// RBAC & SECURITY: Suriin kung ang order ay umiiral at pagmamay-ari ng kasalukuyang user
$stmt = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Kapag hindi pagmamay-ari ng user o hindi umiiral ang order
    header("Location: my_orders.php");
    exit();
}
$stmt->close();

// Isinama base sa Tree Structure
include '../includes/header.php';
?>

<div class="container my-5 text-center" style="min-height: 60vh;">
    <div class="card shadow-sm border-0 rounded-4 p-5 mx-auto" style="max-width: 600px;">
        <div class="mb-3 text-success">
            <i class="fas fa-check-circle fa-5x"></i>
        </div>
        <h2 class="fw-bold text-dark mb-2">Thank You for Your Order!</h2>
        <p class="text-muted fs-5">Your order number is <strong class="text-success">#<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></strong>.</p>
        <p class="text-muted mb-4">We have received your order and are preparing your magical items for dispatch.</p>
        
        <div class="d-flex justify-content-center gap-3">
            <a href="../index.php" class="btn btn-ghibli-primary px-4 py-2">Continue Shopping</a>
            <a href="my_orders.php" class="btn btn-outline-secondary px-4 py-2">View My Orders</a>
        </div>
    </div>
</div>

<?php 
// Isinama base sa Tree Structure
include '../includes/footer.php'; 
?>