<?php
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    $count = mysqli_query($conn, "SELECT SUM(quantite) as total FROM panier WHERE user_id=$user_id");
    $data = mysqli_fetch_assoc($count);
    $total = $data['total'] ?? 0;
}
?>

<li>
    <a href="panier.php">
        PANIER 🛒 <span style="color:yellow;">(<?= $total ?>)</span>
    </a>
</li>