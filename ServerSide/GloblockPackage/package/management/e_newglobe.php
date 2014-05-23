<?php
#---------------------------------------------------------------#
# External Support for Insert Management - Globlock
# Filename:	e_newglobe.php
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
	$globename = $globedesc = "undefined";
	$configuration = new configurations();
	$configs = $configuration->configs;
	$success = "Failed!"; 
	$validglobe = false;
	
	/** */
	validateglobe($globename, $globedesc, $validglobe);
	/** */
	function validateglobe(&$globename, &$globedesc, &$validglobe) {
		if(isset($_POST["globename"]) && isset($_POST["globedesc"])){ 
			$globename = $_POST["globename"];
			$globedesc = $_POST["globedesc"];
			$validglobe = true;
			echo "<br/>Valid globe: ".$globename."<br/>";
		}
	}
	
	/** */
	if ($validglobe){
		if(insertIntoDB($globename, $globedesc)) $success = "Successful";
	} else {
		$success = "Failed: Values not set!";
	}
	
	echo "Globe Insert ". $success;
	header( "refresh:2;url=../../Management/globes.php" );
	
	/** */
	function insertIntoDB(&$globename, &$globedesc){
		try{
			$query = "insert_new_globe"; 
			$d_id = dbb_insertNewglobe($query, $globename, $globedesc);
			return true;
		} catch(Exception $e){
			return false;
		}
	}
	
	
?>