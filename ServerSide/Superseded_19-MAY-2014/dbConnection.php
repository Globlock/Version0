<?php 
/*
DBConnection for File Access API - Globlock
Filename:	dbConnection.php
Version: 	1.2
Author: 	Alex Quigley, x10205691
Created: 	22/02/2014
Updated: 	04/03/2014

Dependencies:
	logWrite.php (child)
	FileAccessAPI.php (parent)
	
Description: 
	Communicates to the DB upon failure, writes to a system

Successful Operation Result:
	Creates a successful, persistant connection to the DB

Usage: 
	<?php
		include dbConnection.php;
	?>
*/
/* File References */
//include 'logWrite.php';

/* Define constants to connect to database */
	DEFINE('DATABASE_USER', 'root');
	DEFINE('DATABASE_PASS', '');
	DEFINE('DATABASE_HOST', '127.0.0.1');
	DEFINE('DATABASE_NAME', 'gb_production');
	// TO DO - Read from config
	

/*Define constant to connect to database */
	writeLogInfo("Attempting DB Connection...");
	$databaseConnection = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

	if (!$databaseConnection) {
		writeLogInfo("Connection Failed!");
		writeLogInfo("DB Connection Attempt failed:".mysqli_connect_error(), 1);
		//trigger_error('Could not connect to MySQL: '. mysqli_connect_error());
	} else {
		writeLogInfo("DB Connection Successful!");
	}


?>