<?php

require_once __DIR__.'/../../vendor/autoload.php'; // load composer

use Keboola\Csv\CsvFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

// $csv_file    = new CsvFile('sample.csv');
$csv_file    = new CsvFile($argv[1]);
$csv_header  = $csv_file->getHeader();
$row_count   = 0; 
$file_count  = 1; 
$split_size  = 5; 
$output_rows = array();
$output_dir  = 'tmp/output/'; 


$fs = new Filesystem;

if ( ! $fs->exists($output_dir) ) $fs->mkdir($output_dir,0755);

foreach($csv_file as $row) {

    if ( $row_count % $split_size == 0 ) {

        $current_file_name = sprintf('%soutput%s.csv',$output_dir,$file_count++); 

        $fs->touch($current_file_name);

        $current_file = new CsvFile($current_file_name);
        $current_file->writeRow($csv_header);

    } else {

        $current_file->writeRow($row);

    }

    $row_count++;
}
