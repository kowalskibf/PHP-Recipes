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
            $id = filter_var($id, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM ingredients WHERE id = ?");
            $sql->bind_param("i", $id);
            $sql->execute();
            $result = $sql->get_result();
            //$result = $db->query("SELECT * FROM ingredients WHERE id='$id'")->fetch_assoc();
            $ingredient = new Ingredient($result['ingredient_id'], $result['recipe_id'], $result['description']);
            return $ingredient;
        }

        public static function getIngredientsByRecipeId($recipeId)
        {
            global $db;
            $ingredients = array();
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT ingredients.* FROM ingredients LEFT JOIN ingredients_to_recipes ON ingredients.ingredient_id = ingredients_to_recipes.ingredient_id WHERE ingredients_to_recipes.recipe_id = ?");
            $sql->bind_param("i", $recipeId);
            $sql->execute();
            $results = $sql->get_result();
            while($result = mysqli_fetch_assoc($results))
            {
                array_push($ingredients, new Ingredient($result['ingredient_id'], $result['user_id'], $result['description']));
            }
            return $ingredients;
        }

        public static function insertIngredient($recipeId, $description)
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $description = filter_var($description, FILTER_SANITIZE_STRING);
            //$db->query("INSERT INTO ingredients VALUES (NULL, '$recipeId', '$description')");
            $sql = $db->prepare("SELECT description FROM ingredients WHERE user_id = ? AND description = ?");
            $sql->bind_param("is", $userId, $description);
            $sql->execute();
            if($sql->get_result()->num_rows == 0)
            {
                unset($sql);
                $sql = $db->prepare("INSERT INTO ingredients VALUES (NULL, ?, ?)");
                $sql->bind_param("is", $userId, $description);
                $sql->execute();
                $ingredientId = $db->insert_id;
            }
            else
            {
                unset($sql);
                $sql = $db->prepare("SELECT ingredient_id FROM ingredients WHERE user_id = ? AND description = ?");
                $sql->bind_param("is", $userId, $description);
                $sql->execute();
                $ingredientId = $sql->get_result()->fetch_assoc()['ingredient_id'];
            }
            unset($sql);
            $sql = $db->prepare("SELECT * FROM ingredients_to_recipes WHERE ingredient_id = ? AND recipe_id = ?");
            $sql->bind_param("ii", $ingredientId, $recipeId);
            $sql->execute();
            if($sql->get_result()->num_rows == 0)
            {
                unset($sql);
                $sql = $db->prepare("INSERT INTO ingredients_to_recipes VALUES (?, ?)");
                $sql->bind_param("ii", $ingredientId, $recipeId);
                $sql->execute();
            }
        }

        public static function deleteIngredientById($ingredientId)
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $ingredientId = filter_var($ingredientId, FILTER_SANITIZE_STRING);
            if(Ingredient::getIngredientUseCountById($ingredientId) == 0)
            {
                $sql = $db->prepare("DELETE FROM ingredients WHERE user_id = ? AND ingredient_id = ?");
                $sql->bind_param("ii", $userId, $ingredientId);
                $sql->execute();
            }
        }

        public static function deleteIngredientByRecipeId($recipeId)
        {
            global $db;
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
           // $sql = $db->prepare("SELECT ingredients.ingredient_id FROM ingredients LEFT JOIN ingredients_to_recipes ON ingredients.ingredient_id = ingredients_to_recipes.ingredient_id WHERE ingredients_to_recipes.recipe_id = ?");
            $sql = $db->prepare("DELETE FROM ingredients_to_recipes WHERE recipe_id = ?");
            $sql->bind_param("i", $recipeId);
            $sql->execute();
        }

        public static function updateIngredientsByRecipeId($recipeId, $data)
        {
            global $db;
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            //$db->query("DELETE FROM ingredients WHERE recipeid='$recipeId'");
            Ingredient::deleteIngredientByRecipeId($recipeId);
            foreach($data as $value)
            {
                $value = filter_var($value, FILTER_SANITIZE_STRING);
                Ingredient::insertIngredient($recipeId, $value);
            }
        }

        public static function getMyIngredients()
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $ingredients = array();
            $sql = $db->prepare("SELECT * FROM ingredients WHERE user_id = ?");
            $sql->bind_param("i", $userId);
            $sql->execute();
            $results = $sql->get_result();
            while($result = mysqli_fetch_assoc($results))
            {
                $ingredient = new Ingredient($result['ingredient_id'], $result['user_id'], $result['description']);
                array_push($ingredients, $ingredient);
            }
            return $ingredients;
        }

        public static function insertSingleIngredient($ingredient)
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $ingredient = filter_var($ingredient, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM ingredients WHERE user_id = ? AND description = ?");
            $sql->bind_param("is", $userId, $ingredient);
            $sql->execute();
            if($sql->get_result()->num_rows == 0)
            {
                unset($sql);
                $sql = $db->prepare("INSERT INTO ingredients VALUES (NULL, ?, ?)");
                $sql->bind_param("is", $userId, $ingredient);
                $sql->execute();
            }
        }

        public static function getIngredientUseCountById($ingredientId)
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $ingredientId = filter_var($ingredientId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT COUNT(*) AS total FROM ingredients_to_recipes WHERE ingredient_id = ?");
            $sql->bind_param("i", $ingredientId);
            $sql->execute();
            return $sql->get_result()->fetch_assoc()['total'];
        }
    }

?>