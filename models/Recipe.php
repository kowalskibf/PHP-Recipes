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
            $results = $db->query("SELECT * FROM recipes WHERE userid='$userId'");
            while($result = mysqli_fetch_assoc($results))
            {
                $recipeId = $result['id'];
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
            $sql = $db->prepare("SELECT * FROM recipes WHERE id = ? AND userid = ?");
            $sql->bind_param("ii", $id, $userId);
            $sql->execute();
            $result = $sql->get_result()->fetch_assoc();
            //$result = $db->query("SELECT * FROM recipes WHERE id='$id' AND userid='$userId'")->fetch_assoc();
            $ingredients = Ingredient::getIngredientsByRecipeId($id);
            $steps = Step::getStepsByRecipeId($id);
            $recipe = new Recipe($result['id'], $userId, $result['title'], $result['description'], $result['public'], $ingredients, $steps);
            return $recipe;
        }

        public static function createRecipe($data)
        {
            $userId = $_SESSION['user']->getId();
            global $db;
            $title = filter_var($data['title'], FILTER_SANITIZE_STRING);
            $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
            $public = filter_var($data['privacy'], FILTER_SANITIZE_STRING);
            //$db->query("INSERT INTO recipes VALUES (NULL, '$userId', '$title', '$description', '$public')");
            $sql = $db->prepare("INSERT INTO recipes VALUES (NULL, ?, ?, ?, ?)");
            $sql->bind_param("issi", $userId, $title, $description, $public);
            $sql->execute();
            $recipeId = $db->insert_id;
            $ingredients = $data['ingredients'];
            $steps = $data['steps'];
            foreach($ingredients as $ingredient)
            {
                Ingredient::insertIngredient($recipeId, $ingredient);
            }
            $number = 1;
            foreach($steps as $step)
            {
                Step::insertStep($recipeId, $number, $step);
                $number++;
            }
        }

        public static function deleteRecipeById($id)
        {
            $userId = $_SESSION['user']->getId();
            global $db;
            $id = filter_var($id, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM recipes WHERE id = ? AND userid = ?");
            $sql->bind_param("ii", $id, $userId);
            $sql->execute();
            if($sql->get_result()->num_rows)
            //if(mysqli_num_rows($db->query("SELECT * FROM recipes WHERE id='$id' AND userid='$userId'")))
            {
                //$db->query("DELETE FROM recipes WHERE id='$id' AND userid='$userId'");
                $sql = $db->prepare("DELETE FROM recipes WHERE id = ? AND userid = ?");
                $sql->bind_param("ii", $id, $userId);
                $sql->execute();
                //$db->query("DELETE FROM favorites WHERE recipeid='$id'");
                $sql = $db->prepare("DELETE FROM favorites WHERE recipeid = ?");
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
            $sql = $db->prepare("SELECT * FROM recipes WHERE id = ? AND userid = ?");
            $sql->bind_param("ii", $id, $userId);
            $sql->execute();
            $sql->store_result();
            if($sql->num_rows > 0)
            //if(mysqli_num_rows($db->query("SELECT * FROM recipes WHERE id='$id' AND userid='$userId'")))
            {
                $title = filter_var($data['title'], FILTER_SANITIZE_STRING);
                $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
                $isPublic = filter_var($data['privacy'], FILTER_SANITIZE_STRING);
                //$db->query("UPDATE recipes SET title='$title', description='$description', public='$isPublic' WHERE id='$id'");
                $sql = $db->prepare("UPDATE recipes SET title = ?, description = ?, public = ? WHERE id = ?");
                $sql->bind_param("ssii", $title, $description, $isPublic, $id);
                $sql->execute();
                if(!$isPublic)
                {
                    //$db->query("DELETE FROM favorites WHERE recipeid='$id'");
                    $sql = $db->prepare("DELETE FROM favorites WHERE recipeid = ?");
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
                $recipeId = $result['id'];
                $ingredients = Ingredient::getIngredientsByRecipeId(intval($recipeId));
                $steps = Step::getStepsByRecipeId(intval($recipeId));
                $recipe = new Recipe($recipeId, $result['userid'], $result['title'], $result['description'], $result['public'], $ingredients, $steps);
                array_push($recipes, $recipe);
            }
            return $recipes;
        }

        public static function getPublicRecipeById($id)
        {
            global $db;
            $id = filter_var($id, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM recipes WHERE id = ? AND public=1");
            $sql->bind_param("i", $id);
            $sql->execute();
            $result = $sql->get_result();
            //$result = $db->query("SELECT * FROM recipes WHERE id='$id' AND public=1");
            if($result->num_rows > 0)
            {
                $result = $result->fetch_assoc();
                $ingredients = Ingredient::getIngredientsByRecipeId($id);
                $steps = Step::getStepsByRecipeId($id);
                $recipe = new Recipe($result['id'], Recipe::getRecipeUsernameByRecipeId($id), $result['title'], $result['description'], $result['public'], $ingredients, $steps);
                return $recipe;
            }
            return null;
        }

        public static function getRecipeUsernameByRecipeId($recipeId)
        {
            global $db;
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT users.username FROM users LEFT JOIN recipes ON users.id=recipes.userid WHERE recipes.id = ?");
            $sql->bind_param("i", $recipeId);
            $sql->execute();
            return $sql->get_result()->fetch_assoc()['username'];
            //return $db->query("SELECT users.username FROM users LEFT JOIN recipes ON users.id=recipes.userid WHERE recipes.id='$recipeId'")->fetch_assoc()['username'];
        }

        public function getRecipeUsername()
        {
            global $db;
            $userId = $this->getUserId();
            $userId = filter_var($userId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT username FROM users WHERE id = ?");
            $sql->bind_param("i", $userId);
            $sql->execute();
            return $sql->get_result()->fetch_assoc()['username'];
            //return $db->query("SELECT username FROM users WHERE id='$userId'")->fetch_assoc()['username'];
        }

        public static function addRecipeToFavorites($recipeId)
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM recipes WHERE id = ? AND public=1");
            $sql->bind_param("i", $recipeId);
            $sql->execute();
            $isPublic = $sql->get_result()->num_rows;
            $sql2 = $db->prepare("SELECT * FROM favorites WHERE userid = ? AND recipeid = ?");
            $sql2->bind_param("ii", $userId, $recipeId);
            $sql2->execute();
            $isAlreadyFavorite = $sql2->get_result()->num_rows;
            if($isPublic && !$isAlreadyFavorite)
            //if($sql->get_result()->num_rows && !$sql2->get_result()->num_rows)
            //if(mysqli_num_rows($db->query("SELECT * FROM recipes WHERE id='$recipeId' AND public=1")))
            {
                $sql = $db->prepare("INSERT INTO favorites VALUES (NULL, ?, ?)");
                $sql->bind_param("ii", $userId, $recipeId);
                $sql->execute();
                //$db->query("INSERT INTO favorites VALUES (NULL, '$userId', '$recipeId')");
            }
        }

        public static function removeRecipeFromFavorites($recipeId)
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("DELETE FROM favorites WHERE userid = ? AND recipeid = ?");
            $sql->bind_param("ii", $userId, $recipeId);
            $sql->execute();
            //$db->query("DELETE FROM favorites WHERE userid='$userId' AND recipeid='$recipeId'");
        }

        public static function isRecipeFavorite($recipeId)
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM favorites WHERE userid = ? AND recipeid = ?");
            $sql->bind_param("ii", $userId, $recipeId);
            $sql->execute();
            return $sql->get_result()->num_rows ? true : false;
            /*if(mysqli_num_rows($db->query("SELECT * FROM favorites WHERE userid='$userId' AND recipeid='$recipeId'")))
            {
                return true;
            }
            return false;*/
        }

        public static function getFavoriteRecipes()
        {
            global $db;
            $userId = $_SESSION['user']->getId();
            $recipes = array();
            $sql = $db->prepare("SELECT recipes.* FROM recipes LEFT JOIN favorites ON recipes.id=favorites.recipeid WHERE favorites.userid = ?");
            $sql->bind_param("i", $userId);
            $sql->execute();
            $results = $sql->get_result();
            //$results = $db->query("SELECT recipes.* FROM recipes LEFT JOIN favorites ON recipes.id=favorites.recipeid WHERE favorites.userid='$userId'");
            while($result = mysqli_fetch_assoc($results))
            {
                $recipeId = $result['id'];
                $ingredients = Ingredient::getIngredientsByRecipeId(intval($recipeId));
                $steps = Step::getStepsByRecipeId(intval($recipeId));
                $recipe = new Recipe($recipeId, Recipe::getRecipeUsernameByRecipeId($result['id']), $result['title'], $result['description'], $result['public'], $ingredients, $steps);
                array_push($recipes, $recipe);
            }
            return $recipes;
        }

    }
?>