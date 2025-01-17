<?php
/*
Write to logfile - Globlock
Filename:	logWrite.php
Version: 	1.2
Author: 	Alex Quigley, x10205691
Created: 	10/02/2014
Updated:	04/03/2014

Dependencies:
	FileAccessAPI.php (parent)

Description:
	File containing functions to write simple transactions information to a logfile, 
	or error information to an error logfile, or error information to a security error logfile.
	Logs regular transactions, system failures and security breaches (failed API access attempts).
	
Successful Operation Result:
	Successfully writes to a file.
	>>Successfully write to a DB

Usage: 
	<?php
		include 'logWrite.php';
		writeLogInfo("Writing to Transaction Log");
		writeLogInfo("Writing to System Error Log", 1);
		writeLogInfo("Writing to Security Log", -1);
		//Test Case
		writeLogInfo("Writing to Test Log Log", 99);
	?>
	
TO DO:
>> Have file locations configurable from a central location
>> Include writing log information to DB.
>><< Convert file locations to an Array/Ordered Map - Done
	
*/

/* Declarations [S01]*/
	// Database
		/* >>TO DO */
	// Logfiles
	$logFiles = array (
    "transactions" => array("directory" => "LogFiles/", "filename" => "transactions.log"),
    "system_error" => array("directory" => "LogFiles/", "filename" => "system_error.log"),
    "security_err" => array("directory" => "LogFiles/", "filename" => "security_err.log"),
	"test_logging" => array("directory" => "LogFiles/", "filename" => "test_logging.log"),
	);
	
	
/** writeLogInfo
	[required] Parameter $info, which defines the information to be written. 
	[optional] Parameter $type, which defines which log to write to. 
	'0', Type value, by default, is transaction information log
	'1', Type is security information log
	'-1', Type is error information log 
*/	
function writeLogInfo($info, $type=0){
	global $logFiles;
	switch ($type) {
		case 0:
			$info = addHeaderInfo($info, "Transaction");
			$location = $logFiles["transactions"]["directory"].$logFiles["transactions"]["filename"];
			break;
		case 1:
			$info = addHeaderInfo($info, "System");
			$location = $logFiles["system_error"]["directory"].$logFiles["system_error"]["filename"];
			break;
		case -1:
			$info = addHeaderInfo($info, "!!SECURITY!!");
			$location = $logFiles["security_err"]["directory"].$logFiles["security_err"]["filename"];
			break;
		case 99:
			$info = addHeaderInfo($info, "--TESTING--");
			$location = $logFiles["test_logging"]["directory"].$logFiles["test_logging"]["filename"];
			break;
		
	}
	writeToLog($info, $location);
	return true;
}

/** fileExists [S02]
	Proxy to check if file exists and attempts to create the file if not found.
	[required] Parameter $filename, which defines the concatenated location and file name to be written to. 
*/
function fileExists($filename){
	$dirname = pathinfo($filename)['dirname'];
	if (!file_exists($dirname)){
		if (!mkdir($dirname, 0777, true)) die('Failed to create folders...');
	}
	if (file_exists($filename)) {
		return true;
	} else {
		touch($filename);
		file_put_contents($filename, addHeaderInfo("File Created..."), FILE_APPEND | LOCK_EX);
		return file_exists($filename);
	}
}

/** writeToLog [S06]
	Write the information to the logfile. Can be called directly but is unhandled.
	[required] Parameter $location, which defines the concatenated location and file name to be written to. 
	[required] Parameter $info, which defines the information to be written. 
*/
function writeToLog($info, $location){
	if(fileExists($location)) file_put_contents($location, $info, FILE_APPEND | LOCK_EX);
}

/** addHeaderInfo [S05]
	Adds Header information to the $info string, to allow time-stamping and categorisation of the information.
	Also appends a new line after each string to improve readability.
	[required] Parameter $info, which defines the information to be written. 
	[optional] Parameter $Type, which defines which category of log. Default value of '-'.
*/
function addHeaderInfo($info, $type="-"){
	$dateStamp = date('Ymd-His');
	return $dateStamp."|".$type."|".$info.PHP_EOL;
}


?>
