<?php
/*
Session Handler for File Access API - Globlock
Filename:	sessionHandler.php
Version: 	1.2
Author: 	Alex Quigley, x10205691
Created: 	28/02/2014
Updated: 	04/03/2014

Dependencies:
	logWrite.php (child)
	FileAccessAPI.php (parent)
	dbConnection.php
	
Description: 
	Handles all session related calls and information.
	Handles future calls from the client, and ensures call authenticity and integrity.
	Uses a stepped/stage token approach, so a step can only be completed once for each token.
	If a step/stage is repeated, the Session Token is dropped by the system and future calls 
	under the token are rejected and logged in the security log.
	
Successful Operation Result:
	Successfully creates a Session Token in the DB, and returns to the client for 
	interaction with the system.

Usage: 
	<?php
		include sessionHandler.php;
		??
		??
	?>

TO DO:
<< Insert Token in DB
<< Have single array maintaining all queries
>> Tidy up definitions
>> Handle other stages

*/
/* File References */
//include 'logWrite.php';
//include 'dbconnection.php';
//include 'encryptionHelper.php';


/* Declarations */
$sqlStatements = array(
	"test_table" => 
		"SELECT 1 from client_sessions", 
	"create_table" => 
		"CREATE TABLE IF NOT EXISTS client_sessions (
			session_id int(11) NOT NULL AUTO_INCREMENT, 
			session_token CHAR(64) NOT NULL DEFAULT '1',
			session_activity int(11) NOT NULL DEFAULT '0',
			session_create datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (session_id))", 
	"insert_placeholder" => 
		"INSERT INTO client_sessions (
			session_id, session_token, session_activity ) 
			VALUES ( NULL, 0, 0 )",
	"select_session" => 
		"SELECT session_id FROM client_sessions 
			WHERE session_activity =?
			AND session_token=?",
	"select_session_token" => 
		"SELECT session_id FROM client_sessions 
			WHERE session_token =?
			AND session_activity != -1",
	"update_token" => "UPDATE client_sessions 
			SET session_activity =?, session_token=?
			WHERE session_id =?",
	"update_session" => "UPDATE client_sessions 
			SET session_activity =?, session_token=?
			WHERE session_id =?",		
	"dispose_session" => 
		"UPDATE client_sessions 
			SET session_activity =-1
			WHERE session_token =? 
			AND session_activity != -1",
	"dispose_expired" =>
		"UPDATE client_sessions
			SET session_activity =-1
			WHERE DATE_SUB(NOW(),INTERVAL 1 HOUR) > session_create
			AND session_activity != -1"
);

$sessionStages = array(
	"initialise_token" 	=> 0,
	"session_request" 	=> 1,
	"globe_validation" 	=> 2,
	"globe_deallocate" 	=> -1,
	"globe_assignment"  => -1,
	"globe_pull_assoc"	=> -1,
	"globe_push_assoc"	=> -1,	
	
);
				
/** generateSessionToken */
function generateSessionToken(){
	writeLogInfo("Generating 'session_token' in [client_sessions]...");
	//createDBPlaceholder();
	$session_token = generateToken();
	//$session_record = selectToken("0","0");
	//echo "<br/>Sess Record: ".$session_record ."<br/>";
	echo "<br/>Sess Token: ".$session_token ."<br/>";
	if (!insertToken($session_token)) return -1;
	disposeExpired();	
	return $session_token;
}

