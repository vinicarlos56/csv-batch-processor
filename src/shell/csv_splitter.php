<?php

require_once __DIR__.'/../config/config.php';


use Helpers\CSV\CsvSplitter;
use Keboola\Csv\CsvFile;

$splitter = new CsvSplitter(new CsvFile(__DIR__.'/../../sample.csv'),__DIR__.'/tmp/output/');
$splitter->split(5);
$splitter->clearOutputFiles();


