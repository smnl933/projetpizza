<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
session_start();

/* 🔒 SÉCURITÉ VISITEUR */
if(!isset($_SESSION['user_id'])){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        echo "❌ Non connecté";
        exit();
    }

    header("Location: connexion.php");
    exit();
}

/* 🔒 BLOQUER RESTAURATEUR */
if(isset($_SESSION['role']) && $_SESSION['role'] === 'restaurateur'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        echo "❌ Accès interdit";
        exit();
    }

    header("Location: accueil.php");
    exit();
}

/* 🔥 USER */
$user_id = $_SESSION['user_id'];

/* 🔥 AJOUT */
if(isset($_POST['add'])){
    $pizza_id = intval($_POST['pizza_id']); // 🔒 sécurité

    $check = $pdo->prepare("SELECT 1 FROM panier WHERE user_id=? AND pizza_id=?");
    $check->execute([$user_id, $pizza_id]);

    if($check->rowCount()){
        $pdo->prepare("UPDATE panier SET quantite = quantite + 1 WHERE user_id=? AND pizza_id=?")
            ->execute([$user_id, $pizza_id]);
    } else {
        $pdo->prepare("INSERT INTO panier (user_id, pizza_id, quantite) VALUES (?, ?, 1)")
            ->execute([$user_id, $pizza_id]);
    }

    exit("ok");
}

/* 🔥 DELETE */
if(isset($_POST['delete'])){
    $pizza_id = intval($_POST['pizza_id']); // 🔒 sécurité

    $pdo->prepare("DELETE FROM panier WHERE user_id=? AND pizza_id=?")
        ->execute([$user_id, $pizza_id]);

    exit("ok");
}

/* 🔥 UPDATE */
if(isset($_POST['update'])){
    $pizza_id = intval($_POST['pizza_id']);
    $change = intval($_POST['change']);

    $pdo->prepare("UPDATE panier SET quantite = quantite + ? WHERE user_id=? AND pizza_id=?")
        ->execute([$change, $user_id, $pizza_id]);

    $pdo->prepare("DELETE FROM panier WHERE user_id=? AND pizza_id=? AND quantite<=0")
        ->execute([$user_id, $pizza_id]);

    exit("ok");
}

/* 🔥 VALIDER COMMANDE */
if(isset($_POST['valider'])){

    if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'client'){
        echo "❌ Non autorisé";
        exit();
    }

    $pdo->prepare("
        INSERT INTO commandes (user_id, statut) 
        VALUES (?, 'en_attente')
    ")->execute([$user_id]);

    $commande_id = $pdo->lastInsertId();

    $sql = $pdo->prepare("SELECT * FROM panier WHERE user_id=?");
    $sql->execute([$user_id]);

    while($item = $sql->fetch()){
        $pdo->prepare("
            INSERT INTO commande_details (commande_id, pizza_id, quantite)
            VALUES (?, ?, ?)
        ")->execute([$commande_id, $item['pizza_id'], $item['quantite']]);
    }

    $pdo->prepare("DELETE FROM panier WHERE user_id=?")
        ->execute([$user_id]);

    echo "ok";
    exit;
}

/* 🔥 LOAD PANIER */
if(isset($_GET['load'])){

    $sql = $pdo->prepare("
        SELECT pizzas.*, panier.quantite
        FROM panier
        JOIN pizzas ON pizzas.id = panier.pizza_id
        WHERE panier.user_id=?
    ");
    $sql->execute([$user_id]);

    $total = 0;

    foreach($sql as $item){
        $sous_total = $item['prix'] * $item['quantite'];
        $total += $sous_total;

        echo "
        <div class='encadre'>
            <img src='{$item['image']}' width='80'>
            <h3>{$item['nom']}</h3>
            <p>Qté : {$item['quantite']}</p>
            <p>{$item['prix']} €</p>
            <p>Sous-total : $sous_total €</p>

            <button onclick='updateQty({$item['id']}, -1)'>➖</button>
            <button onclick='updateQty({$item['id']}, 1)'>➕</button>
            <button onclick='removeItem({$item['id']})'>❌</button>
        </div>";
    }

    echo "<h2>Total : $total €</h2>";

    echo "
    <div style='text-align:center; margin-top:20px;'>
        <button onclick='validateCart()' 
        style='padding:12px 20px; background:#f7b500; color:white; border:none; border-radius:8px; cursor:pointer; font-weight:bold;'>
        Valider la commande ✅
        </button>
    </div>";

    exit;
}
?>