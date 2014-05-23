<?php
#---------------------------------------------------------------#
# External Support Table Populater for Management - Globlock
# Filename:	e_grouptable.php
# Version: 	1.0
# Author: 	Alex Quigley, x10205691
# Created: 	18/05/2014
# Updated: 	20/05/2014
#---------------------------------------------------------------#
require_once('../package/s_logWrite.php');
require_once('../package/b_configBroker.php');
require_once('../package/b_databaseBroker.php');

$query = "select_all_groups";
$list = dbb_selectAllGroups($query);
if (!empty($list)){
	foreach ($list as $record) {
		echo "<tr>";
		echo "<td>" . $record["name"]. "</td>";
		echo "<td>" . $record["desc"]. "</td>";
		echo "<td>" . $record["date"]. "</td>";
		echo "</tr>";
	}
}
?>