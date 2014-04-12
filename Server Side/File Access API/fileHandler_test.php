<?php
//fileHandler Test

include 'configurations.php';
include 'encryptionHelper.php';
include 'fileHandler.php';
include 'functionTimer.php';

testFunction();


function testFunction(){
	$folder = getPublishDirectory();
	echo "<br/> ". $folder;
	createDirectory($folder);
	publishFiles("LogFiles", $folder);
}

