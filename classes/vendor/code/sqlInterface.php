<?php
class Vendor_SqlInterface{
    private $conn;

    function __construct(){
        $this->conn = mysqli_connect(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_base);
        if (!$this->conn) EXCEPTOR::die('No conection to database', '/classes/vendor/code/sqlInterface.php', 'ToneCore couldn\'t connect to the sql server.');
    }

    public function getLastObjectId(){
        $result = mysqli_query($this->conn, "SELECT object_id FROM Vendors ORDER BY object_id DESC");
        if (mysqli_num_rows($result) > 0) return mysqli_fetch_assoc($result)['object_id'];
        return 0;
    }

    public function insertVendor($vendor){
        $id = $this->getLastObjectId() + 1;
        if ($this->conn->query("INSERT INTO Vendors (address_id, name, run, phone, mail, logo_url) VALUES ($vendor->address_id, \"$vendor->name\", \"$vendor->run\", \"$vendor->phone\", \"$vendor->mail\", \"$vendor->logo_url\")") === TRUE)
            return $id;
        
        EXCEPTOR::die('Invalid Vendor Insert', '/classes/vendor/code/sqlInterface.php', 'Couldtn\'t commit these changes to Database.');
    }

    public function updateVendor($id, $vendor){
        if (!$this->vendorIdExists($id))
            return $this->insertVendor($vendor);
        
        print_r("updateVendor called");
        if (mysqli_query($this->conn, "UPDATE Vendors SET address_id=$vendor->address_id, name=\"$vendor->name\", run=\"$vendor->run\", phone=\"$vendor->phone\", mail=\"$vendor->mail\", logo_url=\"$vendor->logo_url\" WHERE object_id LIKE $id"))
            return $id;
            
        EXCEPTOR::die('Invalid Vendor Update', '/classes/vendor/code/sqlInterface.php', 'Couldtn\'t commit these changes to Database.');
    }

    public function getVendor($id){
        if (!$this->vendorIdExists($id)) return null;
        $result = mysqli_query($this->conn, "SELECT * FROM Vendors WHERE object_id LIKE $id");
        if (mysqli_num_rows($result) != 1) return null;
        return mysqli_fetch_assoc($result);
    }

    public function vendorIdExists($id){
        $result = mysqli_query($this->conn, "SELECT * FROM Vendors WHERE object_id LIKE $id");
        return mysqli_num_rows($result) > 0;
    }

    private function dispose(){
        $this->conn = null;
    }
}