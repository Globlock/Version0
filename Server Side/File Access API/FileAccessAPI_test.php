<?php


echo 
"<html>
<body>
<form action='FileAccessAPI.php' method='post'>
Header: <input type='text' name='request_header'><br>
Message: <input type='text' name='request_body'><br>
TEST: <input type='text' name='testtest'><br>
Session Token: <input type='text' name='session_token'><br>
Username: <input type='text' name='username'><br>
Password: <input type='text' name='password'><br>
<input type='submit'>
</form>
</body>
</html>
";

//initialize array
$myArray = array();
$myArrayfull = array();

//set up the nested associative arrays using literal array notation
$header_array = array("HEAD" => "Response Header", "value" => "");
$error_array = array("ERROR" => "No Errors", "Error Code" => 0);
$actions = array("PUSH" => 1, "PULL" => 1);
$response = array("File Location" => "1AE935B2B311367882707989F3E23F6F39475E65", "Expiry" => 1);
$footer = 
//push items onto main array with bracket notation (this will result in numbered indexes)
$myArray[] = $header_array;
$myArray[] = $error_array;
$myArray[] = $actions;
$myArray[] = $response;

//convert to json
$json = json_encode($myArray);
echo $json;
echo "</br>";
echo "</br>";
//$params = array ( "123"=>"123","231"=>"231","321"=>"321" );
$response = xmlrpc_encode ( $myArray );
echo ( $response );
echo "</br>";
testFunction();
function testFunction(){
	otherFunction();
}

function otherFunction(){
echo __METHOD__;
}
?>
