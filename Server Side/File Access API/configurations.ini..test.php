<?php
/* configurations.ini.php test */
// Parse without sections
$ini_array = parse_ini_file("configurations.ini.php");
print_r($ini_array);

// Parse with sections
$ini_array = parse_ini_file("configurations.ini.php", true);
print_r($ini_array);

echo "<br/>";
echo json_encode($ini_array);
echo "<br/>";
echo "<br/>";
echo "<br/>";

echo json_encode($ini_array["test_array"]["test1"]);

echo "<br/>";
echo "<br/>";


echo $ini_array["test_array"]["test1"][0];
?>