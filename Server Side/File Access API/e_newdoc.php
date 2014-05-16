<?php
	//Insert a new document
	include 's_logWrite.php';
	include 'b_configBroker.php';
	include 'b_databaseBroker.php';
	
	/** */
	$docname = $docdesc = $docfile = $doctype = "undefined";
	$configuration = new configurations();
	$configs = $configuration->configs;
	$allowedExts = $configs["file_upload_types"]["ext"];
	$document_directory = $configs["file_locations"]["storage_directory"] ."/". $configs["file_locations"]["document_directory"];
	$validDocument = false; $success = "Failed!"; $d_id = 0;
	
	/** */
	validateDocument($docname, $docdesc, $docfile, $doctype, $validDocument, $allowedExts);
	
	/** */
	function validateDocument(&$docname, &$docdesc, &$docfile, &$doctype, &$validDocument, $allowedExts){
		if(isset($_POST["docname"])) $docname = $_POST["docname"];
		if(isset($_POST["docdesc"])) $docdesc = $_POST["docdesc"];
		if(isset($_FILES["file"]["name"])){
			echo "<br/>Document Set<br/>";
			$docfile = $_FILES["file"]["name"];
			$temp = explode(".", $_FILES["file"]["name"]);
			$doctype = end($temp);
			echo "<br/>Doc Name: ".$docname." <br/>";
			echo "<br/>Doc Desc: ".$docdesc." <br/>";
			echo "<br/>Doc File: ".$docfile." <br/>";
			echo "<br/>Doc Type: ".$doctype." <br/>";
			if (in_array($doctype, $allowedExts)) $validDocument = true;
		}
		//echo "<br/>Valid Extension: ".$doctype."<br/>";
	}
		
	/** */
	if ($validDocument){
		if(insertIntoDB($docname, $docdesc, $docfile, $doctype, $d_id))
			moveToOriginals($document_directory, $docfile, $d_id);	
		echo "Document Upload ". $success;
		//header( "refresh:2;url=index.php" );//TODO: Redirect location from global
	}

	/** */
	function insertIntoDB(&$docname, &$docdesc, &$docfile, &$doctype, &$d_id){
		try{
			$query = "insert_new_document"; 
			echo "<br/>Query: ".$query."<br/>";
			$d_id = dbb_insertNewDocument($query, $docname, $docdesc , $docfile, $doctype);
			return true;
		} catch(Exception $e){
			return false;
		}
	}
		
	/** */
	function moveToOriginals(&$document_directory, $docfile, $d_id){
		try{
			$document_directory = $document_directory . "/". $d_id;
			if (createDirectory($document_directory)){
				echo "<p>The '$document_directory' was successfully created and file move complete. </p>";
				$fullPath = $document_directory . "/". $docfile;
			}
			if (move_uploaded_file($_FILES["file"]["tmp_name"], $fullPath)){
				echo "<p>The file '$docfile' was successfully uploaded. </p>";
				$success = "Successful!";
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

?>