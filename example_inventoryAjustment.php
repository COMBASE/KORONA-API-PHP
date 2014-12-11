<?php
require_once("KoronaApi.class.php");


$token = "your token";

$orgNo = "139";

$api = new KoronaApi($token);

$org = $api->getObjByNumber("organizationalUnits",$orgNo);

echo "Org: ".$org->name."\r\n\n";

file_put_contents("error.txt","# import \r\n",FILE_APPEND);
file_put_contents("404.txt","# import \r\n",FILE_APPEND);
    
$row = 0;
$importNo = 2000;
$items = array();
if (($handle = fopen("my_csv_file.csv", "r")) !== FALSE) 
{
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
	{
		$sku = trim($data[0]);
		$count = intval(trim($data[1]));
		echo "sku: $sku \t\t count: $count ";
		try {
			$result = $api->getObjByNumber("products",$sku);

			if (isset($result->uuid) && !empty($result->uuid)) {
				$adjItem = (object) array();
				$adjItem->product = $result->uuid;
				$adjItem->additionalGoods = $count;
				$items[] = $adjItem;			

				echo "OK -> ".$result->name." \r\n";
				$row++;
			}
			else
				file_put_contents("error.txt","$sku;$count\r\n",FILE_APPEND);
		}
		catch(Exception $e)
		{
			echo "404 \n";
			file_put_contents("404.txt","$sku;$count\r\n",FILE_APPEND);
		}

		if (count($items) >= 100)
		{
			echo "post list \n\n";
			$adj = (object) array();
			$adj->reason = "import";
			$adj->warehouse = $org->uuid;
			$adj->externalId = "import-$importNo";
			$adj->number = $importNo;
			$adj->time = date("Y-m-d")."T".date("h:i:sP");

			$adj->items = $items;

			//echo json_encode($adj)."\n\n\n";
			
			$result = $api->post("stocks/adjust/", $adj);

			$items = array();
			$importNo++;
			
		}
	}
	if (count($items) > 0)
	{
		echo "post list \n\n";
		$adj = (object) array();
		$adj->reason = "import";
		$adj->warehouse = $org->uuid;
		$adj->externalId = "import-$importNo";
		$adj->number = $importNo;
		$adj->time = date("Y-m-d")."T".date("h:i:sP");

		$adj->items = $items;

		//echo json_encode($adj)."\n\n\n";

		$result = $api->post("stocks/adjust/", $adj);

		$items = array();

	}
	fclose($handle);
}




?>
