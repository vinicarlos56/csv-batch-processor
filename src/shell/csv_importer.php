<?php

require_once __DIR__.'/../../vendor/autoload.php'; // load composer

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\PhpProcess;

ini_set('display_errors',true);
error_reporting(E_ALL);


$log_file_name = 'teste';

$output_dir = __DIR__.'/../../tmp/output/';

$files = array_filter(scandir($output_dir),function($file_name){
    return preg_match('/^output.*\.csv$/',$file_name) ? $file_name : null;
});

foreach ($files as $file_name) {

    $file_name = $output_dir.$file_name;
    $process = new ProcessBuilder(array('/usr/bin/php',__DIR__.'/csv_process.php', $file_name));
    $process->getProcess()->run(function ($type, $buffer) {
        if (Process::ERR === $type) {
            echo $buffer.PHP_EOL;
        } else {
            echo $buffer.PHP_EOL;
        }
    });

}


