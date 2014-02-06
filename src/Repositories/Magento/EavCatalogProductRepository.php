<?php

namespace Repositories\Magento;

use Helpers\Magento\MageWrapper;

class EavCatalogProductRepository
{
    private $magentoInstance;

    function __construct(MageWrapper $magentoInstance)
    {
       $this->magentoInstance = $magentoInstance; 
    }

	public function attributeValueExists($arg_attribute, $arg_value)
	{
		return $this->getAttributeId($arg_attribute,$arg_value) ? true : false;
	}

    
    public function createAttribute($arg_attribute, $arg_value)
    {
        return $this->createAttributeOption($arg_attribute, $arg_value);
        // if ( ! $this->attributeValueExists($arg_attribute, $arg_value) ) {
        //
        //     return $this->attributeRepository->createAttributeOption($arg_attribute,$arg_value);
		// }
    }

	public function getAttributeId($attributeName,$attributeValue)
	{
        $options = $this->getAttributeOptions($attributeName);
        
        foreach($options as $option)
		{
			if ($option['label'] == $attributeValue)
			{
				return $option['value'];
			}
		}
	}

// end

    public function getModel($modelName)
    {
        return $this->magentoInstance->getModel($modelName);
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
        if ( $this->attributeValueExists($attributeName, $attributeValue) ) return;

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
