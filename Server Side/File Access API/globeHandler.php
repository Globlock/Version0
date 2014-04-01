<?php
/*
Globe Handler for File Access API - Globlock
Filename:	globeHandler.php
Version: 	1.0
Author: 	Alex Quigley, x10205691
Created: 	04/03/2014
Updated: 	04/03/2014

Dependencies:
	FileAccessAPI.php (parent)
	
Description: 
	Handles globe requests, such as globe validation
	Only accessed with valid SESSION and USER information

Successful Operation Result:
	Returns the required information / handles files and DB transactions
	
	
Usage: 
	<?php
		include globeHandler.php;
	?>
TO DO:
>> Validation
>> Return possible Actions
>> Verify Action permission
>> ASSIGNMENT
>> FORCE
>> DROP
>> ABORT



*/
function searchGlobe(&$broker){
//TO DO
	global $databaseConnection;
	$configuration = new configurations();
	$configs = $configuration->configs;
	try {	
		$prepSTMT =  $databaseConnection->prepare($configs["database_statements"]["verify_globe"]);
		if(!$prepSTMT) throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		$result = $prepSTMT->bind_param('s', $broker->brokerData['globe']['name'],  $broker->brokerData['user']['pass']);
		if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
		$prepSTMT->execute();
		$result = $prepSTMT->get_result();
		$prepSTMT->close();
		if ( $myrow = $result->fetch_assoc()) return $myrow["user_id"];
		return 0;
	} catch(Exception $e) { 
		writeLogInfo("User select error in [searchUser]!");
		writeLogInfo("Exception occurred in [searchUser] !  | [". $e ."]", 1) ;
		return -1;
	}
}

function getActions(&$broker){

}
function actionPermitted(&$broker){

}
?>