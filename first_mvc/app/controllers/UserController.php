<?php
// Remplacez Name par le nom du controller
class UserController{
    public function view(string $method, array $params = []){
        try {
            call_user_func([$this, $method], $params);
        } catch (Error $e) {
            require_once(__DIR__.'/../views/404.php');
            // ou bien la méthode par défaut...
        }
    }

     public function profile() {
        require_once(__DIR__ . '/../views/user-profile.php');
    }

    public function settings() {
        require_once(__DIR__ . '/../views/user-settings.php');
    }
    
    // Remplacez methodName par le nom d'une méthode
    //public function methodName($params = []){
        // Remplacez vue-name par le nom de la vue
    //    require_once(__DIR__.'/../views/vue-name.php');
    //}
}