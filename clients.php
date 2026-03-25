<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 1;

/* 🔥 SUPPRIMER COMMANDE (AJAX) */
if(isset($_POST['delete_cmd'])){
    $id = $_POST['id'];

    $pdo->prepare("DELETE FROM commande_details WHERE commande_id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM commandes WHERE id=? AND user_id=?")->execute([$id, $user_id]);

    echo "ok";
    exit;
}

/* 🔥 récupérer commandes + détails */
$sql = $pdo->prepare("
    SELECT c.id, c.date_commande, c.statut, p.nom, p.prix, cd.quantite
    FROM commandes c
    JOIN commande_details cd ON c.id = cd.commande_id
    JOIN pizzas p ON p.id = cd.pizza_id
    WHERE c.user_id = ?
    ORDER BY c.date_commande DESC
");
$sql->execute([$user_id]);

$commandes = $sql->fetchAll(PDO::FETCH_ASSOC);

/* 🔥 regrouper */
$grouped = [];

foreach($commandes as $row){
    $grouped[$row['id']]['date'] = $row['date_commande'];
    $grouped[$row['id']]['statut'] = $row['statut']; // 🔥 AJOUT
    $grouped[$row['id']]['items'][] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mes commandes</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'navbar.php'; ?>

<h2 style="text-align:center;">📦 Mes commandes</h2>

<div style="padding:20px;">

<?php foreach($grouped as $id => $cmd): 
    $total = 0;
?>

<div class="encadre" id="cmd-<?= $id ?>">

    <h3>Commande #<?= $id ?></h3>
    <p>Date : <?= $cmd['date'] ?></p>

    <!-- 🔥 STATUT AJOUTÉ -->
    <p>
    Statut : 
    <?php
    if($cmd['statut'] == 'en_attente') echo "<span style='color:orange;'>⏳ En attente</span>";
    elseif($cmd['statut'] == 'acceptee') echo "<span style='color:green;'>✅ Acceptée</span>";
    elseif($cmd['statut'] == 'refusee') echo "<span style='color:red;'>❌ Refusée</span>";
    ?>
    </p>

    <?php foreach($cmd['items'] as $item): 
        $sous = $item['prix'] * $item['quantite'];
        $total += $sous;
    ?>
        <p>🍕 <?= $item['nom'] ?> x<?= $item['quantite'] ?> = <?= $sous ?> €</p>
    <?php endforeach; ?>

    <strong>Total : <?= $total ?> €</strong>

    <!-- 🔥 bouton supprimable seulement si pas acceptée -->
    <?php if($cmd['statut'] != 'acceptee'): ?>
    <button onclick="deleteCmd(<?= $id ?>)" style="
        margin-top:10px;
        padding:10px;
        width:100%;
        background:#f77f00;
        color:white;
        border:none;
        border-radius:8px;
        cursor:pointer;
    ">
        Supprimer ❌
    </button>
    <?php endif; ?>

</div>

<?php endforeach; ?>

</div>

<script>
function deleteCmd(id){
    if(confirm("Supprimer cette commande ?")){
        fetch('clients.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'delete_cmd=1&id=' + id
        })
        .then(res => res.text())
        .then(data => {
            if(data === "ok"){
                document.getElementById("cmd-"+id).remove();
            }
        });
    }
}
</script>

</body>
</html>