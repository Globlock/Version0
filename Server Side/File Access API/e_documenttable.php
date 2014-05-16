<?php
include 's_logWrite.php';
include 'b_configBroker.php';
include 'b_databaseBroker.php';

$query = "select_all_documents";
$list = dbb_selectAllDocuments($query);
if (!empty($list)){
	foreach ($list as $record) {
		echo "<tr>";
		echo "<td>" . $record["doc"] . "</td>";
		echo "<td>" . $record["desc"]. "</td>";
		echo "<td>" . $record["file"]. "</td>";
		echo "<td>" . $record["date"]. "</td>";
		echo "</tr>";
	}
}
?>

