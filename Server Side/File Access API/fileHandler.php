<?php
/*
File Handler - Globlock
Filename:	fileHandler.php
Version: 	1.0
Author: 	Alex Quigley, x10205691
Created: 	07/04/2014
Updated: 	07/04/2014

Dependencies:
	FileAccessAPI.php (parent)
	encryptionHandler.php (sibling)
	
Description: 
	Handles file requests, such as file pushing and pulling
	Only accessed with valid SESSION and USER information

Successful Operation Result:
	Returns the required information / handles files and DB transactions
	
Usage: 
	<<example code usage>> *[optional]

TO DO:
>>Example Code usage
>>createPublishDirectory
>>moveFilesForPublish
>>Initiate Garbage Collector
>>logWrite and error handling

*/

/** */
function pushRequest(&$broker){
	//TO DO
	
}

function pullRequest(&$broker, $globe_id){
	$funcTy = new functionTimer();
	$configuration = new configurations();
	$configs = $configuration->configs;

	try {
		$working_Directory = getWorkingDirectory($globe_id, $configs);
		$publish_Directory = createDirectory(getPublishDirectory());
		publishFiles($working_Directory, $publish_Directory);
		$funcTy->getSeconds($time_seconds);
	} catch (Exception $e){
		// TO DO
		echo "<br/>Exception!!<br/>";
	}
}

/** */
function getWorkingDirectory($globe_id, $configs){
	$storage_directory = $configs["file_locations"]["storage_directory"];
	$working_directory = $configs["file_locations"]["working_directory"];
	$full_Working_Directory = $storage_directory .'/'. $globe_id .'/'. $working_directory;
	return $full_Working_Directory;
}

/** */
function createDirectory($directory){
	if (!file_exists($directory)) {
		if (!mkdir($directory, 0777, true)){
			// TO DO (replace with writeLog)
			die('Failed to create folders...');	
		}
	}
	return true;
}

/** */
function getPublishDirectory(){
	$configuration = new configurations();
	$configs = $configuration->configs;
	
	$publish_directory = $configs["file_locations"]["publish_directory"];
	$sub_Directory = strtoupper(encryptMessage(addSalt(date("Ymdhis") . rand(1,1000), "folder")));
	
	$full_Directory = $publish_directory .'/'. $sub_Directory;
	return $full_Directory;
}

/** */
function publishFiles($directoryFrom, $directoryTo){
	// Calculate the time taken to move files.
	// If this is outside our limits, it will require further development
	if (file_exists($directoryFrom)) {
		foreach(glob($directoryFrom.'/*') as $file) {
			$filename = pathinfo($file)['filename'];
			copy($file, $directoryTo.'/'.$filename);
 		}
	} else {
		return false;
	}
	return true;
}

/** */
function archiveFile($globe_id, $revision, $configs){
	// TO DO (change to logWrite and exception throw)
	$working_directory = getWorkingDirectory($globe_id, $configs);
	$archive_directory = getArchiveDirectory($globe_id, $revision, $configs);
	if (!file_exists($working_directory)) return false;
	if (!file_exists($archive_directory)){
		if (!createDirectory()) return false;
	}
	foreach(glob($working_directory .'/*') as $file) {
		$filename = pathinfo($file)['filename'];
	}
}

/** */
function getArchiveDirectory($globe_id, $revision, $configs){
	$storage_directory = $configs["file_locations"]["storage_directory"];
	$archive_directory = $configs["file_locations"]["archive_directory"];
	$full_Archive_Directory = $storage_directory .'/'. $globe_id .'/'. $archive_directory .'/'.$revision;
	return $full_Archive_Directory;
}

/** */
function initiateGarbageCollector($directory, $timeout=60){
	// TO DO
	
}


?>