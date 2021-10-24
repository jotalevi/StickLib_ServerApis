<?php

include_once 'sqlInterface.php';

class Pack
{
    private $__sql;
    public $packId;
    public $packAuthor;
    public $packIdentifier;
    public $packName;
    public $packImgCount;
    public $packInteractionsCounter;

    function __construct($id)
    {
        $this->__sql = new Pack_SqlInterface();
        if ($id != null){
            $this->packId = $id;
            $this->autopopulate($this->__sql->getPack($id));
        }else{
            $this->packId = $this->__sql->getLastObjectId() + 1;
        }
    }

    function autopopulate($pack){
        if ($pack == null) EXCEPTOR::rDie("{\"data\":{\"message\": \"the query pack does not exists\"}}");

        $this->packAuthor = $pack['packauthor'];
        $this->packName = $pack['packname'];
        $this->packImgCount = $pack['packimgcount'];
        $this->packIdentifier = $pack['packidentifier'];
        $this->packInteractionsCounter = strval($pack['packinteractionscounter'] ?? 0);
    }
    
    function commit(){
        $this->__sql->updatePack($this);
        return $this;
    }

    function getObjectJson(){
        return json_encode($this);
    }

    static function newFromSqlData($sqlData){
        $pack = new Pack(null);

        $pack->packAuthor = $sqlData['packauthor'];
        $pack->packName = $sqlData['packname'];
        $pack->packImgCount = $sqlData['packimgcount'];
        $pack->packIdentifier = $sqlData['packidentifier'];
        $pack->packInteractionsCounter = strval($sqlData['packinteractionscounter'] ?? 0);
        
        return $pack;
    }

    static function newFromJson($stdObj){
        $pack = new Pack(null);

        $pack->packAuthor = $stdObj['packauthor'];
        $pack->packName = $stdObj['packname'];
        $pack->packImgCount = $stdObj['packimgcount'];
        $pack->packIdentifier = $pack->packId . $pack->packAuthor . (new User($pack->packAutho))->userName;
        $pack->packInteractionsCounter = "0";
        
        return $pack;
    }

    static function newFromPostInfo(){
        $pack = new Pack(null);

        $pack->packAuthor = $_POST['packauthor'];
        $pack->packName = $_POST['packname'];
        $pack->packImgCount = $_POST['packimgcount'];
        $pack->packIdentifier = $pack->packId . $pack->packAuthor . (new User($pack->packAuthor))->userName;
        $pack->packInteractionsCounter = "0";
        
        return $pack;
    }
}

function uploadImgFile($id, $packFolder){
    $file_name = $_FILES['image_file' . $id]['name'];   
    $temp_file_location = $_FILES['image_file' . $id]['tmp_name'];
    $file_type = $_FILES['image_file' . $id]['type'];
    $ext = end(explode(".", $file_name));

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
        'Key'    =>  $packFolder . '/' . $id . '.' . $ext,
        'SourceFile' => $temp_file_location,
        'ContentType' => $file_type
    ]);
}

function packNew_route(){
    $pack = Pack::newFromPostInfo(json_decode(file_get_contents('php://input'), true))->commit();

    for ($i = 0; $i < $pack->packImgCount; $i+= 1){
        uploadImgFile($i, $pack->packIdentifier);
    }

    uploadImgFile('_trayicon', $pack->packIdentifier);

    return $pack->getObjectJson();
}

function packGetId_route($id){
    return (new Pack($id))->getObjectJson();
}

function packIncrementInteraction_route($id){
    $pack = new Pack($id);
    $pack->packInteractionsCounter += 1;
    $pack->commit();
    return $pack->getObjectJson();
}

Router::routeRegPathSimple('/pack/new', Router::$POST, packNew_route);
Router::routeRegPathLast('/pack/get/{id}', Router::$GET, packGetId_route);
Router::routeRegPathLast('/pack/incrementcounter/{id}', Router::$GET, packIncrementInteraction_route);