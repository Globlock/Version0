<?php
/*
Request Broker for File Access API - Globlock
Filename:	requestBroker.php
Version: 	1.2
Author: 	Alex Quigley, x10205691
Created: 	04/03/2014
Updated: 	12/03/2014

Dependencies:
	FileAccessAPI.php (parent)
	configurations.php (sibling)
	
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

	public $brokerData;
	private $emptyBroker;
	
	function __construct($request_header, $session_user){
		$configuration = new configurations();
		$this->emptyBroker = $configuration->extractSection("empty_broker");
		$this->brokerData = $configuration->extractSection("empty_broker");
		$this->brokerData['header']['type'] = $request_header;
		$this->brokerData['user']['name'] = $session_user;
	}
	
	public function setValue($section, $type, $value){
		$value = $this->sanitiseValue($value);
		$this->brokerData[$section][$type] = $value;
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
		
	function validateHeader(){
		switch ($this -> brokerData['header']['type']){
			case "HANDSHAKE":
			case "SESSION":
			case "ABORT":
			case "VALIDATE":
			case "SET":
			case "REDO":
			case "DROP":
			case "PULL":
			case "PUSH":
			case "TEST":
				return true;
			default:
				return false;
		}
	}

	public function flushBroker(){
		$this->brokerData = $this->emptyBroker;
	}
	
	function handleErrors($errorMessage, $errorCode){
		$this->flushBroker();
		$this -> setValue("header", "type", "ERROR!");
		$this -> setValue("header", "message", "Error occurred during transaction request [".$errorCode."]");
		$this -> setValue("error", "message", $errorMessage);
		$this -> setValue("error", "code", $errorCode);
	}
	
	function returnJSON($headEncode = 0){
		if ($headEncode <> 0) header('Content-Type: application/json');
		return json_encode($this->brokerData );
	}
}

?>