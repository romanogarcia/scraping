<?php
//$url = "https://tasty.co/recipe/spinach-and-cheese-bread-dumplings";
/*
Request URL: https://tasty.co/api/proxy/tasty/food/recent?size=20&from=12&featured=false
Request URL: https://tasty.co/api/proxy/tasty/food/recent?size=20&from=32&featured=false
Request URL: https://tasty.co/api/proxy/tasty/food/recent?size=20&from=52&featured=false
Request URL: https://tasty.co/api/proxy/tasty/food/recent?size=20&from=732&featured=false
Request URL: https://tasty.co/api/proxy/tasty/food/recent?size=20&from=752&featured=false
Request URL: https://tasty.co/api/proxy/tasty/food/recent?size=20&from=772&featured=false
*/

$url = "https://www.allrecipes.com/recipe/234536/how-to-make-homemade-pizza-sauce/";
$data = scrape_HTML($url);

function scrape_HTML($url) {
    //$url = $_POST['scraper_target_url'];
    // $html = wp_remote_get($url);

    // preg_match("/<title>(.+)<\/title>/siU", file_get_contents($url), $matches);
    // echo $title;
    $content = file_get_contents($url);
    preg_match_all('#<script type="application\/ld\+json">(.*?)</script>#is', $content, $matches);
print_r($matches[1][0]);
/*
    $data = trim(preg_replace('/\s\s+/', ' ', $matches[1][0]));
    $data = json_decode($data);
print_r($data[1]);
/*

    $recipe_data = $data[1];
    $categorys = $recipe_data->recipeCategory;
    $ingredients = $recipe_data->recipeIngredient;
    $instructions = $recipe_data->recipeInstructions;

    console_log($recipe_data);
    console_log($ingredients);

    $display_HTML = '';

    $display_HTML .= '<div id="nested">';

    $display_HTML .= '<div class="sortable-DOM">';
        $display_HTML .= '<div class="filtered"><h2>Categorys</h2></div>';
        foreach($categorys as $category) {
            $display_HTML .= '<div class="list-group-item"><span class="handle" style="cursor:grab;margin-right:5px;">#</span>' . $category .  '</div>';
        }
    $display_HTML .= '</div>';
    
    $display_HTML .= '<div class="sortable-DOM">';
        $display_HTML .= '<div class="filtered"><h2>Ingredients</h2></div>';
        foreach($ingredients as $ingredient) {
            $display_HTML .= '<div class="list-group-item"><span class="handle" style="cursor:grab;margin-right:5px;">#</span>' . $ingredient .  '</div>';
        }
    $display_HTML .= '</div>';

    $display_HTML .= '<div class="sortable-DOM">';
        $display_HTML .= '<div class="filtered"><h2>Instructions</h2></div>';
        foreach($instructions as $instruction) {
            $display_HTML .= '<div class="list-group-item"><span class="handle" style="cursor:grab;margin-right:5px;">#</span>' . $instruction->text .  '</div>';
        }
    $display_HTML .= '</div>';

    $display_HTML .= '</div>';

    echo $display_HTML;
*/
}
