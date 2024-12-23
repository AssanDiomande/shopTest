<?php

namespace Repository;


use App\Database;

/**
 * Classe permettant de gérer les données de la table 'user' de la base de données
 * 
 */
class UserRepository {
    
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
     * fonction recuperant un utilisateur enregistré
     * 
     * @param string $email email d'un utilisateur
     * 
     * @return array|bool
     */
    public function findUserByEmail(string $email): array|bool
    {
        $query = $this->db->prepare('SELECT * FROM user where email = :email');
        $query->bindValue('email', $email);
        $query->execute();
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return $result;
    }
}