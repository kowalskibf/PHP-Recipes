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

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if ($action === 'login') {
            $userController->getLoginPage();
        } elseif ($action === 'register') {
            $userController->getRegisterPage();
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'login') {
            $userController->login($_POST['username'], $_POST['password']);
        } elseif ($action === 'register') {
            $userController->register($_POST['username'], $_POST['password']);
        }
    }
    
    /*if(!isset($_SESSION['user']))
    {
        if($action == 'register')
        {
            $userController->getRegisterPage();
        }
        else
        {
            $userController->getLoginPage();
        }
    }
    else*/ if(isset($_SESSION['user']))
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

?>
