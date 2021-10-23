<?php
class Image_SqlInterface{
    private $conn;

    function __construct(){
        $this->conn = mysqli_connect(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_base);
        if (!$this->conn) EXCEPTOR::die('No conection to database', '/classes/image/code/sqlInterface.php', 'ToneCore couldn\'t connect to the sql server.');
    }

    public function getLastObjectId(){
        $result = mysqli_query($this->conn, "SELECT object_id FROM Images ORDER BY object_id DESC");
        if (mysqli_num_rows($result) > 0) return mysqli_fetch_assoc($result)['object_id'];
        return 0;
    }

    public function insertImage($image){
        $id = $this->getLastObjectId() + 1;
        if ($this->conn->query("INSERT INTO Images (refer_id, image_url, image_title, image_alt, image_type) VALUES ($image->refer_id, '$image->image_url', '$image->image_title', '$image->image_alt', '$image->image_type')") === TRUE)
            return $id;
        
        EXCEPTOR::die('Invalid Image Insert', '/classes/image/code/sqlInterface.php', 'Couldtn\'t commit these changes to Database.');
    }

    public function getImage($id){
        if (!$this->ImageIdExists($id)) return null;
        $result = mysqli_query($this->conn, "SELECT * FROM Images WHERE object_id LIKE $id");
        if (mysqli_num_rows($result) != 1) return null;
        return mysqli_fetch_assoc($result);
    }

    public function ImageIdExists($id){
        $result = mysqli_query($this->conn, "SELECT * FROM Images WHERE object_id LIKE $id");
        return mysqli_num_rows($result) > 0;
    }

    public function getImageFromRefer($refer_id, $type){
        $result = mysqli_query($this->conn, "SELECT * FROM Images WHERE refer_id LIKE '%$refer_id%' AND image_type LIKE '$type'");
        if (mysqli_num_rows($result) < 1) return null;
        $img_list = [];
        while ($row = mysqli_fetch_assoc($result)){
            $img_list[] = Image::newFromSqlData($row);
        }
        
        return $img_list;
    }

    private function dispose(){
        $this->conn = null;
    }
}