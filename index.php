<?php

ini_set('display_errors',true);
error_reporting(E_ALL);

include __DIR__.'/src/config/config.php';

require_once $config['magento_full_path'];

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;
use Helpers\CSV\CsvSplitter;
use Helpers\ProcessLocker;
use Keboola\Csv\CsvFile;
use Slim\Slim;


$app = new Slim(array(
    'mode'=>'development',
    'templates.path' => $config['templates_path']
));

$app->get('/', function () use ($app,$config){

    
    
    $app->render('page.php',array(
        'csv_files_path'=> $config['csv_files_path'],
        'log_files_path'=> $config['log_files_path'],
    ));

});

$app->get('/download_report', function () use ($app,$config){
    
    $fileName = $app->request()->params('filename');

    echo "<pre>";
    echo file_get_contents($config['log_files_path'].$fileName);



});

$app->get('/process/', function () use ($app,$config){

    $fileName = $app->request()->params('filename');

    $locker = new ProcessLocker($config['lock_file']);

    if ( ! $locker->isLocked() ) {

        $splitter = new CsvSplitter(new CsvFile($fileName),$config['output_csv_path']);
        $splitter->split($config['split_size']);

        $command = $config['nohup_path'].' '.$config['php_path'].' '.__DIR__.'/src/shell/csv_importer.php '.$config['output_csv_path'].'  > /dev/null 2>&1 &';
        $process = new Process($command,$config['proc_working_path']);

        $process->run();
    }

    $app->redirect('/csv_batch_process/index.php');

});

use Helpers\Magento\AttributeManager;
use Repositories\Magento\CatalogRepository;
use Repositories\Magento\EavCatalogProductRepository;
use Processors\Magento\ProductImporter;


$app->get('/test/', function () use ($app,$config){


Mage::app('admin', 'store', array('global_ban_use_cache'=>true))->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$catalog_repository   = new CatalogRepository(Mage);
$attribute_repository = new EavCatalogProductRepository(Mage);
$attribute_manager    = new AttributeManager($attribute_repository);
$magento_processor    = new ProductImporter($catalog_repository,$attribute_manager);
$magento_processor->process(new CsvFile('sample.csv'),'sample.csv');

// probably new usage
// $mageWrapper        = new MageWrapper(Mage);
// $eavRepository      = new EavCatalogProductRepository($mageWrapper);
// $catalogRepository  = new CatalogRepository($mageWrapper,$eavRepository);
// $magentoProcessor   = new ProductImporter($catalogRepository);

// $magento_processor->process(new CsvFile($argv[1]),basename($argv[1]));

});
$app->run();








