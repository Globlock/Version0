<?php
#---------------------------------------------------------------#
# Database Broker for File Access API - Globlock
# Filename:	b_databaseBroker.php
# Version: 	2.0
# Author: 	Alex Quigley, x10205691
# Created: 	10/02/2014
# Updated: 	20/05/2014
#---------------------------------------------------------------#
# Dependencies:
# 	GLOBLOCK.php (parent)
#	s_logWrite.php
#---------------------------------------------------------------#
# Description: 
# 	File that acts as a broker, containing functions to carry 
# 	out CRUD operations on the systems database. 
#---------------------------------------------------------------#
# Successful Operation Result:
# 	Successfully Creates, Reads, Updates or Deletes to/from DB
#---------------------------------------------------------------#
# Usage: 
#	include 'b_databaseBroker.php';
#---------------------------------------------------------------#

/** Declarations */
$databaseConnection = null;

/** Calls Initial DB setup */
setupDatabaseConnection($databaseConnection);

#---------------------------------------------------------------#
# SETUP {
	/** SETUP DATABASE CONNECTION 
	 * Takes a database connection as a reference, and reads appropriate values 
	 * from the configurations file, and attempts to connect to the database
	 * [required] Parameter $databaseConnection, which defines the DB Connection.
	 * Passed by reference, and updates for system use.
	 */
		function setupDatabaseConnection(&$databaseConnection){
			# Configurations
			$configuration = new configurations();
			$configs = $configuration->configs;
			$db_host = $configs['database_info']['db_host'];
			$db_user = $configs['database_info']['db_user'];
			$db_pass = $configs['database_info']['db_pass'];
			$db_name = $configs['database_info']['db_name'];
			# Create Connection
			$databaseConnection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
			# Connection Validation
			if (!$databaseConnection) {
				writeLogInfo("Connection Failed!");
				writeLogInfo("DB Connection Attempt failed:".mysqli_connect_error(), 1);
				//trigger_error('Could not connect to MySQL: '. mysqli_connect_error());
			} else {
				writeLogInfo("DB Connection Successful!");
			}
		}
