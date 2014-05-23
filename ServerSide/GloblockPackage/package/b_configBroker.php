<?php
#---------------------------------------------------------------#
# Configurations Broker for File Access API - Globlock
# Filename:	b_configBroker.php
# Version: 	2.0
# Author: 	Alex Quigley, x10205691
# Created: 	10/03/2014
# Updated: 	20/05/2014
#---------------------------------------------------------------#
# Dependencies:
# 	GLOBLOCK.php (parent)
#	s_configs.ini.php (child)
#---------------------------------------------------------------#
# Description: 
# 	Contains constant delcarations class of static constants, 
#	and values described in configurations.ini.php file,
#	for use throughout the Globlock Project files.
#---------------------------------------------------------------#
# Successful Operation Result:
# 	Creates constants and configurations from an ini file
#---------------------------------------------------------------#
# Usage: 
#	include 'b_configBroker.php';
#	$configuration = new configurations();
#	$configs = $configuration->configs;
#	echo json_encode($configs["project_info"]["name"]);
#---------------------------------------------------------------#

/** CONFIGURATIONS BROKER CLASS */
class configurations {

	/** Data Members */
	public $configs = array();
	
	/** Returns a PRIVATE_CONST (not currently set */  
		public static function getPrivate_Const() {
			return self::PRIVATE_CONST;
		}
	
	/** GET CONFIGURATIONS 
	 * Parses the ini file and returns a multi dimensional array from its values
	 */
		public static function getConfigurations(){
			$configurations_array = parse_ini_file("s_configs.ini.php", true);
			return $configurations_array;
		}
	
	/** CONSTRUCTOR
	 * Constructor for configurations class instance
	 */
		public function __construct(){
			 $this->configs = $this::getConfigurations();
		} 
	
	/** EXTRACT SECTION
	 * Extracts a particular section of the INI file and returns it
	 */
		public function extractSection($subsection){
			try {
				return $this->configs[$subsection];
			} catch (Exception $e) {
				return null;
			}
		}
}

?>