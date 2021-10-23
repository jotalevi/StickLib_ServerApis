<?php

include_once 'sqlInterface.php';

class Image
{
    private $__sql;
    public $object_id;
    public $refer_id;
    public $image_url;
    public $image_alt;
    public $image_title;
    public $image_type;

    function __construct($id)
    {
        $this->__sql = new Image_SqlInterface();
        if ($id != null){
            $this->object_id = $id;
            $this->autopopulate($this->__sql->getImage($id));
        }else{
            $this->object_id = $this->__sql->getLastObjectId() + 1;
        }
    }

    function autopopulate($image){
        if ($image == null) EXCEPTOR::rDie("{\"data\":{\"message\": \"the query image does not exists\"}}");

        $this->refer_id = $image['refer_id'];
        $this->image_url = $image['image_url'];
        $this->image_alt = $image['image_alt'];
        $this->image_title = $image['image_title'];
        $this->image_type = $image['image_type'];
    }

    function commit(){
        $this->__sql->insertImage($this);
        return $this;
    }

    function getObjectJson(){
        return "
        {
            \"type\": \"TC-Image\",
            \"object_data\":{
                \"object_id\": \"" . $this->object_id . "\",
                \"refer_id\": \"" . $this->refer_id . "\",
                \"title\": \"" . $this->image_title . "\",
                \"type\":\"" . $this->image_type . "\",
                \"url\": \"" . $this->image_url . "\",
                \"alt\": \"" . $this->image_alt . "\"
            }
        }";
    }

    function listFromRefer($refer_id, $type){
        return $this->__sql->getImageFromRefer($refer_id, $type);
    }

    static function newFromSqlData($sqlData){
        $img = new Image(null);
        $img->object_id = $sqlData['object_id'];
        $img->refer_id = $sqlData['refer_id'];
        $img->image_url = $sqlData['image_url'];
        $img->image_alt = $sqlData['image_alt'];
        $img->image_title = $sqlData['image_title'];
        $img->image_type = $sqlData['image_type'];
        return $img;
    }

    static function newFromJson($stdObj){
        $img = new Image(null);
        $img->refer_id = $stdObj['refer_id'] ?? 0;
        $img->image_url = $stdObj['image_url'] ?? '';
        $img->image_alt = $stdObj['image_alt'] ?? '';
        $img->image_title = $stdObj['image_title'] ?? '';
        $img->image_type = $stdObj['image_type'] ?? 'Asset';
        return $img;
    }
}

function imageNew_route(){
    $img = Image::newFromJson(json_decode(file_get_contents('php://input'), true))->commit();
    return $img->getObjectJson();
}

function imageUpload_route(){
    $file_name = $_FILES['image_file']['name'];   
    $temp_file_location = $_FILES['image_file']['tmp_name'];
    $file_type = $_FILES['image_file']['type'];
    $ext = end((explode(".", $file_name)));

    $img = new Image(null);
    $img->refer_id = $_POST['refer_id'] ?? '';
    $img->image_alt = $_POST['image_alt'] ?? '';
    $img->image_title = $_POST['image_title'] ?? '';
    $img->image_type= $_POST['image_type'] ?? '';

    $s3 = new Aws\S3\S3Client([
        'region'  => Config::$aws_access_api_rg,
        'version' => 'latest',
        'credentials' => [
            'key'    => Config::$aws_access_key_id,
            'secret' => Config::$aws_access_key_sc,
        ]
    ]);

    $result = $s3->putObject([
        'Bucket' => Config::$aws_bucket_s3_name,
        'Key'    =>  'assets/' . $img->image_type . '/' . $img->refer_id . '-' . $img->object_id . '.' . $ext,
        'SourceFile' => $temp_file_location,
        'ContentType' => $file_type
    ]);

    $img->image_url = $result['ObjectURL'];
    return ($img->commit())->getObjectJson();

}

function imageGetId_route($id){
    return (new Image($id))->getObjectJson();
}

function imageGetFromReferProduct_route($refer_id){
    $imgList = (new Image(null))->listFromRefer($refer_id, 'Product');
    $arr = array(
        "refer_id" => $refer_id,
        "gallery_length" => count($imgList),
        "images" => $imgList
    );
    return(json_encode($arr));
}

function imageGetFromReferVendor_route($refer_id){
    $imgList = (new Image(null))->listFromRefer($refer_id, 'Vendor');
    $arr = array(
        "refer_id" => $refer_id,
        "gallery_length" => count($imgList),
        "images" => $imgList
    );
    return(json_encode($arr));
}

Router::routeRegPathSimple('/image/new', Router::$POST, imageNew_route);
Router::routeRegPathSimple('/image/upload', Router::$POST, imageUpload_route);
Router::routeRegPathLast('/image/get/{id}', Router::$GET, imageGetId_route);
Router::routeRegPathLast('/image/getfromrefer/Product/{rid}', Router::$GET, imageGetFromReferProduct_route);
Router::routeRegPathLast('/image/getfromrefer/Vendor/{rid}', Router::$GET, imageGetFromReferVendor_route);