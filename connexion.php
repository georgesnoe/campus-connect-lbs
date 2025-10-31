<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Récuperer toutes les informations saisies par l'utilisateur
  $email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : null;
  $mot_de_passe = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : null;

  // Vérifier que toutes les informations sont renseignées
  if (empty($email) || empty($mot_de_passe)) {
    $erreur = "Veuillez remplir toutes les informations";
  }

  // L'adresse email doit se terminer par @lomebs.com
  else if (!preg_match('/^[a-zA-Z0-9._%+-]+@lomebs\.com$/', $email)) {
    $erreur = "L'adresse email doit se terminer par @lomebs.com";
  }

  // Vérifier que le mot de passe contient au minimum 8 caractères
  else if (strlen($mot_de_passe) < 8) {
    $erreur = "Le mot de passe doit contenir au minimum 8 caractères";
  }

  // Si toutes les conditions sont remplies, se connecter
  else {
    try {
      // Connexion à la base de données
      $pdo = new PDO("mysql:host=localhost;dbname=campus_connect_lbs", "root", "root");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // Vérifier si l'email est valide
      $sql = "SELECT * FROM utilisateurs WHERE email = :email";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':email', $email);
      $resultat = $stmt->execute();
      if ($stmt->rowCount() > 0) {
        $erreur = "Cette adresse email n'est pas valide";
      } else {
        $compte = $stmt->fetch(PDO::FETCH_ASSOC);
        // Vérifier si le mot de passe est correct
        if (password_verify($mot_de_passe, $compte['mot_de_passe'])) {
          $erreur = "Le mot de passe est invalide";
        } else {
          // Stocker des cookies afin de garder l'utilisateur connecté
          setcookie("email", $email, time() + (86400 * 30), "/",  $_SERVER['SERVER_NAME'], false, true);
          setcookie("matricule", $compte['matricule'], time() + (86400 * 30), "/",  $_SERVER['SERVER_NAME'], false, true);
          setcookie("nom", $compte['nom'], time() + (86400 * 30), "/",  $_SERVER['SERVER_NAME'], false, true);
          setcookie("prenoms", $compte['prenoms'], time() + (86400 * 30), "/",  $_SERVER['SERVER_NAME'], false, true);
          setcookie("id", $compte['id'], time() + (86400 * 30), "/",  $_SERVER['SERVER_NAME'], false, true);
        }
      }
    } catch (PDOException $e) {
      $erreur = "Erreur lors de la connexion à la base de données. Veuillez réessayer plus tard : " . $e->getMessage();
    }
  }
}
?>

<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Connexion • Campus Connect LBS</title>
</head>

<body>
  <form action="connexion.php" method="post">
    <?= isset($erreur) ? "<p>$erreur</p>" : ""; ?>
    <label for="email">
      <p>Adresse email</p>
      <input type="email" name="email" id="email" placeholder="Adresse email" required />
    </label>

    <label for="mot_de_passe">
      <p>Mot de passe</p>
      <input type="password" minlength="8" name="mot_de_passe" id="mot_de_passe" placeholder="••••••••" required />
    </label>
  </form>
</body>

</html>
