<?php

namespace Router;

use Controller\SecurityController;
use Controller\StoreController;
use Controller\StoreTestController;

class Routes
{
    private $routes = [];

    //Enregistrement des différentes routes lors l'instanciation de l'objet
    public function __construct()
    {
        $this->register('', function() {
            echo "Voir fichier Documentation.txt à la racine du projet";
        });
        $this->register('login', function(?string $entityId,?string $query) {
            $securityController = new SecurityController();
            return $securityController->login();
        });
        $this->register('stores', function(?string $entityId,?string $query) {
            $storeController = new StoreController();
            return $storeController->getStores($query);
        });
        $this->register('store', function(?string $entityId,?string $query) {
            
            $storeController = new StoreController();
            return $storeController->processRessource($entityId);
        });
        $this->register('testStores', function(?string $entityId,?string $query) {
            $storeController = new StoreTestController();
            return $storeController->getStores($query);
        });
        $this->register('testStore', function(?string $entityId,?string $query) {
            $storeController = new StoreTestController();
            return $storeController->processRessource($entityId);
        });
    }

    //cette fonction coordonne pour chaque endpoint le controller qui lui sera associé
    public function register(string $path, callable $action)
    {
        $this->routes[$path] = $action;
    }

    /**
     * dissocie l'endpoint de l'url puis appelle la fonction correspondante
     * 
     * @param string $uri
     * 
     * @return string|null
     */
    public function resolve(string $uri): ?string
    {
        //récupération de l'endpoint, du paramètre et de la requête url si chacune est renseignée
        $url = parse_url($uri);
        $path = explode('/', $url['path'])[2];
        $entityId = explode('/', $url['path'])[3] ?? null;
        $query = $url['query'] ?? null;
        $action = $this->routes[$path] ?? null;
        //si l'endpoint n'existe pas on signale une erreur
        if ($action === null || !is_callable($action)) {
            return json_encode([
                "status" => "failed",
                "code" => 404,
                "message" => "Ressource introuvable",
            ]);
        }

        //sinon on retourne le résultat de la requête
        return $action($entityId,$query);
    }
}