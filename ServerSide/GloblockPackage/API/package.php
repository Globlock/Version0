<?php
#-----------------------------------------------#
# API Package Links for Include - Globlock
# Filename:	package.php
# Version: 	2.0
# Author: 	Alex Quigley, x10205691
# Updated: 	19/05/2014
#-----------------------------------------------#
# Dependencies:
# 	FileAccessAPI.php (parent)
#-----------------------------------------------# 	
# Description: 
# 	Class used to store and manage include files 
#	for use throughout the API.
#-----------------------------------------------#
# Usage: 
 	# include package.php;	
#-----------------------------------------------#
	# SUPPORT
		# Configuration Settings
		include '../package/s_logWrite.php';
	
	# BROKERS
		# Configuration Settings
		include '../package/b_configBroker.php';
		# Database Transactions
		include '../package/b_databaseBroker.php';
		# Broker to capture client requests and JSON
		include '../package/b_requestBroker.php';
		# Encryption for Session Token Creation
	
	# REQUEST HANDLERS
		# Encryption handler (E.G. Session Token Creation)
		include	'../package/h_encryption_handler.php';
		# Session calls and state handler
		include '../package/h_session_handler.php';
		# User handler
		include '../package/h_user_handler.php';
		# Globe validation and handler
		include '../package/h_globe_handler.php';
		# File access handler
		include '../package/h_file_handler.php';
		# Function Timer for Testing
		include '../package/t_functionTimer.php';
	
	
?>