<?php

require '../vendor/autoload.php';

session_start();
use Router\Routes;

//appel de la fonction correspondant à l'url renseignée
try {
    //ajout automatique des classes requises pour fonctionner l'API
    $router = new Routes();
    echo $router->resolve($_SERVER['REQUEST_URI']);
} catch (Exception $e) {
    echo json_encode([
        "status" => "failed",
        "code" => 404,
        "message" => "Ressource introuvable : " . $e->getMessage(),
    ]);
}