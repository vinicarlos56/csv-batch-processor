<?php

require_once __DIR__.'/../../vendor/autoload.php'; // load composer

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\PhpProcess;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

$logger = new Logger('csv_batch_importer');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../logs/report_'.date('d-m-Y h-i-s').'.log', Logger::DEBUG));
$logger->pushHandler(new FirePHPHandler());

$output_dir = __DIR__.'/../../tmp/output/';

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


