<?php
#---------------------------------------------------------------#
# External Support Table Populater for Management - Globlock
# Filename:	e_usertable.php
# Version: 	1.0
# Author: 	Alex Quigley, x10205691
# Created: 	18/05/2014
# Updated: 	20/05/2014
#---------------------------------------------------------------#
require_once('../package/s_logWrite.php');
require_once('../package/b_configBroker.php');
require_once('../package/b_databaseBroker.php');

$query = "select_all_users";
$list = dbb_selectAllUsers($query);
if (!empty($list)){
	foreach ($list as $record) {
		echo "<tr>";
		//echo "<td>" . $record["user_id"]. "</td>";
		echo "<td>" . $record["name"]. "</td>";
		echo "<td>" . $record["last"]. "</td>";
		echo "<td>" . $record["email"]. "</td>";
		echo "<td>" . $record["groupname"]. "</td>";
		echo "<td>" . $record["superuser"]. "</td>";
		echo "</tr>";
	}
}
?>