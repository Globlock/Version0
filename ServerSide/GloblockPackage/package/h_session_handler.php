<?php
#---------------------------------------------------------------#
# Session Handler for File Access API - Globlock
# Filename:	h_session_handler.php
# Version: 	2.0
# Author: 	Alex Quigley, x10205691
# Created: 	28/02/2014
# Updated: 	20/05/2014
#---------------------------------------------------------------#
# Dependencies:
# 	GLOBLOCK.php (parent)
#	s_logWrite.php
#---------------------------------------------------------------#
# Description: 
# 	Handles all session related calls and information.
#	Handles future calls from the client, and ensures call 
#	authenticity and integrity.	Uses a stepped/stage token 
#	approach, so a step can only be completed once for each 
#	token.
#	If a step/stage is repeated, the Session Token is dropped 
#	by the system and future calls under the token are rejected 
#	and logged in the security log.
#---------------------------------------------------------------#
# Successful Operation Result:
# 	Successfully creates a Session Token in the DB, 
#	and returns to the client for interaction with the system.
#---------------------------------------------------------------#
# Usage: 
#	include 'h_session_handler.php';
#---------------------------------------------------------------#

	/** GET SESSION TOKEN
	 * Generates a Token, and returns the token if insert possible, or undefined if not
	 * Token generated using encrypted random string from salt value, random digit and timestamp
	 */
		function sh_getSessionToken(){
			writeLogInfo("SESSION[GEN_SESS_TKN]:Attempting Generate");
			$randString = addSalt(date("Ymdhis") . rand(1,1000), "session");
			$token = strtolower(encryptMessage($randString));
			writeLogInfo("SESSION[GEN_SESS_TKN]:Token=$token");
			if (tableAccessible()){
				$query = "insert_session_token";
				$result = dbb_insertNewSessionToken($query, $token);
				if($result > 0) {
				writeLogInfo("SESSION[GEN_SESS_TKN]:Added!");
				return $token;
				} else {
					writeLogInfo("SESSION[GEN_SESS_TKN]:Table not accessible!");
				}
			return "undefined!";
			}
		}

	/** VALIDATE SESSION TOKEN
	 * Validates a session token, and updates the activity if valid
	 * [required] Parameter $broker by reference, defines broker to be used.
	 * [required] Parameter $activity, defines current activity cycle.
	 */
		function sh_validSessionToken(&$broker, $activity){
			writeLogInfo("SESSION[VAL_SESS_TKN]:Attempting Validation");
			$sessiontoken = $broker->brokerData['session']['token'];
			$query = "select_active_session"; 
			$result = dbb_selectActiveSession($query, "session_id", "si", $activity, $sessiontoken);
			if ($result > 0){
				writeLogInfo("SESSION[VAL_SESS_TKN]:Valid!=$sessiontoken");
				$query = "update_active_session";
				$activity++;
				if ($activity > 2) $activity = -1;
				$result = dbb_updateActiveSession($query, $activity, $result);
				return ($result > 0);
			} else {
				writeLogInfo("SESSION[VAL_SESS_TKN]:Invalid Token Attempt!", -1);
				$broker->setValue('header', 'message', "SESSION NOT FOUND!");
			}
			return false;
		}
		
	/**
	 * Tests to see if the table is available and will create if not 
	 * Support for table creation on first use
	 */
		function tableAccessible(){
			//Create New Table if not exists 
			return true;	
		}
?>