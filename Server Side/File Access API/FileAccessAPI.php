<?php 
/*
File Access API - Globlock
Filename:	FileAccessAPI.php
Version: 	1.2
Author: 	Alex Quigley, x10205691
Created: 	10/02/2014
Updated: 	01-Apr-14

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
	include 'userHandler.php';
	include 'globeHandler.php';
	
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
	//echo $broker->brokerData['header']['type'];
	return true;
}	

function handleRequest(&$broker){
		//To DO - Handle Request, start to finish
		$broker->setValue("header", "type", $_POST["request_header"]);
		if (!$broker->validateHeader()) {
			$broker->handleErrors("BAD REQUEST: UNDEFINED OR MALFORMED HEADER REQUEST", 400);
			echo $broker->returnJSON();
			return false;
		}
		switch ($broker->brokerData['header']['type']){
			case "HANDSHAKE":	//
				$broker->setValue('header', 'type', $_POST["request_header"]);
				echo returnHandshake($broker);
				break;
			case "SESSION":
				if (validUser($broker)) getSessionToken($broker);
				echo $broker->returnJSON();
				break;
			case "VALIDATE":
				reqValidate($broker);
				echo $broker->returnJSON();
				break;
		}
}

/**
HANDSHAKE	- in [message], out [response]
SESSION		- in [user/pass], out [Session Token]
VALIDATE	- in [Session, Globe ID], out [Action list]
ABORT		- in [Session, Abort Header], session dropped
SET			- in [Session, Globe Project, Globe ID], out [success result]
FORCE		- in [Session, Globe Project, Globe ID], out [success result]
DROP		- in [Session, Globe Project, Globe ID], out [success result]
PUSH		- in [Session, Globe Project, Globe ID, Files], out [success result]
PULL		- in [Session, Globe Project, Globe ID], out [File list]

 */


/** */
//VALIDATE GLOBE
function reqVALIDATE(&$broker){
	$broker->setValue('header','type', "VALIDATE RESPONSE");
	//Pass broker to validSession wityh activity at 1 (gets updated)
	//if (validSession($broker, 1)){
		validGlobe($broker);
	//}
	
	
}






function returnHandshake(&$broker){
	if (empty($_POST["request_body"])){ 
		$broker->handleErrors("LENGTH REQUIRED: MESSAGE REQUEST BODY EMPTY",411);	
		echo $broker->returnJSON();
		return false;
	} else {
		$broker->setValue('header', 'message', $_POST["request_body"]);
		$message = getHandShakeResponse($broker->brokerData['header']['message']);
		$broker->setValue('header','type', "HANDSHAKE RESPONSE");
		$broker->setValue('header', 'message', $message);
		return $broker->returnJSON();
	}
}

function getSessionToken(&$broker){
	$message = generateSessionToken();
	$broker->setValue('header', 'type', "SESSION TOKEN RESPONSE");
	$broker->setValue('session', 'token', $message);
}
?>