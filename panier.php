<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
session_start();

/*  LOGIN */
$isLogged = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

/*  AJOUT */
if(isset($_POST['add'])){
    if(!$isLogged) exit("not_connected");

    $pizza_id = $_POST['pizza_id'];

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

/*  DELETE */
if(isset($_POST['delete'])){
    if(!$isLogged) exit("not_connected");

    $pdo->prepare("DELETE FROM panier WHERE user_id=? AND pizza_id=?")
        ->execute([$user_id, $_POST['pizza_id']]);

    exit("ok");
}

/*  UPDATE */
if(isset($_POST['update'])){
    if(!$isLogged) exit("not_connected");

    $pdo->prepare("UPDATE panier SET quantite = quantite + ? WHERE user_id=? AND pizza_id=?")
        ->execute([$_POST['change'], $user_id, $_POST['pizza_id']]);

    $pdo->prepare("DELETE FROM panier WHERE user_id=? AND pizza_id=? AND quantite<=0")
        ->execute([$user_id, $_POST['pizza_id']]);

    exit("ok");
}

/*  VALIDER */
if(isset($_POST['valider'])){
    if(!$isLogged) exit("not_connected");

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

/*  LOAD PANIER */
if(isset($_GET['load'])){

    if(!$isLogged){
        echo "<h2 style='text-align:center;'>⚠️ Connecte-toi pour voir ton panier</h2>";
        exit;
    }

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

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Panier</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'navbar.php'; ?>

<div id="panier"></div>

<script>
const send = (data) =>
    fetch('panier.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams(data)
    })
    .then(r => r.text())
    .then(res => {
        if(res === "not_connected"){
            alert("⚠️ Connecte-toi !");
            window.location.href = "connexion.php";
            return;
        }
        loadCart();
    });

function loadCart(){
    fetch('panier.php?load=1')
    .then(r => r.text())
    .then(html => panier.innerHTML = html);
}

function removeItem(id){
    send({delete:1, pizza_id:id});
}

function updateQty(id, change){
    send({update:1, pizza_id:id, change});
}

function validateCart(){
    fetch('panier.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'valider=1'
    })
    .then(r=>r.text())
    .then(res=>{
        if(res==="not_connected"){
            alert("⚠️ Connecte-toi !");
            window.location.href="connexion.php";
        }
        if(res==="ok") location.href="clients.php";
    });
}

loadCart();
</script>

</body>
</html>