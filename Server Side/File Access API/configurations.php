<?php
/*
Constant Declaration Library - Globlock
Filename:	configurations.php
Version: 	1.2
Author: 	Alex Quigley, x10205691
Created: 	10/03/2014
Updated: 	12/03/2014

Dependencies:
	FileAccessAPI.php (parent)
	Configurations.ini.php (child)
	
Description: 
	Contains constant delcarations class of static constants, 
	and values described in configurations.ini.php file,
	for use throughout the Globlock Project files.

Successful Operation Result:
	Creates constants and configurations from an ini file
	
Usage: 
	<?php
		include 'configurations.php';
		$configuration = new configurations();
		$configs = $configuration->configs;
		echo json_encode($configs["project_info"]["name"]);
	?>


*/
class configurations {

    /** General Project Constants */
    //private const PRIVATE_CONST;

	/** General Project Constants */
	//public const PUBLIC_CONST = "Globlock";
	public $configs = array();
	   
	public static function getPrivate_Const() {
        return self::PRIVATE_CONST;
    }
	
	public static function getConfigurations(){
		$configurations_array = parse_ini_file("configurations.ini.php", true);
		return $configurations_array;
	}
	
	public function __construct(){
		 $this->configs = $this::getConfigurations();
	} 
	
	public function extractSection($subsection){
		try {
			return $this->configs[$subsection];
		} catch (Exception $e) {
			return null;
		}
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



/** Database Constants */
define("DB_HOST", "Welcome to W3Schools.com!", true);
define("DB_NAME", "Welcome to W3Schools.com!", true);
define("DB_USER", "Welcome to W3Schools.com!", true);
define("DB_PASS", "Welcome to W3Schools.com!", true);

//initialize array
//echo json_encode($myArray);

//set up the nested associative arrays using literal array notation
//$firstArray = array("id" => 1, "data" => 45);
//$secondArray = array("id" => 3, "data" => 54);

//push items onto main array with bracket notation (this will result in numbered indexes)
//$myArray[] = $firstArray;
//$myArray[] = $secondArray;

//convert to json
//$json = json_encode($myArray);

?>