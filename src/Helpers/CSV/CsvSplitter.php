<?php

namespace Helpers\CSV;

use Keboola\Csv\CsvFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class CsvSplitter
{
    private $file;   	
    private $outputDir;   	
    
    public function __construct(CsvFile $file,$outputDir)
    {
	$this->file 	 = $file;	
	$this->outputDir = $outputDir;
    }

    public function clearOutputFiles()
    {
	$fs = new Filesystem;
	$fs->remove($this->outputDir);
    }

    public function split($splitSize,$outputName = 'output')
    {
	$csv_header  = $this->file->getHeader();
	$rowCount   = 0; 
	$fileCount  = 1; 

	$fs = new Filesystem;

	if ( ! $fs->exists($this->outputDir) ) $fs->mkdir($this->outputDir);

	foreach($this->file as $row) {

	    if ( $rowCount % $splitSize == 0 ) {

		$currentFileName = sprintf('%s'.$outputName.'%s.csv',$this->outputDir,$fileCount++); 

		$fs->touch($currentFileName);

		$currentFile = new CsvFile($currentFileName);
		$currentFile->writeRow($csv_header);

	    } else {

		$currentFile->writeRow($row);

	    }

	    $rowCount++;
	}
    }
}

