<?php
include 'db.php';
session_start();

/* 🔥 TRAITEMENT AJAX */
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Vérifier si email existe
    $check = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $check->execute([$email]);

    if($check->rowCount() > 0){
        echo "❌ Email déjà utilisé";
    } else {

        $insert = $pdo->prepare("
            INSERT INTO users (nom, prenom, email, motdepasse, role)
            VALUES (?, ?, ?, ?, ?)
        ");

        $insert->execute([$nom, $prenom, $email, $password, $role]);

        // 🔥 connexion auto
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['role'] = strtolower($role);

        echo "success";
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="page-inscription">

<h2>Créer un compte</h2>

<form id="registerForm" class="form-auth">

    <input type="text" name="nom" placeholder="Nom" required>

    <input type="text" name="prenom" placeholder="Prénom" required>

    <input type="email" name="email" placeholder="Email" required>

    <input type="password" name="motdepasse" placeholder="Mot de passe" required>

    <select name="role">
        <option value="client">Client</option>
        <option value="restaurateur">Restaurateur</option>
    </select>

    <button type="submit">S'inscrire</button>

</form>

<p id="message"></p>

<p>
    Déjà un compte ?
    <a href="connexion.php">Se connecter</a>
</p>

<!-- 🔥 AJAX -->
<script>
document.getElementById("registerForm").addEventListener("submit", function(e){

    e.preventDefault();

    let formData = new FormData(this);

    fetch("inscription.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {

        if(data.trim() === "success"){
            window.location.href = "menu.php";
        } else {
            document.getElementById("message").innerText = data;
        }

    });

});
</script>

</body>
</html>