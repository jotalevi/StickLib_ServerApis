<?php
class Product_SqlInterface{
    private $conn;

    function __construct(){
        $this->conn = mysqli_connect(Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_base);
        if (!$this->conn) EXCEPTOR::die('No conection to database', '/classes/product/code/sqlInterface.php', 'ToneCore couldn\'t connect to the sql server.');
    }

    public function getLastObjectId(){
        $result = mysqli_query($this->conn, "SELECT object_id FROM Products ORDER BY object_id DESC");
        if (mysqli_num_rows($result) > 0) return mysqli_fetch_assoc($result)['object_id'];
        return 0;
    }

    public function insertProduct($product){
        $id = $this->getLastObjectId() + 1;
        if ($this->conn->query("INSERT INTO Products (title, short_description, full_description, reference_price, sale_price, cost_price, stock_quantity, weight, sku, vendor, category, active, promot, brand_id, unity_name, unity_price, front_image) VALUES (\"$product->title\", \"$product->short_description\", \"$product->full_description\", $product->reference_price, $product->sale_price, $product->cost_price, $product->stock_quantity, $product->weight, \"$product->sku\", $product->vendor_id, $product->category_id, $product->is_active, $product->is_promot, $product->brand_id, \"$product->unity_name\", $product->unity_price, \"$product->front_image\")") === TRUE)
            return $id;
        
        EXCEPTOR::die('Invalid Product Insert', '/classes/product/code/sqlInterface.php', 'Couldtn\'t commit these changes to Database.');
    }

    public function updateProduct($id, $product){
        if (!$this->productIdExists($id))
            return $this->insertProduct($product);
        
        if (mysqli_query($this->conn, "UPDATE Products SET title='$product->title', short_description='".$product->short_description."', full_description='".$product->full_description."', reference_price=$product->reference_price, sale_price=$product->sale_price, cost_price=$product->cost_price, stock_quantity=$product->stock_quantity, weight=$product->weight, sku='".$product->sku."', vendor=$product->vendor_id, category=$product->category_id, active=$product->is_active, promot=$product->is_promot, brand_id=$product->brand_id, unity_name=$product->unity_name, unity_price=$product->unity_price, front_image=$product->front_image WHERE object_id LIKE $id"))
            return $id;
            
        EXCEPTOR::die('Invalid Product Update', '/classes/product/code/sqlInterface.php', 'Couldtn\'t commit these changes to Database.');
    }

    public function getProduct($id){
        if (!$this->productIdExists($id)) return null;
        $result = mysqli_query($this->conn, "SELECT * FROM Products WHERE object_id LIKE $id");
        if (mysqli_num_rows($result) != 1) return null;
        return mysqli_fetch_assoc($result);
    }

    public function getProductSearchStr($str){
        $result = mysqli_query($this->conn, "SELECT * FROM Products WHERE title LIKE '%$str%' or sku LIKE '%$str%'");
        if (mysqli_num_rows($result) < 1) return null;
        
        $prdList = [];
        while ($row = mysqli_fetch_assoc($result)){
            $prdList[] = Product::newFromSqlData($row);
        }
        return $prdList;
    }

    public function productIdExists($id){
        $result = mysqli_query($this->conn, "SELECT * FROM Products WHERE object_id LIKE $id");
        return mysqli_num_rows($result) > 0;
    }

    private function dispose(){
        $this->conn = null;
    }
}