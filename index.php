<?php

ini_set('display_errors',true);
error_reporting(E_ALL);

require_once __DIR__.'/vendor/autoload.php'; // load composer
require_once "/home/carlos/www/magento/app/Mage.php";

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Processors\Magento\ProductImporter;
use Helpers\CSV\CsvSplitter;
use Keboola\Csv\CsvFile;
use Slim\Slim;

$mage = Mage::app('admin', 'store', array('global_ban_use_cache'=>true))->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

// load the main file HttpGet
// split the file in chunks Splitter
// process each file Impoter > Process
// clear the files Splitter

// $magento_processor = new ProductImporter(Mage);
// $magento_processor->process(new CsvFile(__DIR__.'/sample.csv'));

$app = new Slim(array(
    'mode'=>'development',
));
$app->get('/', function () {
    
    $splitter = new CsvSplitter(new CsvFile(__DIR__.'/sample.csv'),__DIR__.'/tmp/output/');
    $splitter->split(20);

    $process = new ProcessBuilder(array('/usr/bin/php',__DIR__.'/src/shell/csv_importer.php',__DIR__.'/tmp/output/','&'));
    $process->getProcess()->run(function ($type, $buffer) {
        if (Process::ERR === $type) {
            echo($buffer);
        } else {
            echo($buffer);
        }
    });

    // echo 'Process is running';
    $splitter->clearOutputFiles();

});
$app->run();
