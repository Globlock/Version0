<?php
#---------------------------------------------------------------#
# User Handler for File Access API - Globlock
# Filename:	h_user_handler.php
# Version: 	2.0
# Author: 	Alex Quigley, x10205691
# Created: 	31/03/2014
# Updated: 	20/05/2014
#---------------------------------------------------------------#
# Dependencies:
# 	GLOBLOCK.php (parent)
#---------------------------------------------------------------#
# Description: 
# 	Handles all user updates and login requests, 
#	as well as verifying user identity.
#---------------------------------------------------------------#
# Successful Operation Result:
# 	Returns a success flag, and updates the broker, 
#	if the user is found in the DB and has access
#---------------------------------------------------------------#
#---------------------------------------------------------------#


	/** VALID USER
	 * Requires $_POST user_name and user_pass to be set
	 * [required] Parameter $broker by reference, which defines the broker.
	*/
		function validUser(&$broker){
			try{
				//Test values values
				if (!(isset($_POST["user_name"]) && isset($_POST["user_pass"]))) throw new Exception("Exception Thrown (EMPTY POST):");
				//Sanitize and Assign to broker
				$broker->setValue('user', "name", $_POST["user_name"]);
				$broker->setValue('user', "pass", $_POST["user_pass"]);
				//Validate in database
				if (searchUser($broker) <1) throw new Exception("Exception Thrown (Resultset):");
				$broker->setValue('user', "pass", "*validated*");
				$result = true;
			} catch (Exception $e){
				writeLogInfo("Exception occurred in [validateUser]! | [". $e ."]", 1) ;
				$broker->handleErrors("UNAUTHORIZED ACCESS: USER NOT FOUND OR USERNAME AND PASSWORD MISMATCH | [". $e ."]", 401);
				$result = false;
			} finally {
				return $result;
			}
		}

	/** SEARCH USER
	 * Calls on database broker to search for user details entered and returns result.
	 * [required] Parameter $broker by reference, which defines the broker.
	*/
		function searchUser(&$broker){
			try {
				$requestArgs = array($broker->brokerData['user']['name'], $broker->brokerData['user']['pass']);
				$result = accessRequest("search_user", "id", "user_id", 2, "ss", $requestArgs);
				if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");		
			} catch (Exception $e) {
				writeLogInfo("Exception occurred in [searchUser] ! | [". $e ."]", 1) ;
				writeLogInfo("Exception occurred in [searchUser] !  | [". $e ."]", -1); 
				$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO RETRIEVE USERS | [". $e ."]", 401);
				return -1;
			} finally { return $result; }
		}

	/** IS SUPER
	 * Calls on database broker to search for user details entered and returns result if user is a super user.
	 * [required] Parameter $broker by reference, which defines the broker.
	*/
		function isSuper(&$broker){
			try {
				$null_value = null;
				$requestArgs = array($broker->brokerData['user']['name'], $broker->brokerData['user']['pass']);
				$result = accessRequest("search_super", "rows", $null_value, 2, "ss", $requestArgs);
				if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
				if ($result > 0) return 1;
			} catch (Exception $e) {
				writeLogInfo("Exception occurred in [searchUser] ! | [". $e ."]", 1) ;
				writeLogInfo("Exception occurred in [searchUser] !  | [". $e ."]", -1); 
				$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO RETRIEVE USERS | [". $e ."]", 401);
				return -1;
			} finally { return $result; }
		}

?>