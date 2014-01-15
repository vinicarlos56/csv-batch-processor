<?php

namespace Repositories\Magento;

class CatalogRepository
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

    public function createSimpleProduct($productData)
    {
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

    public function updateStock($product,$stockData)
    {
        $stockItem   = $this->getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
        $stock       = array();

        if ( ! $stockItem->getId() ) {
         
            $stock = array (
                'product_id' => $product->getId(),
                'stock_id' => '1',
                'qty' => 'o.0000',
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
        
            if( isset($stockData[$stockAttribute]) )
                $stock[$stockAttribute] = $stockData[$stockAttribute] ?: 0;

            $stockItem->setData($stockAttribute,$stock[$stockAttribute]); 
        } 

        $stockItem->save();

        return $stockItem;
    }

    
}