# } 
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# MULTI REQUEST {
	/** ACCESS REQUEST 
 * Receives multiple paramaters and depending on type, carries our the necessary 
 * CRUD operations, using a secure prepared statement, with [optional] bound params.  
 * [required] Parameter $query, which defines the SQL statement from config.ini.
 * [required] Parameter $type, which defines the type of query and return value.
 * [required] Parameter $idname, (accepts NULL) which defines the column to return.
 * [required] Parameter $count, (accepts 0) which defines the number of parameters passed.
 * [required] Parameter $params, (accepts NULL) which defines the params of prep statement.
 * [required] Parameter $requestArgs, (accepts NULL) which by reference defines the request 
 * arguments.
 */
	function accessRequest($query, $type, $idname, $count, $params, &$requestArgs){
	global $databaseConnection;
	$configuration = new configurations();
	$configs = $configuration->configs;
	try {
		# Prepare SQL Statement
		$prepSTMT = $databaseConnection->prepare($configs["database_statements"][$query]);
		if(!$prepSTMT) throw new Exception("Exception Thrown (Preparing Statement):".mysqli_error($databaseConnection));
		# Handle multiple arguments to bind
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
		# Execute the statement
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
		# Catch Exceptions and write to log
		writeLogInfo("DB Broker Encountered Error! [ACCESSREQUEST]:$e", 1);
	} 
}
# } 
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# SELECT REQUESTS {
	/** SELECT ACTIVE SESSION
	 * Selects the active sessions and returns the row id
	 */
	function dbb_selectActiveSession($query, $record, $params, $activity, $sessionToken){
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			writeLogInfo("Database[SEL_ACTIVE_SESS]:Attempting Select");
		try{
			$prepSTMT = $databaseConnection->prepare($query);
			$params = 'is';
			$prepSTMT ->bind_param($params, $activity, $sessionToken);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$num_of_rows = $prepSTMT->num_rows;
			$prepSTMT->execute();
			$result = $prepSTMT->get_result();
			$prepSTMT->close();
			$record = 0;
			while ($row = $result->fetch_assoc()) $record = $row['session_id'];
			writeLogInfo("Database[SEL_ACTIVE_SESS]:RecordID=$record");
			return $record;
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_ACTIVE_SESS]:$e", 1);
			return -1;
		}
	}

	/** SELECT ALL DOCUMENTS
	 * Select all documents from the database to display as a table.
	 */
	function dbb_selectAllDocuments($query){
			$listHolder = array();
			$record = array();
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			writeLogInfo("Database[SEL_ALL_DOCS]:Attempting Select");
		try{
			$prepSTMT = $databaseConnection->prepare($query);
			$prepSTMT->bind_result($name, $desc, $file, $date);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$numRows = $prepSTMT->num_rows;
			while ($prepSTMT->fetch()){ 
				$record["doc"] = $name;
				$record["desc"] = $desc;
				$record["file"]= $file;
				$record["date"]= $date;
				array_push($listHolder, $record);
			}
			$prepSTMT->close();
			writeLogInfo("Database[SEL_ALL_DOCS]:Rows=$numRows");
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_ALL_DOCS]:$e", 1);
			return -1;
		}
		return $listHolder;
	}

	/** SELECT ALL GROUPS
	 * Select all groups from the database to display as a table.
	 */
	function dbb_selectAllGroups($query){
			$listHolder = array();
			$record = array();
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			writeLogInfo("Database[SEL_ALL_GRPS]:Attempting Select");
		try{
			$prepSTMT = $databaseConnection->prepare($query);
			$prepSTMT->bind_result($name, $desc, $date);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$numRows = $prepSTMT->num_rows;
			while ($prepSTMT->fetch()){ 
				$record["name"] = $name;
				$record["desc"] = $desc;
				$record["date"]= $date;
				array_push($listHolder, $record);
			}
			$prepSTMT->close();
			writeLogInfo("Database[SEL_ALL_GRPS]:Rows=$numRows");
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_ALL_GRPS]:$e", 1);
			return -1;
		}
		return $listHolder;
	}

	/** SELECT ALL GLOBES
	 * Select all globes from the database to display as a table.
	 */
	function dbb_selectAllGlobes($query){
			$listHolder = array();
			$record = array();
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			writeLogInfo("Database[SEL_ALL_GLBS]:Attempting Select");		
		try{
			$prepSTMT = $databaseConnection->prepare($query);
			$prepSTMT->bind_result($name, $desc, $date);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$numRows = $prepSTMT->num_rows;
			while ($prepSTMT->fetch()){ 
				$record["name"] = $name;
				$record["desc"] = $desc;
				$record["date"] = $date;
				array_push($listHolder, $record);
			}
			$prepSTMT->close();
			writeLogInfo("Database[SEL_ALL_GLBS]:Rows=$numRows");
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_ALL_GLBS]:$e", 1);
			return -1;
		}
		return $listHolder;
	}

	/** SELECT ALL GROUP IDS
	 * Select all groups and their associated id's from the database.
	 */
	function select_all_groupids($query){
			$listHolder = array();
			$record = array();
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			writeLogInfo("Database[SEL_ALL_GRP_IDS]:Attempting Select");
		try{
			$prepSTMT = $databaseConnection->prepare($query);
			$prepSTMT->bind_result($id, $name);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$numRows = $prepSTMT->num_rows;
			while ($prepSTMT->fetch()){ 
				$record["id"] = $id;
				$record["name"] = $name;
				array_push($listHolder, $record);
			}
			$prepSTMT->close();
			writeLogInfo("Database[SEL_ALL_GRP_IDS]:Rows=$numRows");
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_ALL_GRP_IDS]:$e", 1);
			return -1;
		}
		return $listHolder;
	}

	/** SELECT ALL USERS
	 * Select all users from the database.
	 */
	function dbb_selectAllUsers($query){
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			$listHolder = array();
			$record = array();
			writeLogInfo("Database[SEL_ALL_USRS]:Attempting Select");
		try{
			
			$prepSTMT = $databaseConnection->prepare($query);
			$prepSTMT->bind_result($id, $name, $last, $email, $group, $super);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$numRows = $prepSTMT->num_rows;
			while ($prepSTMT->fetch()){ 
				$record["user_id"] = $id;
				$record["name"] = $name;
				$record["last"] = $last;
				$record["email"] = $email;
				$record["groupname"] = $group;
				$record["superuser"] = $super;
				array_push($listHolder, $record);
			}
			$prepSTMT->close();
			writeLogInfo("Database[SEL_ALL_USRS]:Rows=$numRows");
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_ALL_USRS]:$e", 1);
			return -1;
		}
		return $listHolder;
	}

	/** SELECT ALL GLOBE IDS
	 * Select all globes and their associated id's from the database.
	 */
	function select_all_globe_ids($query){
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			$listHolder = array();
			$record = array();
			writeLogInfo("Database[SEL_ALL_GLB_IDS]:Attempting Select");
		try{
			$prepSTMT = $databaseConnection->prepare($query);
			$prepSTMT->bind_result($id, $name);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$numRows = $prepSTMT->num_rows;
			while ($prepSTMT->fetch()){ 
				$record["id"] = $id;
				$record["name"] = $name;
				array_push($listHolder, $record);
			}
			$prepSTMT->close();
			writeLogInfo("Database[SEL_ALL_GLB_IDS]:Rows=$numRows");
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_ALL_GLB_IDS]:$e", 1);
			return -1;
		}
		return $listHolder;
	}

	/** SELECT GLOBE ASSET
	 * Selects a particular globe based on its object and returns the asset id
	 */
	function dbb_selectGlobeAsset($query, $recordID, $params, $globe_object){
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			writeLogInfo("Database[SEL_ASSET_ID]:Attempting Select");
		try{
			$prepSTMT = $databaseConnection->prepare($query);
			$record = 0;
			$prepSTMT ->bind_param($params, $globe_object);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$num_of_rows = $prepSTMT->num_rows;
			$prepSTMT->execute();
			$result = $prepSTMT->get_result();
			$prepSTMT->close();
			while ($row = $result->fetch_assoc()) $record = $row[$recordID];
			writeLogInfo("Database[SEL_ASSET_ID]:RecordID=$record");
			return $record;
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_ASSET_ID]:$e", 1);
			return -1;
		}
	}

	/** SELECT GLOBE PROJECT
	 * Selects a particular globe based on its object and returns the project name
	 */
	function dbb_selectGlobeProject($query, $recordID, $params, $globe_object){
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			writeLogInfo("Database[SEL_GLOBE_PROJ]:Attempting Select");	
		try{ 
			$prepSTMT = $databaseConnection->prepare($query);
			$prepSTMT ->bind_param($params, $globe_object);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$num_of_rows = $prepSTMT->num_rows;
			$prepSTMT->execute();
			$result = $prepSTMT->get_result();
			$prepSTMT->close();
			$record = 0;
			while ($row = $result->fetch_assoc()) $record = $row[$recordID];
			writeLogInfo("Database[SEL_GLOBE_PROJ]:Project=$record");
			return $record;
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_GLOBE_PROJ]:$e", 1);
			return -1;
		}
	}

	/** SELECT GLOBE ID
	 * Selects a particular globe ID
	 */
	function dbb_selectGlobeID($query, $recordID, $params, $globe_project){
		try{
			writeLogInfo("Database[SEL_GLOBE_ID]:Attempting Select");	
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			$prepSTMT = $databaseConnection->prepare($query);
			$prepSTMT ->bind_param($params, $globe_project);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$num_of_rows = $prepSTMT->num_rows;//echo "<br/>Rows : ".$num_of_rows."<br/>"; 
			$prepSTMT->execute();
			$result = $prepSTMT->get_result();
			$prepSTMT->close();
			$record = 0;
			while ($row = $result->fetch_assoc()) $record = $row[$recordID];
			writeLogInfo("Database[SEL_GLOBE_ID]:GlobeID=$record");
			return $record;
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_GLOBE_ID]:$e", 1);
			return -1;
		}
	}

	/** SELECT GLOBE REVISION
	 * Returns the current/latest revision of a globe project based on its object value
	 */
	function dbb_selectGlobeRevision($query, $field_name, $params, $globe_project){
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			writeLogInfo("Database[SEL_GLOBE_REV]:Attempting Select");	
		try{
			$prepSTMT = $databaseConnection->prepare($query);
			$prepSTMT ->bind_param($params, $globe_project);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$num_of_rows = $prepSTMT->num_rows;//echo "<br/>Rows : ".$num_of_rows."<br/>"; 
			$prepSTMT->execute();
			$result = $prepSTMT->get_result();
			$prepSTMT->close();
			$record = -1;
			while ($row = $result->fetch_assoc()) $record = $row[$field_name];
			writeLogInfo("Database[SEL_GLOBE_REV]:GlobeRevision=$record");
			return $record;
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_GLOBE_REV]:$e", 1);
			return -1;
		}
	}

	/** SELECT ASSET GLOBE ID
	 * Returns the Globe ID assigned to a particular asset based on its object value
	 */
	function dbb_selectAssetGlobeID($query){
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			writeLogInfo("Database[SEL_ASSET_GLB]:Attempting Select");	
		try{
			$prepSTMT = $databaseConnection->prepare($query);
			$prepSTMT ->bind_param($params, $globe_project);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$num_of_rows = $prepSTMT->num_rows;
			$prepSTMT->execute();
			$result = $prepSTMT->get_result();
			$prepSTMT->close();
			$record = 0;
			while ($row = $result->fetch_assoc()) $record = $row[$recordID];
			writeLogInfo("Database[SEL_ASSET_GLB]:GlobeID=$record");
			return $record;
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_ASSET_GLB]:$e", 1);
			return -1;
		}
	}

	/** SELECT UNNASSIGNED PROJECTS
	 * Select and return all projects not assigned to a globe object
	 */
	function dbb_selectUnnassignedProjects($query){
			$listHolder = array();
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
		try{
			writeLogInfo("Database[SEL_PRJ_UNASIGN]:Attempting Select");	
			$prepSTMT = $databaseConnection->prepare($query);
			$prepSTMT->bind_result($globe_project);
			$prepSTMT->execute();
			$prepSTMT->store_result();
			$numRows = $prepSTMT->num_rows;
			array_push($listHolder, strval($numRows));
			while ($prepSTMT->fetch()) array_push($listHolder, $globe_project);
			writeLogInfo("Database[SEL_PRJ_UNASIGN]:Rows=$numRows");
			$prepSTMT->close();
		} catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [SEL_PRJ_UNASIGN]:$e", 1);
			$listHolder[0] = -1;	
		}
		return $listHolder;
	}
