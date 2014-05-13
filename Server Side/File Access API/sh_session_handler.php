<?php

/**
	Generates a Token, and returns the token if insert possible, or undefined if not
*/
function sh_getSessionToken(){
	writeLogInfo("Generating 'session_token'...");
	// Create Session Token Value
	$randString = addSalt(date("Ymdhis") . rand(1,1000), "session");
	$token = strtolower(encryptMessage($randString));
	writeLogInfo("Created 'session_token' [".$token."]...");
	// Insert into DB
	if (tableAccessible()){
		$query = "insert_session_token";
		$result = dbb_insertNewSessionToken($query, $token);
		if($result > 0) return $token;	//echo "<br/> Token : ".$token."<br/>"; //echo "<br/> Success : ".$result."<br/>";
	}
	return "undefined!";
}

/**
	Tests to see if the table is available and will create if not // TO DO
*/

function tableAccessible(){
	//Create New Table if not exists //TO DO
	return true;	
}

/** 
	Validates a session token, and updates the activity if valid
*/
function sh_validSessionToken(&$broker, $activity){
	writeLogInfo("Validating 'session_token'...");
	$sessiontoken = $broker->brokerData['session']['token'];
	echo "<br/>Token in Broker: ".$sessiontoken."<br/>";
	$query = "select_active_session"; 
	$result = dbb_selectActiveSession($query, "session_id", "si", $activity, $sessiontoken);
	echo "<br/><br/><br/><br/><br/>".$result."<br/><br/><br/><br/><br/>";
	if ($result > 0){
		echo "<br/>Valid Session<br/>";
		$query = "update_active_session";
		$activity++;
		if ($activity = 2) $activity = -1;	// Abort Clause (Session token can only be used for an action once)
		$result = dbb_updateActiveSession($query, $activity, $result);
		echo "<br/>Successful Update: ".$result."<br/>";
		return ($result > 0);
	}
	return false;
}

?>