function insertToken($session_token){
	try {
		//echo "<br/> Starting Token Insert <br/>";
		$activity = 1;
		$requestArgs = array($session_token, $activity );
		$result = dbb_insertToken("insert_session_token", "si", $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
		//echo "<br/>TEST: Update Complete <br/>";
		writeLogInfo("Successfully Updated Session Token in [disposeSessions]! | [". $session_record ."]", -1) ;
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [updateToken] ! | [". $e ."]", 1) ;
		//$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO UPDATE TOKEN | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */
function createDBPlaceholder(){
	try {
		$nullValue = null; //Only variables can be passed by reference
		$result = accessRequest("test_table", "rows", $nullValue, 0, $nullValue, $nullValue);
		if($result == FALSE) createSessionTable();
		$result = insertPlaceholder();
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
		writeLogInfo("Successfully Inserted Placeholder in [client_sessions]! | ", -1) ;
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [createDBPlaceholder] ! | [". $e ."]", 1) ;
		//$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO RETRIEVE TOKEN | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */
function insertPlaceholder(){
	try {
		$nullValue = null; //Only variables can be passed by reference
		$result = accessRequest("insert_placeholder", "rows", $nullValue, 0, $nullValue, $nullValue);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
		writeLogInfo("Successfully Inserted Placeholder in [client_sessions]! | ", -1) ;
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [insertPlaceholder] ! | [". $e ."]", 1) ;
		//$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO RETRIEVE TOKEN | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */
function selectToken($session_token, $session_activity=0){
	try {
		$requestArgs = array($session_activity, $session_token);
		$result = dbb_selectSessionID("select_session_id", "session_id", "is", $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
		echo "<br/>Token: ".$result."<br/>";
		writeLogInfo("Successfully Retrieved Session Token in [disposeSessions]! | Session Record [". $result ."], Session Token [". $session_token ."]", -1) ;
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [selectToken] ! | [". $e ."]", 1) ;
		//$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO RETRIEVE TOKEN | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */
function updateToken($session_record, $session_activity){
	try {
		echo "<br/> Starting Update <br/>";
		$requestArgs = array($session_activity, $session_record);
		print_r($requestArgs);
		$result = dbb_updateToken("update_session_activity", "ii", $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
		echo "<br/> Update Complete <br/>";
		writeLogInfo("Successfully Updated Session Token in [disposeSessions]! | [". $session_record ."]", -1) ;
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [updateToken] ! | [". $e ."]", 1) ;
		//$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO UPDATE TOKEN | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}


/** */
function updateSession($session_record, $session_activity){
	try {
		$nullValue = null; //Only variables can be passed by reference
		$requestArgs = array($session_record, $session_activity);
		$result = accessRequest("update_session", "rows", $nullValue, 2, "ii", $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
		writeLogInfo("Successfully Updated Session in [updateSession]! | Session Record [". $session_record ."]", -1) ;
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [updateSession] ! | [". $e ."]", 1) ;
		//$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO UPDATE TOKEN | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */
function generateToken(){
	$randString = addSalt(date("Ymdhis") . rand(1,1000), "session");
	return strtolower(encryptMessage($randString));
}

/** */
function verifySession($sessionToken, $session_activity){
	//return true/false;
	try{
		$session_record = selectToken($sessionToken, $session_activity);
		if($session_record == -1) throw new Exception("Exception Thrown (session Mismatch), unable to find session at stage [".$session_activity."]");
		return true;		
	} catch (Exception $e){
		writeLogInfo("Token verification failed in [verifySession]!");
		writeLogInfo("Exception occurred in [verifySession]! | [". $e ."]", -1) ;
		disposeSessions($sessionToken);
		return false;
	}
}

function disposeSessions($sessionToken){
	try {
		echo "inDispose()";
		$nullValue = null; //Only variables can be passed by reference
		$requestArgs = array($sessionToken);
		$result = accessRequest("dispose_session", "rows", null, 1, "s", $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
		writeLogInfo("Successfully Dropped Session in [disposeSessions]! | [". $sessionToken ."]", -1) ;
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [sessionDispose] ! | [". $e ."]", 1) ;
		//$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO DISPOSE SESSION | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** Calls on verify Session */
function validSession(&$broker, $activity){
	//TO DO - Return -1 for access denied, 0 for read, 1 for write
	$update = 2;
	if ($activity == 2) $update = -1;
	try{
				
		if (!(isset($_POST["session_token"]))) throw new Exception("Exception Thrown (EMPTY POST):");
		$broker->setValue("header", "type", $_POST["request_header"]);
		$broker->setValue("session", "token", $_POST["session_token"]);
		$token = $broker->brokerData['session']['token'];
		if (!verifySession($token, $activity)) 
			throw new Exception("Exception Thrown (SESSION NOT FOUND):");
		// Update Activity or Drop
		
		if (!(updateToken(selectToken($token, $activity), $update)))
			throw new Exception("Exception Thrown (UPDATE SESSION FAILED):");
		return true;
		
		} catch (Exception $e){
		writeLogInfo("Exception occurred in [validSession]! | [". $e ."]", 1) ;
		$broker->handleErrors("UNAUTHORIZED ACCESS: SESSION TOKEN NOT SET, FOUND OR UPDATABLE | [". $e ."]", 401);
		return false;
	}
}

function sessionToBroker(&$broker){
	$broker->setValue("header", "type", $_POST["request_header"]);
	$broker->setValue("session", "token", $_POST["session_token"]);
	$token = $broker->brokerData['session']['token'];
}

/** */
function disposeExpired(){
	try {
		$nullValue = null; //Only variables can be passed by reference
		$result = accessRequest("dispose_expired", "rows", $nullValue, 0, $nullValue, $nullValue);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
		writeLogInfo("Disposed of  ".$result." expired 'session_token' in [client_sessions] Table");
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [createDBPlaceholder] ! | [". $e ."]", 1) ;
		//$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO DISPOSE EXPIRED SESSIONS | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */
function createSessionTable(){
	try {
		$nullValue = null; //Only variables can be passed by reference
		$result = accessRequest("table_sessions", "create", $nullValue, 0, $nullValue, $nullValue);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
		writeLogInfo("Table [client_sessions] created!"); 
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [searchUser] ! | [". $e ."]", 1) ;
		writeLogInfo("Exception occurred in [searchUser] !  | [". $e ."]", -1) ;
		//$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO CREATE SESSION TABLE | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */

?>