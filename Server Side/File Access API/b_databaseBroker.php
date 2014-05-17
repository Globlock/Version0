<?php

$databaseConnection = null;
setupDatabaseConnection($databaseConnection);

function setupDatabaseConnection(&$databaseConnection){

	$configuration = new configurations();
	$configs = $configuration->configs;
	$db_host = $configs['database_info']['db_host'];
	$db_user = $configs['database_info']['db_user'];
	$db_pass = $configs['database_info']['db_pass'];
	$db_name = $configs['database_info']['db_name'];

	$databaseConnection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

	if (!$databaseConnection) {
		writeLogInfo("Connection Failed!");
		writeLogInfo("DB Connection Attempt failed:".mysqli_connect_error(), 1);
		//trigger_error('Could not connect to MySQL: '. mysqli_connect_error());
	} else {
		writeLogInfo("DB Connection Successful!");
	}
}
/* OLD CONSTANTS

	Define constants to connect to database 
	DEFINE('DATABASE_USER', 'root');
	DEFINE('DATABASE_PASS', '');
	DEFINE('DATABASE_HOST', '127.0.0.1');
	DEFINE('DATABASE_NAME', 'gb_production');
	// TO DO - Read from config


	writeLogInfo("Attempting DB Connection...");
	$databaseConnection = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

	if (!$databaseConnection) {
		writeLogInfo("Connection Failed!");
		writeLogInfo("DB Connection Attempt failed:".mysqli_connect_error(), 1);
		//trigger_error('Could not connect to MySQL: '. mysqli_connect_error());
	} else {
		writeLogInfo("DB Connection Successful!");
	}
*/


