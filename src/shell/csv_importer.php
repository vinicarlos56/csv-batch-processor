<?php

include_once __DIR__.'/../config/config.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Filesystem\Filesystem;
use Helpers\ProcessLocker; 

$locker = new ProcessLocker($config['lock_file']);

if ($locker->isLocked()) {
    return;
}

$locker->lockProcess();

$logFileName = $config['log_files_path'].'report '.date('d-m-Y H i s').'.log';

$fs = new FileSystem;
$fs->touch($logFileName);

$logger = new Logger('csv_batch_importer');
$logger->pushHandler(new StreamHandler($logFileName, Logger::DEBUG));
$logger->pushHandler(new FirePHPHandler());

$files = array_filter(scandir($config['output_csv_path']),function($file_name){
    return preg_match('/^output.*\.csv$/',$file_name) ? $file_name : null;
});

foreach ($files as $file_name) {

    $file_name = $config['output_csv_path'].$file_name;

    $process_builder = new ProcessBuilder(array(
        $config['php_path'],
        __DIR__.'/csv_process.php', 
        $file_name)
    ,$config['proc_working_path']);

    $process_builder->setTimeout(0);
    $process_builder->getProcess()->run(function ($type, $buffer) use($logger) {
        if (Process::ERR === $type) {
            $logger->addError($buffer);
        } else {
            $logger->addInfo($buffer);
        }
    });

}

$fs = new Filesystem;
$fs->remove($config['output_csv_path']);

$locker->unlockProcess();



