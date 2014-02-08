<?php

use Helpers\CSV\CsvSplitter as CsvSplitter;
use \Mockery as m;

class CsvSplitterTest extends PHPUnit_Framework_TestCase
{
    public function testSplitGenerateOutputFiles()
    {
        $outputDir = '/tmp/';
        $outputName = 'output';
        $items = array(
            array('sku','qty'),
            array('teste','4'),
            array('teste1','5'),
            array('teste2','6'),
            array('teste3','7'),
            array('teste4','8'),
            array('teste5','9'),
            array('teste6','4'),
            array('teste7','7'),
        );
        $currentFilename = '/tmp/'.$outputName;

        $csvFile = $this->getMockBuilder('Keboola\Csv\CsvFile')
            ->disableOriginalConstructor()
            ->getMock();

        $csvFile->expects($this->at(0))
            ->method('getHeader')
            ->will($this->returnValue($items[0]));

        $csvFileMock = $this->iteratorHelper($csvFile,$items,1);

        $csvFileHandlerMock = m::mock('Helpers\CSV\CsvHandler');
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'1.csv',$items[0]);
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'1.csv',$items[1]);
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'2.csv',$items[0]);
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'2.csv',$items[2]);
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'2.csv',$items[3]);
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'3.csv',$items[0]);
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'3.csv',$items[4]);
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'3.csv',$items[5]);
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'4.csv',$items[0]);
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'4.csv',$items[6]);
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'4.csv',$items[7]);
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'5.csv',$items[0]);
        $csvFileHandlerMock->shouldReceive('writeRow')->with($currentFilename.'5.csv',$items[8]);

        $fileHandlerMock = m::mock('stdClass');
        $fileHandlerMock->shouldReceive('exists')->andReturn(true);
        $fileHandlerMock->shouldReceive('touch')->with($currentFilename.'1.csv');
        $fileHandlerMock->shouldReceive('touch')->with($currentFilename.'2.csv');
        $fileHandlerMock->shouldReceive('touch')->with($currentFilename.'3.csv');
        $fileHandlerMock->shouldReceive('touch')->with($currentFilename.'4.csv');
        $fileHandlerMock->shouldReceive('touch')->with($currentFilename.'5.csv');
       
        $csvSplitter = new CsvSplitter($csvFileMock,$outputDir);
        $csvSplitter->setCsvHandler($csvFileHandlerMock);
        $csvSplitter->setFileHandler($fileHandlerMock);
        $csvSplitter->split(2);
    }

    public function iteratorHelper(Iterator $iterator,array $items, $startAt,$includeCallsToKey = false)
    {
        $iterator->expects($this->at($startAt))
            ->method('rewind');

        $counter = $startAt + 1;

        foreach ($items as $key => $value) {

            $iterator->expects($this->at($counter++))
                ->method('valid')
                ->will($this->returnValue(true));

            $iterator->expects($this->at($counter++))
                ->method('current')
                ->will($this->returnValue($value));

            if ( $includeCallsToKey ) {

                $iterator->expects($this->at($counter++))
                    ->method('key')
                    ->will($this->returnValue($key));
            }

            $iterator->expects($this->at($counter++))
                ->method('next');
 
        }

        $iterator->expects($this->at($counter++))
            ->method('next')
            ->will($this->returnValue(false));

        return $iterator;
    }
}
