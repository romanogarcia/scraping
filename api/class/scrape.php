<?php
    class Scrape{

        // Columns
        private $host = "127.0.0.1";
        private $db_name = "recipesearchdb";
        private $username = "recipesearchsql";
        private $password = "pIfanlwathuS0utr"; 

        public $conn;

        public function __construct(){
            
        }

        public function site($params){
            
            switch ($params['site_name']) {
                case "tasty.co":
                    return $this->tastyCo($params);
                    break;
                case "recipes.net":
                    return $this->recipesNet($params);
                    break;
                case "cookingclassy.com":
                    return $this->siteMultiUrl($params);
                    break;
                case "spendwithpennies.com":
                    return $this->siteMultiUrl($params);
                    break;
                case "gimmesomeoven.com":
                    return $this->siteMultiUrl($params);
                    break;
                case "cafedelites.com":
                    return $this->siteMultiUrl($params);
                    break;
                case "skinnytaste.com":
                    return $this->siteMultiUrl($params);
                    break;
                default:
                    return "Sitename not found or invalid!";
            }

        }

        public function tastyCo( $params ) {
            if (!empty($params['size']) && !empty($params['from'])){
                $size = $params['size'];
                $from = $params['from'];
                $api_url = "https://tasty.co/api/proxy/tasty/food/recent?size=$size&from=$from&featured=false";
                $response = file_get_contents($api_url);
                $result = $this->formatTastyResponse($response);

                return $result;
            } else {
                // Single url request
                ;
            }
        }

        public function recipesNet(){
            return "REcipesNET";
        }

        public function siteMultiUrl($params){

            $site_name=$params['site_name'];
            $url=$params['url'];
            $size=$params['size'];
            $from=$params['from'];

            // Create connection
            $conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT * FROM `$site_name` LIMIT $size OFFSET $from";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $url_array[] = $row["url"];
                }
            } else {
                echo "0 results";
            } 

            return $this->getSiteContent($url_array);
        }

        public function formatTastyResponse($response){
            $urls = array();
            $data = array();
            $items = json_decode($response);
            foreach ($items->items as $item){
                $recipe_url = "https://tasty.co" . "/" . $item->type . "/" . $item->slug;
                if($this->parseContent($recipe_url))
                    $data[] = $this->parseContent($recipe_url);
            }
            return $data;
        }

        public function getSiteContent($urls){
            $dat = array();
            foreach($urls as $url){
                $data[] = $this->parseContent($url);
            }

            return $data;
        }

        public function parseContent($url){
            $content = file_get_contents($url);
            if ($this->getDomainName($url) == "cookingclassy.com" || 
                $this->getDomainName($url) == "spendwithpennies.com" || 
                $this->getDomainName($url) == "gimmesomeoven.com" 
                //$this->getDomainName($url) == "skinnytaste.com"
            ) { 
                preg_match_all('#<script type="application\/ld\+json" class="yoast-schema-graph">(.*?)</script>#is', $content, $matches);

                if(!isset($matches[1][0])) {
                    preg_match_all("#<script type='application\/ld\+json' class='yoast-schema-graph yoast-schema-graph--main'>(.*?)</script>#is",$content,$matches);
                }

                $json_data  = trim(preg_replace('/\s\s+/', ' ', $matches[1][0]));

                $data = json_decode($json_data, true);
                // print_r($data); die();

                if (isset($data['@type'])) { ; //single
                } else { // multi
                    foreach ($data as $key=>$values){ 
                        if (is_array($values)){ 
                            foreach ($values as $val){
                                if (is_array($val['@type']))
                                    continue;
                                // echo 'values ' . $val['@type'] . PHP_EOL;
                                if ($val['@type'] == "Recipe"){
                                    $json_data = json_encode($val, true);
                                }
                            }
                        }
                    }
                }
            } else {
                preg_match_all('#<script type="application\/ld\+json">(.*?)</script>#is', $content, $matches);
                $json_data  = trim(preg_replace('/\s\s+/', ' ', $matches[1][0]));
            }

            preg_match_all('#<title>(.*?)</title>#is', $content, $array_title);
            $metatags = get_meta_tags($url);
            $recipe_data = json_decode($json_data);
            
            if (!$recipe_data->recipeInstructions && !$recipe_data->recipeIngredient)
                return;
            //print_r($recipe_data);
            $data = array();
            $data["page_id"] = hash('sha256', $url);
            $data["source"] = $this->getDomainName($url);

            $data["metadata"] = array(
                "title"=>(isset($array_title[1][0])) ? $array_title[1][0] : $metatags['twitter:title'], 
                "description"=>(isset($metatags['description'])) ? $metatags['description'] : null);
            $data["category"] = (isset($recipe_data->recipeCategory)) ? $recipe_data->recipeCategory : null;
            $data["sub_categories"] = array();

            $data["recipe_url"] = $url;
            $data["recipe_name"] = str_replace("Recipe by Tasty", "", $recipe_data->name);
            $data["recipe_img"] =  $recipe_data->image;
            $data["recipe_pin_img"] = [];
            $data["ratings"] = array("average" => (isset($recipe_data->aggregateRating->ratingValue))  ? $recipe_data->aggregateRating->ratingValue : null, "total"=>(isset($recipe_data->aggregateRating->ratingCount))  ? $recipe_data->aggregateRating->ratingCount : null);

            $data["recipe_video_embedded"] = "";
            $data["recipe_type"] = "";
            $data["summary"] = (isset($recipe_data->description)) ? $recipe_data->description : null;
            $data["servings"] = (isset($recipe_data->recipeYield)) ? $recipe_data->recipeYield : null;
            $data["servings_unit"] = "";
            $data["estimated_cost"] = "";
            
            if (isset($recipe_data->prepTime)) 
                $prepTime = $this->secondsToTime($recipe_data->prepTime);
            else { $prepTime['d'] = $prepTime['h'] = $prepTime['m'] = "0"; }

            if (isset($recipe_data->cookTime)) 
                $cookTime = $this->secondsToTime($recipe_data->cookTime);
            else { $cookTime['d'] = $cookTime['h'] = $cookTime['m'] = "0"; }
            
            if (isset($recipe_data->customTime)) 
                $customTime = $this->secondsToTime($recipe_data->customTime);
            else { $customTime['d'] = $customTime['h'] = $customTime['m'] = "0"; }  
            
            if (isset($recipe_data->totalTime)) 
                $totalTime = $this->secondsToTime($recipe_data->totalTime);
            else { $totalTime['d'] = $totalTime['h'] = $totalTime['m'] = "0"; }

            $data["time"] = array(
                "prep_time"=>array(
                    "no_of_days"=>$prepTime['d'],
                    "no_of_hours"=>$prepTime['h'],
                    "no_of_minutes"=>$prepTime['m'],
                ),
                "cook_time"=>array(
                    "no_of_days"=>$cookTime['d'],
                    "no_of_hours"=>$cookTime['h'],
                    "no_of_minutes"=>$cookTime['m'],
                ),
                "custom_time"=>array(
                    "no_of_days"=>$customTime['d'],
                    "no_of_hours"=>$customTime['h'],
                    "no_of_minutes"=>$customTime['m'],
                ),
                "total_time"=>array(
                    "no_of_days"=>$totalTime['d'],
                    "no_of_hours"=>$totalTime['h'],
                    "no_of_minutes"=>$totalTime['m'],
                )
            );
            
            // $data["categories"] = (isset($recipe_data->recipeCategory)) ? $recipe_data->recipeCategory : null;
            // isset($recipe_data->recipeCategory)) ? $recipe_data->recipeCategory
            $data["categories"] = $this->getCategoriesObj($recipe_data);

            if(isset($recipe_data->tool)){ 
            // if ($recipe_data->tool){
                $data["equipments"] = $this->getEquipments($recipe_data->tool);
            } else $data["equipments"] = null;

            if ($recipe_data->recipeInstructions){
                $instructions = $this->getInstructions($recipe_data->recipeInstructions);
            } else $instructions = null;
            $data["instructions"] = $instructions;

            if ($recipe_data->recipeIngredient){
                $ingredients = $this->getIngredients($recipe_data->recipeIngredient);
            } else $ingredients = null;
            $data["ingredients"] = $ingredients;

            if ($recipe_data->nutrition){
                $nutritions = $this->getNutritionalFacts($recipe_data->nutrition);
            } else $nutritions = null;
            $data["nutritional_facts"] = $nutritions;

            $data["notes"] = (isset($recipe_data->notes)) ? $recipe_data->notes : null;
            
            return $data;
        }

        public function getDomainName($url){
            $pieces = parse_url($url);
            $domain = isset($pieces['host']) ? $pieces['host'] : '';
            if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)){
                return $regs['domain'];
            }
            return "Invalid site url!";
        }

        public function secondsToTime($time) {
            //PT1H45M
            $pos = strrpos($time, "H");
            if ($pos === false) {
                // not found...
                $sec = str_replace("M","", str_replace("PT","", $time)) * 60;
            } else {
                $time = explode("H", $time);
                $hrs = str_replace("PT","", $time[0]) * 60 * 60;
                $min = str_replace("M","", $time[1]) * 60;
                $sec = $hrs + $min;
            }

            $dtF = new \DateTime('@0');
            $dtT = new \DateTime("@$sec");
            $data['d'] = $dtF->diff($dtT)->format('%a');
            $data['h'] = $dtF->diff($dtT)->format('%h');
            $data['m'] = $dtF->diff($dtT)->format('%i'); 
            $data['s'] = $dtF->diff($dtT)->format('%s'); 
            return $data;
        }

        public function getInstructions($instructions){
            $data = array();
            foreach($instructions as $instruction) {
                $data[] = array(
                    "id"=>"", 
                    "type"=>"text",
                    "value"=>(isset($instruction->text)) ? $instruction->text : "", 
                    "image"=>array( 
                        "src"=>"",
                        "alt"=>"",
                        "title"=>"" 
                    ) 
                );
            }
            return $data;
        }

        public function getIngredients($ingredients){
            $special_chars = array('½', '⅓', '⅔', '¼', '¾', '⅕', '⅙', '⅐', '⅛');
            $data = array();
            foreach($ingredients as $ingredient) {
                $old = $ingredient;
                $amt = $s = "";
                $new = preg_replace('# {2,}#', ' ', $old); //remove the 2 spaces
                
                if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $ingredient)) { 
                    if ($old == $new){ // echo "no changes here";
                        $ar = explode(" ", $ingredient);
                        $unit = $ar[1];
                        if (in_array($ar[1], $special_chars) ){
                            $unit = "";
                        }
                        foreach ($ar as $c){
                            if (is_numeric($c) || in_array($c, $special_chars) ){ 
                                $amt .= $c . ' ';
                            }
                            else {
                                if (strpos($c, '/') !== FALSE){  // Found
                                    $amt .= $c . ' ';
                                    $unit = "";
                                }
                                else 
                                    $s .= $c . ' ';
                            }
                        }
                    } else { //"changes here";
                        $unit = "";
                    }
                } else {
                    $amt = $unit = "";
                }
                    
                $data[] = array(
                    "ing_type"=> "text",
                    "ing_amt"=> trim($amt),
                    "ing_unit"=> trim($unit),
                    "ing_name"=> trim( str_replace($amt, "", str_replace($unit, "", trim($ingredient)))),
                    "ing_notes"=> "",
                    "ing_link"=> "",
                    "ing_link_text"=> ""
                );
                $amt = $unit = "";
            }

            return $data;
        }

        public function getEquipments($equipments){
            if(!isset($equipments) || empty($equipments) )
                return null;
            
            $data = array();
            if ($equipments && (gettype($equipments)=='array' || gettype($equipments )=='object')) {
                foreach($equipments as $equipment) {
                    $data[] = array(
                        "type"=>"text", 
                        "value"=>$equipment,
                    );
                }
            }
            
            return $data;
        }

        public function getCategoriesObj($recipe_data){
            $data = [];  
            $category = (isset($recipe_data->recipeCategory)) ? $recipe_data->recipeCategory : null;
            $data = array(
                    'courses'=>$this->getCourses($category),
                    'cuisines'=>$this->getCuisines($recipe_data->recipeCuisine),
                    'difficulties'=>$this->getDifficulties($recipe_data),
                    'keywords'=> $this->getKeywords($recipe_data->keywords),
                    'healthy_recipes'=> array(),
                    'seasonalities'=> array(),
                    'meals'=>array() 
                );
        
            return $data;
        }

        public function getCourses($courses){
            //$courses = array("Breakfast","Dinner","Dipping Sauce");
            if ($courses == null) return;

            $data = [];
            if ($courses){
                if(is_array($courses)){ 
                    foreach ($courses as $course){
                        $data[] = array("value"=>$course, "type"=>"text");
                    } 
                    return $data;
                }
                $data[] = array("value"=>$courses, "type"=>"text");
            }
            
            return $data;
        }

        public function getDifficulties(){
            $data = [];
            return $data;
        }

        public function getCuisines($cuisines){
            $data = [];
            if ($cuisines){ 
                if (is_array($cuisines)){ 
                    foreach ($cuisines as $cuisine){
                        $data[] = array("value"=>$cuisine, "type"=>"text");
                    }
                }
                $data[] = array("value"=>$cuisines, "type"=>"text");
            }
            return $data;
        }

        public function getKeywords($keywords){
            $data = [];
            // $keywords = "A Luxury Meatloaf, Alabama Breakfast Souffle";
            if ($keywords){
                $values = explode(",", $keywords);
                foreach($values as $value){
                    $data[] = array("value"=>$value,"type"=>"text");
                }
            }
            return $data;
        }

        public function getNutritionalFacts($nutritions){
            $data = [];
            $d=(array)($nutritions);
            foreach( $d as $key => $value ){
                if ($key == "@type")
                    continue;
                $vars = explode(" ", $value);
                $unit = end($vars);
                $nutrition_data[$key] = array( "qty"=>trim(str_replace($unit,"",$value)), "unit"=>$unit );
            }

            return $nutrition_data;
        }

        public function countUrl($params){
            $site_name = $params['count_url'];
            $data['site_name'] = $site_name;
            // Create connection
            $conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 
            $query = "SELECT url FROM `$site_name`";
            if ($stmt = $conn->prepare($query)) {
                /* execute query */
                $stmt->execute();
                /* store result */
                $stmt->store_result();
                $data['total_rows'] = $stmt->num_rows;
                /* close statement */
                $stmt->close();
            }
            /* close connection */
            $conn->close();

            return $data;
        }

    }
?>