<?php 

include_once "/var/www/html/tone/exceptor.php";
include_once "/var/www/html/tone/routes.php";

foreach (glob("/var/www/html/tone/*.php") as $filename)
    include_once $filename;

foreach (glob("/var/www/html/classes/*/code/class.php") as $filename)
    include_once $filename;

//  plugin implementation is due to 18th Oct.
//  foreach (glob("/var/www/html/plugins/*/register.php") as $filename)
//      include_once $filename;
