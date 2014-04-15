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
function pushRequest(&$broker, $globe_id){
	// TO DO - Error handling and writeLog
	$funcTy = new functionTimer();
	$configuration = new configurations();
	$configs = $configuration->configs;
	
	try {
	/* 
		$working_Directory = getWorkingDirectory($globe_id, $configs); 					echo "<br/>Work Dir: ". $working_Directory ."<br/>";// remove	
		$revision = getCurrentRevision($broker, $globe_id); 							echo "<br/>Revision: ". $revision ."<br/>";// remove
	
		$archive_Directory = getArchiveDirectory($globe_id, $revision, $configs);		echo "<br/>Arch Dir: ". $archive_Directory ."<br/>";// remove
		archiveFiles($globe_id, $revision, $configs);
		
		$success = incrementRevision($broker);											echo "<br/>Success1: ". $success ."<br/>";// remove
		 */
		
		if (empty($_FILES)) throw new Exception("Exception Thrown while Pushing files (Files Empty!):");
			//move_uploaded_file($_FILES['ufile']['tmp_name'][0], "small-image/" . $_FILES['ufile']['name'][0]);
		
		$keyName = array_keys($_FILES)[0];												//ALT: foreach($_FILES as $key=>$file) { $keyName = $key; }
		$file_Array =  rearrangeFileArray($_FILES[$keyName]);
		
		foreach ($file_Array as $file) {
			if (strlen($file['name']) > 1){
				// TO DO - Move files to the Current Directory
				$count++;
				echo 'File Name: '. $file['name'] ."<br/>";
				echo 'File Type: '. $file['type'] ."<br/>";
				echo 'File Size: '. $file['size'] ."<br/>";
			}
		}
	} catch (Exception $e){
		// TO DO - Error handling and writeLog
		echo "<br/>Exception! :<br/>".$e;
		
	}
}

/** */
function rearrangeFileArray($file_post){
	$file_Array = array();
	$file_Count = count($file_post['name']);
	$file_Keys  = array_keys($file_post);
	
	    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}


/** */
function pullRequest(&$broker, $globe_id){
	// TO DO - Error handling and writeLog
	$funcTy = new functionTimer();
	$configuration = new configurations();
	$configs = $configuration->configs;

	try {
		$working_Directory = getWorkingDirectory($globe_id, $configs);
		
		prepareRoot($configs["file_locations"]["publish_directory"]);
		$publish_Directory = getPublishDirectory($configs);
		prepareSub($publish_Directory);
		
		publishFiles($working_Directory, $publish_Directory);
		listFiles($directoryFrom, $broker, $configs);
		
	} catch (Exception $e){
		// TO DO
		echo "<br/>Exception in 'pullRequest' !!<br/>";
	}
	$funcTy->getSeconds($time_seconds);
}

/** */
function getWorkingDirectory($globe_id, $configs){
	$storage_directory = $configs["file_locations"]["storage_directory"];
	$working_directory = $configs["file_locations"]["working_directory"];
	$full_Working_Directory = $storage_directory .'/'. $globe_id .'/'. $working_directory;
	return $full_Working_Directory;
}

/** */
function getArchiveDirectory($globe_id, $revision, $configs){
	$storage_directory = $configs["file_locations"]["storage_directory"];
	$archive_directory = $configs["file_locations"]["archive_directory"];
	$full_Archive_Directory = $storage_directory .'/'. $globe_id .'/'. $archive_directory .'/'.$revision;
	return $full_Archive_Directory;
}

/** */
function createDirectory($directory){
	if (!file_exists($directory)) {
		if (!mkdir($directory, 0777, true)){
			// TO DO (replace with writeLog)
			die('Failed to create folders...');	
		}
	}
	return $directory;
}

/** */
function getPublishDirectory(&$configs){
	$publish_directory = $configs["file_locations"]["publish_directory"];
	$sub_Directory = strtoupper(encryptMessage(addSalt(date("Ymdhis") . rand(1,1000), "folder")));
	
	$full_Directory = $publish_directory .'/'. $sub_Directory;
	return $full_Directory;
}

/** */
function prepareRoot($publish_directory){
	// TODO Error handling as this step is critical for security of the file access
	if (!file_exists($publish_directory)){
		createDirectory($publish_directory);
		writeAccessFile("root", $publish_directory);
	}
}

/** */
function prepareSub($publish_Sub_Directory){
	// TODO Error handling as this step is critical for security of the file access
	if (!file_exists($publish_Sub_Directory)){
		createDirectory($publish_Sub_Directory);
		writeAccessFile("sub", $publish_Sub_Directory);
	}
}

/** */
function writeAccessFile($type, $directoryTo){
	// TODO Error handling as this step is critical for security of the files
	// http://stackoverflow.com/questions/7649794/htaccess-deny-root-allow-specific-subfolder-possible Author: nachito
	$filename = ".htaccess";
	$fullname = $directoryTo .'/'. $filename;
	if (file_exists($fullname)) return true;
	
	switch ($type){
		case "root":	
			$first = "Deny"; $second = "Allow";
			break;
		case "sub":
			$first = "Allow"; $second = "Deny";
			break;
	}
	$fileContents = "Order ". $first .",". $second ."\n". $first ." from all";
	if(file_put_contents($fullname, $fileContents)){
		// TO DO - Replace with writelog
		echo "<br/>File created (".basename($fullname).") <br/>";
	}else{
		// TO DO - Replace with writelog
		echo "<br/>Cannot create file (".basename($fullname).") <br/>";
	}

}


/** */
function publishFiles($directoryFrom, $directoryTo){
	// TODO Error handling and writeLog
	if (!file_exists($directoryFrom)) return false;
	// For each file in from, copy 
	foreach(glob($directoryFrom.'/*') as $file) {
	
		$filename = pathinfo($file)['basename'];
		copy($file, $directoryTo.'/'.$filename);
 	}
	return true;
}

/** */
function listFiles($directoryFrom, &$broker, &$configs){
	//todo - Add size information
	$count = $fileSize = 0;
	if (! file_exists($directoryFrom)) return false;
	// for each file published, add to the list
	foreach(glob($directoryFrom.'/*') as $file) {
		$filename = pathinfo($file)['basename'];
		$broker->setValue('list', $count, $filename);
		$fileSize += filesize($file);
		$count++;			
 	}
	// Update the broker meta data
	$full_publish_directory = $configs["file_locations"]["sysroot_directory"] . $directoryFrom;
	$broker->setValue('list', 'count', $count);
	$broker->setValue('list', 'size', getReadableFileSize($fileSize));
	$broker->setValue('list', 'root', $full_publish_directory);
	return true;
}

/** */
function archiveFiles($globe_id, $revision, $configs){
	// TO DO (change to logWrite and exception throw)
	try {
		$working_directory = getWorkingDirectory($globe_id, $configs);
		$archive_directory = getArchiveDirectory($globe_id, $revision, $configs);
		if (!file_exists($working_directory)) return false;
		if (!file_exists($archive_directory)){
			createDirectory($archive_directory);
			writeAccessFile("root", $archive_directory);
		}
		foreach(glob($working_directory .'/*') as $file) {
			$filename = pathinfo($file)['basename'];
			rename($file, $archive_directory.'/'.$filename);
		}
	}catch(Exception $e){
		echo "<br/>Exception! <br/> ";
	}
}

/** */
function getReadableFileSize($bytes, $decimals = 2){
	// Taken from http://www.php.net/manual/en/function.filesize.php author:rommel@rommelsantor.com
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

/** */
function initiateGarbageCollector($directory, $timeout=60){
	// TO DO
	
}


?>