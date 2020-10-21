<?php

$tablename = trim($argv[1]);

$servername = "localhost";
$dbname = "recipesearchdb";
$username = "root"; //"recipesearchsql";
$password = "Roman123456"; //"pIfanlwathuS0utr";
// $tablename = "cookingclassy.com"; // "spendwithpennies.com";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


$context = stream_context_create(
    array(
        "http" => array(
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
        )
    )
);

// $url = "https://dinnerthendessert.com/sitemap_index.xml";
// $url = "https://www.dinneratthezoo.com/sitemap_index.xml";
// $url = "https://cookieandkate.com/sitemap_index.xml";
// $url = "https://cafedelites.com/sitemap_index.xml";
// $url = "https://www.gimmesomeoven.com/sitemap_index.xml";
$url = "https://www.cookingclassy.com/sitemap_index.xml";
// $url = "https://www.skinnytaste.com/sitemap_index.xml";
// $url = "https://www.pillsbury.com/sitemap.xml"; // not ldjson schema
// $url = "https://www.spendwithpennies.com/sitemap_index.xml";
// $url = "https://www.simplyrecipes.com/sitemap.xml"; //forbidden error
// $url = "https://www.allrecipes.com/sitemap.xml";

$scrape_date =  date('Y-m-d H:i:s');
$date_created =  date('Y-m-d H:i:s');
$scrape = "NO";
$myXMLData = file_get_contents($url);
// $contain_schema = 'type":"Recipe';
$contain_schema = 'type="application/ld+json"';

$xml = simplexml_load_string($myXMLData) or die("Error: Cannot create object");
// print_r($xml);die();
foreach ($xml as $val){
	foreach($val->loc as $loc) { 
		$locXml = file_get_contents($loc, false, $context);
		$dataXml = simplexml_load_string($locXml);
		 
		foreach($dataXml as $val){
			$url = $val->loc[0];
			$content = file_get_contents($val->loc[0]);
			if(strpos($content, '@type": "Recipe') !== false || strpos($content, '@type":"Recipe') !== false ){
				$sql = "INSERT INTO `$tablename` (url, scrape, updated, created) VALUES ('".$url."', '".$scrape."', '".$scrape_date."', '".$date_created."')";

				if ($conn->multi_query($sql) === TRUE) {
				  echo "New url created successfully -> " . $val->loc[0] . PHP_EOL;
				} else {
				  echo "Error: " . $sql . "<br>" . $conn->error;
				}

			} else{
			    echo "Not Found " .  $val->loc[0] . PHP_EOL; 
			}
		}

	}
}

$conn->close();
