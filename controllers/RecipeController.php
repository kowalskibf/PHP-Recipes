<?php

    require_once 'models/Recipe.php';

    class RecipeController
    {

        public static function getAllRecipes()
        {
            $recipes = Recipe::getAllRecipes();
            $favoriteRecipes = Recipe::getFavoriteRecipes();
            $allPublicRecipes = Recipe::getAllPublicRecipes();
            require 'views/recipe/index.php';
        }

        public static function createRecipe()
        {
            if($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                $recipeData = $_POST['recipe'];
                Recipe::createRecipe($recipeData);
                header('Location: index.php');
            }
            else
            {
                $myIngredients = Ingredient::getMyIngredients();
                require 'views/recipe/create.php';
            }
        }

        public function editRecipe()
        {
            $recipeId = $_GET['id'];
            if($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                $recipeData = $_POST['recipe'];
                Recipe::updateRecipeById($recipeId, $recipeData);
                header('Location: index.php');
            }
            else
            {
                $recipe = Recipe::getRecipeById($recipeId);
                require 'views/recipe/edit.php';
            }
        }

        public function deleteRecipe()
        {
            $recipeId = $_GET['id'];
            Recipe::deleteRecipeById($recipeId);
            header('Location: index.php');
        }

        public function viewRecipe()
        {
            $recipeId = $_GET['id'];
            $recipe = Recipe::getRecipeById($recipeId);
            require 'views/recipe/view.php';
        }

        public function viewPublicRecipe()
        {
            $recipeId = $_GET['id'];
            if($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                if(isset($_GET['addToFav']))
                {
                    Recipe::addRecipeToFavorites($recipeId);
                    echo 'Dodano przepis do ulubionych!';
                }
                else if(isset($_GET['removeFromFav']))
                {
                    Recipe::removeRecipeFromFavorites($recipeId);
                    echo 'Usunięto przepis z ulubionych!';
                }
            }
            $recipe = Recipe::getPublicRecipeById($recipeId);
            $favorite = Recipe::isRecipeFavorite($recipeId);
            require 'views/recipe/viewPublic.php';
        }

        public function viewMyIngredients()
        {
            if($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                if(isset($_POST['id']))
                {
                    Ingredient::deleteIngredientById($_POST['id']);
                }
                else
                {
                    $ingredient = $_POST['ingredient'];
                    Ingredient::insertSingleIngredient($ingredient);
                    echo 'Dodano składnik do Twoich składników!';
                }
            }
            $ingredients = Ingredient::getMyIngredients();
            foreach($ingredients as $ingredient)
            {
                $useCount[spl_object_hash($ingredient)] = Ingredient::getIngredientUseCountById($ingredient->getId());
            }
            require 'views/recipe/myIngredients.php';
        }
    }

?>
