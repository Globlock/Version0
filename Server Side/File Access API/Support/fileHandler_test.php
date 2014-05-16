<?php
//fileHandler Test
	include 'configurations.php';
	include 'logWrite.php';
	include 'dbconnection.php';
	include 'sessionHandler.php';
	include 'encryptionHelper.php';
	include 'requestBroker.php';
	include 'userHandler.php';
	include 'fileHandler.php';
	include 'functionTimer.php';
	include 'globeHandler.php';
	include 'databaseBroker.php';

//testPull();

testPush();

function testPull(){

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

function testPush(){

	$configuration = new configurations();
	$configs = $configuration->configs;
	$broker = new requestBroker("Initialised", null);
	$broker->setValue('globe', 'project', "sampleglobe");
	$broker->setValue('globe', 'id', "objsample");
	$_POST["globe_id"] = 0;
	$_POST["globe_project"] = 'objsample';
	pushRequest($broker, 0);
	if(isset($_FILES['userfile']['tmp_name'])){
		echo "<br/>Files set! <br/>";
		echo "<br/>". count($_FILES['userfile']['tmp_name']) ."<br/>";
		echo "<br/>". $_FILES['userfile']['name'][0] ."<br/>";
	}
	echo $broker->returnJSON();
}

/*** a message for users ***/
$msg = 'Please select files for uploading';

/*** an array to hold messages ***/
$messages = array();

/*** number of files to upload ***/
$num_uploads = 3;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 
 <head>
	<title>Multiple File Upload</title>
 </head>

 <body>
 
	<h3><?php echo $msg; ?></h3>
	<p>
		<?php
			if(sizeof($messages) != 0) foreach($messages as $err)  echo $err.'<br />';
		?>	
	</p>
	
	<form enctype="multipart/form-data" action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="post">
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>" />
			<?php
				$num = 0;
				while($num < $num_uploads) {
					echo '<div><input name="userfile[]" type="file" /></div>';
					$num++;
				}
			?>
		<input type="submit" value="Upload" />
	</form>

 </body>
 </html>
