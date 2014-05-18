<?php 
/*
File Access API - Globlock
Filename:	FileAccessAPI.php
Version: 	1.2
Author: 	Alex Quigley, x10205691
Created: 	10/02/2014
Updated: 	07/04/2014

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
	//include 'configurations.php';
	//include 'logWrite.php';
	//include 'dbconnection.php';
	//include 'databaseBroker.php';
	//include 'sessionHandler.php';
	//include 'encryptionHelper.php';
	//include 'requestBroker.php';
	//include 'userHandler.php';
	//include 'globeHandler.php';
	
	include 's_logWrite.php';
	include 'b_configBroker.php';
	include 'b_databaseBroker.php';
	include 'b_requestBroker.php';
	include	'h_encryption_handler.php';
	include 'h_session_handler.php';
	include 'h_user_handler.php';
	include 'h_globe_handler.php';
	include 'h_file_handler.php';
	
	include 't_functionTimer.php';
	
	
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
				if (validUser($broker)) 
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
				reAssignGlobe($broker);
				break;
			case "DROP":
				unAssignGlobe($broker);
				break;
			case "PULL":
				handlePull($broker);
				break;
			case "PUSH":
				pushFiles($broker);
				break;
		}
		echo $broker->returnJSON();
}
/** */
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

/** */
function handleSessionToken(&$broker){
	//$super = isSuper(&$broker);
	$super = "1";
	//$session_token = $super . sh_getSessionToken();
	$session_token = sh_getSessionToken();
	$broker->setValue('header', 'type', "SESSION TOKEN RESPONSE");
	$broker->setValue('session', 'token', $session_token);
}

/** */
function handleValidation(&$broker){
	$broker->setValue('header','type', "VALIDATE RESPONSE");
	//Pass broker to validSession with activity at 1 (gets updated)
	if (!(isset($_POST["session_token"]))) throw new Exception("Exception Thrown (SESSION TOKEN NOT SET)");
	$broker->setValue("session", "token", $_POST["session_token"]);

	if (sh_validSessionToken($broker, 1)) {//TO DO  Validate for production
		//echo "<br/>Temp Valid Session<br/>";
		gh_validateGlobe($broker);
	}//TO DO  Validate for production
}

/** */
function handleAbort(&$broker){
	$broker->setValue('header','type', "ABORT RESPONSE");
	if (!(isset($_POST["session_token"]))) throw new Exception("Exception Thrown (SESSION TOKEN NOT SET)");
	$broker->setValue("session", "token", $_POST["session_token"]);
	if (sh_validSessionToken($broker, 2)) {
		//echo "<br/>Session Aborted<br/>";
		$broker->setValue('header','message', "ABORT SUCCESSFUL");
		
	}	
}

function handleSet(&$broker){
	$broker->setValue('header','type', "SET RESPONSE");
	//Pass broker to validSession with activity at 2 (gets updated to -1 and dropped)
	if (!(isset($_POST["session_token"]))) throw new Exception("Exception Thrown (EMPTY POST):");
	$broker->setValue("session", "token", $_POST["session_token"]);
	if (sh_validSessionToken($broker, 2)) { //TO DO  Validate for production
		//echo "<br/>Temp Valid Session<br/>";
		$broker->setValue("header", "message", "Valid Session");
		gh_setGlobeProject($broker);
	} else {
		$broker->setValue("header", "message", "InValid Session");
	}
	//TO DO
	// gh_setGlobeProject($broker);
}

/** */
//PULL FILES
function handlePull(&$broker){
	try{
		//echo "<br/>Attempting To Handle Pull<br/>"; 
		//if(validSession($broker, 2)){
			$broker->setValue('header', 'type', "PULL RESPONSE");
			$fileDetails = gh_getGlobeRevisionDetails($broker);
			//echo "<br/>File Details: <br/>"; 
			//print_r($fileDetails );
			if (($fileDetails['globe_id']==-1)||($fileDetails['asset_revision']==-1)) {
				//echo "<br/>Globe/Revision Failed!<br/>"; 
				throw new Exception("Exception Thrown (INVALID ID OR REVISION):");
			} else {
				//echo "<br/>All good!<br/>"; 
			}
			fh_pullRequest($broker, $fileDetails['globe_id']);
		//}
		}catch(Exception $e){
			//echo "<br/>ERROR!! ".$e."<br/>"; 
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
//ABORT SESSION
function abortSession(&$broker){
	$broker->setValue('header', 'type', "ABORT RESPONSE");
	if(validSession($broker, 2)){
	echo "<br/>Valid<br/>";
		$sessionToken = $broker->brokerData['session']['token'];
		disposeSessions($sessionToken);
	}
}

/** */
//SET GLOBE
function assignGlobe(&$broker){
	if(validSession($broker, 2)){
		$broker->setValue('header', 'type', "SET RESPONSE");
		if (globeAssignable($broker)) assignNewGlobeID($broker);
	}
}

/** */
//FORCE GLOBE
function reAssignGlobe(&$broker){
	if(validSession($broker, 2)){
		$broker->setValue('header', 'type', "FORCE RESPONSE");
		globeOverwrite($broker);
	}
}

/** */
//DROP GLOBE
function unAssignGlobe(&$broker){
	if(validSession($broker, 2)){
		$broker->setValue('header', 'type', "DROP RESPONSE");
		dropAsset($broker);
	}
}

/** */
//PULL FILES
function pullFiles(&$broker){
	if(validSession($broker, 2)){
		$broker->setValue('header', 'type', "PULL RESPONSE");
		$globe_id = searchGlobeProject($broker);
		if ($globe_id > 0){
			pullRequest($broker, $globe_id);
		}
	}
}

/** */
//PUSH FILES
function pushFiles(&$broker){
	// TO DO - Handle validGlobe & project from here
	if(validSession($broker, 2)){
		$broker->setValue('header', 'type', "PUSH RESPONSE");
		$globe_id = searchGlobeProject($broker);
		if ($globe_id > 0){
			pushRequest($broker, $globe_id);
		}
	}
}




?>