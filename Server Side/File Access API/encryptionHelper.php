<?php 
/*
Encryption Helper for File Access API - Globlock
Filename:	encryptionHelper.php
Version: 	1.1
Author: 	Alex Quigley, x10205691
Created: 	27/02/2014
Updated: 	28/02/2014

Dependencies:
	logWrite.php (child)
	FileAccessAPI.php (parent)
	
Description: 
	Contains function that takes a string as input, encrypts it with a salt value 
	and returns the string to the caller.

Successful Operation Result:
	Creates a successfully salted and encrypted string

Usage: 
	<?php
		include encryptionHelper.php;
		getHandShakeResponse("_sometext_");
		addSalt("_sometext_", "handshake"/"other"); //
		addSalt("_sometext_");
		encryptValue("_sometext_");
	?>

TO DO:
>> Have salt value configurable from a central location.
>> Have encryption type selectable (currently only supports SHA1).
*/

/* File References */
//include 'logWrite.php';

/* Declarations */
$saltValues = array("handshake" => "HANDSHAKE:abc123_GloblockDevelopmentTest", 
					"other" => "Other:abc123_GloblockDevelopmentTest",
					"session" => "Session:laundrytokens",
					"default" => "Default:abc123_GloblockDevelopmentTest");

/** Testing Only */
//getHandShakeResponse("Testing");					
					
/** getHandShakeResponse
	Receives a string message, adds the handshake defined salt (addSalt), 
	encrypts the message (encryptValue), and returns the encrypted string.
	Logs the IP address of the request during the process.
	[required] Parameter $message, which defines the information to be encrypted. 
*/					
function getHandShakeResponse($message){
	writeLogInfo("Handshake Request to :". $_SERVER['SERVER_NAME'] ." | From :". $_SERVER['REMOTE_ADDR']);
	$message = addSalt($message, "handshake");
	$message = encryptValue($message);
	return $message;
}

/** addSalt
	Adds the required salt value to the start of the message prior to encryption.
	[required] Parameter $message, which defines the information to be encrypted. 
	[optional] Parameter $salt, which defines the salt value, "default", by default. 
*/	
function addSalt($message, $salt = "default"){
	global $saltValues;
	return $saltValues[$salt] . $message;
}

/**	encryptValue
	Encrypts the message passed in as a parameter (currently uses SHA1).
	[required] Parameter $message, which defines the information to be encrypted. 
*/	
function encryptValue($message){
	return sha1($message);
}

?>