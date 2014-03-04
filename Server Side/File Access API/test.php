<?php
//test
class requestBroker{
	
	var $brokerData = array(
		"broker_header"	=> "",
		"broker_errors"	=> "",
		"session_user" 	=> "",
		"session_token" => "",
		"globe_id" 		=> "",
		"broker_msg"	=> ""
	);
		
	function __construct($request_header, $session_user){
		$this -> brokerData['broker_header'] = $request_header;
		$this -> brokerData['session_user'] = $session_user;
	}
	
	function setValue($type, $value){
		$value = $this->sanitiseValue($value);
		$this -> brokerData[$type] = $value;
	}
	
	private function sanitiseValue($data){
		$data = filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$data = filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		$data = strtoupper($data);
		return $data;
	}
	
	function returnJSON(){
		header('Content-Type: application/json');
		return json_encode($this->brokerData );
	}
}

$bills = new requestBroker("head", "ajqshake");
$bills->setValue("broker_header", "me head");
$jsonresponse = $bills->returnJSON();
echo $jsonresponse;
?>