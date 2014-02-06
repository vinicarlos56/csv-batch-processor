<?php

namespace Processors\Magento;

use Processors\BatchProcessorInterface;
use Repositories\Magento\CatalogRepository;
use Helpers\Magento\AttributeManager;
use Keboola\Csv\CsvFile;

class ProductImporter implements BatchProcessorInterface
{
    private $catalogRepository;
    private $attributeManager;
    private $csvHeader;

    public function __construct(CatalogRepository $catalogRepository)
    {
        $this->catalogRepository = $catalogRepository;
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

        foreach ( $file as $row ) {

            if( $row == $this->csvHeader or $this->rowIsEmpty($row) ) continue;

            $row     = $this->buildAssoc($row);
            $product = $this->catalogRepository->productExists($row['sku']); 

            if ( $product ) {

                $this->catalogRepository->updateProductStock($product,$row);

            } else {

                $this->catalogRepository->createProduct($row);
            }

        }

        echo "End of file: ".$file_name;

    }

    public function rowIsEmpty($row)
    {
        $row = array_filter($row);

        return empty($row);
    }


    // public function updateProductStock($product,$stockData)
    // {
    //     $stockItem = $this->catalogRepository->updateStock($product,$stockData); 
    //
    //     $product->getOptionInstance()->unsetOptions()->clearInstance();
    //     $stockItem->clearInstance();
    //     $product->clearInstance();
    //
    //     echo "Product updated ".$stockData['sku'];
    // }

    // public function loadAttributes($productData,$attributesArray)
    // {
    //     foreach ($attributesArray as $attributeName) {
    //
    //         if ( ! $this->attributeManager->attributeValueExists($attributeName,$productData[$attributeName]) ) {
    //             
    //             $this->attributeManager->createAttribute($attributeName,$productData[$attributeName]);
    //
    //         }
    //
    //         $attributeId  = $this->attributeManager->getAttributeId($attributeName,$productData[$attributeName]);
    //         $productData[$attributeName] = $attributeId;
    //     }
    //
    //     return $productData;
    // }

    // public function createProduct($productData)
    // {
    //         $productData = $this->loadAttributes($productData,array('color','tamanho'));
    //         
    //         $product = $this->catalogRepository->createSimpleProduct($productData);            
    //         $stock   = $this->catalogRepository->updateStock($product,$productData);            
    //        
    //         $product->clearInstance();
    //         $product->getOptionInstance()->unsetOptions()->clearInstance();
    //         $stock->clearInstance();
    //
    //         echo "Product Created ".$productData['sku'] ;
    //
    // }

}
