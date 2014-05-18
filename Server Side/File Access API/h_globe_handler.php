<?php

/**
	Checks globe object against the database and assigns assoc project, 
	possible actions and project name to the broker and returns true if found.
	returns false if not found.
*/
function gh_validateGlobe(&$broker){
	if (!(isset($_POST["globe_id"]))) throw new Exception("Exception Thrown (EMPTY GLOBE OBJECT):");
	$broker->setValue('globe', "id", $_POST["globe_id"]);
	$globe_object = $broker->brokerData['globe']['id'];
	$query = "select_globe_asset"; $record = "asset_id";
	//echo "<br/> VALIDATE Globe! <br/>";
	$result = dbb_selectGlobeAsset($query, $record, "s", $globe_object);
	if($result >= 1){
		//echo "<br/> Found Globe! <br/>";
		$query = "select_globe_project"; $record = "globe_name";
		// return globe project
		$project = dbb_selectGlobeProject($query, $record, 's', $globe_object);
		//echo "<br/>ProjectName: ".$project."<br/>";
		$broker->setValue('globe', "project", $project);
		$broker->setValue('status', "assigned", "true");
		$broker->setValue('action', "drop", "true");
		$broker->setValue('action', "push", "true");
		$broker->setValue('action', "pull", "true");
		$broker->setValue('status', "assigned", "true");
		$broker->setValue('action', "set", "false");
		//echo "<br/>Project Exists: TRUE<br/>";
		return -1;
	} else {
		//echo "<br/>Project Exists: FALSE<br/>";
		$query = "select_globe_project_unnassigned";
		$list = dbb_selectUnnassignedProjects($query);
		listProjectInBroker($broker, $list);
		$broker->setValue('status', "assigned", "false");
		$broker->setValue('action', "set", "true");
		return 1;
	}
}

function listProjectInBroker(&$broker, $list){
	$index = 0;
	$broker->setValue('status', "assigned", "False");
	$broker->setValue('action', "set", "True");
	$broker->setValue('list', "count", $list[$index]);
	$list = array_slice($list, 1);	
	asort($list);	// Sort A-Z
	foreach ($list as $listItem) {
		//echo "<br/> Adding Project: ".$listItem."<br/>";
		$broker->setValue('listitem', $index, $listItem);
		$index++;
	}
}

function gh_setGlobeProject(&$broker){
	try{
		if ((gh_validateGlobe($broker))==1){
			if (!(isset($_POST["globe_project"]))) throw new Exception("Exception Thrown (EMPTY GLOBE PROJECT)");
			$broker->setValue('globe', "project", $_POST["globe_project"]);
			$globe_project = $broker->brokerData['globe']['project'];
			//echo "<br/> Project: ".$globe_project."<br/>";
			$globe_object = $broker->brokerData['globe']['id'];
			//echo "<br/> Project: ".$globe_object."<br/>";
			$query = "select_globe_id"; $record = "globe_id";
			$unassigned = ($broker->brokerData['status']['assigned'] == "false");
			$setable = ($broker->brokerData['action']['set'] == "true");
			if ($unassigned && $setable){
				//echo "<br/> SET ACTION POSSIBLE!<br/>";
				$globe_id = dbb_selectGlobeID($query, $record, 's', $globe_project);
				if($globe_id > 0){
					//echo "<br/> Project Found: ".$globe_id."<br/>";
					$query = "insert_globe_asset";
					$insert_id = dbb_insertNewAsset($query, 'si', $globe_object, $globe_id);
					if ($insert_id < 1) throw new Exception("Exception Thrown (Unable to Insert Asset)");
					//echo "<br/> Asset Created: ".$insert_id."<br/>";
					$broker->setValue('status', "assigned", "true");
					$broker->setValue('action', "set", "false");
					$broker->setValue('header', "message", "SUCCESSFULLY ASSIGNED GLOBE OBJECT TO ".$globe_project."!");
				} else {
					throw new Exception("Exception Thrown (GLOBE PROJECT NOT FOUND)");
				}
			} else {
				$broker->setValue('header', "message", "GLOBE ALREADY ASSIGNED TO A PROJECT");
				$broker->setValue('status', "assigned", "true");
				$broker->setValue('action', "set", "false");
				throw new Exception("Exception Thrown (CANNOT SET OR OBJECT ALREADY ASSIGNED)");
			}
			
		}
	}catch(Exception $e){
		$broker->setValue('header', 'message', 'Something aint right: '. $e);
	}
}

function gh_getGlobeRevisionDetails(&$broker){
	// TO DO Handle invalid details and insert in broker
	$fileDetails = array("globe_id" => -1, 'asset_revision'=>-1);
	if ((gh_validateGlobe($broker))==-1){
		$globe_project = $broker->brokerData['globe']['project'];
		$globe_object = $broker->brokerData['globe']['id'];
		$query = "select_globe_id"; $record = "globe_id";
		$globe_id = dbb_selectGlobeID($query, $record, 's', $globe_project);
		//echo "<br/> Globe Found: ".$globe_id."<br/>";
		//echo "<br/> Globe Project: ".$globe_project."<br/>";
		//echo "<br/> Globe Project: ".$globe_object."<br/>";
		$query = "select_globe_revision"; $field_name = "asset_revision";
		$revision_id = dbb_selectGlobeRevision($query, $field_name,'s', $globe_object );
		//echo "<br/> Revision Found: ".$revision_id."<br/>";
		$fileDetails['globe_id']=$globe_id;
		$fileDetails['asset_revision']=$revision_id;
		print_r($fileDetails);
	}
	return $fileDetails;
}

function gh_updateRevision(){
	
}



?>