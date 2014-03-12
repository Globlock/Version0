<?php 
/*
File Access API - Globlock
Filename:	FileAccessAPI.php
Version: 	1.2
Author: 	Alex Quigley, x10205691
Created: 	10/02/2014
Updated: 	04/03/2014

Dependencies:
	logWrite.php (child)
	encryptionHelper.php
	
Description: 
	Receives communication of data from the (currently supported windows 7) application, 
	and depending on the response, may return the location of the files.

Successful Operation Result:
	Handshake is successful.
	RFID from client application is found in the DB and client login is accepted. 
	Files are moved to a temporary Apache published location, and the location is sent to the client.
	Client downloads the files successfully.
	Client completion flag received and files are disposed of from temporary location.

TO DO:
>>MANAGE USER ACCESS and USER TYPE
*/

	/* Global Files, Libraries and Declarations */
	include 'configurations.php';
	include 'logWrite.php';
	include 'dbconnection.php';
	include 'sessionHandler.php';
	include 'encryptionHelper.php';
	include 'requestBroker.php';
	
	/* Strain Inputs */
	//htmlspecialchars etc..


	$broker = new requestBroker("Initialised", null);
	if(!start($broker)) exit();
	handleRequest($broker);

function start(&$broker){
	if (!($_SERVER["REQUEST_METHOD"] == "POST")) {
		$broker->handleErrors("NON [POST] TYPE SERVER REQUEST ",121);
		echo $broker->returnJSON();
		return false;
	}
	return true;
}	

/** verifyUser
	//TO DO:
*/
function verifyUser(){
	//TO DO:
}

function handleRequest(&$broker){
		$broker->setValue('broker_header', $_POST["request_header"]);
		if (!$broker->validateHeader()) {
			$broker->handleErrors("BAD REQUEST: UNDEFINED OR MALFORMED HEADER REQUEST", 400);
			echo $broker->returnJSON();
			return false;
		}
		switch ($broker->brokerData['broker_header']){
			case "HANDSHAKE":
				$broker->setValue('request_body', $_POST["request_header"]);
				echo returnHandshake($broker);
				break;
			case "SESSION":
				echo returnSessionToken($broker);
				break;
		}
}

function returnHandshake(&$broker){
	if (empty($_POST["request_body"])){ 
		$broker->handleErrors("LENGTH REQUIRED: MESSAGE REQUEST BODY EMPTY",411);	
		echo $broker->returnJSON();
		return false;
	} else {
		$broker->setValue('request_body', $_POST["request_body"]);
		$message = getHandShakeResponse($broker->brokerData['request_body']);
		$broker->setValue('broker_header', "HANDSHAKE RESPONSE");
		$broker->setValue('request_body', $message);
		return $broker->returnJSON();
	}
}

function returnSessionToken(&$broker){
	$message = generateSessionToken();
	$broker->setValue('broker_header', "SESSION TOKEN RESPONSE");
	$broker->setValue('session_token', $message);
	return $broker->returnJSON();
}
?>