<?php
$search_str = '<script type="application/ld+json';
$array = array(0 => '<script type="xapplication/ld+json', 1 => '<script type="applidcation/ld+json', 2 => '<scridpt type="application/ld+json', 3 => '<script type="application/ld+json');


$key = array_search($search_str, $array); // $key = 2;
// print_r($array[$key]);


// Allrecipes check multiple type of schema
// https://www.allrecipes.com/sitemap.xml

// Tasty.co w/schema
// https://tasty.co/sitemaps/tasty/sitemap.xml

// foodnetwork.com w/schema
// https://www.foodnetwork.com/fn-dish.news-sitemap.xml   (incomplete sitemap report)

// delish.com  w/schema

// simplyrecipes.com w/schema    *** in dev  ***************
// https://www.simplyrecipes.com/sitemap.xml

// spendwithpennies.com w/schema yoast multi   *** in dev  ***************
// https://www.spendwithpennies.com/sitemap_index.xml

// pillsbury.com w/schema not ljson
// https://www.pillsbury.com/sitemap.xml

// skinnytaste.com w/schema *** in dev  ***************
// https://www.skinnytaste.com/sitemap_index.xml

// cookingclassy.com w/schema yoast multi *** in dev **************
// https://www.cookingclassy.com/sitemap_index.xml

// gimmesomeoven.com w/schema yoast multi *** in dev  ***************
// https://www.gimmesomeoven.com/sitemap_index.xml

// cafedelites.com w/schema yoast multi *** in dev ***************
// https://cafedelites.com/sitemap_index.xml

// cookieandkate.com w/schema yoast multi  *** in dev *************
// https://cookieandkate.com/sitemap_index.xml

// dinneratthezoo.com w/schema yoast multi *** in dev ************* 
//https://www.dinneratthezoo.com/sitemap_index.xml

// dinnerthendessert.com w/schema yoast multi  *** in dev ************* 
// https://dinnerthendessert.com/sitemap_index.xml

// /sitemap
// /sitemap.xml
// /sitemap_index.xml


$url = "https://recipes.net/breakfast/poached-egg/poached-eggs-with-sauteed-spinach-and-yogurt-sauce-recipe/"; 
$url = "https://www.allrecipes.com/recipe/21202/ham-and-cheese-breakfast-tortillas/";
$url = "https://www.foodnetwork.com/recipes/ellie-krieger/three-bean-and-beef-chili-recipe-1917076";
$url = "https://www.spendwithpennies.com/air-fryer-home-fries/"; //yoast-
$url = "https://www.simplyrecipes.com/recipes/easy_cucumber_peach_and_basil_salad/";
$url = "https://www.pillsbury.com/recipes/5-ingredient-cheesy-beef-enchilada-crescent-cups/69c5124c-0e18-409e-bc10-b892ddf8afdf";
$url = "https://www.skinnytaste.com/air-fryer-chicken-nuggets/";
$url = "https://www.cookingclassy.com/marinated-grilled-teriyaki-chicken/";
$url = "https://www.gimmesomeoven.com/butternut-squash-quiche/";
$url = "https://cafedelites.com/buttery-garlic-naan-recipe/";
$url = "https://cookieandkate.com/easy-tomato-salad-recipe/";
$url = "https://www.dinneratthezoo.com/honey-chicken/";
$url = "https://dinnerthendessert.com/ground-sesame-chicken/";
$url2 = "https://tasty.co/recipe/cubano-pies-as-made-by-vivian-hernandez-jackson"; //recipe only
// $url = "https://tasty.co/recipe/fajita-parchment-baked-chicken"; //multi schema
$out = file_get_contents($url);
// $start = '<script type="application/ld+json'; //" class="yoast-schema-graph">'; 
// // $start = '<script type="application/ld+json">';
// $end = "</script>";
// $startsAt = strpos($out, $start) + strlen($start);
// $endsAt = strpos($out, $end, $startsAt);
// $result = substr($out, $startsAt, $endsAt - $startsAt);
// // $variable = substr($result, 0, strpos($result, "{")); //remove after
// // $content =  substr($result, strpos($result, '">') ); //retain the {
// $content = ltrim($result, '">'); 


$content = file_get_contents($url);
preg_match_all('#<script type="application\/ld\+json" class="yoast-schema-graph">(.*?)</script>#is', $content, $matches);
$json_data  = trim(preg_replace('/\s\s+/', ' ', $matches[1][0]));
$data = json_decode($json_data, true); 
foreach ($data as $key=>$values){ 
	if (is_array($values)){ 
		foreach ($values as $val){
			if (is_array($val['@type']))
				continue;
			echo 'values ' . $val['@type'] . PHP_EOL;
		}
	}
}

$content2 = file_get_contents($url2);
$word = "yoast-schema-graph";
if(strpos($content2, $word) !== false){
    echo "Word Found! yoast" . PHP_EOL;
} else{
    echo "Word Not Found!" . PHP_EOL;
}

$myXMLData =
"<?xml version='1.0' encoding='UTF-8'?>
<note>
<to>Tove</to>
<from>Jani</from>
<heading>Reminder</heading>
<body>Don't forget me this weekend!</body>
</note>";

$xml=simplexml_load_string($myXMLData) or die("Error: Cannot create object");
print_r($xml);
die();


$data = json_decode($content, true);
print_r($result); die();

if (isset($data['@type'])) { //single
	print_r($data);
	echo PHP_EOL . "Single " . PHP_EOL;
} else { // multi
	foreach ($data as $values){
		// print_r($values);
		if ($values['@type'] == "Recipe"){
			print_r($values);
		}
		echo PHP_EOL . "multi - Single " . PHP_EOL;
	}	
}


function getDomainName($url){
    $pieces = parse_url($url);
    $domain = isset($pieces['host']) ? $pieces['host'] : '';
    if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)){
        return $regs['domain'];
    }
    return "Invalid site url!";
}
//3LebS8eFZDXNgKduXcvPKcMVZzzAJJUdtA
// IP: dev4.tell.com
// Linux:
// username: scrapeadm
// pwd: JEdr1fre6Lp9cr
// print_r($data);
// echo PHP_EOL . count($data) . PHP_EOL;
// $i = 0;


// echo $variable . PHP_EOL;
// print_r($result);
// preg_match_all('#<script type="application\/ld\+json" class="yoast-schema-graph">(.*?)</script>#is', $content, $matches);
// preg_match_all('#<script(.*?)</script>#is', $content, $matches);
// print_r($matches[0]);
// $ckey = array_search( "application", $matches[0] ); // $key = 2;
echo PHP_EOL ;
// print_r($ckey);
// echo ' ] key';
// foreach ($matches as $key => $val){
// 		// print_r($val);
// 	$ckey = array_search( $search_str, $key ); // $key = 2;
// 	print_r($ckey);
// }
// $json_data  = trim(preg_replace('/\s\s+/', ' ', $matches[0][2]));
// foreach($json_data as $data){
	
// }
// print_r($matches[0][2]);
// $recipe_data = json_decode($json_data);
// print_r($recipe_data);