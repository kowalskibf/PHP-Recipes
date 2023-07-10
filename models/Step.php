<?php

    class Step
    {
        private $id;
        private $recipeId;
        private $number;
        private $description;

        public function __construct($id, $recipeId, $number, $description)
        {
            $this->id = $id;
            $this->recipeId = $recipeId;
            $this->number = $number;
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

        public function getNumber()
        {
            return $this->number;
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

        public function setNumber($number)
        {
            $this->number = $number;
        }

        public function setDescription($desc)
        {
            $this->description = $desc;
        }

        
        public static function getStepsByRecipeId($recipeId)
        {
            global $db;
            //$results = $db->query("SELECT * FROM steps WHERE recipeid='$recipeId'");
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("SELECT * FROM steps WHERE recipeid = ?");
            $sql->bind_param("i", $recipeId);
            $sql->execute();
            $results = $sql->get_result();
            $steps = array();
            while($result = mysqli_fetch_assoc($results))
            {
                $new_step = new Step($result['id'], $result['recipeid'], $result['number'], $result['description']);
                array_push($steps, $new_step);
            }
            return $steps;
        }

        public static function insertStep($recipeId, $number, $description)
        {
            global $db;
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $number = filter_var($number, FILTER_SANITIZE_STRING);
            $description = filter_var($description, FILTER_SANITIZE_STRING);
            //$db->query("INSERT INTO steps VALUES (NULL, '$recipeId', '$number', '$description')");
            $sql = $db->prepare("INSERT INTO steps VALUES (NULL, ?, ?, ?)");
            $sql->bind_param("iis", $recipeId, $number, $description);
            $sql->execute();
        }

        public static function deleteStepByRecipeId($recipeId)
        {
            global $db;
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            $sql = $db->prepare("DELETE FROM steps WHERE recipeid = ?");
            $sql->bind_param("i", $recipeId);
            $sql->execute();
            //$db->query("DELETE FROM steps WHERE recipeid='$recipeId'");
        }

        public static function updateStepsByRecipeId($recipeId, $data)
        {
            global $db;
            $recipeId = filter_var($recipeId, FILTER_SANITIZE_STRING);
            //$db->query("DELETE FROM steps WHERE recipeid='$recipeId'");
            $sql = $db->prepare("DELETE FROM steps WHERE recipeid = ?");
            $sql->bind_param("i", $recipeId);
            $sql->execute();
            $i = 1;
            foreach($data as $value)
            {
                $value = filter_var($value, FILTER_SANITIZE_STRING);
                //$db->query("INSERT INTO steps VALUES (NULL, '$recipeId', '$i', '$value')");
                $sql = $db->prepare("INSERT INTO steps VALUES (NULL, ?, ?, ?)");
                $sql->bind_param("iis", $recipeId, $i, $value);
                $sql->execute();
                $i++;
            }
        }
    }

?>