<?php
#---------------------------------------------------------------#
# External Support Option Populater for Management - Globlock
# Filename:	e_globeids.php
# Version: 	1.0
# Author: 	Alex Quigley, x10205691
# Created: 	18/05/2014
# Updated: 	20/05/2014
#---------------------------------------------------------------#
require_once('../package/s_logWrite.php');
require_once('../package/b_configBroker.php');
require_once('../package/b_databaseBroker.php');

$query = "select_all_globe_ids";
$list = select_all_globe_ids($query);
if (!empty($list)){
	foreach ($list as $record) {
		echo "<option value=\"" . $record["id"] . "\" >";
		echo $record["name"];
		echo "</option>";
	}
}
?>