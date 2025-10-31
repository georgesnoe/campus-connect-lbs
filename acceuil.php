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
</body>

</html>
