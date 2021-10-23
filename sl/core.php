<?php

Class Core
{
    
    private $db;
    public $get;
    public $post;
    public static $tcClasses = [];
    
    function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;

        $this->db = new DBInteractor();
    }
}


function dotForbiden_route(){
    return ("
        {\"data\":{
            \"status\": \"200\",
            \"message\": \"This route is forbiden\"
        }}
    ");
}

function dotInfo_route(){
    return ("
        {\"data\":{
            \"status\": \"200\",
            \"message\": \"toneCore ALFA V0.2\"
        }}
    ");
}

function dotInfoPaths_route(){

    $retStr = "{\"data\":{";
    $allRoutes = [];

    foreach (Router::$routes_get as $route){
        $retStr .= "\"get-" . str_replace('}', '', str_replace('{', '', str_replace('/', '', $route['path']))) . "\":{\"path\":\"" . $route['path'] . "\",";
        if ($route['params'] != null){
            $retStr .= "\"params\":{";
            $int = 0;
            foreach ($route['params'] as $param){
                $retStr .= "\"$int\":\"$param\"";
                $int += 1;
                $retStr .= count($route['params']) == $int ? '' : ',';
            }
            $retStr .= "},";
        }
        $retStr .= "\"method\":\"GET\",";
        $retStr .= "\"callback\":\"" . $route['callback'] . "\"},";
    }

    foreach (Router::$routes_post as $route){
        $retStr .= "\"post-" . str_replace('/', '', $route['path']) . "\":{\"path\":\"" . $route['path'] . "\",";
        if ($route['params'] != null){
            $retStr .= "\"params\":{";
            $int = 0;
            foreach ($route['params'] as $param){
                $retStr .= "\"$int\":\"$param\"";
                $int += 1;
                $retStr .= count($route['params']) == $int ? '' : ',';
            }
            $retStr .= "},";
        }
        $retStr .= "\"method\":\"POST\",";
        $retStr .= "\"callback\":\"" . $route['callback'] . "\"},";
    }

    return (substr($retStr, 0, strlen($retStr) - 1) . "}}");
}

Router::routeRegPathSimple('/', Router::$GET, dotForbiden_route);
Router::routeRegPathSimple('/info', Router::$GET, dotInfo_route);
Router::routeRegPathSimple('/info/paths', Router::$GET, dotInfoPaths_route);
