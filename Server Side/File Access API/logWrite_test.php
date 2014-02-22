<?php
/*
TestFile of Write to logfile for Globlock
Filename:	logWrite_test.php
Version: 	1.0
Author: 	Alex Quigley, x10205691
Created: 	22/02/2014
Updated:	22/02/2014

TO DO:
>> Test - file locations configurable from a central location
>> Test - Include writing log information to DB.
	
 */

 
 include 'logWrite.php';

// Environment Variables
echo "<h1>Logfile Environment Variables: </h1>";
var_dump ($logFiles);

// Test IO
echo "<h1>Testing Input/Output: </h1>";
echo "Success : ". writeLogInfo("Testing IO from logWrite_text.php",99);


?>
