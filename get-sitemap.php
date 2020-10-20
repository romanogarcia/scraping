<?php
// $servername = "localhost";
// $dbname = "recipesearchdb";
// $username = "recipesearchsql";
// $password = "pIfanlwathuS0utr";

// // Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);
// // Check connection
// if ($conn->connect_error) {
//   die("Connection failed: " . $conn->connect_error);
// }

// $sql = "INSERT INTO MyGuests (firstname, lastname, email)
// VALUES ('John', 'Doe', 'john@example.com');";

// if ($conn->multi_query($sql) === TRUE) {
//   echo "New records created successfully";
// } else {
//   echo "Error: " . $sql . "<br>" . $conn->error;
// }

// $conn->close();

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
// $url = "https://www.cookingclassy.com/sitemap_index.xml";
// $url = "https://www.skinnytaste.com/sitemap_index.xml";
// $url = "https://www.pillsbury.com/sitemap.xml"; // not ldjson schema
// $url = "https://www.spendwithpennies.com/sitemap_index.xml";
// $url = "https://www.simplyrecipes.com/sitemap.xml";
$url = "https://www.allrecipes.com/sitemap.xml";

$myXMLData = file_get_contents($url);

$xml = simplexml_load_string($myXMLData) or die("Error: Cannot create object");
// print_r($xml);die();
foreach ($xml as $val){
	foreach($val->loc as $loc) {
		
		// $locXml = file_get_contents($loc);
		$locXml = file_get_contents($loc, false, $context);
		$dataXml = simplexml_load_string($locXml);
		 
		foreach($dataXml as $val){
			echo $val->loc[0] . PHP_EOL;
		}
	}
}

function getUrlLoc($array){
	
}