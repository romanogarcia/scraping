<?php

$tablename = trim($argv[1]);
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
$site_urls = array(
	"https://www.allrecipes.com/sitemaps/recipe/1/sitemap.xml",
	"https://www.allrecipes.com/sitemaps/recipe/2/sitemap.xml",
	"https://www.allrecipes.com/sitemaps/recipe/3/sitemap.xml",
	"https://www.allrecipes.com/sitemaps/recipe/4/sitemap.xml",
	"https://www.allrecipes.com/sitemaps/recipe/5/sitemap.xml"
);

$myfile = fopen("allrecipes-url.txt", "w") or die("Unable to open file!");

$n=0;
foreach ($site_urls as $site_url){ 
	$myXMLData = file_get_contents($site_url, false, $context);
	$xml = new SimpleXMLElement($myXMLData);
	$array = (array)($xml);
	$n++;
	$i=0;
	foreach ($array['url'] as $val){	
		$url = $val->loc . "\n";
		echo 'array ' . $n . '  ' . $i . ' ' . $url . PHP_EOL; 
		fwrite($myfile, $url);
		$i++;
		// $content = file_get_contents($val->loc, false, $context);
		// if(strpos($content, '@type": "Recipe') !== false || strpos($content, '@type":"Recipe') !== false ){
		// 	$sql = "INSERT INTO `$tablename` (url, scrape, updated, created) VALUES ('".$url."', '".$scrape."', '".$scrape_date."', '".$date_created."')";

		// 	if ($conn->multi_query($sql) === TRUE) {
		// 		echo "New url created successfully -> " . $val->loc . PHP_EOL;
		// 	} else {
		// 		echo "Error: " . $sql . "<br>" . $conn->error;
		// 	}
		// } else {
		// 		echo "Not Found " .  $val->loc . PHP_EOL; 
		// }
	}
}
fclose($myfile);
$conn->close();

