<?php
include 'db.php';
session_start();

/*  CHANGER STATUT (AJAX) */
if(isset($_POST['action'])){
    $id = $_POST['id'];
    $action = $_POST['action'];

    if($action == "accept"){
        $pdo->prepare("UPDATE commandes SET statut='acceptee' WHERE id=?")->execute([$id]);
    }

    if($action == "refuse"){
        $pdo->prepare("UPDATE commandes SET statut='refusee' WHERE id=?")->execute([$id]);
    }

    echo "ok";
    exit;
}

/*  récupérer commandes */
$sql = $pdo->query("
    SELECT c.id, c.date_commande, c.statut, p.nom, p.prix, cd.quantite
    FROM commandes c
    JOIN commande_details cd ON c.id = cd.commande_id
    JOIN pizzas p ON p.id = cd.pizza_id
    ORDER BY c.date_commande DESC
");

$data = $sql->fetchAll(PDO::FETCH_ASSOC);

/*  regrouper */
$grouped = [];

foreach($data as $row){
    $grouped[$row['id']]['date'] = $row['date_commande'];
    $grouped[$row['id']]['statut'] = $row['statut'];
    $grouped[$row['id']]['items'][] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Restaurateur</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'navbar.php'; ?>

<h2 style="text-align:center;">👨‍🍳 Commandes clients</h2>

<div style="padding:20px;">

<?php foreach($grouped as $id => $cmd): ?>

<div class="encadre" id="cmd-<?= $id ?>">

    <h3>Commande #<?= $id ?></h3>
    <p><?= $cmd['date'] ?></p>

    <p>
    Statut : 
    <span id="statut-<?= $id ?>">
        <?php
        if($cmd['statut'] == 'en_attente') echo "⏳";
        elseif($cmd['statut'] == 'acceptee') echo "✅";
        elseif($cmd['statut'] == 'refusee') echo "❌";
        ?>
    </span>
    </p>

    <?php foreach($cmd['items'] as $item): ?>
        <p>🍕 <?= $item['nom'] ?> x<?= $item['quantite'] ?></p>
    <?php endforeach; ?>

    <!--  boutons seulement si en attente -->
    <?php if($cmd['statut'] == 'en_attente'): ?>
        <button onclick="updateStatus(<?= $id ?>,'accept')" class="btn">Accepter ✅</button>
        <button onclick="updateStatus(<?= $id ?>,'refuse')" class="btn">Refuser ❌</button>
    <?php endif; ?>

</div>

<?php endforeach; ?>

</div>

<script>
function updateStatus(id, action){
    fetch('restaurateur.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=' + action + '&id=' + id
    })
    .then(res => res.text())
    .then(data => {
        if(data === "ok"){
            document.getElementById("statut-"+id).innerHTML =
                action === "accept" ? "✅" : "❌";
        }
    });
}
</script>

</body>
</html>


