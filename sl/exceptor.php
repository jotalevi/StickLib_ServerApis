<?php

class EXCEPTOR{
    static function throw($exeptionTitle, $file, $message){
        $outstr = '[' . date("d-m-Y (D) H:i:s", time()) . '] ' . $exeptionTitle . ' on file ' . $file . ' -> ' . $message . ".\n";
        echo $outstr;
        error_log($outstr);
    }

    static function die($exeptionTitle, $file, $message){
        $outstr = '[' . date("d-m-Y (D) H:i:s", time()) . '] ' . $exeptionTitle . ' on file ' . $file . ' -> ' . $message . ".\n";
        echo $outstr;
        error_log($outstr);
        die;
    }

    static function rDie($output){
        echo $output;
        die;
    }
}