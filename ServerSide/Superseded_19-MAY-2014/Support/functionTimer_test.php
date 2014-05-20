<?php
/*
Function Timer Test - Globlock
Filename:	functionTimer_test.php
Version: 	1.0
Author: 	Alex Quigley, x10205691
Created: 	12/04/2014
Updated: 	12/04/2014

Dependencies:
	functionTimer.php (parent) 
	
Description: 
	Tests the methods and functionality of functionTimer.php

Successful Operation Result:
	Outputs results
*/

include 'functionTimer.php';

$time_start = microtime(true);

// Sleep for a while
usleep(100);

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Timer took $time seconds";

echo "<br/><br/>";
test_ObjectCreate();
echo "- Create wait for 2 and getSeconds";
echo "<br/><br/>";
test_DisposeTime();
echo "- Create wait for 2 and disposeTime";
echo "<br/><br/>";
test_PauseResumeTime();
echo "<br/>";

/** */
function test_ObjectCreate(){
	$funcTy = new functionTimer();
	functionTimer::waitTime(2);
	$funcTy->getSeconds($time_seconds);
	echo $time_seconds;
}

/** */
function test_DisposeTime(){
	$funcTy = new functionTimer();
	functionTimer::waitTime(2);
	$time_seconds = $funcTy->disposeTime();
	echo $time_seconds;
}

/** */
function test_PauseResumeTime(){
	echo "Start:";
	$funcTy = new functionTimer();
	echo $funcTy->getStartTime();
	echo "<br/>";
	echo "Wait: 1 Seconds";
	functionTimer::waitTime(1);
	echo "<br/>";
	echo "Pause: 2 Second";
	$funcTy->pauseTime();
	functionTimer::waitTime(2);
	$funcTy->resumeTime();
	echo "<br/>";
	echo "Resume: 1 second";
	functionTimer::waitTime(1);
	$funcTy->getSeconds($time_seconds);
	echo "<br/>";
	echo "<br/>";
	echo "Wait Time:".$time_seconds;
}

?>