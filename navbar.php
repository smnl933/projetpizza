<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <nav class="navbar">
        <h1 class="logo">🍕 Eats at Siman</h1>
        <ul class="nav-links">

            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="menu.php">Menu</a></li>

            <?php if(isset($_SESSION['user_id'])): ?>

                <!-- CONNECTÉ -->

                <?php if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'restaurateur'): ?>
                    <li><a href="panier.php">Panier</a></li>
                <?php endif; ?>

                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'restaurateur'): ?>
                    <li><a href="restaurateur.php">👨‍🍳 Restaurateur</a></li>
                <?php endif; ?>

                <li><a href="deconnexion.php">Déconnexion</a></li>

            <?php else: ?>

                <!-- VISITEUR -->
                <li><a href="connexion.php">Connexion</a></li>
                <li><a href="inscription.php">Inscription</a></li>

            <?php endif; ?>

        </ul>
    </nav>
</header>