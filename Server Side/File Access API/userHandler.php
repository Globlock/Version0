<?php
/*
User Handler - Globlock
Filename:	userHandler.php
Version: 	1.0
Author: 	Alex Quigley, x10205691
Created: 	31-Mar-14
Updated: 	01-Apr-14

Dependencies:
	FileAccessAPI.php (parent) 
	<<filename>>.<<ext>> (child) *[optional]
	
Description: 
	Handles all user updates and login requests, as well as verifying user identitity

Successful Operation Result:
	Returns a success flag, and updates the broker if the user is found in the DB and has access
	
Usage: 
	//TO DO
	<<example code usage>> *[optional]

TO DO:
	<<to do list items>> *[optional]
	functions:
		get user ID for username
		check user permissions
	
	*/

/** verifyUser
	Requires $_POST user_name and user_pass to be set
*/
	
function searchUser(&$broker){
	global $databaseConnection;
	$configuration = new configurations();
	$configs = $configuration->configs;
	try {	
		$prepSTMT =  $databaseConnection->prepare($configs["database_statements"]["verify_user"]);
		if(!$prepSTMT) throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		$result = $prepSTMT->bind_param('ss', $broker->brokerData['user']['name'],  $broker->brokerData['user']['pass']);
		if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
		$prepSTMT->execute();
		$result = $prepSTMT->get_result();
		$prepSTMT->close();
		if ( $myrow = $result->fetch_assoc()) return $myrow["user_id"];
		return 0;
	} catch(Exception $e) { 
		writeLogInfo("User select error in [searchUser]!");
		writeLogInfo("Exception occurred in [searchUser] !  | [". $e ."]", 1) ;
		writeLogInfo("Exception occurred in [searchUser] !  | [". $e ."]", -1) ;
		return -1;
	}
}

function validUser(&$broker){
	try{
		//Test values values
		if (!(isset($_POST["user_name"]) && isset($_POST["user_pass"]))) throw new Exception("Exception Thrown (EMPTY POST):");
		//Sanitize and Assign to broker
		$broker->setValue('user', "name", $_POST["user_name"]);
		$broker->setValue('user', "pass", $_POST["user_pass"]);
		//Validate in database
		if (searchUser($broker) <1) {
			throw new Exception("Exception Thrown (Resultset):");
		} else {
			$broker->setValue('user', "pass", "*validated*");
			return true;
		}
	} catch (Exception $e){
		writeLogInfo("Token update error in [validateUser]!");
		writeLogInfo("Exception occurred in [validateUser]! | [". $e ."]", 1) ;
		$broker->handleErrors("UNAUTHORIZED ACCESS: USER NOT FOUND OR USERNAME AND PASSWORD MISMATCH | [". $e ."]", 401);
		return false;
	}
}



?>