<?php

require_once __DIR__.'/../config/config.php';
require_once $config['magento_full_path'];

use Processors\Magento\ProductImporter;
use Keboola\Csv\CsvFile;

Mage::app('admin', 'store', array('global_ban_use_cache'=>true))->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$magento_processor = new ProductImporter(Mage);
$magento_processor->process(new CsvFile($argv[1]),basename($argv[1]));

