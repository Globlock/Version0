<?php
//fileHandler Test
include 'package.php';

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
	$broker->setValue('globe', 'id', "72004107E5");
	$_POST["globe_id"] = '72004107E5';
	$_POST["globe_project"] = 'objsample';
	//pushRequest($broker, 0);
	/*
	if (isset($_POST['session_token'])){
		mkdir("temp", 0700);
		$output = print_r($_FILES, true);
		file_put_contents('temp\output.txt', $output, FILE_APPEND);
	}
	
	if(isset($_FILES['file1']['tmp_name'])){
		if (move_uploaded_file($_FILES['file1']["tmp_name"], "upload/".$_FILES['file1']["name"])){
			file_put_contents('output.txt', "\n\r File downloaded! \n\r", FILE_APPEND);
		}		
	}
	if(isset($_FILES['file2']['tmp_name'])){	
		if (move_uploaded_file($_FILES['file2']["tmp_name"], "upload/".$_FILES['file2']["name"])){
			file_put_contents('output.txt', "\n\r File downloaded! \n\r", FILE_APPEND);
		}	
	}
	*/
	fh_pushRequest($broker, "102");
	echo $broker->returnJSON();
}

/** */
function rearrangeFileArray($file_post){
	$file_Array = array();
	$file_Count = count($file_post['name']);
	$file_Keys  = array_keys($file_post);
	
	    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
			
        }
    }
	
	

    return $file_ary;
}

/*** a message for users ***/
$msg = 'Please select files for uploading';

/*** an array to hold messages ***/
$messages = array();

/*** number of files to upload ***/
$num_uploads = 2;

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
				echo "<div><input name='file1' type='file' /></div>";
				echo "<div><input name='file2' type='file' /></div>";
				
			?>
		<input type="submit" value="Upload" />
	</form>

 </body>
 </html>
