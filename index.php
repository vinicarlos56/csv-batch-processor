<?php

ini_set('display_errors',true);
error_reporting(E_ALL);

include __DIR__.'/src/config/config.php';

require_once $config['magento_full_path'];

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;
use Helpers\CSV\CsvSplitter;
use Helpers\ProcessLocker;
use Keboola\Csv\CsvFile;
use Slim\Slim;


$app = new Slim(array(
    'mode'=>'development',
    'templates.path' => $config['templates_path']
));

$app->get('/', function () use ($app,$config){

    
    
    $app->render('page.php',array(
        'csv_files_path'=> $config['csv_files_path'],
        'log_files_path'=> $config['log_files_path'],
    ));

});

$app->get('/download_report', function () use ($app,$config){
    
    $fileName = $app->request()->params('filename');

    echo "<pre>";
    echo file_get_contents($config['log_files_path'].$fileName);



});

$app->get('/process/', function () use ($app,$config){

    $fileName = $app->request()->params('filename');

    $locker = new ProcessLocker($config['lock_file']);

    if ( ! $locker->isLocked() ) {

        $splitter = new CsvSplitter(new CsvFile($fileName),$config['output_csv_path']);
        $splitter->split($config['split_size']);

        $command = $config['nohup_path'].' '.$config['php_path'].' '.__DIR__.'/src/shell/csv_importer.php '.$config['output_csv_path'].'  > /dev/null 2>&1 &';
        $process = new Process($command,$config['proc_working_path']);

        $process->run();
    }

    $app->redirect('/csv_batch_process/index.php');

});

$app->run();
