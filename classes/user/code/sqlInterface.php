<?php
class User_SqlInterface{
    private $conn;

    function __construct(){
        $this->conn = mysqli_connect(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_base);
        if (!$this->conn) EXCEPTOR::die('No conection to database', '/classes/user/code/sqlInterface.php', 'Sl couldn\'t connect to the sql server.');
    }

    public function getLastObjectId(){
        $result = mysqli_query($this->conn, "SELECT userid FROM Users ORDER BY userid DESC");
        if (mysqli_num_rows($result) > 0) return mysqli_fetch_assoc($result)['userid'];
        return 0;
    }

    public function insertUser($user){
        $id = $this->getLastObjectId() + 1;
        if ($this->conn->query("INSERT INTO Users (usermail, username, passhash, profilepic) VALUES ('$user->userMail', '$user->userName', '".$user->getPass()."', '$user->profilePic')") === TRUE)
            return $id;
        
        EXCEPTOR::die('Invalid User Insert', '/classes/user/code/sqlInterface.php', 'Couldtn\'t commit these changes to Database.');
    }

    public function updateUser($user){
        if (!$this->UserIdExists($user->userId)) return $this->insertUser($user);

        if (mysqli_query($this->conn, "UPDATE Users SET username='$user->userName', profilepic='$user->profilePic' WHERE userid LIKE $user->userId"))
            return $id;

        EXCEPTOR::die('Invalid Product Update', '/classes/product/code/sqlInterface.php', 'Couldtn\'t commit these changes to Database.');
    }

    public function getUser($id){
        if (!$this->UserIdExists($id)) return null;

        $result = mysqli_query($this->conn, "SELECT * FROM Users WHERE userid LIKE $id");

        if (mysqli_num_rows($result) != 1) return null;

        return mysqli_fetch_assoc($result);
    }

    public function UserIdExists($id){
        $result = mysqli_query($this->conn, "SELECT * FROM Users WHERE userid LIKE $id");
        return mysqli_num_rows($result) > 0;
    }

    private function dispose(){
        $this->conn = null;
    }
}