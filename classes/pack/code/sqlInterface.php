<?php
class Pack_SqlInterface{
    private $conn;

    function __construct(){
        $this->conn = mysqli_connect(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_base);
        if (!$this->conn) EXCEPTOR::die('No conection to database', '/classes/pack/code/sqlInterface.php', 'Sl couldn\'t connect to the sql server.');
    }

    public function getLastObjectId(){
        $result = mysqli_query($this->conn, "SELECT packid FROM Packs ORDER BY packid DESC");
        if (mysqli_num_rows($result) > 0) return mysqli_fetch_assoc($result)['packid'];
        return 0;
    }

    public function insertPack($pack){
        $id = $this->getLastObjectId() + 1;

        if ($this->conn->query("INSERT INTO Packs (packauthor, packidentifier, packname, packimgcount, packinteractionscounter) VALUES ('$pack->packAuthor', '$pack->packIdentifier', '$pack->packName', '$pack->packImgCount', '$pack->packInteractionsCounter')") === TRUE)
            return $id;
        
        EXCEPTOR::die('Invalid Pack Insert', '/classes/pack/code/sqlInterface.php', 'Couldtn\'t commit these changes to Database.');
    }

    public function updatePack($pack){
        if (!$this->PackIdExists($pack->packId)) return $this->insertPack($pack);

        if (mysqli_query($this->conn, "UPDATE Packs SET packinteractionscounter='$pack->packInteractionsCounter' WHERE packid LIKE $pack->packId"))
            return $id;

        EXCEPTOR::die('Invalid Pack Update', '/classes/user/pack/sqlInterface.php', 'Couldtn\'t commit these changes to Database.');
    }

    public function getPack($id){
        if (!$this->PackIdExists($id)) return null;

        $result = mysqli_query($this->conn, "SELECT * FROM Packs WHERE packid LIKE $id");

        if (mysqli_num_rows($result) != 1) return null;

        return mysqli_fetch_assoc($result);
    }

    public function PackIdExists($id){
        $result = mysqli_query($this->conn, "SELECT * FROM Packs WHERE packid LIKE $id");
        return mysqli_num_rows($result) > 0;
    }

    private function dispose(){
        $this->conn = null;
    }
}