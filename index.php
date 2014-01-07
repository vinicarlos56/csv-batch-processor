<?php

ini_set('display_errors',true);
error_reporting(E_ALL);

require_once __DIR__.'/vendor/autoload.php'; // load composer
require_once "/home/carlos/www/magento/app/Mage.php";

use Symfony\Component\Process\ProcessBuilder;
use Helpers\CSV\CsvSplitter;
use Keboola\Csv\CsvFile;
use Slim\Slim;


// load the main file HttpGet
// split the file in chunks Splitter
// process each file Impoter > Process
// clear the files Splitter


$app = new Slim(array(
    'mode'=>'development',
));
$app->get('/', function () {
    
    $splitter = new CsvSplitter(new CsvFile(__DIR__.'/sample.csv'),__DIR__.'/tmp/output/');
    $splitter->split(20);

    $process = new ProcessBuilder(
	array(
	    '/usr/bin/php',
	    __DIR__.'/src/shell/csv_importer.php',
	    __DIR__.'/tmp/output/',
	)
    );

    $process->getProcess()->start();

    // $splitter->clearOutputFiles();

});
$app->run();
