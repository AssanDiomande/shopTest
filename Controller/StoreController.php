<?php

namespace Controller;

use Repository\StoreRepository;

class StoreController {
    
    private StoreRepository $rep;

    public function __construct()
    {
        $this->rep = new StoreRepository();
    }

    /**
     * fonction permettant d'appeler la bonne fonction en fonction des données transmises par l'utilisateur
     * 
     * @param string|null $id identifiant du magasin
     * 
     * @return string|false
     */
    public function processRessource(?string $id): string|false
    {
        if (!SecurityController::checkToken()) {
            http_response_code(401);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Veuillez renseigner le token",
            ]);
        }
        //vérification que l'id soit bien transmise et soit valide dans le cas où le verbe POST n'est pas utilisé
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && ($id === null || !is_numeric($id))) {
            http_response_code(400);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Erreur lors du rensignement du paramètre",
            ]);
        }

        //appel de la méthode correspondante en fonction du verbe choisi
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                return $this->getStore($id);
                break;
            case 'POST':
                return $this->createStore();
                break;
            case 'PUT':
            case 'PATCH':
                return $this->updateStore($id);
                break;
            case 'DELETE':
                return $this->deleteStore($id);
                break;
            default:
                http_response_code(400);
                return json_encode([
                    "status" => "failed",
                    "code" => http_response_code(),
                    "message" => "Erreur lors de la saisie du verbe",
                ]);
        }
    }

    /**
     * fonction récupérant tous magasins disponibles
     * 
     * @param string|null $query requête de triage envoyé par l'utilisateur
     * 
     * @return string|false
     */
    public function getStores(?string $query): string|false
    {
        //vérification de la validité du token
        if (!SecurityController::checkToken()) {
            http_response_code(401);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Veuillez renseigner le token",
            ]);
        }

        //verification du bon verbe
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(404);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Erreur lors de la saisie de l'url",
            ]);
        }

        //filtre se mettant à jour en fonction des données envoyées par l'utilisateur
        $filters = [];

        //
        if ($query !== null) {
            $filters = $this->prepareFilters($query);
        }
        
        
        //récupération des magasins et envoi de la confirmation, une erreur sera affichée si la requête n'a pu être faite
        $stores = $this->rep->findAll($filters);

        if (!$stores) {
            http_response_code(404);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Ressource introuvable",
            ]); 
        }

        return json_encode([
            "status" => "success",
            "code" => 200,
            "stores" => $stores
        ]);
    }

    /**
     * fonction mettant à jour le filtre de recherche
     * 
     * @param string|null $query requête de triage envoyé par l'utilisateur
     * 
     * @return string|array|false
     */
    public function prepareFilters(string $query): string|array|false
    {
        //si l'utilisateur choisi de trier des données, on vérifie que les champs concernés existe et que la valeur soit bien conforme au langage sql
        $fieldName = explode('=', $query)[0];
        $sortValue = explode('=', $query)[1];
        if (
            !in_array($fieldName, ['id','name','adress','owner','created_at']) ||
            !in_array($sortValue, ['asc', 'desc'])
        ) {
            http_response_code(400);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Veuillez vérifier le nom et la valeur des filtres",
            ]);
        }
        //ajout des filtres en fonction du champ choisi et de la valeur du triage
        $filters['store_' . $fieldName] = $sortValue;

        return $filters;
    }

    /**
     * fonction récupérant un magasin en fonction de l'identifiant
     * 
     * @param string|null $query requête de triage envoyé par l'utilisateur
     * 
     * @return string|false
     */
    public function getStore(string $id): string|false
    {
        //récupération du magasin et envoi de la confirmation, une erreur sera affichée si la requête n'a pu être faite
        $store = $this->rep->findOneById($id);
        if (!$store) {
            http_response_code(404);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Ressource introuvable",
            ]); 
        }

        return json_encode([
            "status" => "success",
            "code" => 200,
            "store" => [$store]
        ]);
    }

    /**
     * fonction créant un magasin
     * 
     * @return string|false
     */
    public function createStore(): string|false
    {
        //récupération des données de la requête envoyé par le client
        $requestDatas = (array) json_decode(file_get_contents('php://input'));

        //on rsignale une erreur si un dess champs requis pour la création d'un magasin n'est pas présent
        if (
            !isset($requestDatas['store_name']) || 
            !isset($requestDatas['store_adress']) ||
            !isset($requestDatas['store_owner']) ||
            !isset($requestDatas['store_created_at'])
        ) {
            http_response_code(400);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Paramètre manquant",
            ]); 
        }

        //si tout est bon crée le magasin via le repository et on envoie un message de confirmation ou une erreur
        try {
            $this->rep->create($requestDatas);
            http_response_code(201);
            return json_encode([
                "status" => "success",
                "code" => http_response_code(),
                "message" => "Ressource créée",
            ]); 
        } catch (\Exception $e) {
            http_response_code(400);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Erreur lors de la création de la ressource : $e",
            ]); 
        }
    }

    /**
     * fonction mettant à jour au moins un champ lié au magasin
     * 
     * @param string $id identifiant du magasin
     * @return string|false
     */
    public function updateStore(string $id): string|false
    {
        //on vérifie que le magasin concerné existe bien, le cas contraire on envoie une erreur
        $store = $this->rep->findOneById($id);
        if (!$store) {
            http_response_code(404);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Ressource introuvable",
            ]); 
        }

        //récupération des données de la requête envoyé par le client
        $requestDatas = (array) json_decode(file_get_contents('php://input'));

        //on rsignale une erreur si un des champs requis pour la mise à jour d'un magasin n'est pas présent
        if (
            !isset($requestDatas['store_name']) && 
            !isset($requestDatas['store_adress']) &&
            !isset($requestDatas['store_owner']) &&
            !isset($requestDatas['store_created_at'])
        ) {
            http_response_code(400);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Veuillez vérifier qu'au moins un champ requis soit renseigné",
            ]); 
        }

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'PATCH') { // Si le verbe est PATCH, l'utilisateur pourra modifier au moins un champ lié au magasin
                $this->rep->partialUpdate($store, $requestDatas);
            } else { // A contrario (verbe PUT), l'utilisateur devra modifier tous les champs requis
                if (
                    !isset($requestDatas['store_name']) ||
                    !isset($requestDatas['store_adress']) ||
                    !isset($requestDatas['store_owner']) ||
                    !isset($requestDatas['store_created_at'])
                ) {
                    http_response_code(400);
                    return json_encode([
                        "status" => "failed",
                        "code" => http_response_code(),
                        "message" => "Veuillez vérifier que tous les champs requis soient renseignés",
                    ]); 
                }
                $this->rep->completeUpdate($id, $requestDatas);
            }
            
            //mise à jour du magasin en fonction des informations envoyées, une erreur sera envoyée sur la requête échoue
            http_response_code(200);
            return json_encode([
                "status" => "success",
                "code" => http_response_code(),
                "message" => "Ressource $id mis à jour",
            ]); 
        } catch (\Exception $e) {
            http_response_code(500);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Erreur lors de la mise à jour de la ressource : $e",
            ]); 
        }
    }

    /**
     * fonction supprimant le magasin en fonction de l'identifiant choisi
     * 
     * @param string $id identifiant du magasin
     * @return string|false
     */
    public function deleteStore(string $id): string|false
    {
        //on vérifie que le magasin concerné existe bien, le cas contraire on envoie une erreur
        $store = $this->rep->findOneById($id);
        if (!$store) {
            return json_encode([
                "status" => "failed",
                "code" => 404,
                "message" => "Ressource introuvable",
            ]); 
        }

        //suppression du magasin, une erreur sera envoyée sur la requête échoue
        try {
            $this->rep->delete($id);
            http_response_code(201);
            return json_encode([
                "status" => "success",
                "code" => http_response_code(),
                "message" => "Ressource $id supprimée",
            ]); 
        } catch (\Exception $e) {
            http_response_code(500);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Erreur lors de la suppression de la ressource : $e",
            ]); 
        }
    }
}