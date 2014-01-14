<?php

namespace Processors\Magento;

use Processors\BatchProcessorInterface;
use Keboola\Csv\CsvFile;

class ProductImporter implements BatchProcessorInterface
{
    private $magentoInstance;
    private $csvHeader;

    public function __construct(Mage $magentoInstance)
    {
        $this->magentoInstance = $magentoInstance;
    }

    public function updateProductStock($product,$stockData)
    {
            $mage        = $this->magentoInstance;
            $productId   = $product->getId();
            $stockItem   = $mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            $stockItemId = $stockItem->getId();
            $stock       = array();

            if (!$stockItemId) {
                $stockItem->setData('product_id', $product->getId());
                $stockItem->setData('stock_id', 1); 
            } else {
                $stock = $stockItem->getData();
            }

            foreach($stock as $stockAttribute => $value) {
                $stock[$stockAttribute] = $stockData[$value];
            } 

            foreach($stock as $field => $value) {
                $stockItem->setData($field, $value?$value:0);
            }

            $stockItem->save();

            $product->getOptionInstance()->unsetOptions()->clearInstance();
            $stockItem->clearInstance();
            $product->clearInstance();

            echo "Product updated ".$stockData['sku'];
    }

    public function createProduct($productData)
    {
            $mage = $this->magentoInstance;

            $productData['type_id'] 	      = 'simple';
            $productData['visibility']        = 1; // catalog, search
            $productData['attribute_set_id']  = 4;  // default attribute set
            $productData['website_ids']       = array(1); //main website

            $stockData['qty']		       = $productData['qty'];
            $stockData['min_qty']  	       = 0;
            $stockData['is_in_stock'] 	       = ((int) $productData['qty']) ? 1 : 0; 
            $stockData['manage_stock']         = 1; 
            $stockData['stock_id'] 	       = 1; 
            $stockData['use_config_manage_stock'] = 0;

            $productModel = $mage::getModel('catalog/product');
            $productModel->setData(array_merge($productModel->getData(),$productData));
            $productModel->save();

            $stockModel = $mage::getModel('cataloginventory/stock_item');
            $stockModel->assignProduct($productModel);
            $stockModel->setData(array_merge($stockModel->getData(),$stockData));
            $stockModel->save();

            $productModel->clearInstance();
            $productModel->getOptionInstance()->unsetOptions()->clearInstance();
            $stockModel->clearInstance();

            echo "Product Created ".$productData['sku'] ;

    }

    public function setCsvHeader($header)
    {
        $this->csvHeader = $header;
    }

    public function buildAssoc($row)
    {
       return array_combine($this->csvHeader,$row); 
    }

    public function process(CsvFile $file,$file_name)
    {
       
        $this->setCsvHeader($file->getHeader());
        $mage = $this->magentoInstance;

        foreach ( $file as $row ) {

	    if( $row == $this->csvHeader ) continue;

            $row     = $this->buildAssoc($row);
            $product = $mage::getModel('catalog/product')->loadByAttribute('sku',$row['sku']); 

            if ( $product ) {

                $this->updateProductStock($product,$row);

            } else {

                $this->createProduct($row);
            }

            // gc_collect_cycles();

        }

        echo "End of file: ".$file_name;

    }

}
