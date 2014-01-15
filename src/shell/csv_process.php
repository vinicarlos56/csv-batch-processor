<?php

require_once __DIR__.'/../config/config.php';
require_once $config['magento_full_path'];

use Keboola\Csv\CsvFile;
use Helpers\Magento\AttributeManager;
use Repositories\Magento\CatalogRepository;
use Repositories\Magento\EavCatalogProductRepository;
use Processors\Magento\ProductImporter;


Mage::app('admin', 'store', array('global_ban_use_cache'=>true))->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$catalog_repository   = new CatalogRepository(Mage);
$attribute_repository = new EavCatalogProductRepository(Mage);
$attribute_manager    = new AttributeManager($attribute_repository);
$magento_processor    = new ProductImporter($catalog_repository,$attribute_manager);

$magento_processor->process(new CsvFile($argv[1]),basename($argv[1]));

