<?php

use Repositories\Magento\EavCatalogProductRepository as EavCatalogProductRepository;

class EavCatalogProductRepositoryTest extends PHPUnit_Framework_TestCase
{
    public function testGetsAttributeOptions()
    {

        $attributeName = 'ATTRIBUTE_NAME';

        $mageMock = $this->getAttributeOptionsMock($attributeName);        

        $eavRepository = new EavCatalogProductRepository($mageMock);
        $result        = $eavRepository->getAttributeOptions($attributeName);
    }

    public function testCreatesAttributeOptions()
    {

        $attributeName  = 'ATTRIBUTE_NAME';
        $attributeValue = 'ATTRIBUTE_VALUE';
        $optionArray    = array(
            'value'=> array(
                'option'=>array(
                    $attributeValue,
                    $attributeValue
                )
            )
        );

        $attributeModelMock = $this->getMockBuilder('stdClass')
            ->setMethods(array('getIdByCode','load','setData','save'))
            ->getMock();

        $attributeModelMock->expects($this->exactly(2))
            ->method('getIdByCode')
            ->with('catalog_product',$attributeName)
            ->will($this->returnValue('CODE'));

        $attributeModelMock->expects($this->exactly(2))
            ->method('load')
            ->with('CODE')
            ->will($this->returnValue($attributeModelMock));

        $attributeModelMock->expects($this->once())
            ->method('setData')
            ->with('option',$optionArray)
            ->will($this->returnValue($attributeModelMock));

        $attributeOptionsModelMock = $this->getMockBuilder('stdClass')
            ->setMethods(array('setAttribute','getAllOptions'))
            ->getMock();

        $attributeOptionsModelMock->expects($this->exactly(2))
            ->method('setAttribute')
            ->with($attributeModelMock);

        $attributeOptionsModelMock->expects($this->once())
            ->method('getAllOptions')
            ->will($this->returnValue(array()));

        $mageMock = $this->getMockBuilder('Helpers\Magento\MageWrapper')
            ->disableOriginalConstructor()
            ->setMethods(array('getModel'))
            ->getMock();

        $mageMock->expects($this->at(0))
            ->method('getModel')
            ->with('eav/entity_attribute')
            ->will($this->returnValue($attributeModelMock));

        $mageMock->expects($this->at(1))
            ->method('getModel')
            ->with('eav/entity_attribute_source_table')
            ->will($this->returnValue($attributeOptionsModelMock));

        $mageMock->expects($this->at(2))
            ->method('getModel')
            ->with('eav/entity_attribute')
            ->will($this->returnValue($attributeModelMock));

        $mageMock->expects($this->at(3))
            ->method('getModel')
            ->with('eav/entity_attribute_source_table')
            ->will($this->returnValue($attributeOptionsModelMock));

        $eavRepository = new EavCatalogProductRepository($mageMock);
        $result        = $eavRepository->createAttributeOption($attributeName,$attributeValue);
    }

    // TODO:
    public function testCreateAttributeOptionNeverCreatesAttributeWhenAlreadyExists()
    {
        $this->markTestIncomplete();
    }

    public function testGetsAttributeId()
    {

        $attributeName  = 'ATTRIBUTE_NAME';
        $attributeValue = 'ATTRIBUTE_VALUE';
        $attributeId  = 'ATTRIBUTE_ID';

        $attributeOptionsArray = array(
            array('label' => $attributeValue,'value' => $attributeId),
        );
        
        $attributeOptionsArray2 = array(
            array('label' => 'foo','value' => 'bar'),
            array('label' => $attributeValue,'value' => $attributeId),
            array('label' => 'bar','value' => 'foo'),
        );

        $mageMock = $this->getAttributeOptionsMock($attributeName,$attributeOptionsArray);        
        
        $eavRepository = new EavCatalogProductRepository($mageMock);
        $result        = $eavRepository->getAttributeId($attributeName,$attributeValue);

        $this->assertEquals($attributeId,$result);

        $mageMock = $this->getAttributeOptionsMock($attributeName,$attributeOptionsArray2);
        
        $eavRepository = new EavCatalogProductRepository($mageMock);
        $result        = $eavRepository->getAttributeId($attributeName,$attributeValue);

        $this->assertEquals($attributeId,$result);

    }

    public function testCheckIfAttributeValueExists()
    {
        $attributeName  = 'ATTRIBUTE_NAME';
        $attributeValue = 'ATTRIBUTE_VALUE';
        $attributeId  = 'ATTRIBUTE_ID';

        $attributeOptionsArray = array(
            array('label' => $attributeValue,'value' => $attributeId),
        );
        
        $attributeOptionsArray2 = array(
            array('label' => 'foo','value' => 'bar'),
            array('label' => 'bar','value' => 'foo'),
        );

        $mageMock = $this->getAttributeOptionsMock($attributeName,$attributeOptionsArray);        
        
        $eavRepository = new EavCatalogProductRepository($mageMock);
        $result        = $eavRepository->attributeValueExists($attributeName,$attributeValue);

        $this->assertTrue($result);

        $mageMock = $this->getAttributeOptionsMock($attributeName,$attributeOptionsArray2);
        
        $eavRepository = new EavCatalogProductRepository($mageMock);
        $result        = $eavRepository->attributeValueExists($attributeName,$attributeValue);

        $this->assertFalse($result);

    }

    private function getAttributeOptionsMock($attributeName,$attributeOptionsArray = array())
    {
        $attributeModelMock = $this->getMockBuilder('stdClass')
            ->setMethods(array('getIdByCode','load'))
            ->getMock();

        $attributeModelMock->expects($this->once())
            ->method('getIdByCode')
            ->with('catalog_product',$attributeName)
            ->will($this->returnValue('CODE'));

        $attributeModelMock->expects($this->once())
            ->method('load')
            ->with('CODE')
            ->will($this->returnValue($attributeModelMock));

        $attributeOptionsModelMock = $this->getMockBuilder('stdClass')
            ->setMethods(array('setAttribute','getAllOptions'))
            ->getMock();

        $attributeOptionsModelMock->expects($this->once())
            ->method('setAttribute')
            ->with($attributeModelMock);

        $attributeOptionsModelMock->expects($this->once())
            ->method('getAllOptions')
            ->will($this->returnValue($attributeOptionsArray));

        $mageMock = $this->getMockBuilder('Helpers\Magento\MageWrapper')
            ->disableOriginalConstructor()
            ->setMethods(array('getModel'))
            ->getMock();

        $mageMock->expects($this->at(0))
            ->method('getModel')
            ->with('eav/entity_attribute')
            ->will($this->returnValue($attributeModelMock));

        $mageMock->expects($this->at(1))
            ->method('getModel')
            ->with('eav/entity_attribute_source_table')
            ->will($this->returnValue($attributeOptionsModelMock));

        return $mageMock;
    }

}

