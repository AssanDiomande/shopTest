<?php

namespace Repository;


use App\Database;

/**
 * Classe permettant de gérer les données de la table 'user' de la base de données
 * 
 */
class LogRepository {
    
    /**
     * 
     * @var PDO $db
     */
    private \PDO $db;

    /**
     * Initialisation du gestionnaire
     * 
     * Le design pattern singleton a été choisi pour que la connexion à la base de données soit toujours unique
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getManager();
    }

    /**
     * 
     * @param string $type
     * @param string $message
     * 
     * @return bool
     */
    public function add(string $type, string $message): bool
    {
      $query = $this->db->prepare('INSERT INTO `log` (`type`,`message`,`created_at`) VALUES (:type, :message, :createdAt)');
      $query->bindValue('type', $type);
      $query->bindValue('message', $message);
      $query->bindValue('createdAt', (new \DateTime())->format('Y-m-d'));
      if (!$query->execute()) {
          return false;
      }
      return true;
    }
}