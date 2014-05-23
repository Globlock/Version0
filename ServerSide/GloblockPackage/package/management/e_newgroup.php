<?php
#---------------------------------------------------------------#
# External Support for Insert Management - Globlock
# Filename:	e_newgroup.php
# Version: 	1.0
# Author: 	Alex Quigley, x10205691
# Created: 	18/05/2014
# Updated: 	20/05/2014
#---------------------------------------------------------------#
	//Includes Needed
	include '../s_logWrite.php';
	include '../b_configBroker.php';
	include '../b_databaseBroker.php';
	
	/** */
	$groupname = $groupdesc = "undefined";
	$configuration = new configurations();
	$configs = $configuration->configs;
	$success = "Failed!"; 
	$validGroup = false;
	
	/** */
	validateGroup($groupname, $groupdesc, $validGroup);
	/** */
	function validateGroup(&$groupname, &$groupdesc, &$validGroup) {
		if(isset($_POST["groupname"]) && isset($_POST["groupdesc"])){ 
			$groupname = $_POST["groupname"];
			$groupdesc = $_POST["groupdesc"];
			$validGroup = true;
			echo "<br/>Valid Group: ".$groupname."<br/>";
		}
	}
	
	/** */
	if ($validGroup){
		if(insertIntoDB($groupname, $groupdesc)) $success = "Successful";
	} else {
		$success = "Failed: Values not set!";
	}
	
	echo "Group Insert ". $success;
	header( "refresh:2;url=../../Management/Groups.php" );
	
	/** */
	function insertIntoDB(&$groupname, &$groupdesc){
		try{
			$query = "insert_new_group"; 
			$d_id = dbb_insertNewGroup($query, $groupname, $groupdesc);
			return true;
		} catch(Exception $e){
			return false;
		}
	}
	
	
?>