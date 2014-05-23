<?php
#---------------------------------------------------------------#
# External Support Insert for Management - Globlock
# Filename:	e_newdoc.php
# Version: 	1.0
# Author: 	Alex Quigley, x10205691
# Created: 	18/05/2014
# Updated: 	20/05/2014
#---------------------------------------------------------------#
	//Includes Needed
	include '../s_logWrite.php';
	include '../b_configBroker.php';
	include '../b_databaseBroker.php';
	
	/** Variables */
	$docname = $docdesc = $docfile = $doctype = "undefined";
	$validDocument = $assignglobe = false; $success = "Failed!"; $d_id = 0; $globe_id = 0; 
	
	/** Configurations */
	$configuration = new configurations();
	$configs = $configuration->configs;
	$allowedExts = $configs["file_upload_types"]["ext"];
	$working_directory = $configs["file_locations"]["working_directory"];
	$storage_directory = '../../'.$configs["file_locations"]["storage_directory"];
	$document_directory = $storage_directory ."/". $configs["file_locations"]["document_directory"];
	
	/*
	echo getcwd();
	echo "<br/>Storage: $storage_directory <br/>";
	echo "<br/>Working: $working_directory <br/>";
	echo "<br/>Document: $document_directory <br/>";
	*/
	
	/** */
	validateDocument($docname, $docdesc, $docfile, $doctype, $validDocument, $allowedExts, $globe_id, $assignglobe, $success);
		
	/** */
	if ($validDocument){
		if(insertIntoDB($docname, $docdesc, $docfile, $doctype, $d_id))
			moveToServer($docfile, $d_id, $globe_id, $success, $assignglobe, $working_directory, $storage_directory, $document_directory);	
		echo "Document Upload ". $success;
		header( "refresh:2;url=../../Management/Documents.php" );//TODO: Redirect location from global
	} else {
		echo "Document Upload ". $success;
		header( "refresh:2;url=../../Management/Documents.php" );//TODO: Redirect location from global
	}

	/** */
	function insertIntoDB(&$docname, &$docdesc, &$docfile, &$doctype, &$d_id){
		try{
			$query = "insert_new_document"; 
			//echo "<br/>Query: ".$query."<br/>";
			$d_id = dbb_insertNewDocument($query, $docname, $docdesc , $docfile, $doctype);
			return true;
		} catch(Exception $e){
			return false;
		}
	}
		
	/** */
	function moveToServer($docfile, $d_id, $globe_id, &$success, $assignglobe, $working_directory, $storage_directory, &$document_directory){
		try{
			$document_directory = $document_directory . "/". $d_id;
			if (createDirectory($document_directory)){
				//echo "<p>The '$document_directory' was successfully created and file move complete. </p>";
				$fullPath = $document_directory . "/". $docfile;
			}
			if (move_uploaded_file($_FILES["file"]["tmp_name"], $fullPath)){
				echo "<p>The file '$docfile' was successfully uploaded. </p>";
				$success = "Successful!";
			}
			if ($assignglobe) {
				$working_dir = $storage_directory ."/". $globe_id ."/". $working_directory;
				//echo "<br/>Working Dir: ".$working_dir."<br/>";
				$working_doc = $working_dir ."/". $docfile;
				//echo "<br/>Working Doc: ".$working_doc."<br/>";
				//echo "<p>Attempting to copy from '$fullPath' to '$working_doc'. </p>";
				if(createDirectory($working_dir)){
					copy($fullPath, $working_doc);
					//echo "<p>The file '$docfile' was successfully copied to '$working_doc'. </p>";
				}
			}
		} catch(Exception $e){
			return;
		}
	}
	/** */
	function createDirectory($directory){
		if (!file_exists($directory)) {
			if (!mkdir($directory, 0777, true)){
				// TO DO (replace with writeLog)
				//die('Failed to create folders...');
				return false;
			}
		}
		return true;
	}	
	
	/** VALIDATE DOCUMENT 
	 * Validates a document and assigns all appropriate values
	 *
	 */
	function validateDocument(&$docname, &$docdesc, &$docfile, &$doctype, &$validDocument, $allowedExts, &$globe_id, &$assignglobe, &$success){
		if(isset($_POST["docname"])) $docname = $_POST["docname"];
		if(isset($_POST["docdesc"])) $docdesc = $_POST["docdesc"];
		if(isset($_FILES["file"]["name"])){
			//echo "<br/>Document Set<br/>";
			$docfile = $_FILES["file"]["name"];
			$temp = explode(".", $_FILES["file"]["name"]);
			$doctype = end($temp);
			//echo "<br/>Doc Name: ".$docname." <br/>";
			//echo "<br/>Doc Desc: ".$docdesc." <br/>";
			//echo "<br/>Doc File: ".$docfile." <br/>";
			//echo "<br/>Doc Type: ".$doctype." <br/>";
			if (in_array($doctype, $allowedExts)) {
				$validDocument = true;
			} else {
				$success = "Failed! Invalid File Type";
			}
		} else {
			$success = "Failed! No File added";
		}
		if(isset($_POST["globe"])){
			$assignglobe = true;
			$globe_id = $_POST["globe"];
			//echo "<br/>Globe ID: ".$globe_id." <br/>";
		}
	}

?>