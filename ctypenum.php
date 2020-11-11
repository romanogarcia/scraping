<?php 
  
// PHP program to check given string is  
// all characters are alphanumeric 
  
$string = array( 'GeeksforGeeks', '4', '5.4', '4s');
foreach ($string as $str) { 
    if ( ctype_alnum($str)) 
        echo "Yes" . $str . PHP_EOL; 
    else 
        echo "No " . $str . PHP_EOL; 
}

    $values = array(
        '-0',
        -0,
        0,
        123,
        -123,
        '123',
        '-123',
        '0123',
        '123 ',
        '0',
        '000',
        '+123',
        '1.23',
        1.23,
        '123e4',
        '0x123',
        'potato',
        'EEBD',
        false,
        null,
    );

    echo("PHP version: ".phpversion(). "<br>");

    foreach($values as $value){
        echo("TRYING WITH ");
        var_dump($value);
        echo "<br>";
        
        echo("is_int: ");
        var_dump(is_int($value));
        echo "<br>";
        
        echo("is_numeric: ");
        var_dump(is_numeric($value));
        echo "<br>";
        
        echo("regex: ");
        var_dump(preg_match('/^[0-9]+$/',$value));
        echo "<br>";
        
        echo("ctype_digit: ");
        var_dump(ctype_digit(trim((string)$value)));
        echo "<br>";
        
        echo("filter_var: ");
        var_dump(filter_var($value, FILTER_VALIDATE_INT));
        echo "<br>";
        echo "<br>";
    }
echo PHP_EOL;
   $val = "22a2";
    if (!preg_match('/^([0-9]*)$/', $val)) {
      echo "mali";
   }   else
      echo "tama";

$str = ltrim($val, "0");  
      echo trim($str);

echo PHP_EOL;
?>
  
