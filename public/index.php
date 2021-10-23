<?php

include_once "/var/www/html/sl/autoload.php";

$TC = new Core();
print_r (Router::EXEC($_SERVER['REQUEST_URI'], Router::$GET));