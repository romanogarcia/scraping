<?php
$search_str = '<script type="application/ld+json';
$array = array(0 => '<script type="xapplication/ld+json', 1 => '<script type="applidcation/ld+json', 2 => '<scridpt type="application/ld+json', 3 => '<script type="application/ld+json');


$key = array_search($search_str, $array); // $key = 2;
// print_r($array[$key]);

// Allrecipes
// Tasty.co
// foodnetwork.com
// delish.com
// simplyrecipes.com
// spendwithpennies.com
// pillsbury.com
// skinnytaste.com
// cookingclassy.com
// gimmesomeoven.com
// cafedelites.com
// cookieandkate.com
// dinneratthezoo.com
// dinnerthendessert.com

// $url = "https://www.spendwithpennies.com/air-fryer-home-fries/"; //yoast-
$url = "https://tasty.co/recipe/cubano-pies-as-made-by-vivian-hernandez-jackson"; //recipe only
// $url = "https://tasty.co/recipe/fajita-parchment-baked-chicken"; //multi schema
$out = file_get_contents($url);
$start = '<script type="application/ld+json'; //" class="yoast-schema-graph">'; 
// $start = '<script type="application/ld+json">';
$end = "</script>";
$startsAt = strpos($out, $start) + strlen($start);
$endsAt = strpos($out, $end, $startsAt);
$result = substr($out, $startsAt, $endsAt - $startsAt);
//$variable = substr($result, 0, strpos($result, "{")); //remove after
$content = substr($result, strpos($result, '{') + 0); //retain the {
$data = json_decode($content, true);
if (isset($data['@type'])) { //single
	print_r($data);
} else { // multi
	foreach ($data as $values){
		// print_r($values);
		if (is_array($values)){ 
			foreach ($values as $val){
				if ($val['@type'] == "Recipe"){
					print_r($val);
					echo PHP_EOL . "FOUND RECIPE " . $i . PHP_EOL;
				}	
			}
		}
	}	
}

//3LebS8eFZDXNgKduXcvPKcMVZzzAJJUdtA

// print_r($data);
echo PHP_EOL . count($data) . PHP_EOL;
$i = 0;

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

