<?php
include 'db.php';
session_start();

/* 🔥 Vérif connexion */
$isLogged = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Eats at Siman</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <nav class="navbar">
        <h1 class="logo">🍕 Eats at Siman</h1>

        <ul class="nav-links">
            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="menu.php">Menu</a></li>

            <?php if($isLogged){ ?>
                <li><a href="panier.php">Panier</a></li>
                <li><a href="deconnexion.php">Déconnexion</a></li>
            <?php } else { ?>
                <li><a href="connexion.php">Connexion</a></li>
                <li><a href="inscription.php">Inscription</a></li>
            <?php } ?>
        </ul>
    </nav>
</header>

<main>

<!-- 🔥 HERO -->
<section class="hero">
    <img src="sitesiman.jpeg" alt="Pizza Eats at Siman">

    <div class="hero-text">
        <h2>Bienvenue chez Eats at Siman</h2>

        <p>
            Découvrez des pizzas artisanales préparées avec passion.  
            Des ingrédients frais, une pâte croustillante et des recettes uniques  
            pour satisfaire toutes vos envies.
        </p>

        <a href="menu.php" class="btn">Voir le menu</a>
    </div>
</section>

<!-- 🔥 PRESENTATION -->
<section class="presentation">
    <h2>Pourquoi choisir Eats at Siman ?</h2>

    <p>
        Chez Eats at Siman, nous mettons un point d'honneur à offrir une expérience
        culinaire exceptionnelle. Nos pizzas sont faites maison avec des produits
        de qualité soigneusement sélectionnés.
    </p>

    <p>
        Que vous soyez amateur de classiques comme la Margherita ou à la recherche
        de nouvelles saveurs, vous trouverez votre bonheur !
    </p>
</section>

</main>

<footer>
    <p>© 2026 Eats at Siman - Tous droits réservés.</p>
</footer>

</body>
</html>