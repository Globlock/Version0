<?php 
#---------------------------------------------------------------#
# Encryption Handler for File Access API - Globlock
# Filename:	h_encryption_handler.php
# Version: 	2.0
# Author: 	Alex Quigley, x10205691
# Created: 	27/02/2014
# Updated: 	20/05/2014
#---------------------------------------------------------------#
# Dependencies:
# 	GLOBLOCK.php (parent)
#	s_logWrite.php
#---------------------------------------------------------------#
# Description: 
# 	Contains function that takes a string as input, 
# 	encrypts it with a salt value and returns 
# 	the string to the caller.
#---------------------------------------------------------------#
# Successful Operation Result:
# 	Creates a successfully salted and encrypted string
#---------------------------------------------------------------#
# Usage: 
# 	include h_encryption_handler.php;	
# 	getHandShakeResponse("_sometext_");
#	addSalt("_sometext_", "handshake"/"other"); 
#	addSalt("_sometext_");
#	encryptMessage("_sometext_");
#---------------------------------------------------------------#

/* Declarations */
$saltValues = array("handshake" => "HANDSHAKE:abc123_GloblockDevelopmentTest", 
					"other" => "Other:abc123_GloblockDevelopmentTest",
					"session" => "Session:laundrytokens",
					"folder" => "Folder:callorfold",
					"default" => "Default:abc123_GloblockDevelopmentTest");

/** Testing Only */
//getHandShakeResponse("Testing");					
					
	/** GET HANDSHAKE RESPONSE
	 * Receives a string message, adds the handshake defined salt (addSalt), 
	 * encrypts the message (encryptMessage), and returns the encrypted string.
	 * Logs the IP address of the request during the process.
	 * [required] Parameter $message, which defines the information to be encrypted. 
	*/					
		function getHandShakeResponse($message){
			writeLogInfo("Handshake Request to :". $_SERVER['SERVER_NAME'] ." | From :". $_SERVER['REMOTE_ADDR']);
			$message = addSalt($message, "handshake");
			$message = encryptMessage($message);
			return $message;
		}

	/** GET FOLDER NAME
	 * Takes a message string as a parameter and generates 
	 * a random string of alphanumeric characters for use by 
	 * file handler to publish to non-descriptive location
	 * [required] Parameter $message, which defines the information to be encrypted.
	*/
		function getFolderName($message){
			writeLogInfo("Folder Name Request for File Publish");
			$message = addSalt($message, "folder");
			$message = encryptMessage($message);
			return $message;
		}

	/** ADD SALT
	 * Adds the required salt value to the start of the message prior to encryption.
	 * [required] Parameter $message, which defines the information to be encrypted. 
	 * [optional] Parameter $salt, which defines the salt value, "default", by default. 
	*/	
		function addSalt($message, $salt = "default"){
			global $saltValues;
			return $saltValues[$salt] . $message;
		}

	/**	encryptMessage
		Encrypts the message passed in as a parameter (currently uses SHA1).
		[required] Parameter $message, which defines the information to be encrypted. 
	*/	
		function encryptMessage($message){
			return sha1($message);
		}

?>