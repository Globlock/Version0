<?php

	//Includes Needed
	include '../s_logWrite.php';
	include '../b_configBroker.php';
	include '../b_databaseBroker.php';
	include '../h_encryption_handler.php';
		
	/** */
	$user = $pass = $last = $first = $email = $dept = $group = $super = "undefined";
	$configuration = new configurations();
	$configs = $configuration->configs;
	$success = "Failed!"; 
	$includegroup = false;
	$validuser = false;
	
	/** */
	validateUser($user, $name, $pass, $last,  $first,  $email,  $dept, $group,  $super, $includegroup, $validuser);
	
	/** */
	function validateUser(&$user, &$name, &$pass, &$last, &$first, &$email, &$dept, &$group, &$super, &$includegroup, &$validuser) {
		//echo "<br/>Validation...<br/>";
		if(isset($_POST["last_name"]) && isset($_POST["first_name"]) 
		&& isset($_POST["username"]) && isset($_POST["password"])
		&& isset($_POST["email"]) && isset($_POST["dept_code"]) 
		&& isset($_POST["user_type"])){
		
			$user 	= $_POST["username"];
			$pass 	= $_POST["password"];
			
			$last 	= $_POST["last_name"] ;
			$first 	= $_POST["first_name"];
			
			$email 	= $_POST["email"];
			$dept 	= $_POST["dept_code"];
			
			$super 	= $_POST["user_type"];
			
			
			if(isset($_POST["group"]) && ($_POST["group"]<>0) ){
				$group  = $_POST["group"];
				//echo "<br/>Group: ".$group."<br/>";
				$includegroup = true;
			}
			$validuser = true;
			// echo "<br/>Valid User: ".$user."<br/>";
			// echo "<br/>Valid User: ".$pass."<br/>";
			// echo "<br/>Valid User: ".$last."<br/>";
			// echo "<br/>Valid User: ".$first."<br/>";
			// echo "<br/>Valid User: ".$email."<br/>";
			// echo "<br/>Valid User: ".$dept."<br/>";
			// echo "<br/>Valid User: ".$group."<br/>";
			// echo "<br/>Valid User: ".$super."<br/>";
			}
	}
	
	/** */
	if ($validuser){
		//echo "<br/>Valid User<br/>";
		if(insertIntoDB($user, $pass, $last, $first, $email,  $dept, $group,  $super, $includegroup)) $success = "Successful";
	} else {
		$success = "Failed: Values not set!";
	}
	
	echo "User Insert ". $success;
	header( "refresh:2;url=../../Management/Users.php" );
	
	/** */
	function insertIntoDB(&$user, &$pass, &$last, &$first, &$email, &$dept, &$group, &$super, $includegroup){
		$pass = encryptMessage($pass);
		try{
			//echo "<br/>Include Group<br/>";
			if ($includegroup){
				//echo "<br/>Include Group<br/>";
				$query = "insert_new_groupuser"; 
				$user_id = dbb_insertNewGroupUser($query, $user, $pass, $first, $last, $email, $dept, $group, $super);
			} else {
				//echo "<br/>Include Without Group<br/>";
				$query = "insert_new_user"; 
				$user_id = dbb_insertNewUser($query, $user, $pass, $first, $last, $email, $dept, $super);
			}
			return true;
		} catch(Exception $e){
			//echo "<br/>Error: ".$e."<br/>";
			return false;
		}
	}
	
	
?>