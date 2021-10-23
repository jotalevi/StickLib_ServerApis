<?php

class DBInteractor
{
    private $host;
    private $user;
    private $pass;
    private $port;
    private $table;
    private $connection;

    function __construct()
    {
        $this->host = Config::$db_host;
        $this->user = Config::$db_user;
        $this->pass = Config::$db_pass;
        $this->port = Config::$db_port;
        $this->base = Config::$db_base;
    }

    function get($table, $fields, $where = null, $like = null){
        if ($where == null && $like == null){
            $query = "SELECT $fields FROM $table;";
        }else{
            $query = "SELECT $fields FROM $table WHERE $where LIKE $like;";
        }
    }

    function update($table, $paramList, $where, $like){
        $pl = "";
        foreach ($paramList as $param=>$value){
            $pl .= "'$param' = '$value',";
        }
        $pl = substr($pl, 0, strlen($pl) - 1);
        $query = "UPDATE $table SET $pl WHERE $where LIKE $like;";
    }

    function insert($table, $paramList){
        if ($paramList == null){
            $query = "INSERT INTO $table";
            $this->connection->query($query);
            return $this->connection->query("SELECT object_id FROM $table WHERE ID = (SELECT IDENT_CURRENT('$table'))");
        }
        $params = "(";
        $values = "(";
        foreach ($paramList as $param=>$value){
            $params .= "'$param ',";
            $values .= "'$value ',";
        }
        $params = substr($params, 0, strlen($params) - 1) . ')';
        $values = substr($values, 0, strlen($values) - 1) . ')';
    }

    function exists($table, $oid){
        //TODO: Implement DBCHECK
        return false;
    }
}