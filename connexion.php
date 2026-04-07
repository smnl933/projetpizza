<?php
include 'db.php';
session_start();

/*  TRAITEMENT AJAX */
if(isset($_POST['login'])){ // si le bouton login existe ducoup on envoie les donnes

    $email = $_POST['email'] ?? '';   // on recuperer le email envoye par le formulaire sinon rien
    $mdp = $_POST['motdepasse'] ?? '';  // on recupere le mdp envoyer par le formulaire sinon rien 

    $sql = $pdo->prepare("SELECT * FROM users WHERE email=?"); // on selectionne tout ce qui vien de la table users  ou l'email et on va preparer une requete sql pour ensuite l'execute
    $sql->execute([$email]);

    if($sql->rowCount() > 0){ // si le resultat est superieur a  0 

        $user = $sql->fetch(); // on recupere les donnes de l'utilisateur dans la db

        //  Vérification mot de passe
        if(password_verify($mdp, $user['motdepasse'])){

            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = strtolower($user['role']); //  AJOUT IMPORTANT

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

<!--  AJAX -->
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