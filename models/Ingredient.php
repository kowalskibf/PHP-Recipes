<?php

    class Ingredient
    {
        private $id;
        private $recipeId;
        private $description;

        public function __construct($id, $recipeId, $description)
        {
            $this->id = $id;
            $this->recipeId = $recipeId;
            $this->description = $description;
        }

        public function getId()
        {
            return $this->id;
        }

        public function getRecipeId()
        {
            return $this->recipeId;
        }

        public function getDescription()
        {
            return $this->description;
        }

        public function setId($id)
        {
            $this->id = $id;
        }

        public function setRecipeId($recipeId)
        {
            $this->recipeId = $recipeId;
        }

        public function setDescription($desc)
        {
            $this->description = $desc;
        }

        public static function getIngredientById($id)
        {
            global $db;
            $result = $db->query("SELECT * FROM ingredients WHERE id='$id'")->fetch_assoc();
            $ingredient = new Ingredient($result['id'], $result['recipeid'], $result['description']);
            return $ingredient;
        }

        public static function getIngredientsByRecipeId($recipeId)
        {
            global $db;
            $ingredients = array();
            $results = $db->query("SELECT * FROM ingredients WHERE recipeid='$recipeId'");
            while($result = mysqli_fetch_assoc($results))
            {
                $new_ingredient = new Ingredient($result['id'], $result['recipeid'], $result['description']);
                array_push($ingredients, $new_ingredient);
            }
            return $ingredients;
        }

        public static function insertIngredient($recipeId, $description)
        {
            global $db;
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $description = filter_var($description, FILTER_SANITIZE_STRING);
            //$db->query("INSERT INTO ingredients VALUES (NULL, '$recipeId', '$description')");
            $sql = $db->prepare("INSERT INTO ingredients VALUES (NULL, ?, ?)");
            $sql->bind_param("is", $recipeId, $description);
            $sql->execute();
        }

        public static function deleteIngredientByRecipeId($recipeId)
        {
            global $db;
            $db->query("DELETE FROM ingredints WHERE recipeid='$recipeId'");
        }

        public static function updateIngredientsByRecipeId($recipeId, $data)
        {
            global $db;
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            //$db->query("DELETE FROM ingredients WHERE recipeid='$recipeId'");
            $sql = $db->prepare("DELETE FROM ingredients WHERE recipeid = ?");
            $sql->bind_param("i", $recipeId);
            $sql->execute();
            foreach($data as $value)
            {
                $value = filter_var($value, FILTER_SANITIZE_STRING);
                //$db->query("INSERT INTO ingredients VALUES (NULL, '$recipeId', '$value')");
                $sql = $db->prepare("INSERT INTO ingredients VALUES (NULL, ?, ?)");
                $sql->bind_param("is", $recipeId, $value);
                $sql->execute();
            }
        }
    }

?>