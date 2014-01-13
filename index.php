<?php

ini_set('display_errors',true);
error_reporting(E_ALL);

require_once __DIR__.'/vendor/autoload.php'; // load composer
require_once __DIR__."/../app/Mage.php";

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;
use Helpers\CSV\CsvSplitter;
use Keboola\Csv\CsvFile;
use Slim\Slim;


// load the main file HttpGet
// split the file in chunks Splitter
// process each file Impoter > Process
// clear the files Splitter


$app = new Slim(array(
    'mode'=>'development',
    'templates.path' => './templates/'
));
$app->get('/', function () use ($app){
    
     $app->render('page.php',array());

});

$app->get('/download_report', function () use ($app){
    
    $fileName = $app->request()->params('filename');

    echo "<pre>";
    echo file_get_contents('logs/'.$fileName);



});
$app->get('/process/', function () use ($app){

    $fileName = $app->request()->params('filename');
    
    $splitter = new CsvSplitter(new CsvFile($fileName),__DIR__.'/tmp/output/');
    $splitter->split(100);

    $process = new Process(
            '/usr/bin/nohup /usr/local/bin/php '.
            __DIR__.'/src/shell/csv_importer.php '.
            __DIR__.'/tmp/output/  > /dev/null 2>&1 &', '/tmp'
    );

    $process->run();
    $app->redirect('/csv_batch_process/index.php');

});
$app->run();
