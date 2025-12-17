<?php
/**
 * 1. Complétez les requêtes SQL (...TODO...) selon votre table.
 * 2. Les méthodes add et edit doivent être adaptées pour gérer les colonnes spécifiques à votre modèle.
 */

class AdminModel{
    private PDO $bdd;
    private PDOStatement $addAdmin;
    private PDOStatement $delAdmin;
    private PDOStatement $getAdmin;
    private PDOStatement $getAdmins;
    private PDOStatement $editAdmin;

    function __construct()
    {
        $this->bdd = new PDO("mysql:host=bdd;dbname=app-database", "root", "root");

        // Adaptez cette requête à votre table Admin
        $this->addAdmin = $this->bdd->prepare("INSERT INTO `Admin`
         (name,price,image) VALUES(:name,:price,:image);");

        $this->delAdmin = $this->bdd->prepare("DELETE FROM `Admin` WHERE `id` = :id;");

        $this->getAdmin = $this->bdd->prepare("SELECT * FROM `Admin` WHERE `id` = :id;");

        // Adaptez cette requête à votre table Admin
        $this->editAdmin = $this->bdd->prepare("UPDATE `Admin` SET id WHERE `id` = :id");

        $this->getAdmins = $this->bdd->prepare(query: "SELECT * FROM `Admin` LIMIT :limit");
    }
    // Éditez les paramètres de la méthode add en fonction de votre table Admin
    public function add(string $name,string $lastname) : void
    {
        // $this->addAdmin->bindValue("...", $columnValue);
        $this->addAdmin->execute();
    }

    public function del(int $id) : void
    {
        $this->delAdmin->bindValue("id", $id);
        $this->delAdmin->execute();
    }
    public function get($id): AdminEntity | NULL
    {
        $this->getAdmin->bindValue("id", $id, PDO::PARAM_INT);
        $this->getAdmin->execute();
        $rawAdmin = $this->getAdmin->fetch();

        // Si le produit n'existe pas, je renvoie NULL
        if(!$rawAdmin){
            return NULL;
        }
        return new AdminEntity(
            // $rawAdmin["columnName"],
        );
    }

    public function getAll(int $limit = 50) : array
    {
        $this->getAdmins->bindValue("limit", $limit, PDO::PARAM_INT);
        $this->getAdmins->execute();
        $rawAdmins = $this->getAdmins->fetchAll();

        $AdminsEntity = [];
        foreach($rawAdmins as $rawAdmin){
            $AdminsEntity[] = new AdminEntity(
                $rawAdmin["name"],
                $rawAdmin["price"],
                $rawAdmin["image"],
                $rawAdmin["id"]
            );
        }

        return $AdminsEntity;
    }

    // À part l'id, les paramètres de la méthode edit sont optionnels.
    // Nous ne voulons pas forcer le développeur à modifier tous les champs
    public function edit(int $id, string $name = NULL, float $price = NULL, string $image = NULl) : AdminEntity | NULL
    {
        $originalAdminEntity = $this->get($id);

        // Si le produit n'existe pas, je renvoie NULL
        if(!$originalAdminEntity){
            return NULL;
        }

        // On utilise un opérateur ternaire ? : ;
        // Il permet en une ligne de renvoyer le nom original du 
        // produit si le paramètre est NULL.
        // En effet, si le paramètre est NULL, cela veut dire que 
        // l'utilisateur ne souhaite pas le modifier.
        // Le même résultat est possible avec des if else
        // Je précise PDO::PARAM_INT car id est un INT
        $this->editAdmin->bindValue("id", $id, PDO::PARAM_INT);

        // $this->editAdmin->bindValue($columnName,
        //  $columnName ? $columnName : $originalAdminEntity->getColumnName() );

        $this->editAdmin->execute();

        // Une fois modifié, je renvoie le Admin en utilisant ma
        // propre méthode public AdminModel::get().
        return $this->get($id);
    }
}

class AdminEntity{
    private $id;
    private $name;
    // private $columnName;
    function __construct($name,  int $id = NULL, $price, )
    {
        // $this->setColumnName($columnName);
        $this ->name = $name;
        $this->id = $id;
    }

    // public function setColumnName($columnValue){
    //     $this->columnName = $columnValue;
    // }
    public function getName(){return $this->name;}
    
    // public function getColumnName(){
    //     return $this->columnName;
    // }
    
    public function getId() : int{
        return $this->id;
    }
}



