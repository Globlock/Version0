<?php
/*
Configurations Test file - Globlock
Filename:	config_test.php
Version: 	1.1
Author: 	Alex Quigley, x10205691
Created: 	11/03/2014
Updated: 	12/03/2014

Dependencies:
	configurations.php (parent) 
	configurations.ini.php (sibling) 
Description: 
	Tests functionality of configurations.php

Successful Operation Result:
	Echo's contents of ini file
	
*/

/* Include configurations file, which in turn reads from configurations.ini.php */
include 'configurations.php';

/* Create a new configuration object and access its public array 'configs' */
$configuration = new configurations();
$configs = $configuration->configs;
//$alternate = $configuration::getConfigurations();
echo "<br/>";

/* output values as JSON */
echo json_encode($configs["project_info"]["name"]);
echo "<br/>";
echo json_encode($configs["project_info"]["description"]);
echo "<br/>";
echo json_encode($configs["project_info"]["version"]);
echo "<br/>";
echo json_encode($configs["project_info"]["runmode"]);

echo "<br/>";
echo "<br/>";

echo $configs["list_items"]["list1"][0].", ";
echo $configs["list_items"]["list1"][1].", ";
echo $configs["list_items"]["list1"][2].", ";
echo $configs["list_items"]["list1"][3];

echo "<br/>";
echo "<br/>";

echo json_encode($configs);

echo "<br/>";
echo "<br/>";

$empty_broker = $configuration->extractSection("empty_broker");
echo json_encode($empty_broker);
echo "<br/>";

echo "<br/>";
echo json_encode($empty_broker["list"][0]);
?>