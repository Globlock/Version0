;<?php
;die(); // For further security
;/*

;Project Information
[project_info]
proj_name="Globlock"
proj_desc="Globlock - Concurrency controlled 2 phase file access, version control and repository system"
proj_vers= 0.1

;Logfile information for logWrite functions
[logs_transactions]
directory="LogFiles/"
filename="transactions.log"
[logs_security]
directory="LogFiles/"
filename="security_err.log"
[logs_system]
directory="LogFiles/"
filename="system_error.log"

;Database Login and Host Information
[database_info]
db_host="127.0.0.1"
db_name="globlock_test"
db_user="root"
db_pass=""

;SQL Statements
[database_statements]
test_table="SELECT 1 from client_sessions"
create_table="CREATE TABLE IF NOT EXISTS client_sessions (session_id int(11) NOT NULL AUTO_INCREMENT, session_token CHAR(64) NOT NULL DEFAULT '1',session_activity int(11) NOT NULL DEFAULT '0',session_create datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (session_id))"
insert_placeholder="INSERT INTO client_sessions (session_id, session_token, session_activity ) VALUES ( NULL, 0, 0 )"
select_session="SELECT session_id FROM client_sessions WHERE session_activity =? AND session_token=?"
select_session_token="SELECT session_id FROM client_sessions WHERE session_token =? AND session_activity != -1"
update_token="UPDATE client_sessions SET session_activity =?, session_token=? WHERE session_id =?"
update_session="UPDATE client_sessions SET session_activity =?, session_token=? WHERE session_id =?"
dispose_session="UPDATE client_sessions SET session_activity =-1 WHERE session_token =?  AND session_activity != -1"
dispose_expired="UPDATE client_sessions SET session_activity =-1 WHERE DATE_SUB(NOW(),INTERVAL 1 HOUR) > session_create AND session_activity != -1"

;Session stages
[session_stages]
dropped_sessions="-1"
initialise_token="0"
session_requests="1"
globe_validation="2"
globe_deallocate="3"
globe_assignment="4"
globe_pull_assoc="5"
globe_push_assoc="6"

;Encryption Salt values
[encryption_salts]
handshake="HANDSHAKE:abc123_GloblockDevelopmentTest"
session="Session:laundrytokens"
other="Other:abc123_GloblockDevelopmentTest"
default="Default:abc123_GloblockDevelopmentTest"

[test_array]
test1[]=1
test1[]=2
test1[]=3
test1[]=4

;*/

;?>