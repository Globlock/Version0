<?php 
/*
File Access API for Globlock
Version: 	1.1
Author: 	Alex Quigley, x10205691
Created: 	10/02/2014
Updated: 	03/03/2014

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

*/
// define variables and set to empty values

	/* Global Files, Libraries and Declarations */
	$header_type = $message = $userid = $userpass = $session = $globe_id = $identifier = "";
	//include dbConnections etc..
	//include encryptionHelper
	//include sessionHandler
	//include login
	
	/* Strain Inputs */
	//htmlspecialchars etc..
	 
	/* DB Configuration/Connectivity */
	//include dbConnections etc..
	 
	/* Error Log/Transaction Log
		error_log("You messed up!", 3, "/var/tmp/my-errors.log");
		error_log("You messed up!", 3, "/var/tmp/my-errors.log");
	*/
class requestBroker{
	var $request_header, $message;
	var $session_token = "";
	var $username = "";
	var $password = "";
}



start();
	
function start(){
	echo "start";
	verifyUser();
	handleRequest();
}	
function verifyUser(){
	
}

function handleRequest(){
	if (!($_SERVER["REQUEST_METHOD"] == "POST")) echo "NON POST SERVER REQUEST ";
	else { 
		if (empty($_POST["header_type"])) {
			echo "JSON.header not set";
			return;
		}
		echo $header_type."</br>";
		$header_type = strainInput($_POST["header_type"]);
		echo $header_type."</br>";
		
		switch ($header_type){
			case "HANDSHAKE":
				echo returnHandshake();
				break;
			case "NEWSESSION":
				echo returnSessionToken();
				break;
		}
	}
}

function strainInput($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = strtoupper($data);
  return $data;
}

function returnHandshake(){
	//validateUser();
	$message=strainInput($_POST["message"]);
	if (empty($message)) echo "JSON.Handshake message not set";	
	else getHandShakeResponse($message);
	
}

function returnSessionToken(){
	generateSessionToken();
}
?>