<?php
/*
sessionHandler for File Access API for Globlock
Filename:	sessionHandler.php
Version: 	1.0
Author: 	Alex Quigley, x10205691
Created: 	28/02/2014
Updated: 	28/02/2014

Dependencies:
	logWrite.php (child)
	FileAccessAPI.php (parent)
	dbConnection.php
	
Description: 
	Handles all session related calls and information.
	Handles future calls from the client, and ensures call authenticity and integrity.
	Uses a stepped/stage token approach, so a step can only be completed once for each token.
	If a step/stage is repeated, the Session Token is dropped by the system and future calls 
	under the token are rejected and logged in the security log.
	
Successful Operation Result:
	Successfully creates a Session Token in the DB, and returns to the client for 
	interaction with the system.

Usage: 
	<?php
		include sessionHandler.php;
		??
		??
	?>

TO DO:
>> Insert Token in DB
>> Tidy up definitions
>> Handle other stages

*/
/* File References */
//include 'logWrite.php';
include 'dbconnection.php';
include 'encryptionHelper.php';
/* Declarations */
$createTable = 	"CREATE TABLE IF NOT EXISTS client_sessions (
					session_id int(11) NOT NULL AUTO_INCREMENT, 
					session_token CHAR(64) NOT NULL DEFAULT '1',
					session_activity int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (session_id)
				)";
				
$insPlHolder = 	"INSERT INTO client_sessions (
					session_id, session_token, session_activity
				) VALUES (
					NULL, '0', '0'
				)";

/** Testing Only */
generateSessionToken();

/** generateSessionToken */
function generateSessionToken(){
	createDBPlaceholder();
	$token = generateToken();
}

/** */
function createDBPlaceholder(){
	global $dbc;
	$val = mysqli_query($dbc, 'SELECT 1 from `client_sessions`');
	if($val == FALSE) createSessionTable();
	insertPlaceholder();
}

/** */
function insertPlaceholder(){
	global $dbc, $insPlHolder;
	if (mysqli_query($dbc,$insPlHolder)) {
			writeLogInfo("Created Placeholder in Table [client_sessions]!"); 
	} else {
		writeLogInfo("Create placeholder in [client_sessions] failed!");
		writeLogInfo("Placeholder cannot be created in Table [client_sessions]! | [". mysqli_error($dbc) ."]", 1) ;
	}
}

/** */
function insertToken(){
	// >> TO DO
}
/** */
function createSessionTable(){
	global $dbc, $createTable;
	writeLogInfo("Table [client_sessions] not found. Creating...");
	if (mysqli_query($dbc,$createTable))  {
			writeLogInfo("Table [client_sessions] created!"); 
	} else {
		writeLogInfo("Create Table [client_sessions] failed!");
		writeLogInfo("Table [client_sessions] cannot be created! | [". mysqli_error($dbc) ."]", 1) ;
	}
}

/** */
function generateToken(){
	$randString = addSalt(date("Ymdhis") . rand(1,1000), "session");
	return encryptValue($randString);
}
?>