function accessRequest($query, $type, $idname, $count, $params, &$requestArgs){
	/** Declarations */
	global $databaseConnection;
	$configuration = new configurations();
	$configs = $configuration->configs;
	try {
		// Prepare the SQL Statement
		$prepSTMT = $databaseConnection->prepare($configs["database_statements"][$query]);
		if(!$prepSTMT) throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		// Handle multiple arguments
		switch ($count){
			case 0:	//
				break;
			case 1:
				$result = $prepSTMT->bind_param($params, $requestArgs[0]);
				if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
				break;
			 case 2:
				$result = $prepSTMT->bind_param($params, $requestArgs[0], $requestArgs[1]);
				if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
				break;
			case 3: 
				$result = $prepSTMT->bind_param($params, $requestArgs[0], $requestArgs[1], $requestArgs[2]);
				if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
				break; 
			case 10:
				$prepSTMT->bind_result($globe_name);
				break;
		}
		
		// Execute the statement
		$prepSTMT->execute();
		switch ($type){
			case "create":
				$result = $prepSTMT->get_result();
				$prepSTMT->close();
				return $result;
			case "id":	// Return an ID if found, otherwise 0
				$result = $prepSTMT->get_result();
				$prepSTMT->close();
				if ( $myrow = $result->fetch_assoc()) return $myrow[$idname];
				return 0;
				break;
			case "rows":
				$updatedRows = $prepSTMT->affected_rows;
				$prepSTMT->close();
				return $updatedRows;
				break;
			case "insert":
				$insert_id->$prepSTMT->insert_id;
				$prepSTMT->close;
				return $insert_id;
				break;
			case "list1": 
				$count = 0;
				$prepSTMT->bind_result($value);
				$prepSTMT->store_result();
				$numRows = $prepSTMT->num_rows;
				array_push($requestArgs, strval($numRows));
				while ($prepSTMT->fetch()) {
					array_push($requestArgs, $value);
					$count++;
				}
				$prepSTMT->close();
				break;
		}
	} catch(Exception $e){
		//$prepSTMT->close();
		//echo "<br/>Error occured<br/>";
	} 
}


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
		echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_selectActiveSession($query, $record, $params, $activity, $sessionToken){
		//echo "<br/>Attempting Active Token Select<br/>"; 
	try{
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
		while ($row = $result->fetch_assoc()) $record = $row['session_id'];
		return $record;
		//echo "<br/><br/><br/><br/><br/>";
	}catch(Exception $e){
		echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_updateActiveSession($query, $activity, $record){
	try{
		//echo "<br/>Attempting Active Token Update<br/>"; 
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
	}catch(Exception $e){
		echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_selectGlobeAsset($query, $recordID, $params, $globe_object){
	try{
		//echo "<br/>Attempting Active Globe Asset Select<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		$prepSTMT = $databaseConnection->prepare($query);
		//echo "<br/>Query: ".$query."<br/>"; echo "<br/>Token: ".$globe_object."<br/>";
		$record = 0;
		$prepSTMT ->bind_param($params, $globe_object);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$num_of_rows = $prepSTMT->num_rows;
		//echo "<br/>Rows : ".$num_of_rows."<br/>"; 
		$prepSTMT->execute();
		$result = $prepSTMT->get_result();
		$prepSTMT->close();
		while ($row = $result->fetch_assoc()) $record = $row[$recordID];
		//echo "<br/>Record: ".$record."<br/>";
		return $record;
	}catch(Exception $e){
		echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_selectGlobeProject($query, $recordID, $params, $globe_object){
	try{
		//echo "<br/>Attempting Active Globe Project Select<br/>"; 
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
		while ($row = $result->fetch_assoc()) $record = $row[$recordID];
		return $record;
		//echo "<br/><br/><br/><br/><br/>";
	}catch(Exception $e){
		echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_selectGlobeID($query, $recordID, $params, $globe_project){
	try{
		//echo "<br/>Attempting Active Globe ID Select<br/>"; 
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
		//echo "<br/>Attempting Active Globe Revision Select<br/>"; 
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
		//echo "<br/>Attempting Active Globe ID Select<br/>"; 
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
		//echo "<br/>Attempting Asset Insert<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		//echo "<br/>Query: ".$query."<br/>"; echo "<br/>PARAMS: ".$params."<br/>"; echo "<br/>ARGS: ".$globe_object.", ".$globe_id."<br/>";
		$prepSTMT = $databaseConnection->prepare($query);
		//echo "<br/>Statement Accepted <br/>";
		$prepSTMT ->bind_param($params, $globe_object, $globe_id);
		$prepSTMT->execute();
		$insert_id = $prepSTMT->insert_id;
		$prepSTMT->close();
		//echo "<br/>Insert ID: ".$insert_id."<br/>"; 
		return $insert_id;
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
		while ($prepSTMT->fetch()) array_push($listHolder, $globe_project);
		$prepSTMT->close();
	} catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		$listHolder[0] = -1;	
	}
	return $listHolder;
}

function dbb_insertNewDocument($query, $docname, $docdesc, $docfile, $doctype ){
	try{
		//echo "<br/>Attempting Document Insert<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		//echo "<br/>Query: ".$query."<br/>"; 
		$prepSTMT = $databaseConnection->prepare($query);
		$params = 'ssss'; 
		$prepSTMT ->bind_param($params, $docname, $docdesc, $docfile, $doctype);
		$prepSTMT->execute();
		$insert_id = $prepSTMT->insert_id;
		//echo "<br/>Insert ID: ".$insert_id."<br/>"; 
		$prepSTMT->close();
		return $insert_id;
	}catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_insertNewGroup($query, $groupname, $groupdesc){
	try{
		//echo "<br/>Attempting Group Insert<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		//echo "<br/>Query: ".$query."<br/>"; 
		$prepSTMT = $databaseConnection->prepare($query);
		$params = 'ss'; 
		$prepSTMT ->bind_param($params, $groupname, $groupdesc);
		$prepSTMT->execute();
		$insert_id = $prepSTMT->insert_id;
		//echo "<br/>Insert ID: ".$insert_id."<br/>"; 
		$prepSTMT->close();
		return $insert_id;
	}catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_insertNewglobe($query, $globename, $globedesc){
	try{
		//echo "<br/>Attempting globe Insert<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		//echo "<br/>Query: ".$query."<br/>"; 
		$prepSTMT = $databaseConnection->prepare($query);
		$params = 'ss'; 
		$prepSTMT ->bind_param($params, $globename, $globedesc);
		$prepSTMT->execute();
		$insert_id = $prepSTMT->insert_id;
		//echo "<br/>Insert ID: ".$insert_id."<br/>"; 
		$prepSTMT->close();
		return $insert_id;
	}catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_insertNewUser($query, $user, $pass, $first, $last, $email, $dept, $super){
	try{
		//echo "<br/>Attempting User Insert<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		//echo "<br/>Query: ".$query."<br/>"; 
		$prepSTMT = $databaseConnection->prepare($query);
		$params = 'ssssssi'; 
		$prepSTMT ->bind_param($params, $user, $pass, $first, $last, $email, $dept, $super);
		$prepSTMT->execute();
		$insert_id = $prepSTMT->insert_id;
		//echo "<br/>Insert ID: ".$insert_id."<br/>"; 
		$prepSTMT->close();
		return $insert_id;
	}catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}

function dbb_insertNewGroupUser($query, $user, $pass, $first, $last, $email, $dept, $group, $super){
	try{
		//echo "<br/>Attempting Group User Insert<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		//echo "<br/>Query: ".$query."<br/>"; 
		$prepSTMT = $databaseConnection->prepare($query);
		$params = 'ssssssii'; 
		$prepSTMT ->bind_param($params, $user, $pass, $first, $last, $email, $dept, $group, $super);
		$prepSTMT->execute();
		$insert_id = $prepSTMT->insert_id;
		//echo "<br/>Insert ID: ".$insert_id."<br/>"; 
		$prepSTMT->close();
		return $insert_id;
	}catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
}


function dbb_selectAllDocuments($query){
	try{
		$listHolder = array();
		$record = array();
		//echo "<br/>Attempting All Document Retrieval<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		//echo "<br/>Query: ".$query."<br/>"; 
		$prepSTMT = $databaseConnection->prepare($query);
		$prepSTMT->bind_result($name, $desc, $file, $date);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$numRows = $prepSTMT->num_rows;
		//echo "<br/>Found : ".$numRows."<br/>"; 
		while ($prepSTMT->fetch()){ 
			$record["doc"] = $name;
			$record["desc"] = $desc;
			$record["file"]= $file;
			$record["date"]= $date;
			array_push($listHolder, $record);
		}
		//print_r($listHolder);
		$prepSTMT->close();
	}catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
	return $listHolder;
}

function dbb_selectAllGroups($query){
	try{
		$listHolder = array();
		$record = array();
		//echo "<br/>Attempting All Groups Retrieval<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		//echo "<br/>Query: ".$query."<br/>"; 
		$prepSTMT = $databaseConnection->prepare($query);
		$prepSTMT->bind_result($name, $desc, $date);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$numRows = $prepSTMT->num_rows;
		//echo "<br/>Found : ".$numRows."<br/>"; 
		while ($prepSTMT->fetch()){ 
			$record["name"] = $name;
			$record["desc"] = $desc;
			$record["date"]= $date;
			array_push($listHolder, $record);
		}
		//print_r($listHolder);
		$prepSTMT->close();
	}catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
	return $listHolder;
}

function dbb_selectAllGlobes($query){
	try{
		$listHolder = array();
		$record = array();
		//echo "<br/>Attempting All Groups Retrieval<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		//echo "<br/>Query: ".$query."<br/>"; 
		$prepSTMT = $databaseConnection->prepare($query);
		$prepSTMT->bind_result($name, $desc, $date);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$numRows = $prepSTMT->num_rows;
		//echo "<br/>Found : ".$numRows."<br/>"; 
		while ($prepSTMT->fetch()){ 
			$record["name"] = $name;
			$record["desc"] = $desc;
			$record["date"] = $date;
			array_push($listHolder, $record);
		}
		//print_r($listHolder);
		$prepSTMT->close();
	}catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
	return $listHolder;
}

function select_all_groupids($query){
	try{
		$listHolder = array();
		$record = array();
		//echo "<br/>Attempting All Groups ID's Retrieval<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		//echo "<br/>Query: ".$query."<br/>"; 
		$prepSTMT = $databaseConnection->prepare($query);
		$prepSTMT->bind_result($id, $name);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$numRows = $prepSTMT->num_rows;
		//echo "<br/>Found : ".$numRows."<br/>"; 
		while ($prepSTMT->fetch()){ 
			$record["id"] = $id;
			$record["name"] = $name;
			array_push($listHolder, $record);
		}
		//print_r($listHolder);
		$prepSTMT->close();
	}catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
	return $listHolder;
}
function dbb_selectAllUsers($query){
	try{
		$listHolder = array();
		$record = array();
		//echo "<br/>Attempting All Groups ID's Retrieval<br/>"; 
		global $databaseConnection;
		$configuration = new configurations();
		$configs = $configuration->configs;
		$query = $configs["database_statements"][$query];
		//echo "<br/>Query: ".$query."<br/>"; 
		$prepSTMT = $databaseConnection->prepare($query);
		$prepSTMT->bind_result($id, $name, $last, $email, $group, $super);
		$prepSTMT->execute();
		$prepSTMT->store_result();
		$numRows = $prepSTMT->num_rows;
		//echo "<br/>Found : ".$numRows."<br/>"; 
		while ($prepSTMT->fetch()){ 
			$record["user_id"] = $id;
			$record["name"] = $name;
			$record["last"] = $last;
			$record["email"] = $email;
			$record["groupname"] = $group;
			$record["superuser"] = $super;
			array_push($listHolder, $record);
		}
		//print_r($listHolder);
		$prepSTMT->close();
	}catch(Exception $e){
		//echo "<br/>Error: ".$e."<br/>"; 
		return -1;
	}
	return $listHolder;
}



?>