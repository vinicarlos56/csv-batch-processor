<?php

namespace Helpers\Magento;

class AttributeManager {

    private $attributeRepository;

    public function __construct($attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }
	
	public function attributeValueExists($arg_attribute, $arg_value)
	{
		return $this->getAttributeId($arg_attribute,$arg_value) ? true : false;
	}

    
    public function createAttribute($arg_attribute, $arg_value)
    {
        if ( ! $this->attributeValueExists($arg_attribute, $arg_value) ) {

            return $this->attributeRepository->createAttributeOption($arg_attribute,$arg_value);
		}
    }

	public function getAttributeId($attributeName,$attributeValue)
	{
        $options = $this->attributeRepository->getAttributeOptions($attributeName);
        
        foreach($options as $option)
		{
			if ($option['label'] == $attributeValue)
			{
				return $option['value'];
			}
		}
	}
	// public function addAttributeValue($arg_attribute, $arg_value)
	// {
	// 	$attribute_model        = Mage::getModel('eav/entity_attribute');
	// 	$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
	// 
	// 	$attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
	// 	$attribute              = $attribute_model->load($attribute_code);
	// 
	// 	$attribute_table        = $attribute_options_model->setAttribute($attribute);
	// 	
	// 
	// 	if(!$this->attributeValueExists($arg_attribute, $arg_value))
	// 	{
	// 		$value['option'] = array($arg_value,$arg_value);
	// 		$result = array('value' => $value);
	// 		$attribute->setData('option',$result);
	// 		$attribute->save();
	// 	}
	// 	
	// 	$options = $attribute_options_model->getAllOptions(false);
	// 	foreach($options as $option)
	// 	{
	// 		if ($option['label'] == $arg_value)
	// 		{
	// 			return $option['value'];
	// 		}
	// 	}
	// 	return true;
	// }
	
	// public function getAttributeValue($arg_attribute, $arg_option_id)
	// {
	// 	$attribute_model        = Mage::getModel('eav/entity_attribute');
	// 	$attribute_table        = Mage::getModel('eav/entity_attribute_source_table');
	// 
	// 	$attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
	// 	$attribute              = $attribute_model->load($attribute_code);
	// 
	// 	$attribute_table->setAttribute($attribute);
	// 
	// 	$option                 = $attribute_table->getOptionText($arg_option_id);
	// 
	// 	return $option;
	// }
	
	
	
}
