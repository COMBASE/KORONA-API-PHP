<?php
require_once("KoronaApi.class.php");


$token = "your-token";

$api = new KoronaApi($token);

$lists = $api->getUpdates("priceLists",0,100,0);

$offset = 0;
$limit = 100;
do {
	echo "load $limit products with offset $offset\r\n";
	$products = $api->getUpdates("products", 0, $limit, $offset);
	
	foreach($products as $product)
	{
		if ($product->deleted)
			continue;
		echo "check product ".$product->name."\n";
		foreach($lists as $list)
		{
			if ($list->deleted)
				continue;
			unset($value);
			foreach($product->prices as $price)
			{
				if ($price->priceList == $list->uuid && !isset($price->organizationalUnit))
				{
					$value = $price->value;
					echo "found price for ".$list->name."\r\n";
				}
			}
	
			if (!isset($value))
				file_put_contents($list->number."_noprice.txt",$product->number."\r\n",FILE_APPEND);
		}
	}
	$offset += $limit;
}
while(count($products) == $limit);


