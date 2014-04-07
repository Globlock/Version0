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

/** */
// CALLED ON FROM VALIDATE GLOBE
function globeValidation(&$broker){
	$result = validGlobe($broker);
	if ($result == -1) {
		return -1;
	} else if ($result == 0) {
		listUnassigned($broker);
	} else {
		updateAssigned($broker, $result); //globe ID and project
	}
}

/** */
//Called on from SET (Assign Globe)
function globeAssignable(&$broker){
	try{
		$result = validGlobe($broker);
		if ($result > 0)
			throw new Exception("Exception Thrown (GLOBE ALREADY ASSIGNED):");
		// TEST GLOBE NOT PREVIOUSLY FOUND AND PROJECT EXISTS
		if (($result == 0) && (validProject($broker) > 0)) return true;
		return false;
	} catch (Exception $e){
		writeLogInfo("Globe assignment error in [globeAssignable]!");
		writeLogInfo("Exception occurred in [globeAssignable]! | [". $e ."]", 1) ;
		$broker->handleErrors("FORBIDDEN: GLOBE ID ALREADY ASSIGNED OR PROJECT NOT FOUND | [". $e ."]", 403);
		return false;
	}
}

function validGlobe(&$broker){
	try {
		//Test values are set
		if (!(isset($_POST["globe_id"]))) 
			throw new Exception("Exception Thrown (EMPTY GLOBE):");
		//Assign globe ID to broker
		$broker->setValue('globe', "id", $_POST["globe_id"]);
		//Search for ID in DB (-1:error, 0:not found, else, found)
		$result = searchGlobeID($broker);
		if ($result == -1)
			throw new Exception("Exception Thrown (Resultset):");
		return $result;
		
	} catch (Exception $e){
		writeLogInfo("Globe validate error in [validGlobe]!");
		writeLogInfo("Exception occurred in [validGlobe]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: GLOBE ID NOT HANDLED OR EMPTY | [". $e ."]", 500);
		return -1;
	}
}

function validProject(&$broker){
	try {
		//Test values are set
		if (!(isset($_POST["globe_project"]))) 
			throw new Exception("Exception Thrown (EMPTY GLOBE):");
		//Assign globe ID to broker
		$broker->setValue('globe', "project", $_POST["globe_project"]);
		//Search for ID in DB (-1:error, 0:not found, else, found)
		$result = searchGlobeProject($broker);
		if ($result == -1)
			throw new Exception("Exception Thrown (Resultset):");
		return $result;
		
	} catch (Exception $e){
		writeLogInfo("Globe validate error in [validGlobe]!");
		writeLogInfo("Exception occurred in [validGlobe]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: GLOBE ID NOT HANDLED OR EMPTY | [". $e ."]", 500);
		return -1;
	}
}

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
		if ( $myrow = $result->fetch_assoc()) return $myrow["asset_id"];
		return 0;
	} catch(Exception $e) { 
		writeLogInfo("Globe ID select error in [searchGlobeID]!");
		writeLogInfo("Exception occurred in [searchGlobeID] !  | [". $e ."]", 1) ;
		return -1;
	}
}

function searchGlobeProject(&$broker){
	global $databaseConnection;
	$configuration = new configurations();
	$configs = $configuration->configs;
	try {	
		$prepSTMT = $databaseConnection->prepare($configs["database_statements"]["search_project"]);
		if(!$prepSTMT) throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		$result = $prepSTMT->bind_param('s', $broker->brokerData['globe']['project']);
		if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
		$prepSTMT->execute();
		$result = $prepSTMT->get_result();
		$prepSTMT->close();
		if ( $myrow = $result->fetch_assoc()) return $myrow["globe_id"];
		return 0;
	} catch(Exception $e) { 
		writeLogInfo("Globe ID select error in [searchGlobeProject]!");
		writeLogInfo("Exception occurred in [searchGlobeProject] !  | [". $e ."]", 1) ;
		return -1;
	}
}

function listUnassigned(&$broker){
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
		writeLogInfo("DB read error in [listUnassigned]!");
		writeLogInfo("Exception occurred in [listUnassigned]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO RETRIEVE LIST | [". $e ."]", 500);
		return -1;
	}
	
	$broker->setValue('status', "assigned", "False");
	$broker->setValue('action', "set", "True");
	$broker->setValue('action', "abort", "True");
	return $count;	
}

/** */
function assignNewGlobeID(&$broker){
	$asset_id = insertNewAsset($broker);
	if ($asset_id == -1) return;
	if (updateAsset(&$broker, $asset_id) >= 1){
		$broker->setValue('header', 'message', "Successfully Assigned New Globe");
	}
}

/** */
function assignNewGlobeID(&$broker){
}



function insertNewAsset($globe_object){
	global $databaseConnection;
	$configuration = new configurations();
	$configs = $configuration->configs;

	try {
		$prepSTMT = $databaseConnection->prepare($configs["database_statements"]["ins_new_asset"]);
		if(!$prepSTMT) throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		$result = $prepSTMT->bind_param('s', $broker->brokerData['globe']['id']);
		$prepSTMT->execute();
		$updateRow->$prepSTMT->insert_id;
		$prepSTMT->close;
		return $updateRow;
	} catch(Exception $e){
		writeLogInfo("Insert new record error in [insertNewAsset]!");
		writeLogInfo("Exception occurred in [insertNewAsset]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO INSERT NEW ASSET | [". $e ."]", 500);
		return -1;
	}
}

function updateAsset(&$broker, $asset_id){
	global $databaseConnection;
	$configuration = new configurations();
	$configs = $configuration->configs;

	try {
		$prepSTMT = $databaseConnection->prepare($configs["database_statements"]["update_asset"]);
		if(!$prepSTMT) throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		$result = $prepSTMT->bind_param('ss', $asset_id, $broker->brokerData['globe']['project']);
		$prepSTMT->execute();
		$updateRow->$prepSTMT->affected_rows;
		$prepSTMT->close;
		return $updateRow;
	} catch(Exception $e){
		writeLogInfo("Update record error in [updateAsset]!");
		writeLogInfo("Exception occurred in [updateAsset]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO UPDATE ASSET | [". $e ."]", 500);
		return -1;
	}
}

function getActions(&$broker){

}
function actionPermitted(&$broker){

}




?>