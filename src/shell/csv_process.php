<?php

require_once "/home/carlos/www/magento/app/Mage.php";

function convert($size)
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}


set_time_limit(0);


Mage::app('admin', 'store', array('global_ban_use_cache'=>true))->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$start_time = microtime(true);

ini_set('display_errors',true);
error_reporting(E_ALL);

$count = 0;

$file_name = $argv[1];


