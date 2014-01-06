<?php

$new_line = PHP_EOL;

function convert($size)
{
$unit=array('b','kb','mb','gb','tb','pb');
return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}


set_time_limit(0);

require_once "/home/carlos/www/magento/app/Mage.php";

Mage::app('admin', 'store', array('global_ban_use_cache'=>true))->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$start_time = microtime(true);

ini_set('upload_max_filesize','10M');
ini_set('post_max_size','10M');
ini_set('display_errors',true);

error_reporting(E_ALL);
 
umask(0);

$count = 0;

$file_name = $argv[1];

$file = fopen($file_name,'r');

while (($line = fgetcsv($file)) !== false) { 

if ($count == 0) {
    foreach ($line as $key=>$value) {
        $cols[$value] = $key;
    } 
} 

$count++;

if ($count == 1) continue;

#Convert the lines to cols 
if ($count > 0) { 

    foreach($cols as $col=>$value) {
        unset(${$col});
        ${$col} = $line[$value];
    } 
}

// Check if SKU exists
$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku); 

if ( $product ) {

    $productId   = $product->getId();
    $stockItem   = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
    $stockItemId = $stockItem->getId();
    $stock       = array();

    if (!$stockItemId) {
        $stockItem->setData('product_id', $product->getId());
        $stockItem->setData('stock_id', 1); 
    } else {
        $stock = $stockItem->getData();
    }

    foreach($cols as $col=>$value) {
        $stock[$col] = $line[$value];
    } 

    foreach($stock as $field => $value) {
        $stockItem->setData($field, $value?$value:0);
    }

    $stockItem->save();

    $product->getOptionInstance()->unsetOptions()->clearInstance();
    $stockItem->clearInstance();
    $product->clearInstance();

    echo "$new_line Product updated $sku ".convert(memory_get_usage(true));
} else {

    $product_data['name'] 	  	       = $name;
    $product_data['short_description'] = $short_description;
    $product_data['sku'] 	  	       = $sku;
    $product_data['status']   	       = $status;
    $product_data['type_id'] 	       = 'simple';
    $product_data['visibility'] 	   = 1; // catalog, search
    $product_data['weight']     	   = $weight;
    $product_data['price'] 		       = $price;
    $product_data['color']      	   = $color;
    $product_data['tamanho']  	       = $tamanho;
    $product_data['colecao']  	       = $colecao;
    $product_data['description'] 	   = $description;
    $product_data['referencia'] 	   = $referencia;
    $product_data['attribute_set_id']  = 4;  // default attribute set
    $product_data['website_ids']       = array(1); //main website
    
    $stock_data['qty'] 		           = $qty;
    $stock_data['min_qty']  	       = 0;
    $stock_data['is_in_stock'] 	       = 1; 
    $stock_data['manage_stock'] 	   = 1; 
    $stock_data['stock_id'] 	       = 1; 
    $stock_data['use_config_manage_stock'] = 0;

    $product_model 	 = Mage::getModel('catalog/product');
    $product_model->setData(array_merge($product_model->getData(),$product_data));
    $product_model->save();

    $stock_model = Mage::getModel('cataloginventory/stock_item');
    $stock_model->assignProduct($product_model);
    $stock_model->setData(array_merge($stock_model->getData(),$stock_data));
    $stock_model->save();

    $product_model->clearInstance();
    $product_model->getOptionInstance()->unsetOptions()->clearInstance();
    $stock_model->clearInstance();

    echo "$new_line Product Created $sku ".convert(memory_get_usage(true));
}

gc_collect_cycles();

}

echo "$new_line End of file: $file_name ".convert(memory_get_usage(true));

fclose($file);
