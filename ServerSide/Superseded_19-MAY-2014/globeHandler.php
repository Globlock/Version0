<?php
/*
Globe Handler for File Access API - Globlock
Filename:	globeHandler.php
Version: 	1.2
Author: 	Alex Quigley, x10205691
Created: 	04/03/2014
Updated: 	07/04/2014

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
function globeValidation(&$broker){
	$result = validGlobe($broker);
	if ($result == -1) return -1;
	else if ($result == 0) listUnassigned($broker);
	else updateAssigned($broker, $result); //globe ID and project
}

/** */
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

function globeOverwrite(&$broker){
	try{
		$asset_id = validGlobe($broker);
		if ($asset_id == -1) return;
		if (validProject($broker) == -1) return;
		if (($result == 0) ||  (validProject($broker) == 0)) throw new Exception("Exception Thrown (GLOBE OR PROJECT NOT FOUND):");
		// TEST GLOBE NOT PREVIOUSLY FOUND AND PROJECT EXISTS
		if (updateAsset($broker, $asset_id) < 1) return;
		$broker->setValue('header', 'message', "Successfully Re-Assigned Globe");
	} catch (Exception $e){
		writeLogInfo("Globe assignment error in [globeOverwrite]!");
		writeLogInfo("Exception occurred in [globeOverwrite]! | [". $e ."]", 1) ;
		$broker->handleErrors("FORBIDDEN: GLOBE OR PROJECT NOT FOUND | [". $e ."]", 403);
		return false;
	}
}

function validGlobe(&$broker){
	try {
		//Test values are set, then assign to broker
		if (!(isset($_POST["globe_id"]))) throw new Exception("Exception Thrown (EMPTY GLOBE):");
		$broker->setValue('globe', "id", $_POST["globe_id"]);
		$result = searchGlobeID($broker);	//Search for ID in DB (-1:error, 0:not found, else, found)
		if ($result == -1) throw new Exception("Exception Thrown (Resultset):");
	} catch (Exception $e){
		writeLogInfo("Exception occurred in [validGlobe]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: GLOBE ID NOT HANDLED OR EMPTY | [". $e ."]", 500);
		return -1;
	} finally {
		return $result;
	}
}

/** */
function validProject(&$broker){
	try {
		//Test values are set, then assign to broker
		if (!(isset($_POST["globe_project"]))) throw new Exception("Exception Thrown (EMPTY GLOBE):");
		$broker->setValue('globe', "project", $_POST["globe_project"]);
		$result = searchGlobeProject($broker);	//Search for ID in DB (-1:error, 0:not found, else, found)
		if ($result == -1) throw new Exception("Exception Thrown (Resultset):");
	} catch (Exception $e){
		writeLogInfo("Exception occurred in [validGlobe]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: GLOBE PROJECT NOT HANDLED OR EMPTY | [". $e ."]", 500);
		return -1;
	} finally {
		return $result;
	}
}

/** */
function searchGlobeID(&$broker){
	try {
		$requestArgs = array($broker->brokerData['globe']['id']);
		$result = accessRequest("search_globe", "id", "asset_id", 1, "s", $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [searchGlobeID]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO RETRIEVE | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

function searchGlobeProject(&$broker){
	try {
		$requestArgs = array($broker->brokerData['globe']['project']);
		$result = accessRequest("search_project", "id", "globe_id", 1, "s", $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [searchGlobeProject]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO RETRIEVE GLOBE PROJECT| [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */
function listUnassigned(&$broker){
	try {
		$requestArgs = array();
		$result = accessRequest("unassigned_globes", "list1", null, 0, null, $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
		listToBroker($broker, $requestArgs);
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [listUnassigned]! | [". $e ."]", 1);
		$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO RETRIEVE LIST | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */
function listToBroker(&$broker, $list){
	
	$broker->setValue('status', "assigned", "False");
	$broker->setValue('action', "set", "True");
	$broker->setValue('action', "abort", "True");
	
	$count = $list[0];
	$index = 0;
	$broker->setValue('list', "count", $count);
	
	while ($index < $count){
		$broker->setValue('list', $index, $list[$index+1]);
		$index++;
	}
	
}

/** */
function assignNewGlobeID(&$broker){
	$asset_id = insertNewAsset($broker);
	if ($asset_id == -1) return;
	if (updateAsset($broker, $asset_id) >= 1){
		$broker->setValue('header', 'message', "Successfully Assigned New Globe");
	}
}

/** */
function insertNewAsset($broker){
	try {
		$requestArgs = array($broker->brokerData['globe']['id']);
		$result = accessRequest("ins_new_asset", "rows", null, 1, "s", $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [insertNewAsset]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO INSERT NEW ASSET | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */
function updateAsset(&$broker, $asset_id){
	try {
		$requestArgs = array($asset_id, $broker->brokerData['globe']['project']);
		if ($requestArgs[0] == -1) return -1;
		$result = accessRequest("update_asset", "rows", null, 2, "is", $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [updateAsset]! | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO UPDATE ASSET | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */
function dropAsset(&$broker){
	try {
		$requestArgs = array(validGlobe($broker));
		if ($requestArgs[0] == -1) throw new Exception("Exception Thrown (GLOBE OR PROJECT INVALID):");
		if ((validProject($broker) == 0)) throw new Exception("Exception Thrown (GLOBE OR PROJECT NOT FOUND):");
		$requestArgs = array($broker->brokerData['globe']['id']);
		$result = accessRequest("drop_asset", "rows", null, 1, "s", $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
		$broker->setValue('header', 'message', "Successfully Dropped Globe");
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [incrementRevision] !  | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO UPDATE ASSET | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */
function incrementRevision(&$broker){
	try {
		$requestArgs = array(validGlobe($broker));
		if ($requestArgs[0] == -1) return -1;
		$result = accessRequest("increment_revision", "rows", null, 1, "i", $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [incrementRevision] !  | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO INCREMENT REVISION | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}

/** */
function getCurrentRevision(&$broker, $globe_id){
	try {
		$requestArgs = array($globe_id);
		$result = accessRequest("search_revision", "id", "Revision_id", 1, "i", $requestArgs);
		if ($result == -1) throw new Exception("Exception Thrown while executing Database Access Request:");
	} catch (Exception $e) {
		writeLogInfo("Exception occurred in [getCurrentRevision] !  | [". $e ."]", 1) ;
		$broker->handleErrors("INTERNAL SERVER ERROR: UNABLE TO RETRIEVE REVISION INFO | [". $e ."]", 500);
		return -1;
	} finally { return $result; }
}





?>