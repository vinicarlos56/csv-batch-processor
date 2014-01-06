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
            // $product_data['name'] 	  	       = $name;
            // $product_data['short_description'] = $short_description;
            // $product_data['sku'] 	  	       = $sku;
            // $product_data['status']   	       = $status;
            // $product_data['type_id'] 	       = 'simple';
            // $product_data['visibility'] 	   = 1; // catalog, search
            // $product_data['weight']     	   = $weight;
            // $product_data['price'] 		       = $price;
            // $product_data['color']      	   = $color;
            // $product_data['tamanho']  	       = $tamanho;
            // $product_data['colecao']  	       = $colecao;
            // $product_data['description'] 	   = $description;
            // $product_data['referencia'] 	   = $referencia;
            // $product_data['attribute_set_id']  = 4;  // default attribute set
            // $product_data['website_ids']       = array(1); //main website
            // 
            // $stock_data['qty'] 		           = $qty;
            // $stock_data['min_qty']  	       = 0;
            // $stock_data['is_in_stock'] 	       = 1; 
            // $stock_data['manage_stock'] 	   = 1; 
            // $stock_data['stock_id'] 	       = 1; 
            // $stock_data['use_config_manage_stock'] = 0;

            // $product_model 	 = $mage::getModel('catalog/product');
            // $product_model->setData(array_merge($product_model->getData(),$product_data));
            // $product_model->save();

            // $stock_model = $mage::getModel('cataloginventory/stock_item');
            // $stock_model->assignProduct($product_model);
            // $stock_model->setData(array_merge($stock_model->getData(),$stock_data));
            // $stock_model->save();

            // $product_model->clearInstance();
            // $product_model->getOptionInstance()->unsetOptions()->clearInstance();
            // $stock_model->clearInstance();

            echo "Product Created ".$productData['sku'] ;

        // code...
    }

    public function setCsvHeader($header)
    {
        $this->csvHeader = $header;
    }

    public function buildAssoc($row)
    {
       return array_combine($this->csvHeader,$row); 
    }

    public function process(CsvFile $file)
    {
       
        $this->setCsvHeader($file->getHeader());
        $mage = $this->magentoInstance;

        foreach ( $file as $row ) {

            $row     = $this->buildAssoc($row);
            $product = $mage::getModel('catalog/product')->loadByAttribute('sku',$row['sku']); 

            if ( $product ) {

                $this->updateProductStock($product,$row);

            } else {

                $this->createProduct($row);
            }

            gc_collect_cycles();

        }

        echo "End of file: ".basename($file_name." ");

        // fclose($file);

    }

}
