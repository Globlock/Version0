<?php
//fileHandler Test

include 'configurations.php';
include 'encryptionHelper.php';
include 'fileHandler.php';

testFunction();


function testFunction(){
	$folder = getPublishDirectory();
	echo "<br/> ". $folder;
	publishFile("LogFiles", $folder);
}