<?php

include_once 'sqlInterface.php';

class User
{
    private $__sql;
    public $userId;
    public $userMail;
    public $userName;
    public $passHash;
    public $profilePic;


    function __construct($id)
    {
        $this->__sql = new User_SqlInterface();
        if ($id != null){
            $this->userId = $id;
            $this->autopopulate($this->__sql->getUser($id));
        }else{
            $this->userId = $this->__sql->getLastObjectId() + 1;
        }
    }

    function autopopulate($user){
        if ($user == null) EXCEPTOR::rDie("{\"data\":{\"message\": \"the query user does not exists\"}}");
        $this->userMail = $user['usermail'];
        $this->userName = $user['username'];
        $this->passHash = $user['passhash'];
        $this->profilePic = $user['profilepic'];
    }
    
    function applyJson($stdObj){
        $this->userName = $stdObj['nusername'] ?? $this->userName;
        $this->profilePic = $stdObj['profilepic'] ?? $this->profilePic;
        $this->passHash = hash('sha256', $stdObj['nusername'] . $stdObj['password']);
    }
    
    function commit(){
        $this->__sql->updateUser($this);
        return $this;
    }

    function getObjectJson(){
        $handle = $this;
        unset($handle->passHash);
        return json_encode($handle);
    }

    function hashId($hash){
        return $this->__sql->getHashId($hash);
    }

    function dataUsed($uname, $umail)
    {
        return ($this->__sql->cmail($umail) || $this->__sql->cname($uname));
    }

    static function newFromSqlData($sqlData){
        $usr = new User(null);
        $usr->userId = $sqlData['userid'];
        $usr->userMail = $sqlData['usermail'];
        $usr->userName = $sqlData['username'];
        $usr->passHash = $sqlData['passhash'];
        $usr->profilePic = $sqlData['profilepic'];
        return $usr;
    }

    static function newFromJson($stdObj){
        $usr = new User(null);
        $usr->userMail = $stdObj['usermail'] ?? '';
        $usr->userName = $stdObj['username'] ?? '';
        $usr->passHash = hash('sha256', $stdObj['username'] . $stdObj['password']);
        $usr->profilePic = $stdObj['profilepic'] ?? '';
        return $usr;
    }

    static function fromHash($hash){
        $usr = new User(null);
        $id = $usr->hashId($hash);
        return ($id == null) ? null : new User($id);
    }
}

function userNew_route(){
    $jsonData = json_decode(file_get_contents('php://input'), true);
    if (!(new User(null))->dataUsed($jsonData['username'], $jsonData['usermail'])){
        $usr = User::newFromJson($jsonData)->commit();
	$usr->userId = strval($usr->userId);
        return $usr->getObjectJson();
    }
    return json_encode(
        array(
            'status' => '200',
            'message' => 'Username or Email is already on use'
        )
    ); 
}

function userLogin_route(){
    $jsonData = json_decode(file_get_contents('php://input'), true);
    
    $usr = User::fromHash(hash('sha256', $jsonData['username'] . $jsonData['password']));

    if ($usr != null){
        return $usr->getObjectJson();
    }

    return json_encode(
        array(
            'status' => '403',
            'message' => 'Wrong credentials'
        )
    );
}

function userGetId_route($id){
    return (new User($id))->getObjectJson();
}

function userUpdateId_route($id){
    $usr = new User($id);
    $jsonData = json_decode(file_get_contents('php://input'), true);

    if ($usr->passHash == hash('sha256', $jsonData['username'] . $jsonData['password'])){
        $usr->applyJson(json_decode(file_get_contents('php://input'), true));
        $usr->commit();
        return $usr->getObjectJson();
    }

    return json_encode(
        array(
            'status' => '403',
            'message' => 'Wrong credentials'
        )
    );
}

function userPassChange_route($id){
    $usr = new User($id);
    $jsonData = json_decode(file_get_contents('php://input'), true);

    $oldPw = hash('sha256', $jsonData['username'] . $jsonData['password']);
    $newPw = hash('sha256', $jsonData['username'] . $jsonData['new_password']);

    if ($usr->passHash == $oldPw){
        $usr->passHash = $newPw;
        $usr->commit();
        return $usr->getObjectJson();
    }
    
    return json_encode(
        array(
            'status' => '403',
            'message' => 'Wrong credentials'
        )
    );
}

Router::routeRegPathSimple('/user/new', Router::$POST, userNew_route);
Router::routeRegPathSimple('/user/login', Router::$POST, userLogin_route);
Router::routeRegPathLast('/user/get/{id}', Router::$GET, userGetId_route);
Router::routeRegPathLast('/user/update/{id}', Router::$POST, userUpdateId_route);
Router::routeRegPathLast('/user/change_password/{id}', Router::$POST, userPassChange_route);
