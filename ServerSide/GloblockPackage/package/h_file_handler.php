<?php
#---------------------------------------------------------------#
# File Handler for File Access API - Globlock
# Filename:	h_file_handler.php
# Version: 	2.0
# Author: 	Alex Quigley, x10205691
# Created: 	07/04/2014
# Updated: 	20/05/2014
#---------------------------------------------------------------#
# Dependencies:
# 	GLOBLOCK.php (parent)
#	encryptionHandler.php
#---------------------------------------------------------------#
# Description: 
# 	Handles file requests, such as file pushing and pulling.
#	Only accessed with valid SESSION and USER information.
#---------------------------------------------------------------#
# Successful Operation Result:
# 	Returns the required information / handles files 
#	and DB transactions.
#---------------------------------------------------------------#
# Usage: 
#	include 'h_session_handler.php';
#---------------------------------------------------------------#

	/** PULL REQUEST
	 * Attempts to publish the requested globe project to a published and accessible location
	 * [required] Parameter $globe_id, defines globe to be pulled.
	 * [required] Parameter $broker by reference, defines broker to be used.
	 */
		function fh_pullRequest(&$broker, $globe_id){
			$funcTy = new functionTimer();
			$configuration = new configurations();
			$configs = $configuration->configs;
			writeLogInfo("FILE[PULL_RQST]:Attempting PULL");
			try {
				$working_Directory = getWorkingDirectory($globe_id, $configs);
				prepareRoot($configs["file_locations"]["publish_directory"]);
				$publish_Directory = getPublishDirectory($configs);
				prepareSub($publish_Directory);
				publishFiles($working_Directory, $publish_Directory);
				listFiles($publish_Directory, $broker, $configs);
			} catch (Exception $e){
				writeLogInfo("FILE[PULL_RQST]:Error Occured!");
			}
			writeLogInfo("FILE[PULL_RQST]:Time Taken [".$funcTy->getSeconds($time_seconds)."]");
		}

	/** PUSH REQUEST
	 * Attempts to push the files uploaded to the requested globe projects current directory, while archiving whats currently there
	 * [required] Parameter $globe_id, defines globe to be pulled.
	 * [required] Parameter $broker by reference, defines broker to be used.
	 */
		function fh_pushRequest(&$broker, $globe_id){
				$funcTy = new functionTimer();
				$configuration = new configurations();
				$configs = $configuration->configs;
				writeLogInfo("FILE[PUSH_RQST]:Attempting PUSH");
			try {
				if (empty($_FILES)) throw new Exception("Exception Thrown while Pushing files (Files Empty!):");
				$latestrevision = gh_AssetRevision($broker);
				$working_Directory = getWorkingDirectory($globe_id, $configs);
				$archive_Directory = getArchiveDirectory($globe_id, $latestrevision, $configs);
				writeLogInfo("FILE[PUSH_RQST]:Globe=$globe_id|LatestRevision=$latestrevision|Archive=$archive_Directory|Working=$working_Directory");
				# Archive
				archiveFiles($globe_id, $latestrevision, $configs);
				# FILE 1
				if(isset($_FILES['file1']['tmp_name'])){
					$newfile = $working_Directory."/".$_FILES['file1']["name"];
					writeLogInfo("FILE[PUSH_RQST]:NewFile=$newfile");
					if(file_exists($newfile)) unlink($newfile);
					if (move_uploaded_file($_FILES['file1']["tmp_name"], $newfile)){
						writeLogInfo("FILE[PUSH_RQST]:'$newfile' uploaded!");
					}		
				}
				# FILE 2
				if(isset($_FILES['file2']['tmp_name'])){	
					$newfile = $working_Directory."/".$_FILES['file2']["name"];
					writeLogInfo("FILE[PUSH_RQST]:NewFile=$newfile");
					if(file_exists($newfile)) unlink($newfile);
					if (move_uploaded_file($_FILES['file2']["tmp_name"], $newfile)){
						writeLogInfo("FILE[PUSH_RQST]:'$newfile' uploaded!");
					}	
				}
				# FILE 3
				if(isset($_FILES['file3']['tmp_name'])){
					$newfile = $working_Directory."/".$_FILES['file3']["name"];
					writeLogInfo("FILE[PUSH_RQST]:NewFile=$newfile");
					if(file_exists($newfile)) unlink($newfile);
					if (move_uploaded_file($_FILES['file3']["tmp_name"], $newfile)){
						writeLogInfo("FILE[PUSH_RQST]:'$newfile' uploaded!");
					}		
				}
				# FILE 4
				if(isset($_FILES['file4']['tmp_name'])){	
					$newfile = $working_Directory."/".$_FILES['file4']["name"];
					writeLogInfo("FILE[PUSH_RQST]:NewFile=$newfile");
					if(file_exists($newfile)) unlink($newfile);
					if (move_uploaded_file($_FILES['file4']["tmp_name"], $newfile)){
						writeLogInfo("FILE[PUSH_RQST]:'$newfile' uploaded!");
					}	
				}
				$success = ((gh_updateRevision($broker, $latestrevision) > 0));
				writeLogInfo("FILE[PUSH_RQST]:Complete!");
			} catch (Exception $e){
				writeLogInfo("FILE[PUSH_RQST]:Error Occured! $e", 1);
			}
		}

	/** GET ARCHIVE DIRECTORY
	 * Generates an Archive directory path
	 * [required] Parameter $globe_id, defines globe project id.
	 * [required] Parameter $revision, defines globe asset revision.
	 * [required] Parameter $configs, defines configurations set to be used for path information.
	 */
		function getArchiveDirectory($globe_id, $revision, $configs){
			$storage_directory = "../".$configs["file_locations"]["storage_directory"];
			$archive_directory = $configs["file_locations"]["archive_directory"];
			$full_Archive_Directory = $storage_directory .'/'. $globe_id .'/'. $archive_directory .'/'.$revision;
			return $full_Archive_Directory;
		}

	/** ARCHIVE FILES
	 * Creates the Archive directory and copies the current file set to the archive folder for PUSH requests
	 * [required] Parameter $globe_id, defines globe project id.
	 * [required] Parameter $revision, defines globe asset revision.
	 * [required] Parameter $configs, defines configurations set to be used for path information.
	 */
		function archiveFiles($globe_id, $revision, $configs){
			writeLogInfo("FILE[ARCH_FLS]:Attempting Archival");
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
					copy($file, $archive_directory.'/'.$filename);
				}
				writeLogInfo("FILE[ARCH_FLS]:Complete");
			}catch(Exception $e){
				writeLogInfo("FILE[ARCH_FLS]:Error Occured! $e", 1);
			}
		}


	/** GET WORKING DIRECTORY
	 * Gets the working directory for the globe project, for PULL and PUSH requests
	 * [required] Parameter $globe_id, defines globe project id.
	 * [required] Parameter $configs, defines configurations set to be used for path information.
	 */
		function getWorkingDirectory($globe_id, $configs){
			$storage_directory = "../".$configs["file_locations"]["storage_directory"];
			$working_directory = $configs["file_locations"]["working_directory"];
			$full_Working_Directory = $storage_directory .'/'. $globe_id .'/'. $working_directory;
			return $full_Working_Directory;
		}

	/** GET WORKING DIRECTORY
	 * Gets the publish directory for the globe project, for PULL requests
	 * Generates the publish directory name from encryption helper
	 * [required] Parameter $globe_id, defines globe project id.
	 * [required] Parameter $configs, defines configurations set to be used for path information.
	 */
		function getPublishDirectory(&$configs){
			$publish_directory = $configs["file_locations"]["publish_directory"];
			$sub_Directory = strtoupper(encryptMessage(addSalt(date("Ymdhis") . rand(1,1000), "folder")));
			$full_Directory = $publish_directory .'/'. $sub_Directory;
			return $full_Directory;
		}

	/** PREPARE SUB
	 * Prepares the sub directory of the publish directory 
	 * for file publishing with PULL requests
	 * [required] Parameter $publish_Sub_Directory, defines directory to prepare sub folder of.
	 */
		function prepareSub($publish_Sub_Directory){
			writeLogInfo("FILE[PREP_SB]:Attempting Archival");
			try {
				if (!file_exists($publish_Sub_Directory)){
					createDirectory($publish_Sub_Directory);
					writeAccessFile("sub", $publish_Sub_Directory);
				}
			}catch(Exception $e){
				writeLogInfo("FILE[PREP_SB]:Error Occured! $e", 1);
			}
		}
		
	/** PREPARE ROOT
	 * Prepares the root directory of the publish directory 
	 * for file publishing with PULL requests
	 * [required] Parameter $publish_directory, defines directory to prepare root of.
	 */
		function prepareRoot($publish_directory){
			writeLogInfo("FILE[PREP_RT]:Attempting Archival");
			try {
				if (!file_exists($publish_directory)){
					createDirectory($publish_directory);
					writeAccessFile("root", $publish_directory);
				}
			}catch(Exception $e){
				writeLogInfo("FILE[PREP_RT]:Error Occured! $e", 1);
			}
		}

	/** CREATE DIRECTORY
	 * Creates the root directory 
	 * [required] Parameter $directory, defines directory to create.
	 */
		function createDirectory($directory){
			if (!file_exists($directory)) {
				if (!mkdir($directory, 0777, true)){
					// TO DO (replace with writeLog)
					die('Failed to create folders...');	
				}
			}
			return $directory;
		}

	/** WRITE ACCESS FILE
	 * Creates an .htaccess file in the folder supplied, to manage apache folder publishing
	 * [required] Parameter $type, defines type of file root/sub
	 * [required] Parameter $directoryTo, directory to write access file to
	 */
		function writeAccessFile($type, $directoryTo){
			writeLogInfo("FILE[ACCSS_FILE]:Creating '$type' htaccess File");
			try{
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
					writeLogInfo("FILE[ACCSS_FILE]:File created (".basename($fullname).")");
				}else{
					writeLogInfo("FILE[ACCSS_FILE]:Cannot create file (".basename($fullname).")", -1);
				}
			}catch(Exception $e){
				writeLogInfo("FILE[ACCSS_FILE]:Error Occured! $e", -1);
			}
			# http://stackoverflow.com/questions/7649794/htaccess-deny-root-allow-specific-subfolder-possible Author: nachito
		}


	/** PUBLISH FILES
	 * Creates the Archive directory and copies the current file set to the archive folder
	 * [required] Parameter $directoryFrom, defines directory to copy From.
	 * [required] Parameter $directoryTo, defines directory to copy To.
	 * [required] Parameter $configs, defines configurations set to be used for path information.
	 */
		function publishFiles($directoryFrom, $directoryTo){
			writeLogInfo("FILE[PBLSH_FLS]:Attempting Publish");
			try {
				if (!file_exists($directoryFrom)) return false;
				// For each file in from, copy 
				foreach(glob($directoryFrom.'/*') as $file) {
					$filename = pathinfo($file)['basename'];
					copy($file, $directoryTo.'/'.$filename);
				}
				writeLogInfo("FILE[PBLSH_FLS]:Complete");
				return true;
			} catch(Exception $e){
				writeLogInfo("FILE[PBLSH_FLS]:Error Occured! $e", 1);
				false;
			}
		}

	/** LIST FILES
	 * Lists files in a particular directory
	 * [required] Parameter $directoryFrom, defines directory to copy From.
	 * [required] Parameter $directoryTo, defines directory to copy To.
	 * [required] Parameter $configs, defines configurations set to be used for path information.
	 */
		function listFiles($directoryFrom, &$broker, &$configs){
			writeLogInfo("FILE[LST_FLS]:Attempting Listing");
			$count = $fileSize = 0;
			if (! file_exists($directoryFrom)) return false;
			foreach(glob($directoryFrom.'/*') as $file) {
				$filename = pathinfo($file)['basename'];
				$broker->setValue('listitem', $count, $filename);
				$fileSize += filesize($file);
				$count++;			
			}
			$full_publish_directory = $configs["file_locations"]["sysroot_directory"] . $directoryFrom;
			$broker->setValue('list', 'count', $count);
			$broker->setValue('list', 'size', getReadableFileSize($fileSize));
			$broker->setValue('list', 'root', $full_publish_directory);
			writeLogInfo("FILE[LST_FLS]:Completed for '$count' files");
			return true;
		}

	/** GET READABLE FILE SIZE 
	 * Returns the file size in a readable format for display and logging
	 */
		function getReadableFileSize($bytes, $decimals = 2){
			// Taken from http://www.php.net/manual/en/function.filesize.php author:rommel@rommelsantor.com
			$sz = 'BKMGTP';
			$factor = floor((strlen($bytes) - 1) / 3);
			return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
		}
		
		
	/** REARRANGE FILE ARRAY
	 * NOT USED IN CURRENT CONTEXT 
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
	*/
?>