<?php 
/*
File Access API for Globlock
Version: 	1.1
Author: 	Alex Quigley, x10205691
Created: 	10/02/2014

Dependencies:
	logWrite.php (child)
	
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

/* Global Files, Libraries and Declarations */

	/* DB Configuration/Connectivity */
	
	/* 

	/* Error Log */
	error_log("You messed up!", 3, "/var/tmp/my-errors.log");
	/* Transaction Log */
	error_log("You messed up!", 3, "/var/tmp/my-errors.log");


?>