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
function searchGlobeID(&$broker){
//TO DO
	global $databaseConnection;
	$configuration = new configurations();
	$configs = $configuration->configs;
	try {	
		$prepSTMT = $databaseConnection->prepare($configs["database_statements"]["search_globe"]);
		if(!$prepSTMT) throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		$result = $prepSTMT->bind_param('s', $broker->brokerData['globe']['id']);
		if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
		$prepSTMT->execute();
		$result = $prepSTMT->get_result();
		$prepSTMT->close();
		if ( $myrow = $result->fetch_assoc()) return $myrow["globe_asset_id"];
		return 0;
	} catch(Exception $e) { 
		writeLogInfo("Globe ID select error in [searchGlobeID]!");
		writeLogInfo("Exception occurred in [searchGlobeID] !  | [". $e ."]", 1) ;
		return -1;
	}
}

function validGlobe(&$broker){
	try {
		//Test values
		if (!(isset($_POST["globe_id"]))) 
			throw new Exception("Exception Thrown (EMPTY GLOBE):");
		//Sanitize and Assign to broker
		$broker->setValue('globe', "id", $_POST["globe_id"]);
		//Validate in database
		$result = searchGlobeID($broker);
		if ($result == -1){
			throw new Exception("Exception Thrown (Resultset):");
		} else if ($result == 0) {
			updateUnassigned($broker);
			return 0;
		} else {
			
		}
	} catch (Exception $e){
		writeLogInfo("Globe validate error in [validGlobe]!");
		writeLogInfo("Exception occurred in [validGlobe]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: GLOBE ID NOT FOUND OR EMPTY | [". $e ."]", 500);
		return -1;
	}
}

function updateUnassigned(&$broker){
	global $databaseConnection;
	$configuration = new configurations();
	$configs = $configuration->configs;
	$count = 0;

	try {
		$prepSTMT = $databaseConnection->prepare($configs["database_statements"]["unassigned_globes"]);
		if(!$prepSTMT) throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		$prepSTMT->execute();
		$prepSTMT->bind_result($globe_id, $globe_name, $temp1, $temp2, $temp3, $temp4 );
		$prepSTMT->store_result();
		$numRows = $prepSTMT->num_rows;
		$broker->setValue('list', "count", $numRows);
		
		while ($prepSTMT->fetch()) {
			echo "|".$globe_id."|".$globe_name."<br/>";
			$broker->setValue('list', $count, $globe_name);
			$count++;
		}
		$prepSTMT->close;
	} catch(Exception $e){
		writeLogInfo("DB read error in [updateUnassigned]!");
		writeLogInfo("Exception occurred in [updateUnassigned]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO RETRIEVE LIST | [". $e ."]", 500);
		return -1;
	}
	
	$broker->setValue('status', "assigned", "False");
	$broker->setValue('action', "set", "True");
	$broker->setValue('action', "abort", "True");
	return $count;	
}

function getActions(&$broker){

}
function actionPermitted(&$broker){

}
?>