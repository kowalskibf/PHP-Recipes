<?php

    require_once 'models/User.php';

    class UserController
    {

        public function getLoginPage()
        {
            require_once 'views/login.php';
        }

        public function getRegisterPage()
        {
            require_once 'views/register.php';
        }

        public function login($username, $password)
        {
            $user = User::getUserByUsername($username);
            if(strlen($username) > 0 && strlen($password) > 0)
            {
                if($user && password_verify($password, $user->getPassword()))
                {
                    session_start();
                    $_SESSION['user'] = $user;
                    header("Location: index.php?action=myRecipes");
                }
                else
                {
                    echo 'Błędna nazwa użytkownika lub hasło.';
                    $this->getLoginPage();
                }
            }
            else
            {
                echo 'Nazwa użytkownika oraz hasło nie mogą być puste!';
                $this->getLoginPage();
            }
        }

        public function register($username, $password)
        {
            if(strlen($username) > 0 && strlen($username) < 32 && strlen($password) > 0 && strlen($password) < 32)
            {
                if(!User::isUsernameAvailable($username))
                {
                    echo 'Nazwa użytkownika jest już zajęta.';
                    $this->getRegisterPage();
                }
                else
                {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $user = new User(0, $username, $hashedPassword);
                    $user->register();
                    echo 'Rejestracja zakończona pomyślnie!';
                    $this->login($username, $password);
                }
            }
            else
            {
                echo 'Długości nazwy użytkownika oraz hasła muszą mieścić się w przedziale 1 - 31 znaków!';
                $this->getRegisterPage();
            }
        }

        public function logout()
        {
            session_destroy();
            header("Location: index.php?action=login");
        }
    }

?>
