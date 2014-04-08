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

function pullRequest(&$broker){
//TO DO
}

/** */
function getPublishDirectory(){
	$configuration = new configurations();
	$configs = $configuration->configs;
	
	$root_directory = $configs["file_publishing"]["root"];
	$subDirectory = strtoupper(encryptMessage(addSalt(date("Ymdhis") . rand(1,1000), "folder")));
	
	$fullDirectory = $root_directory . $subDirectory;
	return createPublishDirectory($fullDirectory);

}

/** */
function createPublishDirectory($directory){
	if (!file_exists($directory)) {
		if (!mkdir($directory, 0777, true)){
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
function initiateGarbageCollector($directory, $timeout=60){

}


?>