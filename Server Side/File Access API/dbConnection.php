<?php 
/*
DBConnection for File Access API for Globlock
Filename:	dbConnection.php
Version: 	1.1
Author: 	Alex Quigley, x10205691
Created: 	22/02/2014
Updated: 	22/02/2014

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
include 'logWrite.php';

/* Define constants to connect to database */
	//DEFINE('DATABASE_USER', 'aquigley_glblck');
	//DEFINE('DATABASE_PASS', '67glblck76');
	//DEFINE('DATABASE_HOST', '50.116.97.181');
	//DEFINE('DATABASE_NAME', 'aquigley_globlock');
	DEFINE('DATABASE_USER', 'root');
	DEFINE('DATABASE_PASS', '');
	DEFINE('DATABASE_HOST', '127.0.0.1');
	DEFINE('DATABASE_NAME', 'globlock_test');
	

/*Define constant to connect to database */
	writeLogInfo("Attempting DB Connection...");
	$dbc = @mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

	if (!$dbc) {
		writeLogInfo("Connection Failed!");
		writeLogInfo("DB Connection Attempt failed:".mysqli_connect_error(), 1);
		//trigger_error('Could not connect to MySQL: '. mysqli_connect_error());
	} else {
		writeLogInfo("DB Connection Successful!");
	}


?>