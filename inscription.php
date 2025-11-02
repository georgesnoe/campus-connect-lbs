<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Récuperer toutes les informations saisies par l'utilisateur
  $nom = isset($_POST['nom']) ? trim(htmlspecialchars($_POST['nom'])) : null;
  $prenoms = isset($_POST['prenoms']) ? trim(htmlspecialchars($_POST['prenoms'])) : null;
  $email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : null;
  $matricule = isset($_POST['matricule']) ? trim(htmlspecialchars($_POST['matricule'])) : null;
  $mot_de_passe = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : null;
  $mot_de_passe_confirmation = isset($_POST['mot_de_passe_confirmation']) ? $_POST['mot_de_passe_confirmation'] : null;

  // Vérifier que toutes les informations sont renseignées
  if (empty($nom) || empty($prenoms) || empty($email) || empty($matricule) || empty($mot_de_passe)) {
    $erreur = "Veuillez remplir toutes les informations";
  }

  // Vérifier que les mots de passe sont identiques
  else if ($mot_de_passe !== $mot_de_passe_confirmation) {
    $erreur = "Les mots de passe ne correspondent pas";
  }

  // L'adresse email doit se terminer par @lomebs.com
  else if (!preg_match('/^[a-zA-Z0-9._%+-]+@lomebs\.com$/', $email)) {
    $erreur = "L'adresse email doit se terminer par @lomebs.com";
  }

  // Vérifier que le matricule respecte le regex /^[0-9]{2}B[SM][0-9]{4}$/
  else if (!preg_match('/^[0-9]{2}B[SM][0-9]{4}$/', $matricule)) {
    $erreur = "Le matricule que vous avez saisi est invalide";
  }

  // Vérifier que le mot de passe contient au minimum 8 caractères
  else if (strlen($mot_de_passe) < 8) {
    $erreur = "Le mot de passe doit contenir au minimum 8 caractères";
  }

  // Si toutes les conditions sont remplies, inscrire l'utilisateur
  else {
    try {
      // Connexion à la base de données
      $pdo = new PDO("mysql:host=localhost;dbname=campus_connect_lbs", "root", "root");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // Vérifier si l'email est déjà utilisé
      $sql = "SELECT * FROM utilisateurs WHERE email = :email";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':email', $email);
      $resultat = $stmt->execute();
      if ($stmt->rowCount() > 0) {
        $erreur = "Cette adresse email a déjà été utilisée. Veuillez en choisir une autre";
      }

      // Vérifier si le matricule est déjà utilisé
      $sql = "SELECT * FROM utilisateurs WHERE matricule = :matricule";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':matricule', $matricule);
      $resultat = $stmt->execute();
      if ($stmt->rowCount() > 0) {
        $erreur = "Ce matricule a déjà été utilisé. Veuillez en choisir un autre";
      }

      if (!isset($erreur)) {
        // Ajout de l'utilisateur dans la base de données
        $sql = "INSERT INTO utilisateurs (nom, prenoms, email, matricule, mot_de_passe) VALUES (:nom, :prenoms, :email, :matricule, :mot_de_passe)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':prenoms', $prenoms);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':matricule', $matricule);
        $stmt->bindValue(':mot_de_passe', password_hash($mot_de_passe, PASSWORD_BCRYPT));
        $resultat = $stmt->execute();

        if (!$resultat) {
          $erreur = "Erreur lors de la création du compte. Veuillez réessayer plus tard";
        } else {
          // Récuperer le résultat de la requête et stocker les cookies
          $compte = $stmt->fetch(PDO::FETCH_ASSOC);
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
  <title>Inscription • Campus Connect LBS</title>
</head>

<body>
  <form action="inscription.php" method="post">
    <?= isset($erreur) ? "<p>$erreur</p>" : ""; ?>
    <label for="nom">
      <p>Nom</p>
      <input type="text" name="nom" id="nom" placeholder="Nom" value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ""; ?>" required />
    </label>

    <label for="prenoms">
      <p>Prénoms</p>
      <input type="text" name="prenoms" id="prenoms" placeholder="Prénoms" value="<?= isset($_POST['prenoms']) ? htmlspecialchars($_POST['prenoms']) : ""; ?>" required />
    </label>

    <label for="email">
      <p>Adresse email</p>
      <input type="email" name="email" id="email" placeholder="Adresse email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ""; ?>" required />
    </label>

    <label for="matricule">
      <p>Matricule</p>
      <input type="text" name="matricule" id="matricule" placeholder="Numéro matricule" value="<?= isset($_POST['matricule']) ? htmlspecialchars($_POST['matricule']) : ""; ?>" required />
    </label>

    <label for="mot_de_passe">
      <p>Mot de passe</p>
      <input type="password" minlength="8" name="mot_de_passe" id="mot_de_passe" placeholder="••••••••" value="<?= isset($_POST['mot_de_passe']) ? htmlspecialchars($_POST['mot_de_passe']) : ""; ?>" required />
    </label>

    <label for="mot_de_passe_confirmation">
      <p>Confirmation du mot de passe</p>
      <input type="password" minlength="8" name="mot_de_passe_confirmation" id="mot_de_passe_confirmation" placeholder="••••••••" value="<?= isset($_POST['mot_de_passe_confirmation']) ? htmlspecialchars($_POST['mot_de_passe_confirmation']) : ""; ?>" required />
    </label>

    <button type="submit">Inscription</button>
  </form>
</body>

</html>
