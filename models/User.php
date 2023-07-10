<?php

    class User
    {
        private $id;
        private $username;
        private $password;
        public $db;

        public function __construct($id, $username, $password)
        {
            $this->id = $id;
            $this->username = $username;
            $this->password = $password;
            $this->db = @new mysqli("localhost", "root", "", "usosprzepisy");
        }

        public function getId()
        {
            return $this->id;
        }

        public function getusername()
        {
            return $this->username;
        }

        public function getPassword()
        {
            return $this->password;
        }

        public function setId($id)
        {
            $this->id = $id;
        }

        public function setusername($username)
        {
            $this->username = $username;
        }

        public function setPassword($password)
        {
            $this->password = $password;
        }

        public static function getUserByUsername($username)
        {
            global $db;
            $username = filter_var($username, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM users WHERE username = ?");
            $sql->bind_param("s", $username);
            $sql->execute();
            $result = $sql->get_result();
            //$result = $db->query("SELECT * FROM users WHERE username='$username'");
            if(mysqli_num_rows($result))
            {
                $result = $result->fetch_assoc();
                return new User($result['id'], $result['username'], $result['password']);
            }
            return null;
        }

        public static function isUsernameAvailable($username)
        {
            global $db;
            $username = filter_var($username, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM users WHERE username = ?");
            $sql->bind_param("s", $username);
            $sql->execute();
            $result = $sql->get_result();
            return $result->num_rows > 0 ? false : true;
            //return $db->query("SELECT * FROM users WHERE username='$username'")->num_rows > 0 ? false : true;
        }

        public function register()
        {
            if($this->isUsernameAvailable($this->username))
            {
                global $db;
                $username = $this->username;
                $password = $this->password;
                $username = filter_var($username, FILTER_SANITIZE_STRING);
                $password = filter_var($password, FILTER_SANITIZE_STRING);
                $sql = $db->prepare("INSERT INTO users VALUES (NULL, ?, ?)");
                $sql->bind_param("ss", $username, $password);
                $sql->execute();
                //$db->query("INSERT INTO users VALUES (NULL, '$username', '$password')");
            }
        }

    }

?>