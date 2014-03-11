<?php
/*
Constant Declaration Library - Globlock
Filename:	configurations.php
Version: 	1.0
Author: 	Alex Quigley, x10205691
Created: 	10/03/2014
Updated: 	10/03/2014

Dependencies:
	FileAccessAPI.php (parent)
	Configurations.ini
	
Description: 
	Contains constant delcarations class of static constants,
	for use throughout the Globlock Project files.

Successful Operation Result:
	Creates constants
	
Usage: 
	<?php
		include 'configurations.php';
	?>

TO DO:
>>Add Declarations for DB

*/
class configurations {

    /** General Project Constants */
    private const PRIVATE_CONST;

	/** General Project Constants */
	public const PROJ_TITL = "Globlock";
	public const PROJ_DESC = "Globlock - Concurrency controlled 2 phase file access, version control and repository system";
	public const PROJ_VERS = 0.1;
	public const PROJ_AUTH = "Alex Quigley, x10205691";
	public $Proj = array();
	    
	
	public static function getPrivate_Const() {
        return self::$_instance->_someProperty; // allowed, self::$_instance is static, but a real object nonetheless
    }

	
}

//Sample code
//define('BIRD', 'Dodo bird');

// Parse without sections
//$ini_array = parse_ini_file("sample.ini");
//print_r($ini_array);

// Parse with sections
//$ini_array = parse_ini_file("sample.ini", true);
//print_r($ini_array);



/** General Constants */
$proj_constants = array();
define("PROJ_TITL", "Globlock", true);
define("PROJ_DESC", "Globlock - Concurrency controlled 2 phase file access, version control and repository system", true);
define("PROJ_VERS", "0.1", true);
define("PROJ_AUTH", "Alex Quigley, x10205691", true);

$myArray[] = define;

/** Database Constants */
define("DB_HOST", "Welcome to W3Schools.com!", true);
define("DB_NAME", "Welcome to W3Schools.com!", true);
define("DB_USER", "Welcome to W3Schools.com!", true);
define("DB_PASS", "Welcome to W3Schools.com!", true);

//initialize array
echo json_encode($myArray);

//set up the nested associative arrays using literal array notation
//$firstArray = array("id" => 1, "data" => 45);
//$secondArray = array("id" => 3, "data" => 54);

//push items onto main array with bracket notation (this will result in numbered indexes)
//$myArray[] = $firstArray;
//$myArray[] = $secondArray;

//convert to json
//$json = json_encode($myArray);

?>