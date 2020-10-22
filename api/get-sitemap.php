<?php

$tablename = trim($argv[1]);
$url = trim($argv[2]);
$servername = "127.0.0.1";
$dbname = "recipesearchdb";
$username = "recipesearchsql";
$password = "pIfanlwathuS0utr";

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

$scrape_date =  date('Y-m-d H:i:s');
$date_created =  date('Y-m-d H:i:s');
$scrape = "NO";
$myXMLData = file_get_contents($url);
$contain_schema = 'type="application/ld+json"';

$xml = simplexml_load_string($myXMLData) or die("Error: Cannot create object");
foreach ($xml as $val){
	foreach($val->loc as $loc) { 
		$locXml = file_get_contents($loc, false, $context);
		$dataXml = simplexml_load_string($locXml);
		 
		foreach($dataXml as $val){
			$url = $val->loc[0];
			$content = file_get_contents($val->loc[0], false, $context);
			if(strpos($content, '@type": "Recipe') !== false || strpos($content, '@type":"Recipe') !== false ){
				$sql = "INSERT INTO `$tablename` (url, scrape, updated, created) VALUES ('".$url."', '".$scrape."', '".$scrape_date."', '".$date_created."')";

				if ($conn->multi_query($sql) === TRUE) {
				  echo "New url created successfully -> " . $val->loc[0] . PHP_EOL;
				} else {
				  echo "Error: " . $sql . "<br>" . $conn->error;
				}
			} else {
			    echo "Not Found " .  $val->loc[0] . PHP_EOL; 
			}
		}
	}
}

$conn->close();
