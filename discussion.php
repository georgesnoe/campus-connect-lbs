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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['strategy']) && $_GET['strategy'] === 'polling' && isset($_GET['lastMessageId'])) {
  // Check if database has been updated
  $sql = "SELECT * FROM messages WHERE id_expediteur = :id_expediteur AND id_destinataire = :id_destinataire AND id > :id ORDER BY id ASC";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':id_expediteur', $_COOKIE["id"]);
  $stmt->bindValue(':id_destinataire', $_GET['id']);
  $stmt->bindValue(':id', $_GET['lastMessageId']);
  $resultat = $stmt->execute();
  if ($stmt->rowCount() > 0) {
    // Database has been updated, return the last message
    $messages = $stmt->fetch(PDO::FETCH_ASSOC);
    http_response_code(200);
    $returnValue = array();
    foreach ($messages as $message) {
      $returnValue[] = array(
        "message" => $message["message"],
        "id" => $message["id"],
        "date" => $message["date"],
        "heure" => $message["heure"],
      );
    }
    echo json_encode($returnValue);
    exit();
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header("Content-Type: application/json");
  // Récuperer toutes les informations saisies par l'utilisateur
  $message = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'])) : null;

  // Vérifier que toutes les informations sont renseignées
  if (empty($message)) {
    echo json_encode(
      array(
        "erreur" => "Veuillez remplir toutes les informations"
      )
    );
  }

  // Si toutes les conditions sont remplies, envoyer le message
  else {
    try {
      // Connexion à la base de données
      $pdo = new PDO("mysql:host=localhost;dbname=campus_connect_lbs", "root", "root");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // Ajouter le message dans la base de données
      $sql = "INSERT INTO messages (id_expediteur, id_destinataire, message) VALUES (:id_expediteur, :id_destinataire, :message)";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':id_expediteur', $_COOKIE["id"]);
      $stmt->bindValue(':id_destinataire', $_GET['id']);
      $stmt->bindValue(':message', $message);
      $resultat = $stmt->execute();

      if (!$resultat) {
        http_response_code(500);
        echo json_encode(
          array(
            "erreur" => "Erreur lors de l'envoi du message. Veuillez réessayer plus tard"
          )
        );
      } else {
        // Récuperer le résultat de la requête et afficher le message
        $message = $stmt->fetch(PDO::FETCH_ASSOC);
        http_response_code(200);
        echo json_encode(
          array(
            "message" => $message["message"]
          )
        );
      }
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(
        array(
          "erreur" => "Erreur lors de la connexion à la base de données. Veuillez réessayer plus tard : " . $e->getMessage()
        )
      );
    }
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
    <form method="post" id="message-form">
      <input type="hidden" name="id_destinataire" value="<?= $utilisateur["id"]; ?>" />
      <input type="hidden" name="id_expediteur" value="<?= $_COOKIE["id"]; ?>" />
      <label for="message">
        <p>Message</p>
        <textarea name="message" id="message" placeholder="Votre message" required></textarea>
      </label>
      <button type="submit">Envoyer</button>
    </form>
  </main>
  <script src="message.js"></script>
</body>

</html>
