<?php namespace Helpers\Magento;

class MageWrapper
{
    private $mage;
    
    public function __construct(Mage $mage)
    {
        $this->mage = $mage;
    }
    public function getModel($model)
    {
        $mage = $this->mage;

        return $mage::getModel($model);
    }    
    
}

