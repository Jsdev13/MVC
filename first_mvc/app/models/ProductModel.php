<?php

class ProductEntity
{
    private const NAME_MIN_LENGTH = 3;
    private const PRICE_MIN = 0;
    private const DEFAULT_IMG_URL = "/public/default.png";
    private $name;
    private $price;
    private $image;
    private $id;

    function __construct(string $name, float $price, string $image, int $id = null)
    {
        $this->setName($name);
        $this->setPrice($price);
        $this->setImage($image);
        $this->id = $id;
    }

    public function setName(string $name)
    {
        if (strlen($name) < $this::NAME_MIN_LENGTH) {
            throw new Error("Name is too short minimum 
            length is " . $this::NAME_MIN_LENGTH);
        }
        $this->name = $name;
    }
    public function setPrice(float $price)
    {
        if ($price < 0) {
            throw new Error("Price is too short minimum price is " . $this::PRICE_MIN);
        }
        $this->price = $price;
    }
    public function setImage(string $image)
    {
        if (strlen($image) <= 0) {
            $this->image = $this::DEFAULT_IMG_URL;
        }
        $this->image = $image;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function getPrice(): float
    {
        return $this->price;
    }
    public function getImage(): string
    {
        return $this->image;
    }
    public function getId(): int
    {
        return $this->id;
    }
}


class ProductModel
{
    // PDO instance pour la connexion à la base
    private PDO $bdd;

    private PDOStatement $addProduct;   // Requête préparée pour ajouter un produit
    private PDOStatement $delProduct;   // Requête préparée pour supprimer un produit
    private PDOStatement $getProduct;   // Requête préparée pour obtenir un produit
    private PDOStatement $editProduct;  // Requête préparée pour modifier un produit
    private PDOStatement $getProducts;  // Requête préparée pour obtenir plusieurs produits


    // Constructeur : initialisation de la connexion et des requêtes
    function __construct()
    {
        // Connexion à la base de données MySQL avec PDO
        $this->bdd = new PDO("mysql:host=bdd;dbname=app-database", "root", "root");

        // Requête préparée pour ajouter un produit
        // :name, :price, :image sont des paramètres sécurisés pour éviter les injections SQL
        $this->addProduct = $this->bdd->prepare("INSERT INTO `Produit`
         (name,price,image) VALUES(:name,:price,:image);");

        // Requête préparée pour supprimer un produit par son ID
        $this->delProduct = $this->bdd->prepare("DELETE FROM `Produit`
         WHERE `Produit`.`id` = :id;");

        // Requête préparée pour récupérer un produit par son ID
        $this->getProduct = $this->bdd->prepare("SELECT * FROM 
        `Produit` WHERE `Produit`.`id` = :id;");

        // Requête préparée pour modifier un produit
        $this->editProduct = $this->bdd->prepare("UPDATE `Produit` 
        SET `name` = :name, `price` = :price, `image` = :image WHERE `Produit`.`id` = :id");


        // Requête préparée pour récupérer plusieurs produits avec limite
        //  Note : PDO ne permet pas de lier directement LIMIT avec :limit
        $this->getProducts = $this->bdd->prepare("SELECT * FROM 
        `Produit` LIMIT :limit");

    }
    public function add(string $name, float $price, string $image): void
    {
        $this->addProduct->bindValue("name", $name);
        $this->addProduct->bindValue("price", $price);
        $this->addProduct->bindValue("image", $image);
        $this->addProduct->execute();
    }
    public function del(int $id): void
    {
        $this->delProduct->bindValue("id", $id, PDO::PARAM_INT);
        $this->delProduct->execute();
    }
    public function get($id): ProductEntity|null
    {
        $this->getProduct->bindValue("id", $id, PDO::PARAM_INT);
        $this->getProduct->execute();
        $rawProduct = $this->getProduct->fetch();

        // Si le produit n'existe pas, je renvoi NULL
        if (!$rawProduct) {
            return NULL;
        }
        return new ProductEntity(
            $rawProduct["name"],
            $rawProduct["price"],
            $rawProduct["image"],
            $rawProduct["id"]
        );
    }
    public function getAll(int $limit = 50): array
    {
        $this->getProducts->bindValue("limit", $limit, PDO::PARAM_INT);
        $this->getProducts->execute();
        $rawProducts = $this->getProducts->fetchAll();

        $productsEntity = [];
        foreach ($rawProducts as $rawProduct) {
            $productsEntity[] = new ProductEntity(
                $rawProduct["name"],
                $rawProduct["price"],
                $rawProduct["image"],
                $rawProduct["id"]
            );
        }

        return $productsEntity;
    }

    // A part l'id les paramètres de la méthode edit sont optionnel.
    // Nous ne voulons pas forcer le développeur à modifier tout les champs
    public function edit(
        int $id,
        string $name = NULL,
        float $price = NULL,
        string $image = NULL
    ): ProductEntity|null {
        $originalProductEntity = $this->get($id);

        // Si le produit n'existe pas, je renvoi NULL
        if (!$originalProductEntity) {
            return NULL;
        }
           
        // On uilise un opérateur ternaire ? : ;
        // Il permet en une ligne de renvoyer le nom original du 
        // produit si le paramètre est NULL.
        // En effet si le paramètre est NULL celà veux dire que 
        // l'utilisateur ne souhaite pas le modifier.
        // Le même resultat est possible avec des if else
        $this->editProduct->bindValue(
            "name",
            $name ? $name : $originalProductEntity->getName()
        );
        $this->editProduct->bindValue(
            "price",
            $price ? $price : $originalProductEntity->getPrice()
        );
        $this->editProduct->bindValue(
            "image",
            $image ? $image : $originalProductEntity->getImage()
        );

        // Je précise PDO::PARAM_INT car id est un INT
        $this->editProduct->bindValue("id", $id, PDO::PARAM_INT);

        $this->editProduct->execute();

        // Une fois modifié, je renvoi le produit en utilisant ma
        // propre méthode public ProductModel::get().
        return $this->get($id);
    }
}







?>