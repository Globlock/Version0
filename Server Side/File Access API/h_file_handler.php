<?php

/** */
function fh_pullRequest(&$broker, $globe_id){
	// TO DO - Error handling and writeLog
	$funcTy = new functionTimer();
	$configuration = new configurations();
	$configs = $configuration->configs;

	try {
		//echo "<br/>Attempting File Pull Request<br/>"; 
		$working_Directory = getWorkingDirectory($globe_id, $configs);
		//echo "<br/>Working Directory: ".$working_Directory."<br/>"; 
		prepareRoot($configs["file_locations"]["publish_directory"]);
		$publish_Directory = getPublishDirectory($configs);
		//echo "<br/>Publish Directory: ".$publish_Directory."<br/>"; 
		prepareSub($publish_Directory);
		publishFiles($working_Directory, $publish_Directory);
		listFiles($publish_Directory, $broker, $configs);
		
	} catch (Exception $e){
		// TO DO
		//echo "<br/>Exception in 'pullRequest' !!<br/>";
	}
	$funcTy->getSeconds($time_seconds);
	//echo "<br/>Time Taken".$time_seconds."<br/>";
}


/** */
function getWorkingDirectory($globe_id, $configs){
	$storage_directory = $configs["file_locations"]["storage_directory"];
	$working_directory = $configs["file_locations"]["working_directory"];
	$full_Working_Directory = $storage_directory .'/'. $globe_id .'/'. $working_directory;
	return $full_Working_Directory;
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
	//echo "<br/>Attempting Root Preparation<br/>"; 
	if (!file_exists($publish_directory)){
		createDirectory($publish_directory);
		writeAccessFile("root", $publish_directory);
	}
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
		//echo "<br/>File created (".basename($fullname).") <br/>";
	}else{
		// TO DO - Replace with writelog
		//echo "<br/>Cannot create file (".basename($fullname).") <br/>";
	}

}

/** */
function prepareSub($publish_Sub_Directory){
	//echo "<br/>Attempting Sub Folder Preparation<br/>"; 
	// TODO Error handling as this step is critical for security of the file access
	if (!file_exists($publish_Sub_Directory)){
		createDirectory($publish_Sub_Directory);
		writeAccessFile("sub", $publish_Sub_Directory);
	}
}

/** */
function publishFiles($directoryFrom, $directoryTo){
	//echo "<br/>Attempting File Publish<br/>"; 
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
	//echo "<br/>Attempting To List Files<br/>"; 
	//TODO - Add size information
	$count = $fileSize = 0;
	if (! file_exists($directoryFrom)) return false;
	// for each file published, add to the list
	foreach(glob($directoryFrom.'/*') as $file) {
		$filename = pathinfo($file)['basename'];
		$broker->setValue('listitem', $count, $filename);
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
function getReadableFileSize($bytes, $decimals = 2){
	// Taken from http://www.php.net/manual/en/function.filesize.php author:rommel@rommelsantor.com
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}
?>