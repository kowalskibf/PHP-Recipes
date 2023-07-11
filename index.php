<head>
  <meta charset="UTF-8">
</head>

<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once 'db.php';
    global $db;
    $db = @new mysqli($host, $db_user, $db_password, $db_name);

    require_once 'controllers/UserController.php';
    require_once 'controllers/RecipeController.php';

    $userController = new UserController();
    $recipeController = new RecipeController();

    $action = isset($_GET['action']) ? $_GET['action'] : 'index';
    $logged = isset($_SESSION['user']);
    $method = $_SERVER['REQUEST_METHOD'];

    if($logged)
    {
        switch($action)
        {
            case 'logout':
                $userController->logout();
                break;
            case 'home':
                $recipeController->getAllRecipes();
                break;
            case 'create':
                $recipeController->createRecipe();
                break;
            case 'view':
                $recipeController->viewRecipe();
                break;
            case 'viewPublic':
                $recipeController->viewPublicRecipe();
                break;
            case 'edit':
                $recipeController->editRecipe();
                break;
            case 'delete':
                $recipeController->deleteRecipe();
                break;
            default:
                $recipeController->getAllRecipes();
                break;
        }
    }
    else
    {
        switch($action)
        {
            case 'login':
                if($method === 'GET')
                {
                    $userController->getLoginPage();
                }
                elseif($method === 'POST')
                {
                    $userController->login($_POST['username'], $_POST['password']);
                }
                break;
            case 'register':
                if($method === 'GET')
                {
                    $userController->getRegisterPage();
                }
                elseif($method === 'POST')
                {
                    $userController->register($_POST['username'], $_POST['password']);
                }
                break;
            default:
                $userController->getLoginPage();
                break;
        }
    }

?>
