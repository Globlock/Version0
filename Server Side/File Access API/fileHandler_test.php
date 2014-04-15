<?php
//fileHandler Test

include 'configurations.php';
include 'encryptionHelper.php';
include 'fileHandler.php';
include 'functionTimer.php';
include 'requestBroker.php';

testFunction();


function testFunction(){

	$configuration = new configurations();
	$configs = $configuration->configs;
	$broker = new requestBroker("Initialised", null);
	
	$working_Directory = getWorkingDirectory(0, $configs);
	echo "Working Directory: ". $working_Directory ."<br/>";
	
	prepareRoot($configs["file_locations"]["publish_directory"]);
	$publish_Directory = getPublishDirectory($configs);
	prepareSub($publish_Directory);
	echo "Publish Directory: ". $publish_Directory ."<br/>";
		
	publishFiles($working_Directory, $publish_Directory);
	echo "Published!";	
	
	listFiles($publish_Directory, $broker, $configs);
	echo $broker->returnJSON();

}

