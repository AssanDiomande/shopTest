<?php

namespace Repository;


use App\Database;

/**
 * Classe permettant de gérer les données de la table 'store' de la base de données
 * 
 */
class StoreRepository {
    
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
     * fonction renvoyant tous les magasins disponibles
     * 
     * @param array $filters filtre de recherche sur les magasins
     * 
     * @return array
     */
    public function findAll(array $filters): array
    {
        //ajout dynamique de filtre en fonction de ceux choisi par l'utilisateur
        $filter = '';
        foreach ($filters as $field => $filtervalue) {
            $filter.= ' ORDER BY '. $field . ' ' . $filtervalue;
        }

        $query = $this->db->prepare('SELECT * FROM store' . $filter);
        $query->execute();
        $result = [];
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        return $result;
    }

    /**
     * fonction renvoyant un magasin en fonction de son identifiant
     * 
     * @param string $id identifiant du magasin
     * 
     * @return array
     */
    public function findOneById(string $id): array|bool
    {
        $query = $this->db->prepare('SELECT * FROM store where store_id = :id');
        $query->bindValue('id', $id);
        $query->execute();
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * fonction creant un magasin
     * 
     * @param array $datas données envoyé par le client pour la création d'un magasin
     * 
     * @return void
     */
    public function create(array $datas)
    {
        $query = $this->db->prepare('INSERT INTO `store` (`store_name`,`store_adress`,`store_owner`,`store_created_at`) VALUES (:name, :adress, :owner, :createdAt)');
        $query->bindValue('name', $datas['store_name']);
        $query->bindValue('adress', $datas['store_adress']);
        $query->bindValue('owner', $datas['store_owner']);
        $query->bindValue('createdAt', $datas['store_created_at']);
        $query->execute();
    }

    /**
     * fonction mettant seulement mettre à jour les champs renseignés par l'utilisateur
     * 
     * @param array $store données d'un magasin existant
     * @param array $datas données envoyé par le client pour la création d'un magasin
     * 
     * @return bool
     */
    public function partialUpdate(array $store,array $datas): bool
    {
        $query = $this->db->prepare('UPDATE `store` SET `store_name` = :name, `store_adress` = :adress, `store_owner` = :owner, `store_created_at` = :createdAt where `store_id` = :id');
        
        $name = isset($datas['store_name']) ? $datas['store_name'] : $store['store_name'];
        $adress = isset($datas['store_adress']) ? $datas['store_adress'] : $store['store_adress'];
        $owner = isset($datas['store_owner']) ? $datas['store_owner'] : $store['store_owner'];
        $ceatedAt = isset($datas['store_created_at']) ? $datas['store_created_at'] : $store['store_created_at'];

        $query->bindValue('name', $name);
        $query->bindValue('adress', $adress);
        $query->bindValue('owner', $owner);
        $query->bindValue('createdAt', $ceatedAt);
        $query->bindValue('id', $store['store_id']);

        if (!$query->execute()) {
            return false;
        }
        return true;
    }

    /**
     * fonction mettant à jour completement un magasin
     * 
     * @param string $id identifiant du magasin
     * @param array $datas données envoyé par le client pour la création d'un magasin
     * 
     * @return bool
     */
    public function completeUpdate(string $id,array $datas): bool
    {
        $query = $this->db->prepare('UPDATE `store` SET `store_name` = :name, `store_adress` = :adress, `store_owner` = :owner, `store_created_at` = :createdAt where `store_id` = :id');
        $query->bindValue('name', $datas['store_name']);
        $query->bindValue('adress', $datas['store_adress']);
        $query->bindValue('owner', $datas['store_owner']);
        $query->bindValue('createdAt', $datas['store_created_at']);
        $query->bindValue('id', $id);

        if (!$query->execute()) {
            return false;
        }
        return true;
    }

    /**
     * fonction supprimant un magasin
     * 
     * @param string $id identifiant du magasin
     * 
     * @return bool
     */
    public function delete(string $id): bool
    {
        $query = $this->db->prepare('DELETE FROM store where store_id = :id');
        $query->bindValue('id', $id);

        if (!$query->execute()) {
            return false;
        }
        return true;
    }
}