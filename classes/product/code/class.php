<?php

include_once 'sqlInterface.php';

class Product
{

    private $__sql;
    public $object_id = "";
    public $vendor_id = "";
    public $category_id = "";
    public $brand_id = "";
    public $title = "";
    public $short_description = "";
    public $full_description = "";
    public $reference_price = "";
    public $sale_price = "";
    public $cost_price = "";
    public $stock_quantity = "";
    public $weight = "";
    public $sku = "";
    public $is_active = 0;
    public $is_promot = 0;
    public $unity_name = "";
    public $unity_price = 0;
    public $front_image = "";


    function __construct($id){
        $this->__sql = new Product_SqlInterface();
        if ($id != null){
            $this->object_id = $id;
            $this->autopopulate($this->__sql->getProduct($id));
        }else{
            $this->object_id = $this->__sql->getLastObjectId() + 1;
        }
    }

    function autopopulate($product){
        
        if ($product == null) EXCEPTOR::rDie("{\"data\":{\"message\": \"the query product does not exists\"}}");

        $this->vendor_id = $product['vendor'];
        $this->category_id = $product['category'];
        $this->brand_id = $product['brand'];
        $this->title = $product['title'];
        $this->short_description = $product['short_description'];
        $this->full_description = $product['full_description'];
        $this->reference_price = $product['reference_price'];
        $this->sale_price = $product['sale_price'];
        $this->cost_price = $product['cost_price'];
        $this->unity_price = $product['unity_price'];
        $this->stock_quantity = $product['stock_quantity'];
        $this->weight = $product['weight'];
        $this->sku = $product['sku'];
        $this->is_active = $product['active'];
        $this->is_promot = $product['promot'];
        $this->unity_name = $product['unity_name'];
        $this->front_image = $product['front_image'];
    }

    function commit(){
        $this->__sql->updateProduct($this->object_id, $this);
        return $this;
    }

