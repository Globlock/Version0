<?php 
/*
API and Entry Point - Globlock
Filename:	GLOBLOCK.php
Version: 	2.0
Author: 	Alex Quigley, x10205691
Created: 	10/02/2014
Updated: 	19/05/2014

Dependencies:
	package.php
	
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
	include 'package.php';
	
/** Creates an Empty Broker Object
 * If Start() returns successful, calls handleRequest 
 */
	$broker = new requestBroker("Initialised", null);
	if(!start($broker)) exit();
	handleRequest($broker);
	
	
/** Validates server request type conforms to HTTP POST,
 * If not correct format, updates the brokers error code and message,
 * and echo's the JSON response string message 
 *
 */
	function start(&$broker){
		if (!($_SERVER["REQUEST_METHOD"] == "POST")) {
			$broker->handleErrors("NON [POST] TYPE SERVER REQUEST ",121);
			echo $broker->returnJSON();
			return false;
		}
		return true;
	}	

/** HANDLE REQUEST
 * Validates Request Header Type and carries out appropriate action
 */
function handleRequest(&$broker){
		$broker->setValue("header", "type", $_POST["request_header"]);
		if (!$broker->validateHeader()) {
			$broker->handleErrors("BAD REQUEST: UNDEFINED OR MALFORMED HEADER REQUEST", 400);
			echo $broker->returnJSON();
			return false;
		}
		switch ($broker->brokerData['header']['type']){
			case "HANDSHAKE":	//
				$broker->setValue('header', 'type', $_POST["request_header"]);
				returnHandshake($broker);
				break;
			case "SESSION":
				handleSessionToken($broker);
				break;
			case "VALIDATE":
				handleValidation($broker);
				break;
			case "ABORT":
				handleAbort($broker);
				break;
			case "SET":
				handleSet($broker);
				break;
			case "FORCE":
				handleForce($broker);
				break;
			case "DROP":
				handleDrop($broker);
				break;
			case "PULL":
				handlePull($broker);
				break;
			case "PUSH":
				handlePush($broker);
				break;
		}
		echo $broker->returnJSON();
}

/** HANDSHAKE
 * Validates request body and if successful
 * generates a handshake response and assigns to broker
 */
function returnHandshake(&$broker){
	if (empty($_POST["request_body"])){ 
		$broker->handleErrors("LENGTH REQUIRED: MESSAGE REQUEST BODY EMPTY",411);	
	} else {
		$broker->setValue('header', 'message', $_POST["request_body"]);
		$message = getHandShakeResponse($broker->brokerData['header']['message']);
		$broker->setValue('header','type', "HANDSHAKE RESPONSE");
		$broker->setValue('header', 'message', $message);
	}
}

/** SESSION
 * Attempts to retrieve user from DB by calling method in user handler
 * If valid, generates a session token in session handler, and updates broker
 */
function handleSessionToken(&$broker){
	if (validUser($broker)){
		$session_token = sh_getSessionToken();
		$broker->setValue('header', 'type', "SESSION TOKEN RESPONSE");
		$broker->setValue('session', 'token', $session_token);	
	}
}

/** */
function handleValidation(&$broker){
	$broker->setValue('header','type', "VALIDATE RESPONSE");
	//Pass broker to validSession with activity at 1 (gets updated)
	if (!(isset($_POST["session_token"]))) throw new Exception("Exception Thrown (SESSION TOKEN NOT SET)");
	$broker->setValue("session", "token", $_POST["session_token"]);

	if (sh_validSessionToken($broker, 1)) {
		gh_validateGlobe($broker);
	}
}

