<?php
    class Scrape{

        // Columns
        // public $id;

        public function site($params){
            
            switch ($params['site_name']) {
                case "tasty.co":
                    return $this->tastyCo($params);
                    break;
                case "recipes.net":
                    return $this->recipesNet($params);
                    break;
                // case: "delish.com":
                //     return "delish.com";
                //     break;
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

        public function parseContent($url){
            $content = file_get_contents($url);
            preg_match_all('#<script type="application\/ld\+json">(.*?)</script>#is', $content, $matches);
            preg_match_all('#<title>(.*?)</title>#is', $content, $array_title);
            $metatags = get_meta_tags($url);
            
            $json_data  = trim(preg_replace('/\s\s+/', ' ', $matches[1][0]));
            $recipe_data = json_decode($json_data);

            if (!$recipe_data->recipeInstructions && !$recipe_data->recipeIngredient)
                return;
            //print_r($recipe_data);
            $data = array();
            $data["page_id"] = hash('sha256', $url);
            $data["source"] = $this->getDomainName($url);

            $data["metadata"] = array(
                "title"=>(isset($array_title[1][0])) ? $array_title[1][0] : $metatags['twitter:title'], 
                "description"=>$metatags['description'] );
            $data["category"] = $recipe_data->recipeCategory;
            $data["sub_categories"] = array();

            $data["recipe_url"] = $url;
            $data["recipe_name"] = str_replace("Recipe by Tasty", "", $recipe_data->name);
            $data["recipe_img"] =  $recipe_data->image;
            $data["recipe_pin_img"] = [];
            $data["ratings"] = array("average" => (isset($recipe_data->aggregateRating->ratingValue))  ? $recipe_data->aggregateRating->ratingValue : null, "total"=>(isset($recipe_data->aggregateRating->ratingCount))  ? $recipe_data->aggregateRating->ratingCount : null);

            $data["recipe_video_embedded"] = "";
            $data["recipe_type"] = "";
            $data["summary"] = (isset($recipe_data->description)) ? $recipe_data->description : null;
            $data["servings"] = $recipe_data->recipeYield;
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
                    "value"=>$instruction->text, 
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
                    "ing_name"=> trim($ingredient),
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
            $data = array(
                    'courses'=>$this->getCourses($recipe_data->recipeCategory),
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
            // [nutrition] => stdClass Object
            // (
            //     [@type] => NutritionInformation
            //     [calories] => 287 kcal
            //     [carbohydrateContent] => 62 g
            //     [proteinContent] => 7 g
            //     [fatContent] => 2 g
            //     [saturatedFatContent] => 1 g
            //     [cholesterolContent] => 70 mg
            //     [sodiumContent] => 624 mg
            //     [fiberContent] => 5 g
            //     [sugarContent] => 24 g
            //     [servingSize] => 1 serving
            // )
            // "nutritional_facts": {
            //     "serving_size": {
            //       "qty": "",
            //       "unit": ""
            //     },
            // }
            // if($nutritions){
            //     foreach( $nutritions as $key => $nutrition ){ 
            //         $nutrition['label']; 
            //         $nutrition['value'] . $nutrition['unit']  
            //     }

            // }
            return;
        }

    }
?>