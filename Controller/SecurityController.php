<?php

namespace Controller;

use EventSubscriber\LogSubscriber;
use Event\LogEvent;
use Model\Log;
use Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SecurityController {

    private UserRepository $rep;

    public function __construct()
    {
        $this->rep = new UserRepository();
    }

    /**
     * Fonction vérifiant que l'utilisateur a bien fourni un token et que ce dernier correspond à celui fourni après l'authentification
     * 
     * @return bool
     */
    public static function checkToken(): bool
    {
        if ($_SERVER['HTTP_AUTHORIZATION'] === null || $_SERVER['HTTP_AUTHORIZATION'] !== $_SESSION['token']) {
            return false;
        }
        return true;
    }

    /**
     * fonction retournant un token si l'authentification est réussie, si cette dernière échoue un message d'erreur sera affiché
     * 
     * @return string|false
     */
    public function login(): string|false
    {
        //vérification du verbe de la requête
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "verbe de la requête invalide",
            ]); 
        }

        //récupération des données de la requête et vérification de la présence de tous les champs
        $requestDatas = (array) json_decode(file_get_contents('php://input'));
        if (
            !isset($requestDatas['email']) || 
            !isset($requestDatas['password']) 
        ) {
            http_response_code(400);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "login / mot de passe requis",
            ]); 
        }
        $user = $this->rep->findUserByEmail($requestDatas['email']);

        //si l'utilisateur n'existe pas ou que dernier existe mais que son mot de passe rensignée est invalide, on envoie un message d'erreur
        if (!$user || !password_verify($requestDatas['password'], $user['password'])) {
            http_response_code(400);
            return json_encode([
                "status" => "failed",
                "code" => http_response_code(),
                "message" => "Erreur d'authentification, vérifier le mail ou le mot de passe"
            ]);
        }

        //création du token et sauvegarde dans une variable session
        $token = bin2hex(random_bytes(32));
        $_SESSION['token'] = 'Bearer ' . $token;

        $log = new Log('login', 'user logged');
        $logEvent = new LogEvent($log);
        $logSubscriber = new LogSubscriber($logEvent);
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($logSubscriber);
        $eventDispatcher->dispatch($logEvent, LogEvent::LOGIN);
        
        return json_encode([
            "status" => "success",
            "code" => 200,
            "token" => $token
        ]);
    }
}