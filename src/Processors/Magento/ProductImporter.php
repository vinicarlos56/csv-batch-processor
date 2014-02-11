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

                $this->catalogRepository->updateStock($row);

            } else {

                $this->catalogRepository->createSimpleProduct($row);
                $this->catalogRepository->updateStock($row);

            }

        }

        echo "End of file: ".$file_name;

    }

    public function rowIsEmpty($row)
    {
        if ( ! $row or ! is_array($row) ) return true;

        $row = array_filter($row);

        return empty($row);
    }
}
