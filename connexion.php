<?php
include 'db.php';
session_start();

/* 🔥 TRAITEMENT AJAX */
if(isset($_POST['login'])){

    $email = $_POST['email'] ?? '';
    $mdp = $_POST['motdepasse'] ?? '';

    $sql = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $sql->execute([$email]);

    if($sql->rowCount() > 0){

        $user = $sql->fetch();

        // 🔥 Vérification mot de passe
        if(password_verify($mdp, $user['motdepasse'])){

            // ✅ SESSION CORRIGÉE
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = strtolower($user['role']); // 🔥 AJOUT IMPORTANT

            echo "success";

        } else {
            echo "Mot de passe incorrect";
        }

    } else {
        echo "Email introuvable";
    }

    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<h2>Connexion</h2>

<form id="loginForm" class="form-auth">

    <input type="email" name="email" placeholder="Email" required>

    <input type="password" name="motdepasse" placeholder="Mot de passe" required>

    <button type="submit">Se connecter</button>

</form>

<p id="message"></p>

<p>
    Pas de compte ? <a href="inscription.php">S'inscrire</a>
</p>

<!-- 🔥 AJAX -->
<script>
document.getElementById("loginForm").addEventListener("submit", function(e){

    e.preventDefault();

    let email = document.querySelector("input[name='email']").value;
    let mdp = document.querySelector("input[name='motdepasse']").value;

    fetch("connexion.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "login=1&email=" + encodeURIComponent(email) +
              "&motdepasse=" + encodeURIComponent(mdp)
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