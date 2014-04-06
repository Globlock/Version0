;<?php
;die(); // For further security
;/*

;Project Information
[project_info]
name="Globlock"
description="Globlock - Concurrency controlled 2 phase file access, version control and repository system"
version= 0.2
runmode=test


;Logfile information for logWrite functions
[logs_transactions]
directory="LogFiles/"
filename="transactions.log"
[logs_security]
directory="LogFiles/"
filename="security_err.log"
[logs_system]
directory="LogFiles/"
filename="system_error.log"session

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
verify_user="SELECT * FROM system_user WHERE user_name = ? AND user_pass = ?"
search_globe="SELECT * FROM globe_assets_test WHERE globe_object = ?"
unassigned_globes="SELECT * FROM globes_test WHERE globe_asset = 0"

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

[list_items]
list1[]=1
list1[]=2
list1[]=3
list1[]=4

[sample_broker]
header["type"]="TEST_HEAD"
header["message"]="Test messsage"
error["code"]=0001
error["message"]="No errors"
user["name"]="sampleuser@gmail.com"
user["pass"]="pass123"
session["token"]="abcd1234"
globe["id"]=0000
globe["project"]=0000
status["assigned"]=true
action["test"]=true
action["set"]=false
action["abort"]=false
action["redo"]=false
action["drop"]=false
action["pull"]=false
action["push"]=false
list["count"]=2
list["size"]=1000
list[]="file0.txt"
list[]="file1.txt"
list[]="file2.txt"
list[]="file3.txt"
list[]="file4.txt"
list[]="file5.txt"
list[]="file6.txt"
list[]="file7.txt"
list[]="file8.txt"
list[]="file9.txt"

;
[empty_broker]
header["type"]="-"
header["message"]="-"
error["code"]=0000
error["message"]="-"
user["name"]="-"
user["pass"]="-"
session["token"]="-"
globe["id"]=0000
globe["project"]=0000
status["assigned"]=false
action["test"]=false
action["set"]=false
action["abort"]=false
action["redo"]=false
action["drop"]=false
action["pull"]=false
action["push"]=false
list["count"]=0
list["size"]=0
list[]="-"
list[]="-"
list[]="-"
list[]="-"
list[]="-"
list[]="-"
list[]="-"
list[]="-"
list[]="-"
list[]="-"

;List of different error codes fro return
[error_codes]
code[404]="Not Found"


;*/

;?>