    function getObjectJson(){
        return "
        {
            \"type\": \"TC-Product\",
            \"object_data\":{
                \"object_id\": \"" . $this->object_id . "\",
                \"vendor_id\": \"" . $this->vendor_id . "\",
                \"category_id\": \"" . $this->category_id . "\",
                \"brand_id\": \"" . $this->brand_id . "\",
                \"title\": \"" . $this->title . "\",
                \"short_description\": \"" . $this->short_description . "\",
                \"full_description\": \"" . $this->full_description . "\",
                \"reference_price\": \"" . $this->reference_price . "\",
                \"sale_price\": \"" . $this->sale_price . "\",
                \"cost_price\": \"" . $this->cost_price . "\",
                \"unity_price\": \"" . $this->unity_price . "\",
                \"stock_quantity\": \"" . $this->stock_quantity . "\",
                \"front_image\": \"" . $this->front_image . "\",
                \"weight\": \"" . $this->weight . "\",
                \"active\": \"" . $this->is_active . "\",
                \"promot\": \"" . $this->is_promot . "\",
                \"unity_name\": \"" . $this->unity_name . "\",
                \"sku\": \"" . $this->sku . "\",
            }
        }";
    }

    function applyJson($stdObj){
        $this->vendor_id = $stdObj['vendor_id'] ?? $this->vendor_id;
        $this->category_id = $stdObj['category_id'] ?? $this->category_id;
        $this->brand_id = $stdObj['brand_id'] ?? $this->brand_id;
        $this->title = $stdObj['title'] ?? $this->title;
        $this->short_description = $stdObj['short_description'] ?? $this->short_description;
        $this->full_description = $stdObj['full_description'] ?? $this->full_description;
        $this->reference_price = $stdObj['reference_price'] ?? $this->reference_price;
        $this->sale_price = $stdObj['sale_price'] ?? $this->sale_price;
        $this->cost_price = $stdObj['cost_price'] ?? $this->cost_price;
        $this->unity_price = $stdObj['unity_price'] ?? $this->unity_price;
        $this->stock_quantity = $stdObj['stock_quantity'] ?? $this->stock_quantity;
        $this->weight = $stdObj['weight'] ?? $this->weight;
        $this->sku = $stdObj['sku'] ?? $this->sku;
        $this->is_active = $stdObj['active'] ?? $this->is_active;
        $this->is_promot = $stdObj['promot'] ?? $this->is_promot;
        $this->unity_name = $stdObj['unity_name'] ?? $this->unity_name;
        $this->front_image = $stdObj['front_image'] ?? $this->front_image;
    }

    function listFromSearchString($searchString){
        return $this->__sql->getProductSearchStr($searchString);
    }

    static function newFromJson($stdObj){
        $prd = new Product(null);
        $prd->vendor_id = $stdObj['vendor_id'] ?? 1;
        $prd->category_id = $stdObj['category_id'] ?? 1;
        $prd->brand_id = $stdObj['brand_id'] ?? 1;
        $prd->title = $stdObj['title'] ?? "NO TITLE";
        $prd->short_description = $stdObj['short_description'] ?? "NO SHORT DESCRIPTION";
        $prd->full_description = $stdObj['full_description'] ?? "NO DESCRIPTION";
        $prd->reference_price = $stdObj['reference_price'] ?? 0;
        $prd->sale_price = $stdObj['sale_price'] ?? 0;
        $prd->cost_price = $stdObj['cost_price'] ?? 0;
        $prd->unity_price = $stdObj['unity_price'] ?? 0;
        $prd->stock_quantity = $stdObj['stock_quantity'] ?? 0;
        $prd->weight = $stdObj['weight'] ?? 0;
        $prd->sku = $stdObj['sku'] ?? "TC::NULLSKU:SKUCODE";
        $prd->is_active = $stdObj['active'] ?? 0;
        $prd->is_promot = $stdObj['promot'] ?? 0;
        $prd->unity_name = $stdObj['unity_name'] ?? 'unidad';
        $prd->front_image = $stdObj['front_image'] ?? '';
        return $prd;
    }

    static function newFromSqlData($sqlData){
        $prd = new Product(null);
        $prd->object_id = $sqlData['object_id'];
        $prd->vendor_id = $sqlData['vendor'];
        $prd->category_id = $sqlData['category'];
        $prd->brand_id = $sqlData['brand_id'] ?? 0;
        $prd->title = $sqlData['title'];
        $prd->short_description = $sqlData['short_description'];
        $prd->full_description = $sqlData['full_description'];
        $prd->reference_price = $sqlData['reference_price'];
        $prd->sale_price = $sqlData['sale_price'];
        $prd->cost_price = $sqlData['cost_price'];
        $prd->unity_price = $sqlData['unity_price'] ?? $prd->sale_price;
        $prd->stock_quantity = $sqlData['stock_quantity'];
        $prd->weight = $sqlData['weight'];
        $prd->sku = $sqlData['sku'];
        $prd->is_active = $sqlData['active'];
        $prd->is_promot = $sqlData['promot'];
        $prd->unity_name = $sqlData['unity_name'] ?? 'unidad';
        $prd->front_image = $sqlData['front_image'] ?? '';
        return $prd;
    }
}

function productNew_route(){
    $prd = Product::newFromJson(json_decode(file_get_contents('php://input'), true))->commit();
    return $prd->getObjectJson();
}

function productUpdate_route(){
    $stdObj = json_decode(file_get_contents('php://input'), true);
    $prd = new Product($stdObj['object_id']);
    $prd->applyJson($stdObj['update_fields']);
    return $prd->commit()->getObjectJson();
}

function productGetId_route($id){
    return (new Product($id))->getObjectJson();
}

function productGetFromSearchString_route($searchString){
    $prdList = (new Product(null))->listFromSearchString($searchString);
    $arr = array(
        "search_string" => $searchString,
        "result_count" => count($prdList),
        "results" => $prdList
    );
    return json_encode($arr);
}

function productGetFireBaseRelStrFromSearchString_route($searchString){
    $prdList = (new Product(null))->listFromSearchString($searchString);
    $arr = [];
    foreach($prdList as $prd_){
        $arr[] = array(
            $prd_->object_id => $prd_,
        );
        
    }
    $str = json_encode($arr);
    return(substr($str, 1, strlen($str) - 1));
}

function productAll_route(){
    $prdList = (new Product(null))->listFromSearchString($searchString);
    $linkedPrdList = [];
    foreach ($prdList as $prd){
        $linkedPrdList[$prd->object_id] = $prd;
    }
    return json_encode($linkedPrdList);
}

Router::routeRegPathSimple('/product/new', Router::$POST, productNew_route);
Router::routeRegPathSimple('/product/update', Router::$POST, productUpdate_route);
Router::routeRegPathSimple('/product/all', Router::$GET, productAll_route);
Router::routeRegPathLast('/product/get/{id}', Router::$GET, productGetId_route);
Router::routeRegPathLast('/product/search/{string}', Router::$GET, productGetFromSearchString_route);