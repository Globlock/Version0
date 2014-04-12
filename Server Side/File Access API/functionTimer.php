<?php
/*
Function Timer - Globlock
Filename:	functionTimer.php
Version: 	1.0
Author: 	Alex Quigley, x10205691
Created: 	12/04/2014
Updated: 	12/04/2014

Dependencies:
	FileAccessAPI.php (parent) 
	
Description: 
	Simple object which on creation, logs a time, and on dispose returns the time as an interval in seconds

Successful Operation Result:
	On creation logs a time, and on dispose, returns the time in seconds from start and end
	
Usage: 
	<<example code usage>> *[optional]

TO DO:
>>Example usage
*/
class functionTimer {

	private $time_start, $time_end, $time_pause;
	
	/** Constructor */
	public function __construct(){
		 $this->time_start =  microtime(true);
		 $this->time_end = 0;
		 $this->time_pause = 0;
	} 
		
	/** Getters */
	public function getStartTime() {
        return $this->time_start;
    }
	public function getEndTime() {
        return $this->time_end;
    }
	public function getPauseTime() {
        return $this->time_pause;
    }
	
	/** Pause and resume */
	public function pauseTime() {
		$this->time_pause = microtime(true);
	}	
	public function resumeTime(){
		$this->time_pause = microtime(true) - $this->time_pause;
	}
	
	/** Stop, Dipose and Reset */
	public function stopTime(){
		return disposeTime();
	}
	public function disposeTime(){
		$this->time_end = microtime(true);
		$time_total = $this->time_end - ($this->time_start + $this->time_pause);
		return round($time_total, 4);
	}
	public function resetTime(){
		$this->time_start = microtime(true);
		$this->time_pause = 0;
	}
	
	/** Stop timer and return value by reference */
	public function getSeconds(&$time_seconds){
		$time_seconds = round($this->disposeTime(),4);
	}
	
	/** wait for a number of seconds */
	public static function waitTime($time_Second=1){
		usleep($time_Second*1000000);
	}
	
}
?>