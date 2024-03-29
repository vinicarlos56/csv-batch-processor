<?php

namespace Repositories\Magento;

use Repositories\Magento\EavCatalogProductRepository as EavCatalogProductRepository;
use Helpers\Magento\MageWrapper;

class CatalogRepository
{
    private $magentoInstance;
    
    function __construct(MageWrapper $magentoInstance, EavCatalogProductRepository $attributeManager)
    {
       $this->magentoInstance  = $magentoInstance; 
       $this->attributeManager = $attributeManager; 
    }

    public function getModel($modelName)
    {
        return $this->magentoInstance->getModel($modelName);
    }

    public function loadAttributes($productData,$attributesArray)
    {
        foreach ($attributesArray as $attributeName) {

            if ( ! $this->attributeManager->attributeValueExists($attributeName,$productData[$attributeName]) ) {
                
                $this->attributeManager->createAttribute($attributeName,$productData[$attributeName]);
            }

            $attributeId  = $this->attributeManager->getAttributeId($attributeName,$productData[$attributeName]);
            $productData[$attributeName] = $attributeId;
        }

        return $productData;
    }

    public function productExists($sku)
    {
        return (bool) $this->getModel('catalog/product')->loadByAttribute('sku',$sku);
    }

    public function createSimpleProduct($productData)
    {
        //TODO: load the attributes
        $productData['type_id'] 	      = 'simple';
        $productData['visibility']        = 1; // catalog, search
        $productData['attribute_set_id']  = 4;  // default attribute set
        $productData['website_ids']       = array(1); //main website

        $productModel = $this->getModel('catalog/product');

        $productData = array_merge($productModel->getData(),$productData);

        $productModel->setData($productData);
        $productModel->save();

        return $productModel;
    }

    public function updateStock($productData)
    {
        $product     = $this->getModel('catalog/product')->loadByAttribute('sku',$productData['sku']);  
        $stockItem   = $this->getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
        $stock       = array();

        if ( ! $stockItem->getId() ) {
         
           
            // TODO:check why do I need this
            $stock = array (
                'product_id' => $product->getId(),
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
        } else {
            $stock = $stockItem->getData();
        }
        
        foreach($stock as $stockAttribute => $value) {
        
            // if the data exists in productDataArray
            if( isset($productData[$stockAttribute]) ) {
                // update the stockDataArray
                $stock[$stockAttribute] = $productData[$stockAttribute] ?: 0;
            }
            //then set the stockAttributeValue to his key
            $stockItem->setData($stockAttribute,$stock[$stockAttribute]); 
           
        } 

        $stockItem->save();

        return $stockItem;
    }

    
}
