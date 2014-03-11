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
	"globe_deallocate" 	=> 3,
	"globe_assignment"  => 4,
	"globe_pull_assoc"	=> 5,
	"globe_push_assoc"	=> 6,	
	
);
				
/** generateSessionToken */
function generateSessionToken(){
	global $sessionStages;
	writeLogInfo("Generating 'session_token' in [client_sessions]...");
	createDBPlaceholder();
	$session_token = generateToken();
	$session_record = selectToken(0,0);
	if (!updateToken($session_record, $session_token, $sessionStages['session_request'])) return -1;
	writeLogInfo("Created 'session_token' in [client_sessions] Table");
	disposeExpired();	
	return $session_token;
}
/** */
function disposeExpired(){
	global $databaseConnection, $sqlStatements;
	try{	
		if ($stmt = $databaseConnection->prepare($sqlStatements['dispose_expired'])) {
			$stmt->execute();
			$count = $stmt->affected_rows;
			if($count > 0) writeLogInfo("Disposed of  ".$count." expired 'session_token' in [client_sessions] Table");
			$stmt->close();
		}
	} catch (Exception $e){
		writeLogInfo("Exception occurred in [createDBPlaceholder] ! | [". $e ."]", 1) ;
	}
}
/** */
function createDBPlaceholder(){
	global $databaseConnection, $sqlStatements;
	try{	
		$result = mysqli_query($databaseConnection, $sqlStatements['test_table']);
		if($result == FALSE) createSessionTable();
		insertPlaceholder();
	} catch (Exception $e){
		writeLogInfo("Exception occurred in [createDBPlaceholder] ! | [". $e ."]", 1) ;
	}
}

/** */
function createSessionTable(){
	global $databaseConnection, $sqlStatements;
	try{
		writeLogInfo("Table [client_sessions] not found. Creating...");
		$result = mysqli_query($databaseConnection, $sqlStatements['create_table']);
		if ($result) writeLogInfo("Table [client_sessions] created!"); 
		else throw new Exception("Exception Thrown:".mysqli_error($databaseConnection));	
	} catch (Exception $e) {
		writeLogInfo("Create Table [client_sessions] failed!");
		writeLogInfo("Exception occurred in [createSessionTable] ! | [". $e ."]", 1) ;
	}
}

/** */
function insertPlaceholder(){
	global $databaseConnection, $sqlStatements;
	try {	
		if (!mysqli_query($databaseConnection, $sqlStatements['insert_placeholder'])) 
		throw new Exception("Exception Thrown:".mysqli_error($databaseConnection));		
	} catch (Exception $e){
		writeLogInfo("Create Placeholder in [client_sessions] failed!");
		writeLogInfo("Exception occurred in [insertPlaceholder] ! | [". $e ."]", 1) ;
	}
}

/** */
function selectToken($session_token, $session_activity=0){
	global $databaseConnection, $sqlStatements;
	try {	
		$prepSTMT =  $databaseConnection->prepare($sqlStatements['select_session']);
		if(!$prepSTMT) throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		$result = $prepSTMT->bind_param('is', $session_activity, $session_token);
		if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
		$prepSTMT->execute();
		$result = $prepSTMT->get_result();
		if ( $myrow = $result->fetch_assoc()) return $myrow["session_id"];
		else throw new Exception("Exception Thrown (Resultset):".mysqli_error($databaseConnection));
		$prepSTMT->close();
	} catch(Exception $e) { 
		writeLogInfo("Token select error in [selectToken]!");
		writeLogInfo("Exception occurred in [selectToken] !  | [". $e ."]", 1) ;
		return -1;
	}
}

/** */
function updateToken($session_record, $session_token, $session_activity){
	global $databaseConnection, $sqlStatements;
	try{
		$prepSTMT =  $databaseConnection->prepare($sqlStatements['update_token']);
		if(!$prepSTMT)throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		$result = $prepSTMT->bind_param('isi', $session_activity, $session_token, $session_record);
		if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
		$prepSTMT->execute();
		$result = $prepSTMT->affected_rows;
		$prepSTMT->close();
		return ($result >= 1);
	} catch (Exception $e){
		writeLogInfo("Token update error in [updateToken]!");
		writeLogInfo("Exception occurred in [updateToken]! | [". $e ."]", 1) ;
	}
}

/** */
function updateSession($session_record, $session_activity){
	global $databaseConnection, $sqlStatements;
	try{
		$prepSTMT =  $databaseConnection->prepare($sqlStatements['update_session']);
		if(!$prepSTMT)throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		$result = $prepSTMT->bind_param('ii', $session_activity, $session_record);
		if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
		$prepSTMT->execute();
		$result = $prepSTMT->affected_rows;
		$prepSTMT->close();
		return ($result >= 1);
	} catch (Exception $e){
		writeLogInfo("Token update error in [updateToken]!");
		writeLogInfo("Exception occurred in [updateToken]! | [". $e ."]", 1) ;
	}
}

/** */
function generateToken(){
	$randString = addSalt(date("Ymdhis") . rand(1,1000), "session");
	return strtoupper(encryptMessage($randString));
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
	global $databaseConnection, $sqlStatements;
	try{
		$prepSTMT =  $databaseConnection->prepare($sqlStatements['dispose_session']);
		if(!$prepSTMT)throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		$result = $prepSTMT->bind_param('s', $sessionToken);
		if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
		$prepSTMT->execute();
		$result = $prepSTMT->affected_rows;
		$prepSTMT->close();
		writeLogInfo("Dropping session [disposeSessions]! | [". $sessionToken ."]", -1) ;
		return ($result >= 1);
	} catch (Exception $e){
		writeLogInfo("Token update error in [sessionDispose]!");
		writeLogInfo("Exception occurred in [sessionDispose]! | [". $e ."]", 1) ;
	}
}


?>