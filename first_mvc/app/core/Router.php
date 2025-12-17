<?php

require_once(__DIR__ . "/../controllers/ProductController.php");
require_once(__DIR__ . "/../controllers/HomeController.php");
require_once(__DIR__ . "/../controllers/NotFoundController.php");
require_once(__DIR__ . "/../controllers/AdminController.php");

class Router
{
    public static function getController(string $controllerName): HomeController|NotFoundController|ProductController|AdminController
    {
        switch ($controllerName) {
            // Si la route est /product 
            case 'product':
                // Je renvoi le controleur ProductController
                return new ProductController();
            case '':
                return new HomeController();
                
            case 'admin':
                return new AdminController();

            default:
                return new NotFoundController();

        }
    }
}



