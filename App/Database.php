<?php

namespace App;

class Database {
    private static $instance = null;
    private \PDO $manager;

    //récupère les données de configuration et établi la connexion à la base de données
    private function __construct() {  

        //récuération des données de configuration à la base de données
        $iniPath = __DIR__ .'/../Config/database.ini';
        if (!file_exists($iniPath)) {
            throw new \Exception('Le fichier de configuration de la bdd n\'existe pas');
        }
        $dbConfig = parse_ini_file($iniPath, true);
        if (
            !array_key_exists('database', $dbConfig) ||
            !array_key_exists('host', $dbConfig['database']) ||
            !array_key_exists('user', $dbConfig['database']) ||
            !array_key_exists('port', $dbConfig['database']) ||
            !array_key_exists('password', $dbConfig['database']) ||
            !array_key_exists('database', $dbConfig['database'])
        ) {
            throw new \Exception('Donnée de configuration manquante');
        }
        $host = $dbConfig['database']['host'];
        $user = $dbConfig['database']['user'];
        $password = $dbConfig['database']['password'];
        $port = $dbConfig['database']['port'];
        $database = $dbConfig['database']['database'];

        //initialisation du gestionnaire de la base de données en fonction des données de connexion
        $this->manager = new \PDO("mysql:host=$host;port=$port;dbname=$database",$user,$password);
    }

    /**
     * Retourne une instance de Database en vérifiant qu'elle n'est pas déjà été créée
     * 
     * @return Database
     */
    public static function getInstance(): Database {
    
        if(is_null(self::$instance)) {
            self::$instance = new Database();  
        }

        return self::$instance;
    }

    /**
     * Get the value of manager
     * 
     * @return \PDO
     */ 
    public function getManager(): \PDO
    {
        return $this->manager;
    }
}
