<?php
/*
Write to logfile for Globlock
Version: 	1.0
Author: 	Alex Quigley, x10205691
Created: 	10/02/2014

Dependencies:
	FileAccessAPI.php (parent)

Description:
	File containing functions to Writes simple transactions information to a logfile, 
	or error information to an error logfile, or error information to a security error logfile.
	Logs regular transactions, system failures and security breaches (failed API access attempts).
	
Successful Operation Result:
	Successfully writes to a file.

TO DO:
>> Convert file locations to an Array/Ordered Map
>> Have file locations configurable from a central location
	
*/

/* Declarations */
	//Transactions
	//const $TRANSACTIONS_DIR = '../../LogFiles/';
	//const $TRANSACTIONS_FILE = $TRANSACTIONS_DIR . 'GloblockTransactions.log';

	//System Errors
	//const $SYSTEM_ERROR_DIR = '../../LogFiles/';
	//const $SYSTEM_ERROR_FILE = $SYSTEM_ERROR_DIR . 'system_err.log';

	//Security Errors
	//const $SECURITY_ERR_DIR = '../../LogFiles/';
	//const $SECURITY_ERR_FILE = $SECURITY_ERR_DIR  . 'security_err.log';
	
	define $logFiles = array (
    "transactions"  => array("directory" => "", "filename" => "trans.log"),
    "system errors" => array("directory" => "", "filename" => "system.log"),
    "security"   => array("directory" => "", "filename" => "security.log"),
	);

	//echo $logFiles["transactions"]["directory"]
	
function writeLogInfo($type){


}

function testLogInfo(){
//if(!file_exists(TRANSACTIONS_DIR)){
//}

}

?>
