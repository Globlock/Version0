<?php
require_once('s_logWrite.php');
require_once('b_configBroker.php');
require_once('b_databaseBroker.php');

$query = "select_all_groupids";
$list = select_all_groupids($query);
if (!empty($list)){
	foreach ($list as $record) {
		echo "<option value=\"" . $record["id"] . "\" >";
		echo $record["name"];
		echo "</option>";
		//echo "option value='" . $record["id"] . "'";
		//echo $record["name"];
		//echo "option";
	}
}
?>