<?php

use Processors\Magento\ProductImporter as ProductImporter;

class ProductImporterTest extends PHPUnit_Framework_TestCase
{
    public function testProcessMethod()
    {
        $sku       = 'sku';
        $fileName  = 'filename';
        $csvHeader = array('sku','qty');
        $firstRow  = array($sku,'4');
        
        $catalogRepositoryMock = $this->getMockBuilder('Repositories\Magento\CatalogRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('productExists','updateProductStock','createProduct'))
            ->getMock();

        $catalogRepositoryMock->expects($this->once())
            ->method('productExists')
            ->with($sku)
            ->will($this->returnValue(false));

        $catalogRepositoryMock->expects($this->once())
            ->method('createProduct')
            ->with(array_combine($csvHeader,$firstRow));

        $csvFileMock = $this->getMockBuilder('Keboola\Csv\CsvFile')
            ->disableOriginalConstructor()
            ->setMethods(array('getHeader','rewind','valid','current','next'))
            ->getMock();

        $csvFileMock->expects($this->at(0))
            ->method('getHeader')
            ->will($this->returnValue($csvHeader));

        $csvFileMock->expects($this->at(1))
            ->method('rewind');

        // header
        $csvFileMock->expects($this->at(2))
            ->method('valid')
            ->will($this->returnValue(true));

        $csvFileMock->expects($this->at(3))
            ->method('current')
            ->will($this->returnValue($csvHeader));

        $csvFileMock->expects($this->at(4))
            ->method('next');

        // first row
        $csvFileMock->expects($this->at(5))
            ->method('valid')
            ->will($this->returnValue(true));

        $csvFileMock->expects($this->at(6))
            ->method('current')
            ->will($this->returnValue($firstRow));

        $csvFileMock->expects($this->at(7))
            ->method('next');

        $csvFileMock->expects($this->at(8))
            ->method('valid')
            ->will($this->returnValue(false));

        $productImporter = new ProductImporter($catalogRepositoryMock);

        ob_start();

        $productImporter->process($csvFileMock,$fileName);
        $outputBuffer = ob_get_contents();

        ob_clean();

        $this->assertEquals("End of file: {$fileName}",$outputBuffer);
    }    
}

