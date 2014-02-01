<?php

class ProductImporterTest extends PHPUnit_Framework_TestCase
{
    public function testProcessMethod()
    {
        $this->assertTrue(true);
        // mock CatalogRepository( mock Mage)
        // mock AttributeManager(mock AttributeRepository(mock Mage))
        // mock CsvFile
        // assert process
        // new ProductImporter(CatalogRepository, AttributeManager)

        // $mage = $this->getMockBuilder('Mage')
        //     ->setMethods(array('getModel','loadByAttribute'))
        //     ->getMock();
        //
        // $mage->expects($this->once())
        //     ->method('getModel')
        //     ->will($this->returnValue($mage));
        //
        // $mage->expects($this->once())
        //     ->method('loadByAttribute')
        //     ->will($this->returnValue(null));
        //
        // $attributeManager = $this->getMockBuilder('AttributeManager')
        //     ->setMethods(array('attributeValueExists','createAttribute','getAttributeId'))
        //     ->getMock();

        // $attribute_repository = new EavCatalogProductRepository(Mage);
        // $attribute_manager    = new AttributeManager($attribute_repository);
        // $magento_processor    = new ProductImporter($catalog_repository,$attribute_manager);

        // $catalogRepository = new CatalogRepository($mage);
        // $attributeManager  = new AttributeManager($mage);
        // $productImporter   = new ProductImporter($catalogRepository);
        //

    }    
}

