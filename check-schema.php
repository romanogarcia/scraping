<?php
//Your Batch Request Number is 0759-1005-7300-0103

// <script type="application/ld+json" class="yoast-schema-graph">

$url = "https://www.spendwithpennies.com/air-fryer-home-fries/"; //yoast-

//$url = "https://tasty.co/recipe/cubano-pies-as-made-by-vivian-hernandez-jackson";
$content = file_get_contents($url);
//preg_match_all('#<script type="application\/ld\+json" class="yoast-schema-graph">(.*?)</script>#is', $content, $matches);
preg_match_all('#<script(.*?)</script>#is', $content, $matches);

// print_r($matches);
$json_data  = trim(preg_replace('/\s\s+/', ' ', $matches[0][2]));
print_r($matches[0][2]);
$recipe_data = json_decode($json_data);
print_r($recipe_data);

