<?php

    include 'Ingredient.php';
    include 'Step.php';

    session_start();

    class Recipe
    {
        private $id;
        private $userId;
        private $title;
        private $description;
        private $isPublic;
        private $ingredients;
        private $steps;

        function __construct($id, $userId, $title, $description, $isPublic, $ingredients, $steps)
        {
            $this->id = $id;
            $this->userId = $userId;
            $this->title = $title;
            $this->description = $description;
            $this->isPublic = $isPublic;
            $this->ingredients = $ingredients;
            $this->steps = $steps;
        }

        function getId()
        {
            return $this->id;
        }

        function getUserId()
        {
            return $this->userId;
        }

        function getTitle()
        {
            return $this->title;
        }

        function getDescription()
        {
            return $this->description;
        }

        function getIsPublic()
        {
            return $this->isPublic;
        }

        function getIngredients()
        {
            return $this->ingredients;
        }

        function getSteps()
        {
            return $this->steps;
        }
        
        function setId($id)
        {
            $this->id = $id;
        }
        
        function setUserId($userId)
        {
            $this->userId = $userId;
        }

        function setTitle($title)
        {
            $this->title = $title;
        }

        function setDescription($description)
        {
            $this->description = $description;
        }

        function setIsPublic($isPublic)
        {
            $this->isPublic = $isPublic;
        }

        function setIngredients($ingredients)
        {
            $this->ingredients = $ingredients;
        }

        function setSteps($steps)
        {
            $this->steps = $steps;
        }

        public static function getAllRecipes()
        {
            $userId = $_SESSION['user']->getId();
            global $db;
            $recipes = array();
            $results = $db->query("SELECT * FROM recipes WHERE user_id='$userId'");
            while($result = mysqli_fetch_assoc($results))
            {
                $recipeId = $result['recipe_id'];
                $ingredients = Ingredient::getIngredientsByRecipeId(intval($recipeId));
                $steps = Step::getStepsByRecipeId(intval($recipeId));
                $recipe = new Recipe($recipeId, $userId, $result['title'], $result['description'], $result['public'], $ingredients, $steps);
                array_push($recipes, $recipe);
            }
            return $recipes;
        }

        public static function getRecipeById($id)
        {
            $userId = $_SESSION['user']->getId();
            global $db;
            $sql = $db->prepare("SELECT * FROM recipes WHERE recipe_id = ? AND user_id = ?");
            $sql->bind_param("ii", $id, $userId);
            $sql->execute();
            $result = $sql->get_result()->fetch_assoc();
            $ingredients = Ingredient::getIngredientsByRecipeId($id);
            $steps = Step::getStepsByRecipeId($id);
            $recipe = new Recipe($result['recipe_id'], $userId, $result['title'], $result['description'], $result['public'], $ingredients, $steps);
            return $recipe;
        }

        public static function createRecipe($data)
        {
            $userId = $_SESSION['user']->getId();
            global $db;
            $title = filter_var($data['title'], FILTER_SANITIZE_STRING);
            $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
            $public = filter_var($data['privacy'], FILTER_SANITIZE_STRING);
            $sql = $db->prepare("INSERT INTO recipes VALUES (NULL, ?, ?, ?, ?)");
            $sql->bind_param("issi", $userId, $title, $description, $public);
            $sql->execute();
            $recipeId = $db->insert_id;
            $ingredients = $data['ingredients'];
            $steps = $data['steps'];
            foreach($ingredients as $ingredient)
            {
                if(strlen($ingredient) != 0)
                {
                    Ingredient::insertIngredient($recipeId, $ingredient);
                }
            }
            $number = 1;
            foreach($steps as $step)
            {
                if(strlen($step) != 0)
                {
                    Step::insertStep($recipeId, $number, $step);
                    $number++;
                }
            }
        }

        public static function deleteRecipeById($id)
        {
            $userId = $_SESSION['user']->getId();
            global $db;
            $id = filter_var($id, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM recipes WHERE recipe_id = ? AND user_id = ?");
            $sql->bind_param("ii", $id, $userId);
            $sql->execute();
            if($sql->get_result()->num_rows)
            {
                $sql = $db->prepare("DELETE FROM recipes WHERE recipe_id = ? AND user_id = ?");
                $sql->bind_param("ii", $id, $userId);
                $sql->execute();
                $sql = $db->prepare("DELETE FROM favorites WHERE recipe_id = ?");
                $sql->bind_param("i", $id);
                $sql->execute();
                Ingredient::deleteIngredientByRecipeId($id);
                Step::deleteStepByRecipeId($id);
            }
        }

        public static function updateRecipeById($id, $data)
        {
            $userId = $_SESSION['user']->getId();
            global $db;
            $id = filter_var($id, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM recipes WHERE recipe_id = ? AND user_id = ?");
            $sql->bind_param("ii", $id, $userId);
            $sql->execute();
            $sql->store_result();
            if($sql->num_rows > 0)
            {
                $title = filter_var($data['title'], FILTER_SANITIZE_STRING);
                $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
                $isPublic = filter_var($data['privacy'], FILTER_SANITIZE_STRING);
                $sql = $db->prepare("UPDATE recipes SET title = ?, description = ?, public = ? WHERE recipe_id = ?");
                $sql->bind_param("ssii", $title, $description, $isPublic, $id);
                $sql->execute();
                if(!$isPublic)
                {
                    $sql = $db->prepare("DELETE FROM favorites WHERE recipe_id = ?");
                    $sql->bind_param("i", $id);
                    $sql->execute();
                }
                Ingredient::updateIngredientsByRecipeId($id, $data['ingredients']);
                Step::updateStepsByRecipeId($id, $data['steps']);
            }
        }

        public static function getAllPublicRecipes()
        {
            global $db;
            $recipes = array();
            $results = $db->query("SELECT * FROM recipes WHERE public=1");
            while($result = mysqli_fetch_assoc($results))
            {
                $recipeId = $result['recipe_id'];
                $ingredients = Ingredient::getIngredientsByRecipeId(intval($recipeId));
                $steps = Step::getStepsByRecipeId(intval($recipeId));
                $recipe = new Recipe($recipeId, $result['user_id'], $result['title'], $result['description'], $result['public'], $ingredients, $steps);
                array_push($recipes, $recipe);
            }
            return $recipes;
        }

        public static function getPublicRecipeById($id)
        {
            global $db;
            $id = filter_var($id, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM recipes WHERE recipe_id = ? AND public=1");
            $sql->bind_param("i", $id);
            $sql->execute();
            $result = $sql->get_result();
            if($result->num_rows > 0)
            {
                $result = $result->fetch_assoc();
                $ingredients = Ingredient::getIngredientsByRecipeId($id);
                $steps = Step::getStepsByRecipeId($id);
                $recipe = new Recipe($result['recipe_id'], Recipe::getRecipeUsernameByRecipeId($id), $result['title'], $result['description'], $result['public'], $ingredients, $steps);
                return $recipe;
            }
            return null;
        }

        public static function getRecipeUsernameByRecipeId($recipeId)
        {
            global $db;
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT users.username FROM users LEFT JOIN recipes ON users.user_id=recipes.user_id WHERE recipes.recipe_id = ?");
            $sql->bind_param("i", $recipeId);
            $sql->execute();
            return $sql->get_result()->fetch_assoc()['username'];
        }

        public function getRecipeUsername()
        {
            global $db;
            $userId = $this->getUserId();
            $userId = filter_var($userId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT username FROM users WHERE user_id = ?");
            $sql->bind_param("i", $userId);
            $sql->execute();
            return $sql->get_result()->fetch_assoc()['username'];
        }

        public static function addRecipeToFavorites($recipeId)
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM recipes WHERE recipe_id = ? AND public=1");
            $sql->bind_param("i", $recipeId);
            $sql->execute();
            $isPublic = $sql->get_result()->num_rows;
            $sql2 = $db->prepare("SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?");
            $sql2->bind_param("ii", $userId, $recipeId);
            $sql2->execute();
            $isAlreadyFavorite = $sql2->get_result()->num_rows;
            if($isPublic && !$isAlreadyFavorite)
            {
                $sql = $db->prepare("INSERT INTO favorites VALUES (?, ?)");
                $sql->bind_param("ii", $userId, $recipeId);
                $sql->execute();
            }
        }

        public static function removeRecipeFromFavorites($recipeId)
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
            $sql->bind_param("ii", $userId, $recipeId);
            $sql->execute();
        }

        public static function isRecipeFavorite($recipeId)
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?");
            $sql->bind_param("ii", $userId, $recipeId);
            $sql->execute();
            return $sql->get_result()->num_rows ? true : false;
        }

        public static function getFavoriteRecipes()
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $recipes = array();
            $sql = $db->prepare("SELECT recipes.* FROM recipes LEFT JOIN favorites ON recipes.recipe_id=favorites.recipe_id WHERE favorites.user_id = ?");
            $sql->bind_param("i", $userId);
            $sql->execute();
            $results = $sql->get_result();
            //$results = $db->query("SELECT recipes.* FROM recipes LEFT JOIN favorites ON recipes.id=favorites.recipeid WHERE favorites.userid='$userId'");
            while($result = mysqli_fetch_assoc($results))
            {
                $recipeId = $result['recipe_id'];
                $ingredients = Ingredient::getIngredientsByRecipeId(intval($recipeId));
                $steps = Step::getStepsByRecipeId(intval($recipeId));
                $recipe = new Recipe($recipeId, Recipe::getRecipeUsernameByRecipeId($result['recipe_id']), $result['title'], $result['description'], $result['public'], $ingredients, $steps);
                array_push($recipes, $recipe);
            }
            return $recipes;
        }

    }
?>