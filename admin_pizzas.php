<?php
include 'db.php';
session_start();
?>

<h2>Ajouter une pizza 🍕</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nom" placeholder="Nom">
    <input type="text" name="description">
    <input type="number" step="0.01" name="prix">
    <input type="file" name="image">
    <button name="add_pizza">Ajouter</button>
</form>