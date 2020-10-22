<?php

// $tablename = trim($argv[1]);
// $url = trim($argv[2]);

$servername = "127.0.0.1";
$dbname = "recipesearchdb";
$username = "recipesearchsql";
$password = "pIfanlwathuS0utr";
$tablename = "simplyrecipes.com";

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

//$url = "https://www.simplyrecipes.com/sitemap-pt-recipe-2020-10.xml";
$site_urls = array(
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2020-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2020-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2020-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2020-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2020-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2020-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2020-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2020-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2020-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2020-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2019-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2019-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2019-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2019-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2019-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2019-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2019-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2019-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2019-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2019-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2019-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2019-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2018-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2018-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2018-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2018-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2018-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2018-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2018-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2018-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2018-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2018-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2018-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2018-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2017-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2017-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2017-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2017-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2017-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2017-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2017-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2017-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2017-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2017-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2017-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2017-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2016-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2016-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2016-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2016-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2016-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2016-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2016-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2016-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2016-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2016-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2016-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2016-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2015-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2015-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2015-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2015-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2015-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2015-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2015-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2015-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2015-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2015-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2015-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2015-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2014-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2014-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2014-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2014-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2014-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2014-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2014-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2014-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2014-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2014-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2014-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2014-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2013-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2013-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2013-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2013-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2013-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2013-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2013-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2013-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2013-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2013-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2013-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2013-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2012-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2012-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2012-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2012-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2012-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2012-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2012-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2012-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2012-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2012-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2012-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2012-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2011-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2011-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2011-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2011-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2011-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2011-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2011-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2011-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2011-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2011-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2011-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2011-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2010-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2010-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2010-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2010-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2010-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2010-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2010-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2010-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2010-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2010-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2010-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2010-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2009-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2009-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2009-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2009-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2009-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2009-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2009-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2009-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2009-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2009-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2009-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2009-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2008-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2008-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2008-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2008-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2008-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2008-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2008-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2008-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2008-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2008-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2008-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2008-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2007-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2007-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2007-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2007-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2007-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2007-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2007-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2007-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2007-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2007-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2007-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2007-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2006-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2006-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2006-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2006-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2006-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2006-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2006-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2006-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2006-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2006-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2006-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2006-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2005-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2005-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2005-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2005-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2005-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2005-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2005-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2005-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2005-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2005-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2005-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2005-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2004-12.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2004-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2004-10.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2004-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2004-08.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2004-07.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2004-06.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2004-05.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2004-04.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2004-03.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2004-02.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2004-01.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2003-11.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2003-09.xml', 
   'https://www.simplyrecipes.com/sitemap-pt-recipe-2003-08.xml'
);
foreach ($site_urls as $site_url){ 
	$myXMLData = file_get_contents($site_url, false, $context);
	$xml = new SimpleXMLElement($myXMLData);
	$array = (array)($xml);
	foreach ($array['url'] as $val){	
		$url = $val->loc;
		$content = file_get_contents($val->loc, false, $context);
		if(strpos($content, '@type": "Recipe') !== false || strpos($content, '@type":"Recipe') !== false ){
			$sql = "INSERT INTO `$tablename` (url, scrape, updated, created) VALUES ('".$url."', '".$scrape."', '".$scrape_date."', '".$date_created."')";

			if ($conn->multi_query($sql) === TRUE) {
				echo "New url created successfully -> " . $val->loc . PHP_EOL;
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		} else {
				echo "Not Found " .  $val->loc . PHP_EOL; 
		}
	}
}

$conn->close();
