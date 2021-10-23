<?php

include_once 'sqlInterface.php';

class User
{
    private $__sql;
    public $userId;
    public $userMail;
    public $userName;
    private $passHash;
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
        $this->setPass = $user['passhash'];
        $this->profilePic = $user['profilepic'];
    }
    
    function applyJson($stdObj){
        $this->userName = $stdObj['username'] ?? $this->userName;
        $this->profilePic = $stdObj['profilepic'] ?? $this->profilePic;
    }
    
    function commit(){
        $this->__sql->updateUser($this);
        return $this;
    }

    function getObjectJson(){
        return json_encode($this);
    }

    function setPass($hash){
        $this->passHash = $hash;
    }

    function getPass(){
        return $this->passHash;
    }

    function hashId($hash){
        return $this->__sql->getHashId($hash);
    }

    static function newFromSqlData($sqlData){
        $usr = new User(null);
        $usr->userId = $sqlData['userid'];
        $usr->userMail = $sqlData['usermail'];
        $usr->userName = $sqlData['username'];
        $usr->setPass($sqlData['passhash']);
        $usr->profilePic = $sqlData['profilepic'];
        return $usr;
    }

    static function newFromJson($stdObj){
        $usr = new User(null);
        $usr->userMail = $stdObj['usermail'] ?? '';
        $usr->userName = $stdObj['username'] ?? '';
        $usr->setPass(hash('sha256', $jsonData['username'] . $jsonData['password']) ?? '');
        $usr->profilePic = $stdObj['profilepic'] ?? '';
        return $usr;
    }

    static function fromHash($hash){
        $usr = new User(null);
        return new User($usr->hashId($hash));
    }
}

function userNew_route(){
    $usr = User::newFromJson(json_decode(file_get_contents('php://input'), true))->commit();
    return $usr->getObjectJson();
}

function userLogin_route(){
    $jsonData = json_decode(file_get_contents('php://input'), true);

    
    $usr = User::fromHash(hash('sha256', $jsonData['username'] . $jsonData['password']));
    if ($usr->userId != 0){
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

    if ($user->getPass() == hash('sha256', $jsonData['username'] . $jsonData['password'])){
        ($usr->applyJson(json_decode(file_get_contents('php://input'), true)))->commit();
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

    if ($user->getPass() == $oldPw){
        $usr->setPass($newPw);
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
Router::routeRegPathSimple('/user/login', Router::$GET, userLogin_route);
Router::routeRegPathLast('/user/get/{id}', Router::$GET, userGetId_route);
Router::routeRegPathLast('/user/update/{id}', Router::$POST, userUpdateId_route);
Router::routeRegPathLast('/user/change_password/{id}', Router::$POST, userPassChange_route);