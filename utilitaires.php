<?php
// Vérifier si les cookies ne sont pas falsifiés
function verifier_cookies(string $nom, string $prenoms, string $matricule, string $email): bool
{
  try {
    $pdo = new PDO("mysql:host=localhost;dbname=campus_connect_lbs", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM utilisateurs WHERE email = :email AND nom = :nom AND prenoms = :prenoms AND matricule = :matricule";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':nom', $nom);
    $stmt->bindValue(':prenoms', $prenoms);
    $stmt->bindValue(':matricule', $matricule);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  } catch (PDOException $e) {
    return false;
    // return "Erreur lors de la connexion à la base de données. Veuillez réessayer plus tard : " . $e->getMessage();
  }
}

// Récupérer les informations de tous les utilisateurs
function recuperer_tous_les_utilisateurs(string $criteres = ""): array
{
  try {
    $pdo = new PDO("mysql:host=localhost;dbname=campus_connect_lbs", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM utilisateurs $criteres";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    return array();
    // return "Erreur lors de la connexion à la base de données. Veuillez réessayer plus tard : " . $e->getMessage();
  }
}

// Récupérer les informations d'un utilisateur
function recuperer_utilisateur(string $id): array
{
  try {
    $pdo = new PDO("mysql:host=localhost;dbname=campus_connect_lbs", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM utilisateurs WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    return array();
    // return "Erreur lors de la connexion à la base de données. Veuillez réessayer plus tard : " . $e->getMessage();
  }
}

// Récupérer l'historique d'une discussion
function recuperer_historique(string $id_expediteur, string $id_destinataire): array
{
  try {
    $pdo = new PDO("mysql:host=localhost;dbname=campus_connect_lbs", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM messages WHERE (id_expediteur = :id_expediteur AND id_destinataire = :id_destinataire) OR (id_expediteur = :id_destinataire AND id_destinataire = :id_expediteur)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_expediteur', $id_expediteur);
    $stmt->bindValue(':id_destinataire', $id_destinataire);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    return array();
    // return "Erreur lors de la connexion à la base de données. Veuillez réessayer plus tard : " . $e->getMessage();
  }
}
