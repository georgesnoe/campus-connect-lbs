<?php
require_once "utilitaires.php";
// Vérifier si les cookies sont présents
if (!isset($_COOKIE['email']) || !isset($_COOKIE['matricule']) || !isset($_COOKIE['nom']) || !isset($_COOKIE['prenoms']) || !isset($_COOKIE['id'])) {
  header("Location: acceuil.php");
} else {
  if (!verifier_cookies($_COOKIE['nom'], $_COOKIE['prenoms'], $_COOKIE['matricule'], $_COOKIE['email'])) {
    header("Location: acceuil.php");
  }
}

// Vérifier s'il y'a un id dans l'URL
if (!isset($_GET['id'])) {
  header("Location: acceuil.php");
}

$utilisateur = recuperer_utilisateur(htmlspecialchars($_GET['id']));
?>

<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Acceuil • Campus Connect LBS</title>
</head>

<body>
  <aside>
    <?php
    // Afficher la liste de tous les utilisateurs
    $utilisateurs = recuperer_tous_les_utilisateurs();
    if (count($utilisateurs) > 0) {
      foreach ($utilisateurs as $utilisateur) {
        if ($utilisateur["id"] == $_COOKIE["id"]) {
          echo "<a href=\"discussion.php?id=" . $utilisateur["id"] . "\">" . $utilisateur["nom"] . " " . $utilisateur["prenoms"] . "</a>";
        }
        echo "<a href=\"discussion.php?id=" . $utilisateur["id"] . ">" . $utilisateur["nom"] . " " . $utilisateur["prenoms"] . "</a>";
      }
    }
    ?>
  </aside>
  <main>
    <p>Discussion avec <?= $utilisateur["nom"] . " " . $utilisateur["prenoms"]; ?></p>
    <div class="historique" id="historique">
      <?php
      $messages = recuperer_historique($_COOKIE["id"], $utilisateur["id"]);
      foreach ($messages as $message) {
        echo "<p>" . $message["message"] . "</p>";
      }
      ?>
    </div>
    <form method="post">
      <input type="hidden" name="id_destinataire" value="<?= $utilisateur["id"]; ?>" />
      <input type="hidden" name="id_expediteur" value="<?= $_COOKIE["id"]; ?>" />
      <label for="message">
        <p>Message</p>
        <textarea name="message" id="message" placeholder="Votre message" required></textarea>
      </label>
      <button type="submit">Envoyer</button>
    </form>
  </main>
</body>

</html>
