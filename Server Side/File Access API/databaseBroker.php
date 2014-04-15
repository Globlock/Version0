<?php
/*
<<Simple File Desc>> - <<Project>>
Filename:	<<filename>>.<<ext>>
Version: 	x.x
Author: 	Alex Quigley, x10205691
Created: 	<<date>>
Updated: 	<<date>>

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


function accessRequest($query, $type, $idname, $count, $params, $requestArgs){
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
		}
		
		// Execute the statement
		$prepSTMT->execute();
		
		switch ($type){
			case "id":	// Return an ID if found, otherwise 0
				$result = $prepSTMT->get_result();
				if ( $myrow = $result->fetch_assoc()) return $myrow[$idname];
				return 0;
				break;
			case "rows":
				$updatedRows = $prepSTMT->affected_rows;
				$prepSTMT->close();
				return $updatedRows;
			case 2:
				$result = $prepSTMT->bind_param($params, $args[0], $args[1]);
				if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
				break;
			case 3: 
				$result = $prepSTMT->bind_param($params, $args[0], $args[1], $args[1]);
				if (!$result) throw new Exception("Exception Thrown (Binding):".mysqli_error($databaseConnection));
				break;
		}
	} catch(Exception $e){
		$prepSTMT->close();
	} 
}

?>