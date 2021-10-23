<?php 

require "vendor/autoload.php";
include_once "/var/www/html/sl/exceptor.php";
include_once "/var/www/html/sl/routes.php";

foreach (glob("/var/www/html/sl/*.php") as $filename)
    include_once $filename;

foreach (glob("/var/www/html/classes/*/code/class.php") as $filename)
    include_once $filename;