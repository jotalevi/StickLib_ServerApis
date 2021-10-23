<?php

class Router{

    public static $ANY = 'ANY';
    public static $GET = 'GET';
    public static $POST = 'POST';
    
    public static $routes_get = [];
    public static $routes_post = [];

    //routeRegPathDirty
    //routeRegPathLinked
    //routeRegPathParallel
    public static function routeRegPathSimple($path, $method, $callback){
        
        if ($method == Router::$ANY){
            Router::routeRegPathSimple($path, Router::$GET, $callback);
            Router::routeRegPathSimple($path, Router::$POST, $callback);
            return;
        }

        if ($method == Router::$GET ? isset(Router::$routes_get[$path]) : isset(Router::$routes_post[$path])){
            EXCEPTOR::die('This method is already registered', '/sl/routes.php:routeRegPathSimple', 'The following calback path or method is already registered \'' . $method . ':' . $path . '\'');
            return;
        }

        $handle = array(
            'path' => $path,
            'callback' => $callback,
            'param' => null,
        );
        $method == Router::$GET ? Router::$routes_get[$path] = $handle : Router::$routes_post[$path] = $handle;
    }

    public static function routeRegPathLast($path, $method, $callback){

        if ($method == Router::$ANY){
            Router::routeRegPathLast($path, Router::$GET, $callback);
            Router::routeRegPathLast($path, Router::$POST, $callback);
            return;
        }

        if ($method == Router::$GET ? isset(Router::$routes_get[$path]) : isset(Router::$routes_post[$path])){
            EXCEPTOR::die('This method is already registered', '/sl/routes.php:routeRegPathLast', 'The following calback path or method is already registered \'' . $method . ':' . $path . '\'');
            return;
        }

        $paramList = [];
        $paramList[] = end(explode('/', $path));

        $handle = array(
            'path' => $path,
            'callback' => $callback,
            'param' => $paramList,
        );
        $method == Router::$GET ? Router::$routes_get[$path] = $handle : Router::$routes_post[$path] = $handle;
    }

    public static function getxtSpecificRoutes($path, $method){
        $routeDepth = count(explode('/', substr($path, 1)));
        
        $routesDepthFiltered = [];
        foreach (Router::$routes_get as $route)
            if (count(explode('/', substr($route['path'], 1))) == $routeDepth && !in_array($route, $routesDepthFiltered) && explode('/', substr($route['path'], 1))[0] == explode('/', substr($path, 1))[0])
                $routesDepthFiltered[] = $route;

        foreach (Router::$routes_post as $route)
            if (count(explode('/', substr($route['path'], 1))) == $routeDepth && !in_array($route, $routesDepthFiltered) && explode('/', substr($route['path'], 1))[0] == explode('/', substr($path, 1))[0])
                $routesDepthFiltered[] = $route;
        
        $routesStrpsFiltered = [];
        foreach ($routesDepthFiltered as $route){
            $i = 0;
            foreach (explode('/', substr($route['path'], 1)) as $routeStr){

                if ($routeStr != explode('/', substr($path, 1))[$i] && strpos($routeStr, '{') === false)
                    break;
                    
                $i += 1;
            }

            if ($i == $routeDepth)
                return $route;
        }
    }

    public static function EXEC($path, $method){
        try{
            $handle = Router::getxtSpecificRoutes($path, $method);
            if ($handle['param'] != null)
                return $handle['callback'](end(explode('/', substr($path, 1))));
            return $handle['callback']();

            throw new Exception('err');

        }catch(Exception $e){
            EXCEPTOR::die('Error on callback for this path or method', '/sl/routes.php::24', 'The following callback path or method is not registered \'' . $method . ':' . $path . '\'');
        }
    }
}