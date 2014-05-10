<?php

/**
	Checks globe object against the database and assigns assoc project, 
	possible actions and project name to the broker and returns true if found.
	returns false if not found.
*/
function gh_validateGlobe(&$broker){
	if (!(isset($_POST["globe_id"]))) throw new Exception("Exception Thrown (EMPTY GLOBE):");
	$broker->setValue('globe', "id", $_POST["globe_id"]);
	$globe_object = $broker->brokerData['globe']['id'];
	$query = "select_globe_asset"; $record = "asset_id";
	$result = dbb_selectGlobeAsset($query, $record, "s", $globe_object);
	if($result == 1){
		echo "<br/> Found Globe! <br/>";
		$query = "select_globe_project"; $record = "globe_name";
		// return globe project
		$project = dbb_selectGlobeProject($query, $record, 's', $globe_object);
		echo "<br/>ProjectName: ".$project."<br/>";
		$broker->setValue('globe', "project", $project);
		$broker->setValue('status', "assigned", "true");
		$broker->setValue('action', "drop", "true");
		$broker->setValue('action', "push", "true");
		$broker->setValue('action', "pull", "true");
		return true;
	} else {
		return false;
	}
}


?>