<?php

namespace Helpers\CSV;

use Keboola\Csv\CsvFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class CsvSplitter
{
    private $file;   	
    private $outputDir;   	
    
    public function __construct($file,$outputDir)
    {
        $this->file 	 = $file;	
        $this->outputDir = $outputDir;
    }

    public function setCsvHandler($csvHandler)
    {
        $this->csvHandler = $csvHandler;
    }

    public function setFileHandler($fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    public function split($splitSize,$outputName = 'output')
    {
        $csv_header  = $this->file->getHeader();
        $rowCount   = 0; 
        $fileCount  = 0; 

        //TODO: fix mkdir
        if ( ! $this->fileHandler->exists($this->outputDir) ) $this->fileHandler->mkdir($this->outputDir);

        foreach($this->file as $row) {

            if ( $rowCount % $splitSize == 0 ) {

                $currentFileName = sprintf('%s'.$outputName.'%s.csv',$this->outputDir,++$fileCount); 

                $this->fileHandler->touch($currentFileName);

                $this->csvHandler->writeRow($currentFileName,$csv_header);

                if ($row != $csv_header) {
                    $this->csvHandler->writeRow($currentFileName,$row);
                }

            } else {

                $this->csvHandler->writeRow($currentFileName,$row);

            }

            $rowCount++;
        }
    }
}

