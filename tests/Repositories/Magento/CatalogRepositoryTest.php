<?php

use Repositories\Magento\CatalogRepository as CatalogRepository;
// use Repositories\Magento\EavCatalogProductRepository as AttributeManager;
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
            ->disableOriginalConstructor()
            ->setMethods(array('getModel','loadByAttribute'))
            ->getMock();

        $mageMock->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($productStub));

        $attributeManagerMock = $this->getMockBuilder('Repositories\Magento\EavCatalogProductRepository')
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
            ->disableOriginalConstructor()
            ->getMock();

        $attributeManagerMock = $this->getMockBuilder('Repositories\Magento\EavCatalogProductRepository')
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
            ->disableOriginalConstructor()
            ->getMock();

        $attributeManagerMock = $this->getMockBuilder('Repositories\Magento\EavCatalogProductRepository')
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

    public function testUpdatesTheStockCorrectlyWhenStockDataAlreadyExists()
    {
        $productId = 4;
        $stockData = $this->getStockData();
        $productData = $this->getProductData();

        $productStub = $this->getMockBuilder('stdClass')
            ->setMethods(array('getId','loadByAttribute'))
            ->getMock();

        $productStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($productId));

        $productStub->expects($this->once())
            ->method('loadByAttribute')
            ->will($this->returnValue($productStub));

        $stockStub = $this->getMockBuilder('stdClass')
            ->setMethods(array('getId','getData','setData','save','loadByProduct'))
            ->getMock();

        $stockStub->expects($this->once())
            ->method('loadByProduct')
            ->with($productId)
            ->will($this->returnValue($stockStub));

        $stockStub->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(true));

        $stockStub->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($stockData));
        
        $stockStub->expects($this->once())
            ->method('save')
            ->will($this->returnValue($stockStub));

        $mageMock = $this->getMockBuilder('Helpers\Magento\MageWrapper')
            ->disableOriginalConstructor()
            ->setMethods(array('getModel','getId','loadByAttribute'))
            ->getMock();

        $mageMock->expects($this->at(0))
            ->method('getModel')
            ->with('catalog/product')
            ->will($this->returnValue($productStub));

        $mageMock->expects($this->at(1))
            ->method('getModel')
            ->with('cataloginventory/stock_item')
            ->will($this->returnValue($stockStub));

        $attributeManagerMock = $this->getMockBuilder('Repositories\Magento\EavCatalogProductRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $catalogRepository = new CatalogRepository($mageMock,$attributeManagerMock);
        $newProductData    = $catalogRepository->updateStock($productData);

    }

    public function testUpdatesTheStockCorrectlyWhenStockDataDoesNotExists()
    {
        $productId = 4;
        $stockData = $this->getStockData();
        $productData = $this->getProductData();

        $productStub = $this->getMockBuilder('stdClass')
            ->setMethods(array('getId','loadByAttribute'))
            ->getMock();

        $productStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($productId));

        $productStub->expects($this->once())
            ->method('loadByAttribute')
            ->will($this->returnValue($productStub));

        $stockStub = $this->getMockBuilder('stdClass')
            ->setMethods(array('getId','getData','setData','save','loadByProduct'))
            ->getMock();

        $stockStub->expects($this->once())
            ->method('loadByProduct')
            ->with($productId)
            ->will($this->returnValue($stockStub));

        $stockStub->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(false));

        $stockStub->expects($this->never())
            ->method('getData');
        
        $stockStub->expects($this->once())
            ->method('save')
            ->will($this->returnValue($stockStub));

        $mageMock = $this->getMockBuilder('Helpers\Magento\MageWrapper')
            ->disableOriginalConstructor()
            ->setMethods(array('getModel'))
            ->getMock();

        $mageMock->expects($this->at(0))
            ->method('getModel')
            ->with('catalog/product')
            ->will($this->returnValue($productStub));

        $mageMock->expects($this->at(1))
            ->method('getModel')
            ->with('cataloginventory/stock_item')
            ->will($this->returnValue($stockStub));


        $attributeManagerMock = $this->getMockBuilder('Repositories\Magento\EavCatalogProductRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $catalogRepository = new CatalogRepository($mageMock,$attributeManagerMock);
        $newProductData    = $catalogRepository->updateStock($productData);

    }

    private function getStockData()
    {
        return array (
                'stock_id' => '1',
                'qty' => '0.0000',
                'min_qty' => '0.0000',
                'use_config_min_qty' => '1',
                'is_qty_decimal' => '0',
                'backorders' => '0',
                'use_config_backorders' => '1',
                'min_sale_qty' => '1.0000',
                'use_config_min_sale_qty' => '1',
                'max_sale_qty' => '0.0000',
                'use_config_max_sale_qty' => '1',
                'is_in_stock' => '0',
                'low_stock_date' => NULL,
                'notify_stock_qty' => NULL,
                'use_config_notify_stock_qty' => '1',
                'manage_stock' => '0',
                'use_config_manage_stock' => '1',
                'stock_status_changed_auto' => '0',
                'use_config_qty_increments' => '1',
                'qty_increments' => '0.0000',
                'use_config_enable_qty_inc' => '1',
                'enable_qty_increments' => '0',
                'is_decimal_divided' => '0',
                'reserved_qty' => '0.0000',
                'type_id' => 'simple',
                'stock_status_changed_automatically' => '0',
                'use_config_enable_qty_increments' => '1',
            );

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
            "qty"=> 4,
            "price"=> 'teste',
            "special_price"=> 'teste',
            "colecao"=> 'teste',
            "visibility"=> '1',
            "is_in_stock"=> 1,
            "type"=> 'teste',
            "attribute_set"=> 'teste',
            "product_website"=> 'teste',
            "status"=> 'teste',
            "weight"=> 'teste'
        );
    }
}
