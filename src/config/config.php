<?php

require_once __DIR__.'/../../vendor/autoload.php'; // load composer

$production_config = array(
    'csv_files_path'    => __DIR__.'/../../csv_files/',
    'log_files_path'    => __DIR__.'/../../logs/',
    'templates_path'    => __DIR__.'/../../templates/',
    'output_csv_path'   => __DIR__.'/../../tmp/output/',
    'php_path'          => '/usr/local/bin/php',
    'nohup_path'        => '/usr/bin/nohup',
    'proc_working_path' => '/tmp',
    'magento_full_path' => __DIR__.'/../../../app/Mage.php',
    'split_size'        => 500,
    'lock_file'         => __DIR__.'/../../locks/process.lock',
);

$local_config = array(
    'csv_files_path'    => __DIR__.'/../../csv_files/',
    'log_files_path'    => __DIR__.'/../../logs/',
    'templates_path'    => __DIR__.'/../../templates/',
    'output_csv_path'   => __DIR__.'/../../tmp/output/',
    'php_path'          => '/usr/bin/php',
    'nohup_path'        => '/usr/bin/nohup',
    'proc_working_path' => '/tmp',
    'magento_full_path' => __DIR__.'/../../../magento/app/Mage.php',
    'split_size'        => 100,
    'lock_file'         => __DIR__.'/../../locks/process.lock',
);

$config = $local_config;

