<?php
#---------------------------------------------------------------#
# Request Broker for File Access API - Globlock
# Filename:	b_requestBroker.php
# Version: 	2.0
# Author: 	Alex Quigley, x10205691
# Created: 	04/03/2014
# Updated: 	18/05/2014
#---------------------------------------------------------------#
# Dependencies:
# 	GLOBLOCK.php (parent)
# 	configurations.php
#---------------------------------------------------------------#
# Description: 
# 	Class used to store and manage request 
#	headers and information from the client.
#---------------------------------------------------------------#
# Successful Operation Result:
# 	Successfully stores the header and content 
#	of the request and publishes as JSON when 
#	requested
#---------------------------------------------------------------#
# Usage: 
# 	include b_requestBroker.php;	
# 	$broker = new requestBroker("Undefined", null);
# 	echo $broker->returnJSON();
# 	echo $broker->handleErrors("A test error has occurred");
# 	echo "</br>";
# 	echo $broker->returnJSON();
#---------------------------------------------------------------#

/** REQUEST BROKER CLASS */
class requestBroker{

	/** Data Members */
	public $brokerData;
	private $emptyBroker;
	
	/** CONSTRUCTOR
	 * Takes request Header and session user as parameters
	 * and using a configuration object, creates and populates an empty broker
	 * [required] Parameter $request_header, which defines the broker header type
	 * [required] Parameter $session_user, which defines the user that requested
	 */	
		function __construct($request_header, $session_user){
			writeLogInfo("Broker Created by :". $_SERVER['SERVER_NAME'] ." | For :". $_SERVER['REMOTE_ADDR']);
			$configuration = new configurations();
			$this->emptyBroker = $configuration->extractSection("empty_broker");
			$this->brokerData = $configuration->extractSection("empty_broker");
			$this->brokerData['header']['type'] = $request_header;
			$this->brokerData['user']['name'] = $session_user;
		}

	/**
	 * SET VALUE
	 * Takes 'Section', 'Type' and 'Value' parameters
	 * Sanitises the value and assigns it the appropriate section and type
	 * [required] Parameter $section, which defines the section header to update
	 * [required] Parameter $type, which defines the sub section value to update
	 * [required] Parameter $value, which defines the value to write
	 */
		public function setValue($section, $type, $value){
			$value = $this->sanitiseValue($value);
			$this->brokerData[$section][$type] = $value;
		}

	/**
	 * SANITISE VALUE
	 * Takes 'Data' passed as a parameter and sanitises the value, for security
	 * Returns the sanitised value
	 * [required] Parameter $data, which defines the value to be sanitised
	 */	
		function sanitiseValue($data){
			$data = filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$data = filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
		}

	/**
	 * VALIDATE HEADER
	 * Validates the current brokerData header type
	 * Returns true/false if acceptable/not acceptable header type
	 */		
		function validateHeader(){
			$header = $this -> brokerData['header']['type'];
			switch ($header){
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
					writeLogInfo("Invalid Header Found :".$header, -1);
					return false;
			}
		}
	/**
	 * FLUSH BROKER
	 * Overwrites the current broker data with empty values defined during construction
	 */	
		public function flushBroker(){
			writeLogInfo("Broker Flushed!");
			$this->brokerData = $this->emptyBroker;
		}
		
	/**
	 * HANDLE ERRORS
	 * Takes 'error message' and 'code' as parameter,
	 * then flushes the broker to prevent misread on client side.
	 * Then assigns error message and code to appropriate section
	 * [required] Parameter $errorMessage, which defines the message to insert
	 * [required] Parameter $errorCode, which defines the code to insert
	 */
		function handleErrors($errorMessage, $errorCode){
			$this->flushBroker();
			$this -> setValue("header", "type", "ERROR!");
			$this -> setValue("header", "message", "Error occurred during transaction request [".$errorCode."]");
			$this -> setValue("error", "message", $errorMessage);
			$this -> setValue("error", "code", $errorCode);
			writeLogInfo("Broker Handled Error! [$errorCode]:$errorMessage", 1);
		}
		
	/**
	 * RETURN JSON
	 * Takes header encoding as a parameter which is set to 0 by default
	 * Encodes the broker data in JSON and returns to calling method 
	 * [optional] Parameter $headEncode, which defines if header is defined as JSON
	 */	
		function returnJSON($headEncode = 0){
			if ($headEncode <> 0) header('Content-Type: application/json');
			$JSON = json_encode($this->brokerData );
			writeLogInfo("JSON Response: $JSON");
			return $JSON;
		}
	
}

?>