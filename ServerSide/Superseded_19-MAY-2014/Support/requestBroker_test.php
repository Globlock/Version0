<?php
/*
Request Broker Test - Globlock
Filename:	requestBroker_test.php
Version: 	1.0
Author: 	Alex Quigley, x10205691
Created: 	12/03/2014
Updated: 	12/03/2014

Dependencies:
	requestBroker.php (parent)
	configurations.php; (sibling)
	
Description: 
	Test brokers functionality and error handling

Successful Operation Result:
	Returns sample JSOn output of a request broker object
*/

include 'configurations.php';
include 'requestBroker.php';

/* Create requestBroker Object */
$broker = new requestBroker("Undefined", null);

/* Setup HTML output */
echo "<html><head></head><body>";

/* echo JSON of broker (no param = no header encoding) */
echo $broker->returnJSON();
echo "<br/><br/>";

/* Validate header information */
echo "Valid header: ".$broker->validateHeader();
echo "<br/><br/><br/>";

/* setup new values and write to broker */
$section = "header";
$type = "type";
$value = "TEST";
$broker->setValue($section, $type, $value);

/* echo JSON again*/
echo $broker->returnJSON();
echo "<br/><br/>";
echo "Valid header: ".$broker->validateHeader();
echo "<br/><br/>";

/*flush the broker object (empty) */
$broker->flushBroker();


/* Close HTML tags */
echo "</body></html>";
?>