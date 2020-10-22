<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once 'class/scrape.php';

    $site_name = isset($_GET['name']) ? $_GET['name'] : null;
    $url = isset($_GET['url']) ? $_GET['url'] : null;
    $size = isset($_GET['size']) ? $_GET['size'] : "";
    $from = isset($_GET['from']) ? $_GET['from'] : "";
    $count_url =  isset($_GET['count']) ? $_GET['count'] : null;
    
    $params['site_name'] = $site_name;
    $params['url'] = $url;
    $params['size'] = $size;
    $params['from'] = $from;
    $params['count_url'] = $count_url;
    
    if($site_name != null && $count_url == null ) {
        $scrape = new Scrape();
        $data = $scrape->site($params);
        http_response_code(200);
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
    } else if ( $site_name == null && $url != null ) {
        $scrape = new Scrape();
        $data = $scrape->parseContent($url);
        http_response_code(200);
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
    } else if (  $site_name == null && $count_url != null ){
        $scrape = new Scrape();
        $data = $scrape->countUrl($params);
        http_response_code(200);
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
    }  else {
        http_response_code(404);
        echo json_encode("Sitename not found.");
    }
?>