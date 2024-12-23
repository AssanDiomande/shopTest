<?php

namespace App;

class Loader
{
    /**
     * chargement automatique des classes requises pour fonctionner le projet
     * le but étant de minimiser au plus l'utilisation de l'instruction 'require_once'
     **/ 
    public static function load() {
        spl_autoload_register(function($class) {
            $path = dirname(__DIR__) . '/' . str_replace('\\','/',$class) . '.php';
            if (file_exists($path)) {
                require_once($path);
            }
        });
    }
}