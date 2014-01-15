<?php

namespace Repositories\Magento;

class EavCatalogProductRepository
{
    private $magentoInstance;

    function __construct(Mage $magentoInstance)
    {
       $this->magentoInstance = $magentoInstance; 
    }

    public function getModel($modelName)
    {
        $mage = $this->magentoInstance;

        return $mage::getModel($modelName);
    }

    public function getAttributeOptions($attributeName)
    {
        $attributeModel        = $this->getModel('eav/entity_attribute');
		$attributeOptionsModel = $this->getModel('eav/entity_attribute_source_table') ;
	
		$attributeCode         = $attributeModel->getIdByCode('catalog_product', $attributeName);
        
		$attribute             = $attributeModel->load($attributeCode);
	
        // TODO: why do I need this?
		$attributeTable        = $attributeOptionsModel->setAttribute($attribute);

		return $attributeOptionsModel->getAllOptions(false);
    }
    
    public function createAttributeOption($attributeName,$attributeValue)
    {
        $attributeModel        = $this->getModel('eav/entity_attribute');
		$attributeOptionsModel = $this->getModel('eav/entity_attribute_source_table') ;
	
		$attributeCode         = $attributeModel->getIdByCode('catalog_product', $attributeName);
        
		$attribute             = $attributeModel->load($attributeCode);
	
        // TODO: why do I need this?
		$attributeTable        = $attributeOptionsModel->setAttribute($attribute);

        $value['option'] = array($attributeValue,$attributeValue);
        $result = array('value' => $value);
        $attribute->setData('option',$result);
        
        return $attribute->save();
    }
}
