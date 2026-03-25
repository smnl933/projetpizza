<?php
include 'db.php';
session_start();

$sql = $pdo->query("SELECT * FROM pizzas");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Menu</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'navbar.php'; ?>

<h2 style="text-align:center;">Nos Pizzas 🍕</h2>

<div class="pizza-scroll-container">

<?php while($pizza = $sql->fetch(PDO::FETCH_ASSOC)): ?>

    <div class="encadre">
        
        <!-- IMAGE -->
       <img src="<?= $pizza['image'] ?>">

        <!-- NOM -->
        <h3><?= $pizza['nom'] ?></h3>

        <!-- DESCRIPTION -->
        <p><?= $pizza['description'] ?></p>

        <!-- PRIX -->
        <p><strong><?= $pizza['prix'] ?> €</strong></p>

        <!-- 🔥 FORMULAIRE AJAX -->
        <form onsubmit="addToCart(event, <?= $pizza['id'] ?>)">
            <button type="submit">Ajouter au panier 🛒</button>
        </form>

    </div>

<?php endwhile; ?>

</div>

<!-- 🔥 SCRIPT AJAX -->
<script>
function addToCart(e, id){
    e.preventDefault(); // empêche le rechargement

    fetch('panier.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'add=1&pizza_id=' + id
    })
    .then(res => res.text())
    .then(data => {
        console.log(data);

        // 🔥 REDIRECTION vers panier
        window.location.href = "panier.php";
    });
}



</script>

</body>
</html>

