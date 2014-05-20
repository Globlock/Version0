<?php
require_once('../package/s_logWrite.php');
require_once('../package/b_configBroker.php');
require_once('../package/b_databaseBroker.php');

$query = "select_all_globes";
$list = dbb_selectAllGlobes($query);
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