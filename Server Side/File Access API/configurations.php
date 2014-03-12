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

?>