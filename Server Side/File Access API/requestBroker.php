<?php
/*
Request Broker for File Access API - Globlock
Filename:	requestBroker.php
Version: 	1.1
Author: 	Alex Quigley, x10205691
Created: 	04/03/2014
Updated: 	04/03/2014

Dependencies:
	FileAccessAPI.php (parent)
	
Description: 
	Class used to store and manage request headers and information from the client.

Successful Operation Result:
	Successfully stores the header and content of the request and publishes as JSON when requested

Usage: 
	<?php
		include requestBroker.php;	
		$broker = new requestBroker("Undefined", null);
		echo $broker->returnJSON();
		echo $broker->handleErrors("A test error has occured");
		echo "</br>";
		echo $broker->returnJSON();
	?>
*/
class requestBroker{
	var $brokerData = array(
		"broker_header"	=> null,
		"error_code"	=> null,
		"error_message"	=> null,
		"session_user" 	=> null,
		"session_token" => null,
		"request_id"	=> null,
		"request_body"	=> null,
		"file_location" => null
	);
	var $emptyBroker; 
		
	function __construct($request_header, $session_user){
		$this -> emptyBroker = $this -> brokerData;
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
		
	private function flushBroker(){
		$this->brokerData = $this->emptyBroker;
	}
	
	function validateHeader(){
		switch ($this -> brokerData['broker_header']){
			case "HANDSHAKE":
			case "SESSION":
			case "VALIDATE":
			case "ASSIGN":
			case "FORCE":
			case "DROP":
			case "PULL":
			case "PUSH":
				return true;
			default:
				return false;
		}
	}
	
	function handleErrors($errorMessage, $errorCode){
		$this->flushBroker();
		$this -> setValue("broker_header", "ERROR!");
		$this -> setValue("error_message", $errorMessage);
		$this -> setValue("error_code", $errorCode);
	}
	
	function returnJSON(){
		header('Content-Type: application/json');
		return json_encode($this->brokerData );
	}
}

?>