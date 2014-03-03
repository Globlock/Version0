<?php
//test
class requestBroker{
	
	var $brokerData = array(
	"header" => ""
	);
		
	var $request_header, $username, $password;
	var $message ="";
	var $session_token = "";
	var $globe_id = "";
		
	function __construct($request_header, $username, $password){
		$this->$brokerData['header'] = $request_header;
		//$this -> $brokerData['header'] = $request_header;
		$this -> $username = $username;
		$this -> $password = $password;
	}
	
	function setValue($type, $value){
		$this -> $type = sanitiseValue($value);
	}
	
	function sanitiseValue($data){
		$data = filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$data = filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		$data = strtoupper($data);
		return $data;
	}
	
	function returnJSON($data){
		header('Content-Type: application/json');
		return json_encode($data);
	}
}

$bills = new requestBroker("head","name","pass");

$jsonresponse = $bills->returnJSON($bills->brokerData);
echo $jsonresponse;
?>