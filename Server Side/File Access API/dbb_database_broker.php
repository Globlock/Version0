<?php
function dbb_insertNewSessionToken($query, $sessionToken){
	try{
		//echo "<br/>Attempting Token Insert<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		$prepSTMT = $databaseConnection->prepare($query);
		$params = 'si'; $activity = 1;
		$prepSTMT ->bind_param($params, $sessionToken, $activity);
		$prepSTMT->execute();
		$insert_id = $prepSTMT->insert_id;
		//echo "<br/>Insert ID: ".$insert_id."<br/>"; 
		return $insert_id;
		//echo "<br/><br/><br/><br/><br/>";
	}catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_selectActiveSession($query, $record, $params, $activity, $sessionToken){
	try{
		echo "<br/>Attempting Active Token Select<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		$prepSTMT = $databaseConnection->prepare($query);
		//echo "<br/>Query: ".$query."<br/>"; echo "<br/>Token: ".$sessionToken."<br/>"; echo "<br/>Activity: ".$activity."<br/>";
		$params = 'is';
		$prepSTMT ->bind_param($params, $activity, $sessionToken);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$num_of_rows = $prepSTMT->num_rows;//echo "<br/>Rows : ".$num_of_rows."<br/>"; 
		$prepSTMT->execute();
		$result = $prepSTMT->get_result();
		//print_r($result->fetch_assoc());
		$prepSTMT->close();
		$record = 0;
		while ($row = $result->fetch_assoc()) {
				$record = $row['session_id'];
		}
		return $record;
		//echo "<br/><br/><br/><br/><br/>";
	}catch(Exception $e){
		echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_updateActiveSession($query, $activity, $record){
	try{
		echo "<br/>Attempting Active Token Update<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		$prepSTMT = $databaseConnection->prepare($query);
		$params = 'ii';
		$prepSTMT ->bind_param($params, $activity, $record);
		$prepSTMT->execute();
		$updatedRows = $prepSTMT->affected_rows;
		//echo "<br/> Updated: ".$updatedRows. "<br/>";
		$prepSTMT->close();
		return $updatedRows;
		//echo "<br/><br/><br/><br/><br/>";
	}catch(Exception $e){
		echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_selectGlobeAsset($query, $recordID, $params, $globe_object){
	try{
		echo "<br/>Attempting Active Globe Asset Select<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		$prepSTMT = $databaseConnection->prepare($query);
		echo "<br/>Query: ".$query."<br/>"; echo "<br/>Token: ".$globe_object."<br/>";
		$record = 0;
		$prepSTMT ->bind_param($params, $globe_object);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$num_of_rows = $prepSTMT->num_rows;
		echo "<br/>Rows : ".$num_of_rows."<br/>"; 
		$prepSTMT->execute();
		$result = $prepSTMT->get_result();
		$prepSTMT->close();
		while ($row = $result->fetch_assoc()) {
			$record = $row[$recordID];
			echo "<br/>Record: ".$record."<br/>";
		}
		//echo "<br/><br/><br/><br/><br/>";
		return $record;
	}catch(Exception $e){
		echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_selectGlobeProject($query, $recordID, $params, $globe_object){
	try{
		echo "<br/>Attempting Active Globe Project Select<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		$prepSTMT = $databaseConnection->prepare($query);
		//echo "<br/>Query: ".$query."<br/>"; echo "<br/>Token: ".$sessionToken."<br/>"; echo "<br/>Activity: ".$activity."<br/>";
		$prepSTMT ->bind_param($params, $globe_object);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$num_of_rows = $prepSTMT->num_rows;//echo "<br/>Rows : ".$num_of_rows."<br/>"; 
		$prepSTMT->execute();
		$result = $prepSTMT->get_result();
		//print_r($result->fetch_assoc());
		$prepSTMT->close();
		$record = 0;
		while ($row = $result->fetch_assoc()) {
			$record = $row[$recordID];
		}
		return $record;
		//echo "<br/><br/><br/><br/><br/>";
	}catch(Exception $e){
		echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_selectGlobeID($query, $recordID, $params, $globe_project){
	try{
		echo "<br/>Attempting Active Globe ID Select<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		$prepSTMT = $databaseConnection->prepare($query);
		//echo "<br/>Query: ".$query."<br/>"; echo "<br/>Project: ".$globe_project."<br/>";
		$prepSTMT ->bind_param($params, $globe_project);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$num_of_rows = $prepSTMT->num_rows;//echo "<br/>Rows : ".$num_of_rows."<br/>"; 
		$prepSTMT->execute();
		$result = $prepSTMT->get_result();
		//print_r($result->fetch_assoc());
		$prepSTMT->close();
		$record = 0;
		while ($row = $result->fetch_assoc()) $record = $row[$recordID];
		return $record;
	}catch(Exception $e){
		echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_selectGlobeRevision($query, $field_name, $params, $globe_project){
	try{
		echo "<br/>Attempting Active Globe Revision Select<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		$prepSTMT = $databaseConnection->prepare($query);
		echo "<br/>Query: ".$query."<br/>"; echo "<br/>Project: ".$globe_project."<br/>";
		$prepSTMT ->bind_param($params, $globe_project);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$num_of_rows = $prepSTMT->num_rows;//echo "<br/>Rows : ".$num_of_rows."<br/>"; 
		$prepSTMT->execute();
		$result = $prepSTMT->get_result();
		//print_r($result->fetch_assoc());
		$prepSTMT->close();
		$record = -1;
		while ($row = $result->fetch_assoc()) $record = $row[$field_name];
		return $record;
	}catch(Exception $e){
		echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}


function dbb_selectAssetGlobeID($query){
	try{
		echo "<br/>Attempting Active Globe ID Select<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		$prepSTMT = $databaseConnection->prepare($query);
		//echo "<br/>Query: ".$query."<br/>"; echo "<br/>Project: ".$globe_project."<br/>";
		$prepSTMT ->bind_param($params, $globe_project);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$num_of_rows = $prepSTMT->num_rows;//echo "<br/>Rows : ".$num_of_rows."<br/>"; 
		$prepSTMT->execute();
		$result = $prepSTMT->get_result();
		//print_r($result->fetch_assoc());
		$prepSTMT->close();
		$record = 0;
		while ($row = $result->fetch_assoc()) $record = $row[$recordID];
		return $record;
	}catch(Exception $e){
		echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}


function dbb_insertNewAsset($query, $params, $globe_object, $globe_id){
	try{
		echo "<br/>Attempting Asset Insert<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		echo "<br/>Query: ".$query."<br/>"; echo "<br/>PARAMS: ".$params."<br/>"; echo "<br/>ARGS: ".$globe_object.", ".$globe_id."<br/>";
		$prepSTMT = $databaseConnection->prepare($query);
		echo "<br/>Statement Accepted <br/>";
		$prepSTMT ->bind_param($params, $globe_object, $globe_id);
		$prepSTMT->execute();
		$insert_id = $prepSTMT->insert_id;
		$prepSTMT->close();
		//echo "<br/>Insert ID: ".$insert_id."<br/>"; 
		return $insert_id;
		echo "<br/><br/><br/><br/><br/>";
	}catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_selectUnnassignedProjects($query){
	try{
		$listHolder = array();
		echo "<br/>Attempting Unassigned Project Retrieval<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		echo "<br/>Query: ".$query."<br/>"; 
		$prepSTMT = $databaseConnection->prepare($query);
		$prepSTMT->bind_result($globe_project);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$numRows = $prepSTMT->num_rows;
		echo "<br/>Found : ".$numRows."<br/>"; 
		array_push($listHolder, strval($numRows));
		while ($prepSTMT->fetch()) {
			array_push($listHolder, $globe_project);
		}
		$prepSTMT->close();
	} catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		$listHolder[0] = -1;	
	}
	return $listHolder;
}




?>