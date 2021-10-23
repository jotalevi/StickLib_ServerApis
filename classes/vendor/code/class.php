<?php

include_once 'sqlInterface.php';

class Vendor
{    
    private $__sql;
    public $object_id;
    public $address_id;
    public $name;
    public $run;
    public $phone;
    public $mail;
    public $logo_url;

    function __construct($id)
    {
        $this->__sql = new Vendor_SqlInterface();
        if ($id != null){
            $this->object_id = $id;
            $this->autopopulate($this->__sql->getVendor($id));
        }else{
            $this->object_id = $this->__sql->getLastObjectId() + 1;
        }
    }

    function autopopulate($vendor){
        if ($vendor == null) EXCEPTOR::rDie("{\"data\":{\"message\": \"the query vendor does not exists\"}}");

        $this->address_id = $vendor['address_id'];
        $this->name = $vendor['name'];
        $this->run = $vendor['run'];
        $this->phone = $vendor['phone'];
        $this->mail = $vendor['mail'];
        $this->logo_url = $vendor['logo_url'];
    }

    function commit(){
        $this->__sql->updateVendor($this->object_id, $this);
        return $this;
    }

    function applyJson($stdObj){
        $this->address_id = $stdObj['address_id'] ?? $this->address_id;
        $this->name = $stdObj['name'] ?? $this->name;
        $this->run = $stdObj['run'] ?? $this->run;
        $this->phone = $stdObj['phone'] ?? $this->phone;
        $this->mail = $stdObj['mail'] ?? $this->mail;
        $this->logo_url = $stdObj['logo_url'] ?? $this->logo_url;
    }

    function getObjectJson(){
        return "
        {
            \"type\": \"TC-Vendor\",
            \"object_data\":{
                \"object_id\": \"". $this->object_id . "\",
                \"address_id\": \"". $this->address_id . "\",
                \"name\": \"". $this->name . "\",
                \"run\": \"". $this->run . "\",
                \"phone\": \"". $this->phone . "\",
                \"mail\": \"". $this->mail . "\",
                \"logo_url\": \"". $this->logo_url . "\",
            }
        }";
    }

    static function newFromJson($stdObj){
        $prd = new Vendor(null);
        $prd->address_id = $stdObj['address_id'] ?? 1;
        $prd->name = $stdObj['name'] ?? "New Vendor";
        $prd->run = $stdObj['run'] ?? "00.000.000-0";
        $prd->phone = $stdObj['phone'] ?? "+562 2222 2222";
        $prd->mail = $stdObj['mail'] ?? "default@tc.com";
        $prd->logo_url = $stdObj['logo_url'] ?? Config::$site_base_url . "assets/defaults/vendor.png";
        return $prd;
    }

}

function vendorNew_route(){
    $vnd = Vendor::newFromJson(json_decode(file_get_contents('php://input'), true))->commit();
    return $vnd->getObjectJson();
}

function vendorUpdate_route(){
    $stdObj = json_decode(file_get_contents('php://input'), true);
    $prd = new Vendor($stdObj['object_id']);
    $prd->applyJson($stdObj['update_fields']);
    return ($prd->commit())->getObjectJson();
}

function vendorGetId_route($id){
    return (new Vendor($id))->getObjectJson();
}

Router::routeRegPathSimple('/vendor/new', Router::$POST, vendorNew_route);
Router::routeRegPathSimple('/vendor/update', Router::$POST, vendorUpdate_route);
Router::routeRegPathLast('/vendor/get/{id}', Router::$GET, vendorGetId_route);