#} SELECT REQUESTS
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# UPDATE REQUESTS {
	/** UPDATE ACTIVE SESSION 
	 * Updates the sessions activity value
	 */
	function dbb_updateActiveSession($query, $activity, $record){
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			writeLogInfo("Database[UPD_ACTIVE_SESS]:Attempting Update");
		try{	
			$prepSTMT = $databaseConnection->prepare($query);
			$params = 'ii';
			$prepSTMT ->bind_param($params, $activity, $record);
			$prepSTMT->execute();
			$updatedRows = $prepSTMT->affected_rows;
			$prepSTMT->close();
			writeLogInfo("Database[UPD_ACTIVE_SESS]:RowsUpdated=$updatedRows");
			return $updatedRows;
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [UPD_ACTIVE_SESS]:$e", 1);
			return -1;
		}
	}

	/** UPDATE ASSET REVISION 
	 * Updates the asset revision value
	 */
	function dbb_updateAssetRevision($query, $globe_object){
			global $databaseConnection;
			$configuration = new configurations();
			$configs = $configuration->configs;
			$query = $configs["database_statements"][$query];
			writeLogInfo("Database[UPD_ASSET_REV]:Attempting Update");
		try{
			$prepSTMT = $databaseConnection->prepare($query);
			$params = 's';
			$prepSTMT ->bind_param($params, $globe_object);
			$prepSTMT->execute();
			$updatedRows = $prepSTMT->affected_rows;
			$prepSTMT->close();
			writeLogInfo("Database[UPD_ASSET_REV]:RowsUpdated=$updatedRows");
			return $updatedRows;
		}catch(Exception $e){
			writeLogInfo("DB Broker Encountered Error! [UPD_ASSET_REV]:$e", 1);
			return -1;
		}
	}
