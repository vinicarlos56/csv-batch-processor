<?php

use Repositories\Magento\CatalogRepository as CatalogRepository;
// use Helpers\Magento\AttributeManager as AttributeManager;
// use Helpers\Magento\MageWrapper as MageWrapper;

class CatalogRepositoryTest extends PHPUnit_Framework_TestCase
{
    
    public function testIsWorking()
    {
        $this->assertTrue(true);
    }

    public function testCreatesSimpleProducts()
    {        
        $productData        = $this->getProductData();
        $defaultProductData = array(
            'type_id'=>'simple',
            'attribute_set_id'=>4,
            'website_ids'=>array(1)
        );

        $productStub = $this->getMockBuilder('stdClass')
            ->setMethods(array('getData','setData','save'))
            ->getMock();

        $productStub->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(array()));

        $productStub->expects($this->once())
            ->method('setData')
            ->with($productData+$defaultProductData);

        $productStub->expects($this->once())
            ->method('save')
            ->will($this->returnValue($productStub));

        $mageMock = $this->getMockBuilder('Helpers\Magento\MageWrapper')
            ->setMethods(array('getModel','loadByAttribute'))
            ->getMock();

        $mageMock->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($productStub));

        $attributeManagerMock = $this->getMockBuilder('Helpers\Magento\AttributeManager')
            ->disableOriginalConstructor()
            ->getMock();

        $catalogRepository = new CatalogRepository($mageMock,$attributeManagerMock);
        $catalogRepository->createSimpleProduct($productData);

    }

    public function testLoadAttributesIdsInProductDataArrayWhenAttributeExists()
    {
        $productData = $this->getProductData();
        $attributesArray = array('color','tamanho');

        $mageMock = $this->getMockBuilder('Helpers\Magento\MageWrapper')
            ->getMock();

        $attributeManagerMock = $this->getMockBuilder('Helpers\Magento\AttributeManager')
            ->disableOriginalConstructor()
            ->setMethods(array('attributeValueExists','createAttribute','getAttributeId'))
            ->getMock();

        $attributeManagerMock->expects($this->never())
            ->method('createAttribute');

        $attributeManagerMock->expects($this->exactly(2))
            ->method('attributeValueExists')
            ->will($this->returnValue(true));

        $attributeManagerMock->expects($this->exactly(2))
            ->method('getAttributeId')
            ->will($this->onConsecutiveCalls(5,6));

        $catalogRepository = new CatalogRepository($mageMock,$attributeManagerMock);
        $newProductData    = $catalogRepository->loadAttributes($productData,$attributesArray);

        $productData['color']   = 5;
        $productData['tamanho'] = 6;

        $this->assertEquals($productData,$newProductData);
    }

    public function testCreateAttributesAndLoadAttributesIdsInProductDataArray()
    {
        // $this->markTestIncomplete();
        $productData = $this->getProductData();
        $attributesArray = array('color','tamanho');

        $mageMock = $this->getMockBuilder('Helpers\Magento\MageWrapper')
            ->getMock();

        $attributeManagerMock = $this->getMockBuilder('Helpers\Magento\AttributeManager')
            ->disableOriginalConstructor()
            ->setMethods(array('attributeValueExists','createAttribute','getAttributeId'))
            ->getMock();

        $attributeManagerMock->expects($this->exactly(2))
            ->method('attributeValueExists')
            ->will($this->returnValue(false));

        $attributeManagerMock->expects($this->at(1))
            ->method('createAttribute')
            ->with($this->equalTo('color'),$this->equalTo('teste'));

        $attributeManagerMock->expects($this->at(4))
            ->method('createAttribute')
            ->with($this->equalTo('tamanho'),$this->equalTo('teste'));

        $attributeManagerMock->expects($this->exactly(2))
            ->method('getAttributeId')
            ->will($this->onConsecutiveCalls(5,6));

        $catalogRepository = new CatalogRepository($mageMock,$attributeManagerMock);
        $newProductData    = $catalogRepository->loadAttributes($productData,$attributesArray);

        $productData['color']   = 5;
        $productData['tamanho'] = 6;

        $this->assertEquals($productData,$newProductData);
    }
    private function getProductData()
    {
        return array(
            "sku" => 'teste',
            "referencia"=> 'teste',
            "name"=> 'teste',
            "description"=> 'teste',
            "short_description"=> 'teste',
            "cod_cor"=> 'teste',
            "color"=> 'teste',
            "cod_tamanho"=> 'teste',
            "tamanho"=> 'teste',
            "qty"=> 'teste',
            "price"=> 'teste',
            "special_price"=> 'teste',
            "colecao"=> 'teste',
            "visibility"=> '1',
            "is_in_stock"=> 'teste',
            "type"=> 'teste',
            "attribute_set"=> 'teste',
            "product_website"=> 'teste',
            "status"=> 'teste',
            "weight"=> 'teste'
        );
    }
}
