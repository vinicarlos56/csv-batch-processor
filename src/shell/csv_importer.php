<?php

require_once __DIR__.'/../../vendor/autoload.php'; // load composer

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Filesystem\Filesystem;

$logFileName = __DIR__.'/../../logs/report_'.date('d-m-Y h-i-s').'.log';

$fs = new FileSystem;
$fs->touch($logFileName);

$logger = new Logger('csv_batch_importer');
$logger->pushHandler(new StreamHandler($logFileName, Logger::DEBUG));
$logger->pushHandler(new FirePHPHandler());

$output_dir = $argv[1];

$files = array_filter(scandir($output_dir),function($file_name){
    return preg_match('/^output.*\.csv$/',$file_name) ? $file_name : null;
});

foreach ($files as $file_name) {

    $file_name = $output_dir.$file_name;
    $process_builder = new ProcessBuilder(array('/usr/bin/php',__DIR__.'/csv_process.php', $file_name));
    $process_builder->setTimeout(0);
    $process_builder->getProcess()->run(function ($type, $buffer) use($logger) {
        if (Process::ERR === $type) {
            $logger->addError($buffer);
        } else {
            $logger->addInfo($buffer);
        }
    });

}