#} UPDATE REQUESTS
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# INSERT REQUESTS {
		/** INSERT SESSION TOKEN 
		 * Inserts a new session token
		 */
		function dbb_insertNewSessionToken($query, $sessionToken){
				global $databaseConnection;
				$configuration = new configurations();
				$configs = $configuration->configs;
				$query = $configs["database_statements"][$query];
				writeLogInfo("Database[INS_SESS_TOKEN]:Attempting Insert");
			try{
				$prepSTMT = $databaseConnection->prepare($query);
				$params = 'si'; $activity = 1;
				$prepSTMT ->bind_param($params, $sessionToken, $activity);
				$prepSTMT->execute();
				$insert_id = $prepSTMT->insert_id;
				writeLogInfo("Database[INS_SESS_TOKEN]:InsertID=$insert_id");
				return $insert_id;
			}catch(Exception $e){
				writeLogInfo("DB Broker Encountered Error! [INS_SESS_TOKEN]:$e", 1);
				return -1;
			}
		}

		/** INSERT NEW ASSET
		 * Inserts a new asset to the database.
		 */
		function dbb_insertNewAsset($query, $params, $globe_object, $globe_id){
				global $databaseConnection;
				$configuration = new configurations();
				$configs = $configuration->configs;
				$query = $configs["database_statements"][$query];
				writeLogInfo("Database[INS_NEW_ASSET]:Attempting Insert");	
			try{
				$prepSTMT = $databaseConnection->prepare($query);
				$prepSTMT ->bind_param($params, $globe_object, $globe_id);
				$prepSTMT->execute();
				$insert_id = $prepSTMT->insert_id;
				$prepSTMT->close();
				writeLogInfo("Database[INS_NEW_ASSET]:InsertID=$insert_id");
				return $insert_id;
			}catch(Exception $e){
				writeLogInfo("DB Broker Encountered Error! [INS_NEW_ASSET]:$e", 1);
				return -1;
			}
		}

		/** INSERT NEW DOCUMENT
		 * Inserts a new document to the database.
		 */
		function dbb_insertNewDocument($query, $docname, $docdesc, $docfile, $doctype ){
				global $databaseConnection;
				$configuration = new configurations();
				$configs = $configuration->configs;
				$query = $configs["database_statements"][$query];
				writeLogInfo("Database[INS_NEW_DOC]:Attempting Insert");
			try{
				$prepSTMT = $databaseConnection->prepare($query);
				$params = 'ssss'; 
				$prepSTMT ->bind_param($params, $docname, $docdesc, $docfile, $doctype);
				$prepSTMT->execute();
				$insert_id = $prepSTMT->insert_id;
				$prepSTMT->close();
				writeLogInfo("Database[INS_NEW_DOC]:InsertID=$insert_id");
				return $insert_id;
			}catch(Exception $e){
				writeLogInfo("DB Broker Encountered Error! [INS_NEW_DOC]:$e", 1);
				return -1;
			}
		}

		/** INSERT NEW GROUP
		 * Inserts a new group to the database.
		 */
		function dbb_insertNewGroup($query, $groupname, $groupdesc){
				global $databaseConnection;
				$configuration = new configurations();
				$configs = $configuration->configs;
				$query = $configs["database_statements"][$query];
				writeLogInfo("Database[INS_NEW_GRP]:Attempting Insert");
			try{
				$prepSTMT = $databaseConnection->prepare($query);
				$params = 'ss'; 
				$prepSTMT ->bind_param($params, $groupname, $groupdesc);
				$prepSTMT->execute();
				$insert_id = $prepSTMT->insert_id;
				$prepSTMT->close();
				writeLogInfo("Database[INS_NEW_GRP]:InsertID=$insert_id");
				return $insert_id;
			}catch(Exception $e){
				writeLogInfo("DB Broker Encountered Error! [INS_NEW_GRP]:$e", 1);
				return -1;
			}
		}

		/** INSERT NEW GLOBE
		 * Inserts a new globe to the database.
		 */
		function dbb_insertNewglobe($query, $globename, $globedesc){
				global $databaseConnection;
				$configuration = new configurations();
				$configs = $configuration->configs;
				$query = $configs["database_statements"][$query];
				writeLogInfo("Database[INS_NEW_GLB]:Attempting Insert");
			try{
				$prepSTMT = $databaseConnection->prepare($query);
				$params = 'ss'; 
				$prepSTMT ->bind_param($params, $globename, $globedesc);
				$prepSTMT->execute();
				$insert_id = $prepSTMT->insert_id;
				$prepSTMT->close();
				writeLogInfo("Database[INS_NEW_GLB]:InsertID=$insert_id|GlobeName=$globename");
				return $insert_id;
			}catch(Exception $e){
				writeLogInfo("DB Broker Encountered Error! [INS_NEW_GLB]:$e", 1);
				return -1;
			}
		}

		/** INSERT NEW USER
		 * Inserts a new user to the database.
		 */
		function dbb_insertNewUser($query, $user, $pass, $first, $last, $email, $dept, $super){
				global $databaseConnection;
				$configuration = new configurations();
				$configs = $configuration->configs;
				$query = $configs["database_statements"][$query];
				writeLogInfo("Database[INS_NEW_USR]:Attempting Insert");
			try{
				$prepSTMT = $databaseConnection->prepare($query);
				$params = 'ssssssi'; 
				$prepSTMT ->bind_param($params, $user, $pass, $first, $last, $email, $dept, $super);
				$prepSTMT->execute();
				$insert_id = $prepSTMT->insert_id;
				$prepSTMT->close();
				writeLogInfo("Database[INS_NEW_USR]:InsertID=$insert_id|Username=$user");
				return $insert_id;
			}catch(Exception $e){
				writeLogInfo("DB Broker Encountered Error! [INS_NEW_USR]:$e", 1);
				return -1;
			}
		}

		/** INSERT NEW GROUP USER
		 * Inserts a new user to the database with an associated group.
		 */
		function dbb_insertNewGroupUser($query, $user, $pass, $first, $last, $email, $dept, $group, $super){
				global $databaseConnection;
				$configuration = new configurations();
				$configs = $configuration->configs;
				$query = $configs["database_statements"][$query];
				writeLogInfo("Database[INS_GRP_USR]:Attempting Insert");
			try{
				$prepSTMT = $databaseConnection->prepare($query);
				$params = 'ssssssii'; 
				$prepSTMT ->bind_param($params, $user, $pass, $first, $last, $email, $dept, $group, $super);
				$prepSTMT->execute();
				$insert_id = $prepSTMT->insert_id;
				writeLogInfo("Database[INS_GRP_USR]:InsertID=$insert_id|Username=$user|Group=$group");
				$prepSTMT->close();
				return $insert_id;
			}catch(Exception $e){
				writeLogInfo("DB Broker Encountered Error! [INS_GRP_USR]:$e", 1);
				return -1;
			}
		}
#} UPDATE REQUESTS
#---------------------------------------------------------------#
?>