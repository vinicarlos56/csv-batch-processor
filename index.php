<?php

ini_set('display_errors',true);
error_reporting(E_ALL);

require_once __DIR__.'/vendor/autoload.php'; // load composer
require_once "/home/carlos/www/magento/app/Mage.php";

use Processors\Magento\ProductImporter;
use Keboola\Csv\CsvFile;
use Slim\Slim;

$mage = Mage::app('admin', 'store', array('global_ban_use_cache'=>true))->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

// load the main file
// split the file in chunks
// process each file 
// clear the files

$magento_processor = new ProductImporter(Mage);
$magento_processor->process(new CsvFile(__DIR__.'/sample.csv'));

// $app = new Slim(array(
//     'mode'=>'development',
// ));
// $app->get('/', function () {
//     echo "Hello";
// });
// $app->run();
