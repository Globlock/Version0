<?php
/*
Database Broker - Globlock
Filename:	databaseBroker.php
Version: 	1.0
Author: 	Alex Quigley, x10205691
Created: 	15/04/2014
Updated: 	15/04/2014

Dependencies:
	<<filename>>.<<ext>> (parent) *[optional]
	<<filename>>.<<ext>> (child) *[optional]
	
Description: 
	<<expanded description>>

Successful Operation Result:
	<<General successflow of use case>>
	
Usage: 
	<<example code usage>> *[optional]

TO DO:
<<to do list items>> *[optional]
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
				$result = $prepSTMT->bind_param($params, $requestArgs[0], $requestArgs[1], $requestArgs[1]);
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
		$prepSTMT->close();
	} 
}

?>