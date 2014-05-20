<?php
require_once('s_logWrite.php');
require_once('b_configBroker.php');
require_once('b_databaseBroker.php');

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