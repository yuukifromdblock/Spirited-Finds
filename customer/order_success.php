<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = intval($_GET['order_id']);

include 'includes/header.php';
?>

<div class="container my-5 text-center" style="min-height: 60vh;">
    <div class="card shadow-sm border-0 rounded-4 p-5 mx-auto" style="max-width: 600px;">
        <div class="mb-3 text-success">
            <i class="fas fa-check-circle fa-5x"></i>
        </div>
        <h2 class="fw-bold text-dark mb-2">Thank You for Your Order!</h2>
        <p class="text-muted fs-5">Your order number is <strong class="text-success">#<?php echo $order_id; ?></strong>.</p>
        <p class="text-muted mb-4">We have received your order and are preparing your magical items for dispatch.</p>
        
        <div class="d-flex justify-content-center gap-3">
            <a href="index.php" class="btn btn-ghibli-primary px-4 py-2">Continue Shopping</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>