/** */
//ABORT SESSION
function handleAbort(&$broker){
	$broker->setValue('header','type', "ABORT RESPONSE");
	if (!(isset($_POST["session_token"]))) throw new Exception("Exception Thrown (SESSION TOKEN NOT SET)");
	$broker->setValue("session", "token", $_POST["session_token"]);
	if (sh_validSessionToken($broker, 2)) {
		$broker->setValue('header','message', "ABORT SUCCESSFUL");
	}	
}
//SET GLOBE
function handleSet(&$broker){
	try{
		$broker->setValue('header','type', "SET RESPONSE");
		if (!(isset($_POST["session_token"]))) throw new Exception("Exception Thrown (EMPTY POST): ");
		$broker->setValue("session", "token", $_POST["session_token"]);
		# Activity updated to -1
		if (sh_validSessionToken($broker, 2)) {
			$broker->setValue("header", "message", "Valid Session");
			gh_setGlobeProject($broker);
		} else {
			$broker->setValue("header", "message", "InValid Session");
		}
	}catch(Exception $e){
		$broker->handleErrors($e."UNABLE TO SET GLOBE OBJECT ",121);
	}
}

/** */
//PULL FILES
function handlePull(&$broker){
	try{
		//echo "<br/>Attempting To Handle Pull<br/>"; 
		//if(sh_validSessionToken($broker, 2)){
			$broker->setValue('header', 'type', "PULL RESPONSE");
			$fileDetails = gh_getGlobeRevisionDetails($broker);
			//echo "<br/>File Details: <br/>"; 
			//print_r($fileDetails );
			if (($fileDetails['globe_id']==-1)||($fileDetails['asset_revision']==-1)) {
				throw new Exception("Exception Thrown (INVALID ID OR REVISION):");
			} else {
				//echo "<br/>All good!<br/>"; 
			}
			fh_pullRequest($broker, $fileDetails['globe_id']);
		//} else {
		//	$broker->setValue("header", "message", "InValid Session");
		//}
		}catch(Exception $e){
			//echo "<br/>ERROR!! ".$e."<br/>"; 
		}
}

/** */
//PUSH FILES
function handlePush(&$broker){
	try {
		// Ensure Globe_ID set
		if (!(isset($_POST["globe_id"]))) throw new Exception("Exception Thrown (GLOBE ID NOT SET)");
		$broker->setValue("globe", "id", $_POST["globe_id"]);
		file_put_contents('output.txt', "\n\r globe_id set ".$_POST["globe_id"]." \n\r", FILE_APPEND);
		//if(sh_validSessionToken($broker, 2)){
			$broker->setValue('header', 'type', "PUSH RESPONSE");
			$globe_id = gh_searchGlobeProject($broker);
			if ($globe_id > 0){
				file_put_contents('output.txt', "\n\r globe_id found $globe_id \n\r", FILE_APPEND);
				fh_pushRequest($broker, $globe_id);
			} else {
				file_put_contents('output.txt', "\n\r No Globe ID charles \n\r", FILE_APPEND);
			}
		//}
	}catch(Exception $e){
		file_put_contents('output.txt', "\n\r No Globe ID not Set \n\r", FILE_APPEND);
	}
}

/** */
//FORCE GLOBE
function handleForce(&$broker){
	if(validSession($broker, 2)){
		$broker->setValue('header', 'type', "FORCE RESPONSE");
		globeOverwrite($broker);
	}
}

/** */
//DROP GLOBE
function handleDrop(&$broker){
	if(validSession($broker, 2)){
		$broker->setValue('header', 'type', "DROP RESPONSE");
		dropAsset($broker);
	}
}

# HANDSHAKE	- in [message], out [response]
# SESSION	- in [user/pass], out [Session Token]
# VALIDATE	- in [Session, Globe ID], out [Action list]
# ABORT		- in [Session, Abort Header], session dropped
# SET		- in [Session, Globe Project, Globe ID], out [success result]
# FORCE		- in [Session, Globe Project, Globe ID], out [success result]
# DROP		- in [Session, Globe Project, Globe ID], out [success result]
# PUSH		- in [Session, Globe Project, Globe ID, Files], out [success result]
# PULL		- in [Session, Globe Project, Globe ID], out [File list]

?>