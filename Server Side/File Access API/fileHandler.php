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

*/

/** */
function pushRequest(&$broker){
//TO DO
	
}

function pullRequest(&$broker, $globe_id){
	$configuration = new configurations();
	$configs = $configuration->configs;

	//TO DO
	$workingDirectory = getWorkingDirectory($globe_id, $configs);
		// retrieve the record id
		// get the directory path for the current directory
		// createPublishDirectory
		// for each file that is in there, publish 
}

/** */
function getPublishDirectory(){
	$configuration = new configurations();
	$configs = $configuration->configs;
	
	$publish_directory = $configs["file_locations"]["publish_directory"];
	$subDirectory = strtoupper(encryptMessage(addSalt(date("Ymdhis") . rand(1,1000), "folder")));
	
	$fullDirectory = $publish_directory .'/'. $subDirectory;
	return createPublishDirectory($fullDirectory);
}

/** */
function createPublishDirectory($directory){
	if (!file_exists($directory)) {
		if (!mkdir($directory, 0777, true)){
			// TO DO (replace with writeLog)
			die('Failed to create folders...');	
		}
	}
	return $directory;
}

/** */
function publishFile($directoryFrom, $directoryTo){
	//>>TO DO
	echo "<br/> in the mix ";
	if (file_exists($directoryFrom)) {
		foreach(glob($directoryFrom.'/*') as $file) {
			$filename = pathinfo($file)['filename'];
			copy($file, $directoryTo.'/'.$filename);
 		}
	} 
	// Initiate garbage Collector
	initiateGarbageCollector($directoryTo);
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
function getWorkingDirectory($globe_id, $configs){
	$storage_directory = $configs["file_locations"]["storage_directory"];
	$working_directory = $configs["file_locations"]["working_directory"];
	$fullWorkingDirectory = $storage_directory .'/'. $globe_id .'/'. $working_directory;
	return $fullWorkingDirectory;
}

/** */
function getArchiveDirectory($globe_id, $revision, $configs){
	$storage_directory = $configs["file_locations"]["storage_directory"];
	$archive_directory = $configs["file_locations"]["archive_directory"];
	$fullArchiveDirectory = $storage_directory .'/'. $globe_id .'/'. $archive_directory .'/'.$revision;
	return $fullArchiveDirectory;
}

/** */
function initiateGarbageCollector($directory, $timeout=60){

}


?>